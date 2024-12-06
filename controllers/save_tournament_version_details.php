<?php
session_start();
require_once "../config/connection.php";

header('Content-Type: application/json');

// Function to sanitize input
function sanitize_input($input)
{
    return htmlspecialchars(strip_tags(trim($input)));
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        // Validate and sanitize inputs
        $version_id = filter_input(INPUT_POST, 'version_id', FILTER_VALIDATE_INT);
        $version_name = sanitize_input($_POST['version_name']);
        $start_date = sanitize_input($_POST['start_date']);
        $end_date = sanitize_input($_POST['end_date']);
        $country = sanitize_input($_POST['country']);
        $city = sanitize_input($_POST['city']);
        $address = sanitize_input($_POST['address']);
        $registration_fee = filter_input(INPUT_POST, 'registration_fee', FILTER_VALIDATE_FLOAT);
        $status = sanitize_input($_POST['status']);
        $google_maps_url = filter_input(INPUT_POST, 'google_maps_url', FILTER_VALIDATE_URL);
        $playing_days = isset($_POST['playing_days']) ? implode(',', array_map('sanitize_input', $_POST['playing_days'])) : '';
        $start_time = $_POST['start_time'];
        $end_time = $_POST['end_time'];
        $match_time_range = $start_time . ' - ' . $end_time;

        // Guardar en la base de datos

        // Validate required fields
        if (!$version_id || !$version_name || !$start_date || !$end_date) {
            throw new Exception("Faltan campos requeridos");
        }

        // Validate dates
        if (strtotime($end_date) <= strtotime($start_date)) {
            throw new Exception("La fecha de fin debe ser posterior a la fecha de inicio");
        }

        // Start transaction
        $con->begin_transaction();

        // Update tournament_version_details
        $sql = "UPDATE tournament_version_details 
                SET tournament_name = ?, start_date = ?, end_date = ?, country = ?, 
                    city = ?, address = ?, registration_fee = ?, status = ?, 
                    google_maps_url = ?, playing_days = ?, match_time_range = ?
                WHERE version_id = ?";

        $stmt = $con->prepare($sql);
        $stmt->bind_param(
            "ssssssdssssi",
            $version_name,
            $start_date,
            $end_date,
            $country,
            $city,
            $address,
            $registration_fee,
            $status,
            $google_maps_url,
            $playing_days,
            $match_time_range,
            $version_id
        );

        if (!$stmt->execute()) {
            throw new Exception("Error al actualizar los detalles de la versión: " . $stmt->error);
        }

        // Get the tournament_id for the response
        $sql = "SELECT tournament_id FROM tournament_versions WHERE id = ?";
        $stmt = $con->prepare($sql);
        $stmt->bind_param("i", $version_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $tournament_data = $result->fetch_assoc();

        if (!$tournament_data) {
            throw new Exception("No se pudo obtener el ID del torneo");
        }

        // Commit transaction
        $con->commit();

        echo json_encode([
            'success' => true,
            'message' => 'Detalles de la versión del torneo actualizados exitosamente',
            'tournament_id' => $tournament_data['tournament_id'],
            'version_id' => $version_id
        ]);

    } catch (Exception $e) {
        // Rollback transaction on error
        $con->rollback();

        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }

    exit();
}

// If not POST, return error
echo json_encode([
    'success' => false,
    'message' => 'Método no permitido'
]);
exit();
