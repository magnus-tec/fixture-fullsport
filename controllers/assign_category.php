<?php
require_once "../config/connection.php";

header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['categoryId']) || !isset($data['versionId'])) {
    echo json_encode(['success' => false, 'message' => 'Missing required data']);
    exit;
}

$categoryId = $data['categoryId'];
$versionId = $data['versionId'];

// Update the teams table to assign the category
$sql = "UPDATE teams SET category_id = ? WHERE tournament_version_id = ?";
$stmt = $con->prepare($sql);
$stmt->bind_param("ii", $categoryId, $versionId);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Category assigned successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Error assigning category: ' . $con->error]);
}

$stmt->close();
$con->close();
?>