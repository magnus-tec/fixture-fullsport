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
                ht.name as home_team_name, ht.id as home_team_id,
                at.name as away_team_name, at.id as away_team_id,
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

// Obtener jugadores de cada equipo
function getPlayers($con, $team_id, $fixture_id) {
    $query = "SELECT p.*, COALESCE(mp.is_starter, 0) as is_starter 
              FROM players p 
              LEFT JOIN match_players mp ON p.id = mp.player_id AND mp.fixture_id = ?
              WHERE p.team_id = ? 
              ORDER BY mp.is_starter DESC, p.shirt_number";
    $stmt = $con->prepare($query);
    $stmt->bind_param("ii", $fixture_id, $team_id);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

$home_players = getPlayers($con, $match['home_team_id'], $fixture_id);
$away_players = getPlayers($con, $match['away_team_id'], $fixture_id);

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

// Procesar el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $home_score = $_POST['home_score'] ?? 0;
    $away_score = $_POST['away_score'] ?? 0;
    
    $con->begin_transaction();
    
    try {
        // Actualizar resultado
        $update_query = "UPDATE fixtures SET 
                        home_team_score = ?, 
                        away_team_score = ?,
                        status = 'Completado' 
                        WHERE id = ?";
        $stmt = $con->prepare($update_query);
        $stmt->bind_param("iii", $home_score, $away_score, $fixture_id);
        $stmt->execute();

        // Actualizar jugadores titulares y suplentes
        $delete_players = "DELETE FROM match_players WHERE fixture_id = ?";
        $stmt = $con->prepare($delete_players);
        $stmt->bind_param("i", $fixture_id);
        $stmt->execute();

        $insert_player = "INSERT INTO match_players (fixture_id, player_id, is_starter) VALUES (?, ?, ?)";
        $stmt = $con->prepare($insert_player);

        foreach ($_POST['home_starters'] as $player_id) {
            $stmt->bind_param("iii", $fixture_id, $player_id, $is_starter);
            $is_starter = 1;
            $stmt->execute();
        }

        foreach ($_POST['away_starters'] as $player_id) {
            $stmt->bind_param("iii", $fixture_id, $player_id, $is_starter);
            $is_starter = 1;
            $stmt->execute();
        }

        foreach ($_POST['home_subs'] as $player_id) {
            $stmt->bind_param("iii", $fixture_id, $player_id, $is_starter);
            $is_starter = 0;
            $stmt->execute();
        }

        foreach ($_POST['away_subs'] as $player_id) {
            $stmt->bind_param("iii", $fixture_id, $player_id, $is_starter);
            $is_starter = 0;
            $stmt->execute();
        }

        // Guardar eventos
        if (isset($_POST['events'])) {
            $delete_events = "DELETE FROM match_events WHERE fixture_id = ?";
            $stmt = $con->prepare($delete_events);
            $stmt->bind_param("i", $fixture_id);
            $stmt->execute();

            $insert_event = "INSERT INTO match_events 
                           (fixture_id, player_id, event_type, card_type, minute, details) 
                           VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $con->prepare($insert_event);

            foreach ($_POST['events'] as $event) {
                $event_type = $event['type'];
                $card_type = $event['type'] === 'card' ? $event['card_type'] : null;
                $details = $event['details'] ?? null;
                $stmt->bind_param("iissss", $fixture_id, $event['player_id'], 
                                $event_type, $card_type, $event['minute'], $details);
                $stmt->execute();
            }
        }

        $con->commit();
        header("Location: match_control.php?fixture_id=$fixture_id&success=1");
        exit();
    } catch (Exception $e) {
        $con->rollback();
        $error = $e->getMessage();
    }

}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Control del Partido</title>
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
    <style>
        :root {
            --primary: #8B5CF6;
            --primary-dark: #7C3AED;
            --secondary: #1F2937;
            --background: #1B1E2B;
            --surface: #252836;
            --text: #F9FAFB;
            --text-secondary: #9CA3AF;
            --success: #10B981;
            --warning: #F59E0B;
            --danger: #EF4444;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: system-ui, -apple-system, sans-serif;
            background-color: var(--background);
            color: var(--text);
            line-height: 1.5;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            padding: 0.5rem 1rem;
            border-radius: 0.5rem;
            font-weight: 500;
            cursor: pointer;
            border: none;
            transition: background-color 0.2s;
        }

        .btn-primary {
            background-color: var(--primary);
            color: white;
        }

        .btn-primary:hover {
            background-color: var(--primary-dark);
        }

        .btn-secondary {
            background-color: var(--secondary);
            color: white;
        }

        .match-info {
            background-color: var(--surface);
            border-radius: 1rem;
            padding: 2rem;
            margin-bottom: 2rem;
            text-align: center;
        }

        .score-container {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 2rem;
            margin: 2rem 0;
        }

        .team-section {
            background-color: var(--surface);
            border-radius: 1rem;
            padding: 1.5rem;
        }

        .team-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }

        .player-list {
            display: grid;
            gap: 0.5rem;
        }

        .player-card {
            background-color: var(--secondary);
            border-radius: 0.5rem;
            padding: 1rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .player-info {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .player-number {
            background-color: var(--primary);
            color: white;
            width: 2rem;
            height: 2rem;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
        }

        .events-section {
            background-color: var(--surface);
            border-radius: 1rem;
            padding: 1.5rem;
            margin-top: 2rem;
        }

        .event-list {
            display: grid;
            gap: 0.5rem;
            margin-top: 1rem;
        }

        .event-card {
            background-color: var(--secondary);
            border-radius: 0.5rem;
            padding: 1rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            align-items: center;
            justify-content: center;
        }

        .modal-content {
            background-color: var(--surface);
            border-radius: 1rem;
            padding: 2rem;
            width: 100%;
            max-width: 500px;
        }

        .form-group {
            margin-bottom: 1rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: var(--text-secondary);
        }

        .form-control {
            width: 100%;
            padding: 0.5rem;
            border-radius: 0.5rem;
            border: 1px solid var(--secondary);
            background-color: var(--secondary);
            color: var(--text);
        }

        .badge {
            padding: 0.25rem 0.5rem;
            border-radius: 9999px;
            font-size: 0.875rem;
            font-weight: 500;
        }

        .badge-goal {
            background-color: var(--success);
            color: white;
        }

        .badge-yellow {
            background-color: var(--warning);
            color: white;
        }

        .badge-red {
            background-color: var(--danger);
            color: white;
        }

        .badge-foul {
            background-color: var(--secondary);
            color: white;
        }

        .badge-penalty {
            background-color: var(--primary);
            color: white;
        }

        .player-selector {
            display: flex;
            gap: 1rem;
        }

        .player-column {
            flex: 1;
        }

        .player-item {
            padding: 0.5rem;
            margin-bottom: 0.5rem;
            background-color: var(--secondary);
            border-radius: 0.5rem;
            cursor: move;
        }

        .player-item.dragging {
            opacity: 0.5;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Control del Partido</h1>
            <div>
                <button class="btn btn-secondary" onclick="window.location.href='calendar.php'">
                    <i class="ri-arrow-left-line"></i>
                    Volver
                </button>
                <button class="btn btn-primary" onclick="window.location.href='match_report.php?fixture_id=<?php echo $fixture_id; ?>'">
                    <i class="ri-file-text-line"></i>
                    Generar Acta
                </button>
            </div>
        </div>

        <div class="match-info">
            <h2><?php echo htmlspecialchars($match['home_team_name']); ?> vs <?php echo htmlspecialchars($match['away_team_name']); ?></h2>
            <p><?php echo date('d/m/Y', strtotime($match['match_date'])); ?> - <?php echo date('H:i', strtotime($match['match_time'])); ?></p>
            <p><?php echo htmlspecialchars($match['tournament_name']); ?></p>
        </div>

        <form id="matchForm" method="POST">
            <div class="score-container">
                <div class="team-section">
                    <div class="team-header">
                        <h3><?php echo htmlspecialchars($match['home_team_name']); ?></h3>
                        <input type="number" name="home_score" class="form-control" style="width: 80px" 
                               value="<?php echo $match['home_team_score'] ?? 0; ?>" min="0">
                    </div>
                    <div class="player-selector">
                        <div class="player-column">
                            <h4>Titulares</h4>
                            <div id="homeStarters" class="player-list">
                                <?php foreach ($home_players as $player): 
                                    if ($player['is_starter']): ?>
                                <div class="player-item" draggable="true" data-player-id="<?php echo $player['id']; ?>">
                                    <span class="player-number"><?php echo $player['shirt_number']; ?></span>
                                    <?php echo htmlspecialchars($player['full_name']); ?>
                                    <input type="hidden" name="home_starters[]" value="<?php echo $player['id']; ?>">
                                </div>
                                <?php endif; endforeach; ?>
                            </div>
                        </div>
                        <div class="player-column">
                            <h4>Suplentes</h4>
                            <div id="homeSubs" class="player-list">
                                <?php foreach ($home_players as $player): 
                                    if (!$player['is_starter']): ?>
                                <div class="player-item" draggable="true" data-player-id="<?php echo $player['id']; ?>">
                                    <span class="player-number"><?php echo $player['shirt_number']; ?></span>
                                    <?php echo htmlspecialchars($player['full_name']); ?>
                                    <input type="hidden" name="home_subs[]" value="<?php echo $player['id']; ?>">
                                </div>
                                <?php endif; endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="team-section">
                    <div class="team-header">
                        <h3><?php echo htmlspecialchars($match['away_team_name']); ?></h3>
                        <input type="number" name="away_score" class="form-control" style="width: 80px"
                               value="<?php echo $match['away_team_score'] ?? 0; ?>" min="0">
                    </div>
                    <div class="player-selector">
                        <div class="player-column">
                            <h4>Titulares</h4>
                            <div id="awayStarters" class="player-list">
                                <?php foreach ($away_players as $player): 
                                    if ($player['is_starter']): ?>
                                <div class="player-item" draggable="true" data-player-id="<?php echo $player['id']; ?>">
                                    <span class="player-number"><?php echo $player['shirt_number']; ?></span>
                                    <?php echo htmlspecialchars($player['full_name']); ?>
                                    <input type="hidden" name="away_starters[]" value="<?php echo $player['id']; ?>">
                                </div>
                                <?php endif; endforeach; ?>
                            </div>
                        </div>
                        <div class="player-column">
                            <h4>Suplentes</h4>
                            <div id="awaySubs" class="player-list">
                                <?php foreach ($away_players as $player): 
                                    if (!$player['is_starter']): ?>
                                <div class="player-item" draggable="true" data-player-id="<?php echo $player['id']; ?>">
                                    <span class="player-number"><?php echo $player['shirt_number']; ?></span>
                                    <?php echo htmlspecialchars($player['full_name']); ?>
                                    <input type="hidden" name="away_subs[]" value="<?php echo $player['id']; ?>">
                                </div>
                                <?php endif; endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="events-section">
                <h3>Eventos del Partido</h3>
                <button type="button" class="btn btn-primary" onclick="openEventModal()">Agregar Evento</button>
                <div class="event-list" id="eventsList"></div>
            </div>

            <div style="margin-top: 2rem; text-align: right;">
                <button type="submit" class="btn btn-primary">
                    <i class="ri-save-line"></i>
                    Guardar Cambios
                </button>
            </div>
        </form>
    </div>

    <!-- Modal para agregar eventos -->
    <div id="eventModal" class="modal">
        <div class="modal-content">
            <h3 style="margin-bottom: 1.5rem;">Agregar Evento</h3>
            <input type="hidden" id="eventPlayerId">
            <input type="hidden" id="eventPlayerName">
            
            <div class="form-group">
                <label>Tipo de Evento</label>
                <select id="eventType" class="form-control" onchange="toggleEventFields()">
                    <option value="goal">Gol</option>
                    <option value="card">Tarjeta</option>
                    <option value="foul">Falta</option>
                    <option value="penalty">Penal</option>
                </select>
            </div>

            <div id="cardTypeContainer" class="form-group" style="display: none;">
                <label>Tipo de Tarjeta</label>
                <select id="cardType" class="form-control">
                    <option value="yellow">Amarilla</option>
                    <option value="red">Roja</option>
                </select>
            </div>

            <div class="form-group">
                <label>Minuto</label>
                <input type="number" id="eventMinute" class="form-control" min="1" max="90">
            </div>

            <div class="form-group">
                <label>Jugador</label>
                <select id="eventPlayer" class="form-control">
                    <option value="">Seleccionar jugador</option>
                    <?php foreach (array_merge($home_players, $away_players) as $player): ?>
                        <option value="<?php echo $player['id']; ?>" data-team="<?php echo $player['team_id']; ?>">
                            <?php echo htmlspecialchars($player['full_name']); ?> (<?php echo $player['shirt_number']; ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label>Detalles</label>
                <textarea id="eventDetails" class="form-control" rows="3"></textarea>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 1rem; margin-top: 1.5rem;">
                <button class="btn btn-secondary" onclick="closeEventModal()">Cancelar</button>
                <button class="btn btn-primary" onclick="saveEvent()">Guardar</button>
            </div>
        </div>
    </div>

    <script>
        let matchEvents = <?php echo json_encode($events); ?>;

        function openEventModal() {
            document.getElementById('eventModal').style.display = 'flex';
        }

        function closeEventModal() {
            document.getElementById('eventModal').style.display = 'none';
            document.getElementById('eventMinute').value = '';
            document.getElementById('eventType').value = 'goal';
            document.getElementById('cardTypeContainer').style.display = 'none';
            document.getElementById('eventPlayer').value = '';
            document.getElementById('eventDetails').value = '';
        }

        function toggleEventFields() {
            const eventType = document.getElementById('eventType').value;
            document.getElementById('cardTypeContainer').style.display = 
                eventType === 'card' ? 'block' : 'none';
        }

        function saveEvent() {
            const eventType = document.getElementById('eventType').value;
            const minute = document.getElementById('eventMinute').value;
            const playerId = document.getElementById('eventPlayer').value;
            const details = document.getElementById('eventDetails').value;

            if (!minute || !playerId) {
                alert('Por favor complete todos los campos requeridos');
                return;
            }

            const playerOption = document.querySelector(`#eventPlayer option[value="${playerId}"]`);
            const playerName = playerOption.textContent;
            const teamId = playerOption.dataset.team;

            const event = {
                id: Date.now(), // Temporal ID for new events
                player_id: playerId,
                player_name: playerName,
                team_id: teamId,
                type: eventType,
                minute: parseInt(minute),
                details: details
            };

            if (eventType === 'card') {
                event.card_type = document.getElementById('cardType').value;
            }

            matchEvents.push(event);
            updateEventsList();
            closeEventModal();
        }

        function removeEvent(eventId) {
            matchEvents = matchEvents.filter(event => event.id != eventId);
            updateEventsList();
        }

        function updateEventsList() {
            const eventsList = document.getElementById('eventsList');
            eventsList.innerHTML = '';

            matchEvents.sort((a, b) => a.minute - b.minute).forEach((event) => {
                const eventDiv = document.createElement('div');
                eventDiv.className = 'event-card';
                
                let badge;
                switch(event.type) {
                    case 'goal':
                        badge = `<span class="badge badge-goal">
                            <i class="ri-football-line"></i> ${event.minute}'
                        </span>`;
                        break;
                    case 'card':
                        badge = `<span class="badge badge-${event.card_type}">
                            <i class="ri-card-2-line"></i> ${event.minute}'
                        </span>`;
                        break;
                    case 'foul':
                        badge = `<span class="badge badge-foul">
                            <i class="ri-error-warning-line"></i> ${event.minute}'
                        </span>`;
                        break;
                    case 'penalty':
                        badge = `<span class="badge badge-penalty">
                            <i class="ri-focus-2-line"></i> ${event.minute}'
                        </span>`;
                        break;
                }

                eventDiv.innerHTML = `
                    <div class="player-info">
                        ${badge}
                        <span>${event.player_name}</span>
                    </div>
                    <div>
                        <span>${event.details}</span>
                        <button type="button" class="btn btn-secondary" onclick="removeEvent(${event.id})">
                            <i class="ri-delete-bin-line"></i>
                        </button>
                    </div>
                `;
                
                eventsList.appendChild(eventDiv);
            });

            // Actualizar campos ocultos del formulario
            const form = document.getElementById('matchForm');
            const oldFields = form.querySelectorAll('input[name^="events"]');
            oldFields.forEach(field => field.remove());

            matchEvents.forEach((event, index) => {
                Object.entries(event).forEach(([key, value]) => {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = `events[${index}][${key}]`;
                    input.value = value;
                    form.appendChild(input);
                });
            });
        }

        // Drag and drop functionality
        const playerLists = document.querySelectorAll('.player-list');
        playerLists.forEach(list => {
            list.addEventListener('dragstart', dragStart);
            list.addEventListener('dragover', dragOver);
            list.addEventListener('drop', drop);
        });

        function dragStart(e) {
            e.dataTransfer.setData('text/plain', e.target.dataset.playerId);
            e.target.classList.add('dragging');
        }

        function dragOver(e) {
            e.preventDefault();
        }

        function drop(e) {
            e.preventDefault();
            const playerId = e.dataTransfer.getData('text');
            const playerElement = document.querySelector(`[data-player-id="${playerId}"]`);
            
            if (e.target.classList.contains('player-list')) {
                e.target.appendChild(playerElement);
            } else if (e.target.classList.contains('player-item')) {
                e.target.parentNode.insertBefore(playerElement, e.target.nextSibling);
            }
            
            playerElement.classList.remove('dragging');
            updatePlayerInputs();
        }

        function updatePlayerInputs() {
            const homeStarters = document.querySelectorAll('#homeStarters .player-item');
            const homeSubs = document.querySelectorAll('#homeSubs .player-item');
            const awayStarters = document.querySelectorAll('#awayStarters .player-item');
            const awaySubs = document.querySelectorAll('#awaySubs .player-item');

            updateInputs(homeStarters, 'home_starters[]');
            updateInputs(homeSubs, 'home_subs[]');
            updateInputs(awayStarters, 'away_starters[]');
            updateInputs(awaySubs, 'away_subs[]');
        }

        function updateInputs(players, inputName) {
            players.forEach(player => {
                const input = player.querySelector('input');
                input.name = inputName;
            });
        }

        // Initialize events list
        updateEventsList();
    </script>
</body>
</html>