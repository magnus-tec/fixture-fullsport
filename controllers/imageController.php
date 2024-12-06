<?php
require_once "../config/connection.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $team_id = $_POST['team_id'];
    $image = $_FILES['team_image'];

    // Manejo de la subida de la imagen
    $target_dir = "../public/imagen/";
    $target_file = $target_dir . basename($image["name"]);
    move_uploaded_file($image["tmp_name"], $target_file);

    // Actualizar la ruta de la imagen en la base de datos
    $sql = "UPDATE teams SET image = ? WHERE id = ?";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("si", $target_file, $team_id);
    $stmt->execute();

    header("Location: ../views/team-detail.php?id=" . $team_id);
    exit();
}
?>