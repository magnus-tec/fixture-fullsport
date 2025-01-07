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

// Convertir el nombre recibido a minúsculas
$lower_name = strtolower($data['name']);

// Consulta SQL para verificar si ya existe la categoría con ese nombre (en minúsculas)
$check_sql = "SELECT COUNT(*) FROM tournament_categories WHERE LOWER(name) = ?  AND tournament_version_id=?";
$check_stmt = $con->prepare($check_sql);
$check_stmt->bind_param("si", $lower_name, $tournament_version_id);
$check_stmt->execute();
$check_stmt->bind_result($count);
$check_stmt->fetch();
$check_stmt->close();

// Verificar si ya existe la categoría
if ($count > 0) {
    echo json_encode(['success' => false, 'message' => 'Ya existe una categoría con este nombre']);
    exit;
}

$sql = "INSERT INTO tournament_categories (name, description, tournament_version_id) VALUES (?, ?, ?)";
$stmt = $con->prepare($sql);
$stmt->bind_param("ssi", $name, $description, $tournament_version_id);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Categoría creada exitosamente']);
} else {
    echo json_encode(['success' => false, 'message' => 'Se produjo un error al crear la categoría: ' . $con->error]);
}
$stmt->close();
$con->close();