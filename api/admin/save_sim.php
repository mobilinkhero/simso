<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');

$conn = include('../../config/database.php');

$input = json_decode(file_get_contents('php://input'), true);

if (!isset($input['phone_number'])) {
    echo json_encode(['success' => false, 'message' => 'Phone number is required']);
    exit;
}

$id = isset($input['id']) && !empty($input['id']) ? intval($input['id']) : null;
$phoneNumber = $conn->real_escape_string($input['phone_number']);
$operatorName = $conn->real_escape_string($input['operator_name'] ?? '');
$status = $conn->real_escape_string($input['status'] ?? 'Active');
$dataPlan = $conn->real_escape_string($input['data_plan'] ?? '');
$roaming = $conn->real_escape_string($input['roaming'] ?? 'Off');
$networkType = $conn->real_escape_string($input['network_type'] ?? '');
$country = $conn->real_escape_string($input['country'] ?? '');
$region = $conn->real_escape_string($input['region'] ?? '');

if ($id) {
    // Update existing record
    $query = "UPDATE sim_data SET 
        phone_number = '$phoneNumber',
        operator_name = '$operatorName',
        status = '$status',
        data_plan = '$dataPlan',
        roaming = '$roaming',
        network_type = '$networkType',
        country = '$country',
        region = '$region'
        WHERE id = $id";
} else {
    // Insert new record
    $query = "INSERT INTO sim_data (phone_number, operator_name, status, data_plan, roaming, network_type, country, region, registration_date)
        VALUES ('$phoneNumber', '$operatorName', '$status', '$dataPlan', '$roaming', '$networkType', '$country', '$region', CURDATE())";
}

if ($conn->query($query)) {
    echo json_encode(['success' => true, 'message' => 'SIM data saved successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $conn->error]);
}

$conn->close();
?>