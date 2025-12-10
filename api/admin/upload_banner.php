<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

$conn = include('../../config/database.php');

// Get banner type (whitelist or alt)
$bannerType = $_POST['banner_type'] ?? '';
$bannerUrl = $_POST['banner_url'] ?? '';

if (empty($bannerType) || !in_array($bannerType, ['whitelist', 'alt'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid banner type']);
    exit;
}

$uploadDir = '../../assets/banners/';
$settingKeyImage = $bannerType . '_banner_image';
$settingKeyUrl = $bannerType . '_banner_url';
$imagePath = '';

// Handle image upload
if (isset($_FILES['banner_image']) && $_FILES['banner_image']['error'] === UPLOAD_ERR_OK) {
    $file = $_FILES['banner_image'];

    // Validate file size (max 2MB)
    if ($file['size'] > 2 * 1024 * 1024) {
        echo json_encode(['success' => false, 'message' => 'File size exceeds 2MB']);
        exit;
    }

    // Validate file type
    $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
    if (!in_array($file['type'], $allowedTypes)) {
        echo json_encode(['success' => false, 'message' => 'Invalid file type. Only JPG, PNG, GIF, WEBP allowed']);
        exit;
    }

    // Create upload directory if doesn't exist
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    // Generate unique filename
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = $bannerType . '_banner_' . time() . '.' . $extension;
    $uploadPath = $uploadDir . $filename;

    // Delete old banner if exists
    $oldBannerQuery = $conn->query("SELECT setting_value FROM settings WHERE setting_key = '$settingKeyImage'");
    if ($oldBannerQuery && $row = $oldBannerQuery->fetch_assoc()) {
        $oldBanner = $row['setting_value'];
        if ($oldBanner && file_exists('../../' . $oldBanner)) {
            unlink('../../' . $oldBanner);
        }
    }

    // Move uploaded file
    if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
        $imagePath = 'assets/banners/' . $filename;
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to upload image']);
        exit;
    }
}

// Update database
try {
    // Update image path if uploaded
    if ($imagePath) {
        $stmt = $conn->prepare("INSERT INTO settings (setting_key, setting_value, description) VALUES (?, ?, ?) 
                               ON DUPLICATE KEY UPDATE setting_value = ?");
        $desc = ucfirst($bannerType) . ' banner image URL';
        $stmt->bind_param("ssss", $settingKeyImage, $imagePath, $desc, $imagePath);
        $stmt->execute();
    }

    // Update URL
    $stmt = $conn->prepare("INSERT INTO settings (setting_key, setting_value, description) VALUES (?, ?, ?) 
                           ON DUPLICATE KEY UPDATE setting_value = ?");
    $desc = ucfirst($bannerType) . ' banner click URL';
    $stmt->bind_param("ssss", $settingKeyUrl, $bannerUrl, $desc, $bannerUrl);
    $stmt->execute();

    $conn->close();

    echo json_encode([
        'success' => true,
        'message' => 'Banner updated successfully',
        'image_path' => $imagePath,
        'banner_url' => $bannerUrl
    ]);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>