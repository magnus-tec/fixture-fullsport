<?php
session_start();
include '../config/connection.php';

header('Content-Type: application/json');

$tournament_id = $_GET['tournament_id'] ?? null; // Obtener el tournament_id de la solicitud

if (!$tournament_id) {
    echo json_encode(['success' => false, 'message' => 'Tournament ID no proporcionado.']);
    exit();
}

$sql = "SELECT id, name, country FROM teams WHERE tournament_id = ?"; // Cambiar a la tabla correcta
$stmt = $con->prepare($sql);
$stmt->bind_param("i", $tournament_id);
$stmt->execute();
$result = $stmt->get_result();

$teams = [];
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $teams[] = $row;
    }
}

echo json_encode(['success' => true, 'teams' => $teams]); // Asegúrate de que la respuesta sea correcta
mysqli_close($con);
?>