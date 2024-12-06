<?php
// Disable error output
ini_set('display_errors', 0);
error_reporting(0);

// Start session and include connection
session_start();
require_once "../config/connection.php";

// Set JSON header
header('Content-Type: application/json');

// Function to send JSON response
function sendJsonResponse($success, $message, $data = null) {
    echo json_encode([
        'success' => $success,
        'message' => $message,
        'data' => $data
    ]);
    exit();
}

// Log errors to file instead of output
function logError($error) {
    error_log(date('Y-m-d h:i K') . " - Error: " . $error . "\n", 3, "../logs/error.log");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        // Validate inputs
        if (!isset($_POST['tournament_id']) || !isset($_POST['format_type'])) {
            sendJsonResponse(false, 'Datos incompletos');
            exit();
        }

        $tournament_id = filter_var($_POST['tournament_id'], FILTER_VALIDATE_INT);
        $format = filter_var($_POST['format_type'], FILTER_SANITIZE_STRING);
        $points_winner = filter_var($_POST['points_winner'], FILTER_VALIDATE_INT) ?? 3;
        $points_draw = filter_var($_POST['points_draw'], FILTER_VALIDATE_INT) ?? 1;
        $points_loss = filter_var($_POST['points_loss'], FILTER_VALIDATE_INT) ?? 0;
        $points_walkover = filter_var($_POST['points_walkover'], FILTER_VALIDATE_INT) ?? 0;

        if ($tournament_id === false) {
            sendJsonResponse(false, 'ID de torneo inválido');
            exit();
        }

        // Start transaction
        $con->begin_transaction();

        // Get tournament name
        $stmt = $con->prepare("SELECT name FROM tournaments WHERE id = ?");
        if (!$stmt) {
            throw new Exception($con->error);
        }
        
        $stmt->bind_param("i", $tournament_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $tournament = $result->fetch_assoc();

        if (!$tournament) {
            throw new Exception("Torneo no encontrado");
        }

        // Modificación aquí: eliminamos uniqid()
        $version_name = $tournament['name'] . " - " . $format;

        // Insert tournament version
        $stmt = $con->prepare("INSERT INTO tournament_versions (tournament_id, format_type, name, points_winner, points_draw, points_loss, points_walkover) VALUES (?, ?, ?, ?, ?, ?, ?)");
        if (!$stmt) {
            throw new Exception($con->error);
        }

        $stmt->bind_param("issiiii", 
            $tournament_id, 
            $format, 
            $version_name, 
            $points_winner, 
            $points_draw, 
            $points_loss, 
            $points_walkover
        );

        if (!$stmt->execute()) {
            throw new Exception($stmt->error);
        }

        $version_id = $con->insert_id;

        // Insert tournament version details
        $stmt = $con->prepare("INSERT INTO tournament_version_details (version_id, tournament_name, start_date, end_date, country, city, address, status) VALUES (?, ?, CURDATE(), DATE_ADD(CURDATE(), INTERVAL 1 MONTH), 'Peru', '', '', 'Pendiente')");
        if (!$stmt) {
            throw new Exception($con->error);
        }

        $stmt->bind_param("is", $version_id, $version_name);
        
        if (!$stmt->execute()) {
            throw new Exception($stmt->error);
        }

        // Commit transaction
        $con->commit();

        sendJsonResponse(true, 'Versión del torneo creada exitosamente', [
            'version_id' => $version_id,
            'tournament_id' => $tournament_id
        ]);

    } catch (Exception $e) {
        // Rollback transaction
        if ($con->connect_errno === 0) {
            $con->rollback();
        }
        
        // Log error
        logError($e->getMessage());
        
        sendJsonResponse(false, 'Error al crear la versión del torneo: ' . $e->getMessage());
    }
} else {
    sendJsonResponse(false, 'Método no permitido');
}
