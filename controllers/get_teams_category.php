<?php
require_once "../config/connection.php";
header('Content-Type: application/json');

if (isset($_POST['version_id']) && isset($_POST['tournament_id']) && isset($_POST['category_id'])) {
  $version_id = $_POST['version_id'];
  $category_id = $_POST['category_id'];
  $tournament_id = $_POST['tournament_id'];

  error_log("version_id: " . $version_id);
  error_log("version_id: " . $category_id);
  error_log("version_id: " . $tournament_id);

  $select_teams_sql = "SELECT * FROM teams WHERE tournament_id = ? AND tournament_version_id = ?  AND category_id = ?";

  if ($stmt = $con->prepare($select_teams_sql)) {
    $stmt->bind_param("iii",  $tournament_id, $version_id, $category_id);
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
