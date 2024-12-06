<?php
include '../config/connection.php';

header('Content-Type: application/json');

$sql = "SELECT * FROM tournaments";
$result = mysqli_query($con, $sql);

$tournaments = [];
while ($row = mysqli_fetch_assoc($result)) {
    $tournaments[] = $row;
}

echo json_encode($tournaments);
mysqli_close($con);
?>

