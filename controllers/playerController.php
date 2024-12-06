<?php 
session_start();
require_once "../config/connection.php";

// Función para manejar la subida de archivos
function handleFileUpload($file, $uploadDir) {
    if ($file['error'] == 0) {
        $fileName = uniqid() . '_' . basename($file['name']);
        $targetPath = $uploadDir . $fileName;
        if (move_uploaded_file($file['tmp_name'], $targetPath)) {
            return $fileName;
        }
    }
    return null;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Agregar nuevo jugador
    if (isset($_POST['team_id']) && !isset($_POST['player_id'])) {
        $team_id = $_POST['team_id'];
        $document_number = $_POST['document_number'];
        $document_type = $_POST['document_type'];
        $full_name = $_POST['full_name'];
        $address = isset($_POST['address']) ? $_POST['address'] : null; // Manejo de campo opcional
        $contact_number = $_POST['contact_number'];
        $birth_date = $_POST['birth_date'];
        $gender = $_POST['gender'];
        $position = $_POST['position'];
        $shirt_number = isset($_POST['shirt_number']) ? $_POST['shirt_number'] : null; // Manejo de campo opcional
        $status = isset($_POST['status']) ? $_POST['status'] : null; // Manejo de campo opcional

        // Manejar la subida de archivos
        $photo = handleFileUpload($_FILES['photo'], "../public/uploads3/photos/");
        $identity_document = handleFileUpload($_FILES['identity_document'], "../public/uploads3/documents/");

        $sql = "INSERT INTO players (document_number, document_type, full_name, address, contact_number, birth_date, gender, position, shirt_number, status, photo, identity_document, team_id) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $con->prepare($sql);
        $stmt->bind_param(
            "ssssssssssssi", 
            $document_number, 
            $document_type, 
            $full_name, 
            $address, 
            $contact_number, 
            $birth_date, 
            $gender, 
            $position, 
            $shirt_number, 
            $status, 
            $photo, 
            $identity_document, 
            $team_id
        );
        
        if ($stmt->execute()) {
            echo "<script>alert('Jugador Guardado Exitosamente'); window.location.href='../views/team-detail.php?id=$team_id';</script>";
        } else {
            echo "<script>alert('Error al guardar el jugador: " . $stmt->error . "'); window.history.back();</script>";
        }
    }
    // Editar jugador existente
    elseif (isset($_POST['player_id'])) {
        $player_id = $_POST['player_id'];
        $team_id = $_POST['team_id'];
        $document_number = $_POST['document_number'];
        $document_type = $_POST['document_type'];
        $full_name = $_POST['full_name'];
        $address = isset($_POST['address']) ? $_POST['address'] : null; // Manejo de campo opcional
        $contact_number = $_POST['contact_number'];
        $birth_date = $_POST['birth_date'];
        $gender = $_POST['gender'];
        $position = $_POST['position'];
        $shirt_number = isset($_POST['shirt_number']) ? $_POST['shirt_number'] : null; // Manejo de campo opcional
        $status = isset($_POST['status']) ? $_POST['status'] : null; // Manejo de campo opcional

        $sql = "UPDATE players 
                SET document_number=?, document_type=?, full_name=?, address=?, contact_number=?, birth_date=?, gender=?, position=?, shirt_number=?, status=? 
                WHERE id=?";
        $stmt = $con->prepare($sql);
        $stmt->bind_param(
            "ssssssssssi", 
            $document_number, 
            $document_type, 
            $full_name, 
            $address, 
            $contact_number, 
            $birth_date, 
            $gender, 
            $position, 
            $shirt_number, 
            $status, 
            $player_id
        );
        
        if ($stmt->execute()) {
            echo "<script>alert('Jugador Actualizado Exitosamente'); window.location.href='../views/team-detail.php?id=$team_id';</script>";
        } else {
            echo "<script>alert('Error al actualizar el jugador: " . $stmt->error . "'); window.history.back();</script>";
        }
    }
    else {
        echo "<script>alert('No se recibieron datos del formulario.'); window.history.back();</script>";
    }
}
// Eliminar jugador
elseif ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['player_id'])) {
    $player_id = $_GET['player_id'];
    
    // Primero, obtener el team_id del jugador
    $sql_get_team = "SELECT team_id FROM players WHERE id = ?";
    $stmt_get_team = $con->prepare($sql_get_team);
    $stmt_get_team->bind_param("i", $player_id);
    $stmt_get_team->execute();
    $result = $stmt_get_team->get_result();
    $player = $result->fetch_assoc();
    $team_id = $player['team_id'];

    // Ahora, eliminar el jugador
    $sql_delete = "DELETE FROM players WHERE id = ?";
    $stmt_delete = $con->prepare($sql_delete);
    $stmt_delete->bind_param("i", $player_id);
    
    if ($stmt_delete->execute()) {
        echo "<script>alert('Jugador Eliminado Exitosamente'); window.location.href='../views/team-detail.php?id=$team_id';</script>";
    } else {
        echo "<script>alert('Error al eliminar el jugador: " . $stmt_delete->error . "'); window.history.back();</script>";
    }
}
else {
    echo "<script>alert('Método no permitido.'); window.history.back();</script>";
}
?>
