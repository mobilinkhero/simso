<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

$conn = include('../../config/database.php');

$query = "SELECT * FROM sim_data ORDER BY created_at DESC";
$result = $conn->query($query);

$sims = [];
while ($row = $result->fetch_assoc()) {
    $sims[] = $row;
}

$conn->close();

echo json_encode([
    'success' => true,
    'sims' => $sims
]);
?>