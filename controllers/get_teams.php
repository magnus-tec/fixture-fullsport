<?php
session_start();
require_once "../config/connection.php";

header('Content-Type: application/json');

try {
    // Modified query to get ALL teams, not just the current user's teams
    $sql = "SELECT t.*, u.name as creator_name 
            FROM teams t 
            LEFT JOIN usertable u ON t.user_id = u.id";
    
    $stmt = $con->prepare($sql);
    
    if (!$stmt) {
        throw new Exception($con->error);
    }
    
    $stmt->execute();
    $result = $stmt->get_result();
    
    $teams = [];
    while ($row = $result->fetch_assoc()) {
        $teams[] = $row;
    }
    
    echo json_encode([
        'success' => true,
        'teams' => $teams
    ]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}

$stmt->close();
$con->close();
?>