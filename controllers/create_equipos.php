<?php 
session_start();
require_once "../config/connection.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $team_name = $_POST['team_name'] ?? null;
    $country = $_POST['country'] ?? null;
    $color = $_POST['color'] ?? null;
    $tournament_id = $_POST['tournament_id'] ?? null;
    $tournament_version_id = $_POST['tournament_version_id'] ?? null;
    $category_id = $_POST['category_id'] ?? null;

    // Ruta del logo por defecto
    $defaultLogoPath = "../public/img/default-logo.png";
    $logoFileName = $defaultLogoPath;

    if ($tournament_id && $tournament_version_id && $team_name && $country && $color && $category_id) {
        $checkTeamSql = "SELECT * FROM teams WHERE name = ? AND tournament_version_id = ?";
        $checkTeamStmt = $con->prepare($checkTeamSql);
        $checkTeamStmt->bind_param("si", $team_name, $tournament_version_id);
        $checkTeamStmt->execute();
        $result = $checkTeamStmt->get_result();

        if ($result->num_rows > 0) {
            echo json_encode(['success' => false, 'message' => 'Ya existe un equipo con ese nombre en esta versi車n del torneo.']);
            exit;
        }

        if (isset($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
            $logoTmpPath = $_FILES['logo']['tmp_name'];
            $uniqueFileName = uniqid() . "_" . $_FILES['logo']['name'];
            $logoDestinationPath = "../public/uploads2/logos/" . basename($uniqueFileName);

            if (move_uploaded_file($logoTmpPath, $logoDestinationPath)) {
                $logoFileName = "../public/uploads2/logos/" . $uniqueFileName; // Ruta de logo personalizado
            } else {
                echo json_encode(['success' => false, 'message' => 'Error al subir el logo. Se usar芍 el logo por defecto.']);
            }
        }

        $sql = "INSERT INTO teams (name, country, color, user_id, tournament_id, tournament_version_id, logo, category_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $con->prepare($sql);
        $stmt->bind_param("ssssisss", $team_name, $country, $color, $_SESSION['user_id'], $tournament_id, $tournament_version_id, $logoFileName, $category_id);

        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Equipo creado exitosamente.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error al crear el equipo.']);
        }
        $stmt->close();
    } else {
        echo json_encode(['success' => false, 'message' => 'Datos del equipo o torneo inv芍lidos.']);
    }
}
?>
