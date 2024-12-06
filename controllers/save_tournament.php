<?php
session_start();
include '../config/connection.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verifica si el usuario está autenticado
    if (!isset($_SESSION['user_id'])) {
        echo json_encode(['success' => false, 'message' => 'Usuario no autenticado.']);
        exit();
    }

    // Obtiene el ID del usuario de la sesión
    $user_id = $_SESSION['user_id'];

    // Sanitize input data
    $name = mysqli_real_escape_string($con, $_POST['name']);
    $description = mysqli_real_escape_string($con, $_POST['description']);
    $competition_type = mysqli_real_escape_string($con, $_POST['competition_type']);
    $sport_type = mysqli_real_escape_string($con, $_POST['sport_type']);
    $gender = mysqli_real_escape_string($con, $_POST['gender']);
    $url_slug = mysqli_real_escape_string($con, $_POST['url_slug']);

    // Insert the tournament into the database
    $sql = "INSERT INTO tournaments (name, description, competition_type, sport_type, gender, url_slug, created_by, user_id) 
            VALUES ('$name', '$description', '$competition_type', '$sport_type', '$gender', '$url_slug', $user_id, $user_id)";

    // Execute the query and return the result
    if (mysqli_query($con, $sql)) {
        echo json_encode(['success' => true, 'message' => 'Torneo creado exitosamente']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error al crear el torneo: ' . mysqli_error($con)]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Método de solicitud no válido']);
}

mysqli_close($con);
?>