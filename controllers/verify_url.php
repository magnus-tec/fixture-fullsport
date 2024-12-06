<?php
include '../config/connection.php';

header('Content-Type: application/json');

if (isset($_GET['slug'])) {
    $slug = mysqli_real_escape_string($con, $_GET['slug']);

    // Consultar si la URL ya existe
    $sql = "SELECT COUNT(*) FROM tournaments WHERE url_slug = '$slug'";
    $result = mysqli_query($con, $sql);

    if (!$result) {
        echo json_encode(['error' => 'Error en la consulta: ' . mysqli_error($con)]);
    } else {
        $exists = mysqli_fetch_row($result)[0] > 0;
        echo json_encode(['exists' => $exists]);
    }
} else {
    echo json_encode(['error' => 'No se proporcionó un slug']);
}

mysqli_close($con);
?>
