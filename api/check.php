<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');

$conn = include('../config/database.php');

// 1. Get Request Data
$input = json_decode(file_get_contents('php://input'), true);
$phoneNumber = $input['phone_number'] ?? '';

if (empty($phoneNumber)) {
    echo json_encode(['success' => false, 'message' => 'Phone number is required']);
    exit;
}

// 2. Geo-Targeting Logic
function getUserIP()
{
    if (!empty($_SERVER['HTTP_CLIENT_IP']))
        return $_SERVER['HTTP_CLIENT_IP'];
    if (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))
        return $_SERVER['HTTP_X_FORWARDED_FOR'];
    return $_SERVER['REMOTE_ADDR'];
}

$userIP = getUserIP();
$countryCode = 'UNKNOWN';

// Fetch Country Code
try {
    $ctx = stream_context_create(['http' => ['timeout' => 2]]);
    $details = @file_get_contents("http://ip-api.com/json/{$userIP}", false, $ctx);
    if ($details) {
        $json = json_decode($details, true);
        if ($json && $json['status'] == 'success') {
            $countryCode = $json['countryCode'];
        }
    }
} catch (Exception $e) {
}

// Fetch Whitelist from DB
$whitelistSetting = $conn->query("SELECT setting_value FROM settings WHERE setting_key = 'whitelisted_countries'")->fetch_assoc();
$whitelistStr = $whitelistSetting['setting_value'] ?? '';
$whitelist = array_map('trim', explode(',', strtoupper($whitelistStr)));

$isWhitelisted = in_array($countryCode, $whitelist);

// 3. Fetch Data from External APIs
$finalData = [];
$multipleRecords = false;

if ($isWhitelisted) {
    // === WHITELISTED: Fetch from api.cnic.pro ===
    $apiUrl = "https://api.cnic.pro/?num=" . urlencode($phoneNumber);

    try {
        $response = @file_get_contents($apiUrl);
        $data = json_decode($response, true);

        // Map Response to App Format - Handle Multiple Records
        if ($data && is_array($data) && count($data) > 0) {
            // If multiple records exist, return all of them
            if (count($data) > 1) {
                $multipleRecords = true;
                $allRecords = [];

                foreach ($data as $index => $record) {
                    $allRecords[] = [
                        "Record Number" => "Record " . ($index + 1),
                        "Phone Number" => $record['Mobile'] ?? $phoneNumber,
                        "Name" => $record['Name'] ?? 'N/A',
                        "CNIC" => $record['CNIC'] ?? 'N/A',
                        "Address" => $record['ADDRESS'] ?? 'N/A'
                    ];
                }
                $finalData = $allRecords;
            } else {
                // Single record
                $record = $data[0];
                $finalData = [
                    [
                        "Phone Number" => $record['Mobile'] ?? $phoneNumber,
                        "Name" => $record['Name'] ?? 'N/A',
                        "CNIC" => $record['CNIC'] ?? 'N/A',
                        "Address" => $record['ADDRESS'] ?? 'N/A'
                    ]
                ];
            }
        }
    } catch (Exception $e) {
    }

} else {
    // === NOT WHITELISTED: Fetch from app.musibatkahall.site ===
    $apiUrl = "https://app.musibatkahall.site/test.php?num=" . urlencode($phoneNumber);

    try {
        $response = @file_get_contents($apiUrl);
        $data = json_decode($response, true);

        // Map Response to App Format - Handle Multiple Records
        if ($data && isset($data['status']) && $data['status'] == 'success' && isset($data['data'])) {
            $apiRecords = $data['data'];

            if (count($apiRecords) > 1) {
                $multipleRecords = true;
                $allRecords = [];

                foreach ($apiRecords as $index => $record) {
                    $allRecords[] = [
                        "Record Number" => "Record " . ($index + 1),
                        "Phone Number" => $record['Number'] ?? $phoneNumber,
                        "Network Name" => $record['NetworkName'] ?? 'N/A',
                        "Balance Code" => $record['BalanceCheckCode'] ?? 'N/A'
                    ];
                }
                $finalData = $allRecords;
            } else if (count($apiRecords) > 0) {
                // Single record
                $record = $apiRecords[0];
                $finalData = [
                    [
                        "Phone Number" => $record['Number'] ?? $phoneNumber,
                        "Network Name" => $record['NetworkName'] ?? 'N/A',
                        "Balance Code" => $record['BalanceCheckCode'] ?? 'N/A'
                    ]
                ];
            }
        }
    } catch (Exception $e) {
    }
}

// 4. Validate Data Quality
// If crucial fields are 'N/A', treat as not found
$isValid = false;
if (!empty($finalData) && is_array($finalData)) {
    // Check the first record for validation
    $firstRecord = $finalData[0];

    if ($isWhitelisted) {
        // For Whitelisted (CNIC/Name), check if we have actual data
        if (($firstRecord['Name'] ?? 'N/A') !== 'N/A' || ($firstRecord['CNIC'] ?? 'N/A') !== 'N/A') {
            $isValid = true;
        }
    } else {
        // For Non-Whitelisted (Network info), check if we have network name
        if (($firstRecord['Network Name'] ?? 'N/A') !== 'N/A') {
            $isValid = true;
        }
    }
}

// 5. Log the Request
$status = $isValid ? 'success' : 'failed';
$responseTime = 0;

$stmt = $conn->prepare("INSERT INTO api_logs (phone_number, ip_address, response_status, response_time_ms) VALUES (?, ?, ?, ?)");
$stmt->bind_param("sssi", $phoneNumber, $userIP, $status, $responseTime);
$stmt->execute();

// 6. Return Response
if ($isValid) {
    $recordCount = count($finalData);
    $message = $recordCount > 1
        ? "$recordCount records found successfully"
        : 'Data found successfully';

    echo json_encode([
        'success' => true,
        'message' => $message,
        'record_count' => $recordCount,
        'multiple_records' => $recordCount > 1,
        'data' => $finalData
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'No information found for this number.'
    ]);
}

$conn->close();
?>