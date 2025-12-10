<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

$conn = include('../../config/database.php');

$query = "SELECT * FROM settings ORDER BY setting_key";
$result = $conn->query($query);

$settings = [];
while ($row = $result->fetch_assoc()) {
    $settings[] = $row;
}

$conn->close();

echo json_encode([
    'success' => true,
    'data' => $settings
]);
?>