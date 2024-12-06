<?php
session_start();
require_once "../config/connection.php";

header('Content-Type: application/json');

// Function to sanitize input
function sanitize_input($input) {
    return htmlspecialchars(strip_tags(trim($input)));
}

// Agregar premio
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['prizeDescription'])) {
    $prizeDescription = sanitize_input($_POST['prizeDescription']);
    $versionId = filter_input(INPUT_POST, 'versionId', FILTER_VALIDATE_INT);

    // Verifica que el ID de la versión sea válido
    if (!$versionId) {
        echo json_encode(['status' => 'error', 'message' => 'ID de versión no válido.']);
        exit();
    }

    // Actualizar la base de datos
    $query = "UPDATE tournament_version_details SET prizes = CONCAT_WS(', ', prizes, ?) WHERE version_id = ?";
    $stmt = $con->prepare($query);
    $stmt->bind_param("si", $prizeDescription, $versionId);

    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Premio agregado correctamente.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Error al agregar el premio.']);
    }
    exit();
}

// Agregar bases del torneo
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['tournamentBasesFile'])) {
    $versionId = filter_input(INPUT_POST, 'versionId', FILTER_VALIDATE_INT);
    $file = $_FILES['tournamentBasesFile'];
    $uploadDir = '../public/docs/bases/';
    $uploadFile = $uploadDir . basename($file['name']);

    // Mover el nuevo archivo a la carpeta de destino
    if (move_uploaded_file($file['tmp_name'], $uploadFile)) {
        // Actualizar la base de datos
        $query = "UPDATE tournament_version_details SET tournament_bases = ? WHERE version_id = ?";
        $stmt = $con->prepare($query);
        $stmt->bind_param("si", $uploadFile, $versionId);

        if ($stmt->execute()) {
            echo json_encode(['status' => 'success', 'message' => 'Base del torneo reemplazada correctamente.', 'file' => $uploadFile]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Error al reemplazar la base.']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Error al subir el archivo.']);
    }
    exit();
}

// If not POST, return error
echo json_encode([
    'success' => false,
    'message' => 'Método no permitido'
]);
exit();