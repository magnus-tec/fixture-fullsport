<?php
session_start();
require_once "../config/connection.php";

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_SESSION['user_id'])) {
        echo json_encode(['success' => false, 'message' => 'Usuario no autenticado.']);
        exit();
    }

    $team_name = mysqli_real_escape_string($con, $_POST['team_name']);
    $country = mysqli_real_escape_string($con, $_POST['country']);
    $color = mysqli_real_escape_string($con, $_POST['color']);
    $user_id = $_SESSION['user_id'];
    

    $sql = "INSERT INTO teams (name, country, color, user_id) VALUES ('$team_name', '$country', '$color', $user_id)";

    if (mysqli_query($con, $sql)) {
        $new_team_id = mysqli_insert_id($con);
        echo json_encode(['success' => true, 'newTeam' => ['id' => $new_team_id, 'name' => $team_name, 'country' => $country]]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error al crear el equipo: ' . mysqli_error($con)]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Método no permitido.']);
}

mysqli_close($con);
?>