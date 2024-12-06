<?php
require_once "../config/connection.php";

// Actualizar información del equipo
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_team'])) {
    $team_id = $_POST['team_id'];
    $nickname = $_POST['nickname'];
    $city = $_POST['city'];
    $origin = $_POST['origin'];
    $coach = $_POST['coach'];
    $social_media = $_POST['social_media'];

    $sql = "UPDATE teams SET nickname = ?, city = ?, origin = ?, coach = ?, social_media = ? WHERE id = ?";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("sssssi", $nickname, $city, $origin, $coach, $social_media, $team_id);
    $stmt->execute();

    header("Location: ../views/team-detail.php?id=" . $team_id);
    exit();
}

// Eliminar equipo
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_team'])) {
    $team_id = $_POST['team_id'];

    $sql = "DELETE FROM teams WHERE id = ?";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("i", $team_id);
    $stmt->execute();

    // Establecer un mensaje de éxito en la sesión
    session_start();
    $_SESSION['message'] = "Equipo borrado exitosamente.";

    header("Location: ../views/tournament_teams.php?tournament_id=<?php echo $tournament_id");
    exit();
}