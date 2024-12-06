<?php
session_start();
require_once "../config/connection.php";

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_SESSION['user_id'])) {
        echo json_encode(['success' => false, 'message' => 'Usuario no autenticado.']);
        exit();
    }

    $tournament_id = $_POST['tournament_id'] ?? null;
    if (!$tournament_id) {
        echo json_encode(['success' => false, 'message' => 'ID del torneo no proporcionado.']);
        exit();
    }

    if (!isset($_FILES['logo']) || $_FILES['logo']['error'] !== UPLOAD_ERR_OK) {
        echo json_encode(['success' => false, 'message' => 'Error al subir el archivo.', 'error' => $_FILES['logo']['error']]);
        exit();
    }

    $file = $_FILES['logo'];
    $fileName = uniqid() . '_' . $file['name'];
    $uploadPath = '../public/uploads/logos/' . $fileName;

    if (!move_uploaded_file($file['tmp_name'], $uploadPath)) {
        echo json_encode(['success' => false, 'message' => 'Error al mover el archivo subido.']);
        exit();
    }

    $relativePath = 'uploads/logos/' . $fileName;
    $sql = "UPDATE tournaments SET logo_image = ? WHERE id = ?";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("si", $relativePath, $tournament_id);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Logo actualizado exitosamente.', 'logo_url' => $relativePath]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error al actualizar el logo en la base de datos.']);
    }

    $stmt->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Método de solicitud no válido']);
}

$con->close();
?>