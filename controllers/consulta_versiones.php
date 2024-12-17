<?php
require_once "../config/connection.php";
header('Content-Type: application/json');

if (isset($_POST['version_id'])) {
    $version_id = $_POST['version_id'];

    error_log("version_id: " . $version_id);

    $select_versions_sql = "
        SELECT * FROM tournament_categories  tc
JOIN tournament_version_details tvd ON tvd.version_id = tc.tournament_version_id
WHERE tc.tournament_version_id = ?;";

    if ($stmt = $con->prepare($select_versions_sql)) {
        $stmt->bind_param("i",  $version_id);
        $stmt->execute();
        $result = $stmt->get_result();

        $versionesTorneo = $result->fetch_all(MYSQLI_ASSOC);

        echo json_encode($versionesTorneo);
    } else {
        echo json_encode([]);
    }
} else {
    echo json_encode([]);
}