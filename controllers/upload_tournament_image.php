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

if (!isset($_FILES['image']) || !isset($_POST['tournament_id'])) {
    echo json_encode(['success' => false, 'message' => 'Datos incompletos']);
    exit;
}

$tournament_id = $_POST['tournament_id'];
$image = $_FILES['image'];

// Validate image
$allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
if (!in_array($image['type'], $allowed_types)) {
    echo json_encode(['success' => false, 'message' => 'Tipo de archivo no permitido']);
    exit;
}

// Create uploads directory if it doesn't exist
$upload_dir = "../public/uploads/tournaments/";
if (!file_exists($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}

// Generate unique filename
$filename = uniqid() . '_' . basename($image['name']);
$upload_path = $upload_dir . $filename;

if (move_uploaded_file($image['tmp_name'], $upload_path)) {
    // Update database
    $relative_path = "uploads/tournaments/" . $filename;
    $sql = "UPDATE tournaments SET cover_image = ? WHERE id = ?";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("si", $relative_path, $tournament_id);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Imagen actualizada exitosamente']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error al actualizar la base de datos']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Error al subir la imagen']);
}