<?php
require_once "../config/connection.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $team_id = $_POST['team_id'];
    $member_name = $_POST['member_name'];
    $social_profile = $_POST['social_profile'];

    $sql = "INSERT INTO members (team_id, name, social_profile) VALUES (?, ?, ?)";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("iss", $team_id, $member_name, $social_profile);
    $stmt->execute();

    header("Location: ../views/team-detail.php?id=" . $team_id);
    exit();
}
?>