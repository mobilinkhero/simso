<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');

$conn = include('../../config/database.php');

$input = json_decode(file_get_contents('php://input'), true);

if (isset($input['setting_key']) && isset($input['setting_value'])) {
    // Single setting update
    $key = $conn->real_escape_string($input['setting_key']);
    $value = $conn->real_escape_string($input['setting_value']);

    // Check if setting exists first
    $check = $conn->query("SELECT setting_key FROM settings WHERE setting_key = '$key'");
    if ($check->num_rows > 0) {
        $query = "UPDATE settings SET setting_value = '$value' WHERE setting_key = '$key'";
        $conn->query($query);
    } else {
        // Insert if not exists (optional, but good for robustness)
        $query = "INSERT INTO settings (setting_key, setting_value) VALUES ('$key', '$value')";
        $conn->query($query);
    }

    echo json_encode(['success' => true, 'message' => 'Setting saved successfully']);

} else {
    // Bulk update (fallback support)
    foreach ($input as $key => $value) {
        $key = $conn->real_escape_string($key);
        $value = $conn->real_escape_string($value);

        $query = "UPDATE settings SET setting_value = '$value' WHERE setting_key = '$key'";
        $conn->query($query);
    }
    echo json_encode(['success' => true, 'message' => 'Settings saved successfully']);
}

$conn->close();
?>