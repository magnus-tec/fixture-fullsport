<?php
session_start();
require_once "../config/connection.php";

if (!isset($_SESSION['email'])) {
    header("Location: ../views/login.php");
    exit();
}

$fixture_id = $_GET['fixture_id'] ?? null;
if (!$fixture_id) {
    header("Location: calendar.php");
    exit();
}

// Obtener información del partido
$match_query = "SELECT f.*, 
                ht.name as home_team_name, ht.color as home_team_color,
                at.name as away_team_name, at.color as away_team_color,
                tv.name as tournament_name, tc.name as category_name
                FROM fixtures f 
                JOIN teams ht ON f.home_team_id = ht.id 
                JOIN teams at ON f.away_team_id = at.id 
                JOIN tournament_versions tv ON f.tournament_version_id = tv.id
                LEFT JOIN tournament_categories tc ON tv.id = tc.tournament_version_id
                WHERE f.id = ?";
$stmt = $con->prepare($match_query);
$stmt->bind_param("i", $fixture_id);
$stmt->execute();
$match = $stmt->get_result()->fetch_assoc();

// Obtener eventos del partido
$events_query = "SELECT me.*, p.full_name, p.shirt_number, p.team_id 
                FROM match_events me 
                JOIN players p ON me.player_id = p.id 
                WHERE me.fixture_id = ? 
                ORDER BY me.minute ASC";
$stmt = $con->prepare($events_query);
$stmt->bind_param("i", $fixture_id);
$stmt->execute();
$events = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Obtener jugadores de cada equipo
function getPlayers($con, $team_id) {
    $query = "SELECT * FROM players WHERE team_id = ? ORDER BY shirt_number";
    $stmt = $con->prepare($query);
    $stmt->bind_param("i", $team_id);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

$home_players = getPlayers($con, $match['home_team_id']);
$away_players = getPlayers($con, $match['away_team_id']);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acta del Partido</title>
    <style>
        @media print {
            .no-print {
                display: none !important;
            }
            body {
                padding: 20px;
            }
        }

        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 1000px;
            margin: 0 auto;
            padding: 20px;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
        }

        .match-info {
            text-align: center;
            margin-bottom: 30px;
        }

        .teams-container {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            margin-bottom: 30px;
        }

        .team-section {
            border: 1px solid #ddd;
            padding: 20px;
            border-radius: 8px;
        }

        .team-header {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #ddd;
        }

        .players-list {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .players-list th,
        .players-list td {
            padding: 8px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        .events-section {
            margin-top: 30px;
        }

        .event-list {
            width: 100%;
            border-collapse: collapse;
        }

        .event-list th,
        .event-list td {
            padding: 8px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        .signatures {
            margin-top: 50px;
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 30px;
        }

        .signature-line {
            border-top: 1px solid #333;
            padding-top: 10px;
            text-align: center;
        }

        .btn {
            display: inline-block;
            padding: 10px 20px;
            background-color: #8B5CF6;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            border: none;
            cursor: pointer;
            font-size: 16px;
        }

        .btn:hover {
            background-color: #7C3AED;
        }
    </style>
</head>
<body>
    <div class="no-print" style="text-align: right; margin-bottom: 20px;">
        <button onclick="window.print()" class="btn">Imprimir Acta</button>
        <button onclick="window.location.href='match_control.php?fixture_id=<?php echo $fixture_id; ?>'" class="btn">
            Volver
        </button>
    </div>

    <div class="header">
        <h1><?php echo htmlspecialchars($match['tournament_name']); ?></h1>
        <p>Categoría: <?php echo htmlspecialchars($match['category_name'] ?? 'No especificada'); ?></p>
    </div>

    <div class="match-info">
        <h2><?php echo htmlspecialchars($match['home_team_name']); ?> vs <?php echo htmlspecialchars($match['away_team_name']); ?></h2>
        <p>Fecha: <?php echo date('d/m/Y', strtotime($match['match_date'])); ?></p>
        <p>Hora: <?php echo date('H:i', strtotime($match['match_time'])); ?></p>
        <h3>Resultado Final: <?php echo $match['home_team_score']; ?> - <?php echo $match['away_team_score']; ?></h3>
    </div>

    <div class="teams-container">
        <div class="team-section">
            <div class="team-header">
                <h3><?php echo htmlspecialchars($match['home_team_name']); ?></h3>
                <p>Color: <?php echo htmlspecialchars($match['home_team_color']); ?></p>
            </div>
            <table class="players-list">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Jugador</th>
                        <th>Goles</th>
                        <th>TA</th>
                        <th>TR</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($home_players as $player): 
                        $goals = 0;
                        $yellow_cards = 0;
                        $red_cards = 0;
                        
                        foreach ($events as $event) {
                            if ($event['player_id'] == $player['id']) {
                                if ($event['event_type'] == 'goal') $goals++;
                                if ($event['event_type'] == 'card' && $event['card_type'] == 'yellow') $yellow_cards++;
                                if ($event['event_type'] == 'card' && $event['card_type'] == 'red') $red_cards++;
                            }
                        }
                    ?>
                    <tr>
                        <td><?php echo $player['shirt_number']; ?></td>
                        <td><?php echo htmlspecialchars($player['full_name']); ?></td>
                        <td><?php echo $goals; ?></td>
                        <td><?php echo $yellow_cards; ?></td>
                        <td><?php echo $red_cards; ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="team-section">
            <div class="team-header">
                <h3><?php echo htmlspecialchars($match['away_team_name']); ?></h3>
                <p>Color: <?php echo htmlspecialchars($match['away_team_color']); ?></p>
            </div>
            <table class="players-list">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Jugador</th>
                        <th>Goles</th>
                        <th>TA</th>
                        <th>TR</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($away_players as $player): 
                        $goals = 0;
                        $yellow_cards = 0;
                        $red_cards = 0;
                        
                        foreach ($events as $event) {
                            if ($event['player_id'] == $player['id']) {
                                if ($event['event_type'] == 'goal') $goals++;
                                if ($event['event_type'] == 'card' && $event['card_type'] == 'yellow') $yellow_cards++;
                                if ($event['event_type'] == 'card' && $event['card_type'] == 'red') $red_cards++;
                            }
                        }
                    ?>
                    <tr>
                        <td><?php echo $player['shirt_number']; ?></td>
                        <td><?php echo htmlspecialchars($player['full_name']); ?></td>
                        <td><?php echo $goals; ?></td>
                        <td><?php echo $yellow_cards; ?></td>
                        <td><?php echo $red_cards; ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="events-section">
        <h3>Eventos del Partido</h3>
        <table class="event-list">
            <thead>
                <tr>
                    <th>Minuto</th>
                    <th>Jugador</th>
                    <th>Equipo</th>
                    <th>Evento</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($events as $event): ?>
                <tr>
                    <td><?php echo $event['minute']; ?>'</td>
                    <td><?php echo htmlspecialchars($event['full_name']); ?> (<?php echo $event['shirt_number']; ?>)</td>
                    <td>
                        <?php echo $event['team_id'] == $match['home_team_id'] ? 
                              htmlspecialchars($match['home_team_name']) : htmlspecialchars($match['away_team_name']); ?>
                    </td>
                    <td>
                        <?php 
                        if ($event['event_type'] == 'goal') {
                            echo 'Gol';
                        } else {
                            echo 'Tarjeta ' . ($event['card_type'] == 'yellow' ? 'Amarilla' : 'Roja');
                        }
                        ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div class="signatures">
        <div class="signature-line">
            <p>Árbitro</p>
            <p>Nombre: _____________________</p>
        </div>
        <div class="signature-line">
            <p>Delegado Local</p>
            <p>Nombre: _____________________</p>
        </div>
        <div class="signature-line">
            <p>Delegado Visitante</p>
            <p>Nombre: _____________________</p>
        </div>
    </div>
</body>
</html>