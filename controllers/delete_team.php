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

$data = json_decode(file_get_contents('php://input'), true);
$team_id = $data['team_id'];

// Delete tournament
$sql = "DELETE FROM teams WHERE id = ?";
$stmt = $con->prepare($sql);
$stmt->bind_param("i", $team_id);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Equipo eliminado exitosamente']);
} else {
    echo json_encode(['success' => false, 'message' => 'Error al eliminar el equipo']);
}