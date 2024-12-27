<?php
require_once "../config/connection.php";

function generateFixture($tournament_id, $version_id, $category_id) {
    global $con;
    
    // Obtener detalles del torneo con la configuración de puntos
    $stmt = $con->prepare("SELECT tv.id as version_id, tv.format_type, 
                          tv.points_winner, tv.points_draw, tv.points_loss,
                          tvd.start_date, tvd.end_date, tvd.playing_days, tvd.match_time_range 
                          FROM tournament_versions tv 
                          JOIN tournament_version_details tvd ON tv.id = tvd.version_id 
                          WHERE tv.tournament_id = ? AND tv.id = ?");
    $stmt->bind_param("ii", $tournament_id, $version_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $tournament = $result->fetch_assoc();
    
    if (!$tournament) {
        return ['success' => false, 'message' => 'Versión del torneo no encontrada'];
    }
    
    // Obtener equipos solo de la versión específica del torneo
    $stmt = $con->prepare("SELECT id FROM teams WHERE tournament_id = ? AND tournament_version_id = ? AND category_id = ?");
    $stmt->bind_param("iii", $tournament_id, $version_id, $category_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $teams = $result->fetch_all(MYSQLI_ASSOC);
    
    if (count($teams) < 2) {
        return ['success' => false, 'message' => 'Se necesitan al menos 2 equipos para generar el fixture'];
    }
    // Iniciar transacción
    $con->begin_transaction();
    
    try {
        // Generar fixtures según el formato del torneo
        $fixtures = [];
        switch ($tournament['format_type']) {
            case 'Liga':
                $fixtures = generateLeagueFixtures($teams, $tournament);
                break;
            case 'Eliminatoria':
                $fixtures = generateKnockoutFixtures($teams, $tournament);
                break;
            case 'Play Off':
                $fixtures = generatePlayOffFixtures($teams, $tournament);
                break;
            default:
                throw new Exception('Formato de torneo no soportado');
        }
        
        if (empty($fixtures)) {
            throw new Exception('No se pudieron generar los fixtures');
        }
        
        // Guardar fixtures en la base de datos
    $stmt = $con->prepare("INSERT INTO fixtures (tournament_version_id, home_team_id, away_team_id, match_date, match_time) VALUES (?, ?, ?, ?, ?)");
    
    foreach ($fixtures as $fixture) {
        $stmt->bind_param("iiiss", $version_id, $fixture['home_team_id'], $fixture['away_team_id'], $fixture['date'], $fixture['time']);
        if (!$stmt->execute()) {
            throw new Exception('Error al guardar los fixtures: ' . $stmt->error);
        }
    }

    // Inicializar tabla de posiciones
    $stmt = $con->prepare("INSERT INTO standings (tournament_version_id, team_id, played, won, drawn, lost, goals_for, goals_against, points) VALUES (?, ?, 0, 0, 0, 0, 0, 0, 0)");
    
    foreach ($teams as $team) {
        $stmt->bind_param("ii", $version_id, $team['id']);
        if (!$stmt->execute()) {
            throw new Exception('Error al inicializar la tabla de posiciones: ' . $stmt->error);
        }
    }

        $con->commit();
        return ['success' => true, 'message' => 'Fixture generado exitosamente'];
        
    } catch (Exception $e) {
        $con->rollback();
        return ['success' => false, 'message' => $e->getMessage()];
    }
}
function generateLeagueFixtures($teams, $tournament) {
    $fixtures = [];
    $num_teams = count($teams);
    $rounds = $num_teams - 1;
    $matches_per_round = $num_teams / 2;
    
    $playing_days_map = [
        'Lunes' => 'Monday', 'Martes' => 'Tuesday', 'Miércoles' => 'Wednesday',
        'Jueves' => 'Thursday', 'Viernes' => 'Friday', 'Sábado' => 'Saturday', 'Domingo' => 'Sunday'
    ];
    
    $playing_days_raw = explode(',', $tournament['playing_days']);
    $playing_days = array_map(function($day) use ($playing_days_map) {
        return $playing_days_map[trim($day)] ?? trim($day);
    }, $playing_days_raw);
    
    $start_date = new DateTime($tournament['start_date']);
    $end_date = new DateTime($tournament['end_date']);
    
    $time_range = explode(' - ', $tournament['match_time_range']);
    $start_time = new DateTime($time_range[0]);
    $end_time = new DateTime($time_range[1]);
    
    $time_diff = $start_time->diff($end_time);
    $total_minutes = ($time_diff->h * 60) + $time_diff->i;
    $match_duration = 120; // 2 hours per match including buffer
    $slots_per_day = max(1, floor($total_minutes / $match_duration));
    
    $current_date = clone $start_date;
    $total_matches = $rounds * $matches_per_round;
    $match_count = 0;
    
    while ($match_count < $total_matches && $current_date <= $end_date) {
        if (in_array($current_date->format('l'), $playing_days)) {
            for ($slot = 0; $slot < $slots_per_day && $match_count < $total_matches; $slot++) {
                $round = floor($match_count / $matches_per_round);
                $match = $match_count % $matches_per_round;
                
                $home_team = $teams[$match];
                $away_team = $teams[$num_teams - 1 - $match];
                
                if ($match == 0 && $round % 2 == 1) {
                    $temp = $home_team;
                    $home_team = $away_team;
                    $away_team = $temp;
                }
                
                $match_time = clone $start_time;
                $match_time->modify('+' . ($slot * $match_duration) . ' minutes');
                
                if ($match_time > $end_time) {
                    $match_time = clone $end_time;
                }
                
                $fixtures[] = [
                    'home_team_id' => $home_team['id'],
                    'away_team_id' => $away_team['id'],
                    'date' => $current_date->format('Y-m-d'),
                    'time' => $match_time->format('H:i:s')
                ];
                
                $match_count++;
            }
        }
        $current_date->modify('+1 day');
    }
    
    if ($match_count < $total_matches) {
        throw new Exception('No hay suficientes días disponibles para programar todos los partidos.');
    }
    
    $last_team = array_pop($teams);
    array_splice($teams, 1, 0, [$last_team]);
    
    return $fixtures;
}

function generateKnockoutFixtures($teams, $tournament) {
    $fixtures = [];
    $num_teams = count($teams);
    $rounds = ceil(log($num_teams, 2));
    
    $playing_days_map = [
        'Lunes' => 'Monday', 'Martes' => 'Tuesday', 'Miércoles' => 'Wednesday',
        'Jueves' => 'Thursday', 'Viernes' => 'Friday', 'Sábado' => 'Saturday', 'Domingo' => 'Sunday'
    ];
    
    $playing_days_raw = explode(',', $tournament['playing_days']);
    $playing_days = array_map(function($day) use ($playing_days_map) {
        return $playing_days_map[trim($day)] ?? trim($day);
    }, $playing_days_raw);
    
    $start_date = new DateTime($tournament['start_date']);
    $end_date = new DateTime($tournament['end_date']);
    
    $time_range = explode(' - ', $tournament['match_time_range']);
    $start_time = new DateTime($time_range[0]);
    $end_time = new DateTime($time_range[1]);
    
    $time_diff = $start_time->diff($end_time);
    $total_minutes = ($time_diff->h * 60) + $time_diff->i;
    $match_duration = 120; // 2 hours per match including buffer
    $slots_per_day = max(1, floor($total_minutes / $match_duration));
    
    shuffle($teams);
    
    $current_date = clone $start_date;
    $match_count = 0;
    $total_matches = $num_teams - 1; // Number of matches in a knockout tournament
    
    for ($round = 0; $round < $rounds; $round++) {
        $matches_in_round = $num_teams / (pow(2, $round + 1));
        for ($match = 0; $match < $matches_in_round; $match++) {
            while ($current_date <= $end_date) {
                if (in_array($current_date->format('l'), $playing_days)) {
                    $slot = $match_count % $slots_per_day;
                    $match_time = clone $start_time;
                    $match_time->modify('+' . ($slot * $match_duration) . ' minutes');
                    
                    if ($match_time > $end_time) {
                        $match_time = clone $end_time;
                    }
                    
                    $home_index = $match * 2;
                    $away_index = $home_index + 1;
                    
                    if (isset($teams[$home_index]) && isset($teams[$away_index])) {
                        $fixtures[] = [
                            'home_team_id' => $teams[$home_index]['id'],
                            'away_team_id' => $teams[$away_index]['id'],
                            'date' => $current_date->format('Y-m-d'),
                            'time' => $match_time->format('H:i:s')
                        ];
                        
                        $match_count++;
                        if ($match_count % $slots_per_day == 0) {
                            $current_date->modify('+1 day');
                        }
                        break;
                    }
                }
                $current_date->modify('+1 day');
            }
            
            if ($current_date > $end_date) {
                throw new Exception('No hay suficientes días disponibles para programar todos los partidos.');
            }
        }
    }
    
    return $fixtures;
}

function generatePlayOffFixtures($teams, $tournament) {
    $fixtures = [];
    $num_teams = count($teams);
    
    // Determine the number of teams that will participate in the playoffs
    $playoff_teams = min(8, $num_teams); // Adjust this number as needed
    $rounds = ceil(log($playoff_teams, 2));
    
    $playing_days_map = [
        'Lunes' => 'Monday', 'Martes' => 'Tuesday', 'Miércoles' => 'Wednesday',
        'Jueves' => 'Thursday', 'Viernes' => 'Friday', 'Sábado' => 'Saturday', 'Domingo' => 'Sunday'
    ];
    
    $playing_days_raw = explode(',', $tournament['playing_days']);
    $playing_days = array_map(function($day) use ($playing_days_map) {
        return $playing_days_map[trim($day)] ?? trim($day);
    }, $playing_days_raw);
    
    $start_date = new DateTime($tournament['start_date']);
    $end_date = new DateTime($tournament['end_date']);
    
    $time_range = explode(' - ', $tournament['match_time_range']);
    $start_time = new DateTime($time_range[0]);
    $end_time = new DateTime($time_range[1]);
    
    $time_diff = $start_time->diff($end_time);
    $total_minutes = ($time_diff->h * 60) + $time_diff->i;
    $match_duration = 120; // 2 hours per match including buffer
    $slots_per_day = max(1, floor($total_minutes / $match_duration));
    
    $current_date = clone $start_date;
    $match_count = 0;
    $total_matches = $playoff_teams - 1; // Number of matches in playoffs
    
    // Generate fixtures for each round
    for ($round = 0; $round < $rounds; $round++) {
        $matches_in_round = $playoff_teams / (pow(2, $round + 1));
        for ($match = 0; $match < $matches_in_round; $match++) {
            while ($current_date <= $end_date) {
                if (in_array($current_date->format('l'), $playing_days)) {
                    $slot = $match_count % $slots_per_day;
                    $match_time = clone $start_time;
                    $match_time->modify('+' . ($slot * $match_duration) . ' minutes');
                    
                    if ($match_time > $end_time) {
                        $match_time = clone $end_time;
                    }
                    
                    $fixtures[] = [
                        'home_team_id' => null, // To be determined based on previous results
                        'away_team_id' => null,
                        'date' => $current_date->format('Y-m-d'),
                        'time' => $match_time->format('H:i:s')
                    ];
                    
                    $match_count++;
                    if ($match_count % $slots_per_day == 0) {
                        $current_date->modify('+1 day');
                    }
                    break;
                }
                $current_date->modify('+1 day');
            }
            
            if ($current_date > $end_date) {
                throw new Exception('No hay suficientes días disponibles para programar todos los partidos de playoffs.');
            }
        }
    }
    
    return $fixtures;
}

// Handle POST request
$data = json_decode(file_get_contents('php://input'), true);
$result = generateFixture($data['tournament_id'], $data['version_id'], $data['category_id']);
echo json_encode($result);