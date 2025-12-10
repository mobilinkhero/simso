<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

$conn = include('../../config/database.php');

// Get statistics
$stats = [];

// Total Requests (All time)
$result = $conn->query("SELECT COUNT(*) as count FROM api_logs");
$stats['total_records'] = $result->fetch_assoc()['count'];

// Today's requests
$result = $conn->query("SELECT COUNT(*) as count FROM api_logs WHERE DATE(created_at) = CURDATE()");
$stats['today_requests'] = $result->fetch_assoc()['count'];

// Success rate (Today)
$result = $conn->query("SELECT 
    COUNT(*) as total,
    SUM(CASE WHEN response_status = 'success' THEN 1 ELSE 0 END) as success
    FROM api_logs WHERE DATE(created_at) = CURDATE()");
$row = $result->fetch_assoc();
$stats['success_rate'] = $row['total'] > 0 ? round(($row['success'] / $row['total']) * 100, 1) : 0;

$conn->close();

echo json_encode([
    'success' => true,
    'data' => $stats
]);
?>