<?php
session_start();
require_once "../config/connection.php";

header('Content-Type: application/json');

if (!isset($_SESSION['email'])) {
    echo json_encode(['success' => false, 'message' => 'No autorizado']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
    exit;
}

$tournament_id = $_POST['tournament_id'];
$name = $_POST['name'];
$description = $_POST['description'];
$competition_type = $_POST['competition_type'];
$sport_type = $_POST['sport_type'];
$gender = $_POST['gender'];
$url_slug = $_POST['url_slug'];

// Verify URL slug is unique
$sql = "SELECT id FROM tournaments WHERE url_slug = ? AND id != ?";
$stmt = $con->prepare($sql);
$stmt->bind_param("si", $url_slug, $tournament_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    echo json_encode(['success' => false, 'message' => 'La URL ya está en uso']);
    exit;
}

// Update tournament
$sql = "UPDATE tournaments SET 
        name = ?, 
        description = ?, 
        competition_type = ?, 
        sport_type = ?, 
        gender = ?, 
        url_slug = ? 
        WHERE id = ?";

$stmt = $con->prepare($sql);
$stmt->bind_param("ssssssi", 
    $name, 
    $description, 
    $competition_type, 
    $sport_type, 
    $gender, 
    $url_slug, 
    $tournament_id
);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Torneo actualizado exitosamente']);
} else {
    echo json_encode(['success' => false, 'message' => 'Error al actualizar el torneo']);
}