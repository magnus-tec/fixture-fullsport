<?php
session_start();
include '../config/connection.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'User not logged in']);
    exit;
}

$user_id = $_SESSION['user_id'];

$sql = "SELECT * FROM tournaments WHERE created_by = ?";
$stmt = mysqli_prepare($con, $sql);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$tournaments = [];
while ($row = mysqli_fetch_assoc($result)) {
    $tournaments[] = $row;
}

echo json_encode($tournaments);
mysqli_close($con);
?>