<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

$conn = include('../../config/database.php');

$query = "SELECT * FROM api_logs ORDER BY created_at DESC LIMIT 100";
$result = $conn->query($query);

$logs = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $logs[] = $row;
    }
}

$conn->close();

echo json_encode([
    'success' => true,
    'data' => $logs
]);
?>