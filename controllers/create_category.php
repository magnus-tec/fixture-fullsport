<?php
require_once "../config/connection.php";

header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['name']) || !isset($data['description']) || !isset($data['tournament_version_id'])) {
    echo json_encode(['success' => false, 'message' => 'Faltan datos requeridos']);
    exit;
}

$name = $data['name'];
$description = $data['description'];
$tournament_version_id = $data['tournament_version_id'];

$sql = "INSERT INTO tournament_categories (name, description, tournament_version_id) VALUES (?, ?, ?)";
$stmt = $con->prepare($sql);
$stmt->bind_param("ssi", $name, $description, $tournament_version_id);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Categoría creada exitosamente']);
} else {
    echo json_encode(['success' => false, 'message' => 'Error al crear la categoría: ' . $con->error]);
}

$stmt->close();
$con->close();
?>