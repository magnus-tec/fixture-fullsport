<?php
require_once "../config/connection.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $team_id = $_POST['team_id'];
    $logo = $_FILES['team_logo'];

    // Manejo de la subida del logo
    $target_dir = "../public/uploads2/logos/";
    $target_file = $target_dir . basename($logo["name"]);
    move_uploaded_file($logo["tmp_name"], $target_file);

    // Actualizar la ruta del logo en la base de datos
    $sql = "UPDATE teams SET logo = ? WHERE id = ?";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("si", $target_file, $team_id);
    $stmt->execute();

    header("Location: ../views/team-detail.php?id=" . $team_id);
    exit();
}
?>