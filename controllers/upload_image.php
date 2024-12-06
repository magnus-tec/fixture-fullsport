<?php
session_start();
require_once "../config/connection.php";

// Verificar si el usuario está autenticado
if (!isset($_SESSION['email'])) {
    echo json_encode(['success' => false, 'message' => 'Usuario no autenticado']);
    exit();
}

// Verificar si se recibió un archivo y un ID de versión
if (!isset($_FILES['image']) || !isset($_POST['version_id'])) {
    echo json_encode(['success' => false, 'message' => 'Datos incompletos']);
    exit();
}

$version_id = intval($_POST['version_id']);

// Verificar si el archivo es una imagen válida
$allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
if (!in_array($_FILES['image']['type'], $allowed_types)) {
    echo json_encode(['success' => false, 'message' => 'Tipo de archivo no permitido']);
    exit();
}

// Generar un nombre único para el archivo
$file_extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
$new_file_name = uniqid() . '.' . $file_extension;
$upload_path = '../public/img/tournament_covers/' . $new_file_name;

// Mover el archivo subido al directorio de destino
if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_path)) {
    // Actualizar la base de datos con la nueva ruta de la imagen
    $sql = "UPDATE tournament_version_details SET cover_image = ? WHERE version_id = ?";
    $stmt = $con->prepare($sql);
    $relative_path = 'img/tournament_covers/' . $new_file_name;
    $stmt->bind_param("si", $relative_path, $version_id);
    
    if ($stmt->execute()) {
        echo json_encode([
            'success' => true, 
            'message' => 'Imagen subida y actualizada correctamente',
            'new_image_path' => $relative_path
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error al actualizar la base de datos']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Error al subir la imagen']);
}