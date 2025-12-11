<?php include 'includes/header.php'; ?>
<?php require_once '../includes/GoogleAccessToken.php'; ?>

<?php
$message = '';
$error = '';
$secureUploadPath = '../uploads/secure/service-account.json';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['upload_json'])) {
        // Handle JSON File Upload
        if (isset($_FILES['service_json']) && $_FILES['service_json']['error'] === UPLOAD_ERR_OK) {
            $fileTmpPath = $_FILES['service_json']['tmp_name'];
            $fileType = $_FILES['service_json']['type'];
            
            // Validate JSON
            $content = file_get_contents($fileTmpPath);
            $json = json_decode($content, true);
            
            if ($json && isset($json['project_id']) && isset($json['private_key'])) {
                if (move_uploaded_file($fileTmpPath, $secureUploadPath)) {
                    $message = "Service Account Key uploaded successfully! HTTP v1 API is ready.";
                    
                    // Save project ID to settings for reference
                    $conn = include('../config/database.php');
                    $projectId = $json['project_id'];
                    $stmt = $conn->prepare("INSERT INTO settings (setting_key, setting_value) VALUES ('fcm_project_id', ?) ON DUPLICATE KEY UPDATE setting_value = ?");
                    $stmt->bind_param("ss", $projectId, $projectId);
                    $stmt->execute();
                } else {
                    $error = "Failed to move uploaded file.";
                }
            } else {
                $error = "Invalid JSON file. Please upload the correct Service Account JSON from Firebase.";
            }
        } else {
            $error = "File upload failed.";
        }
    } elseif (isset($_POST['send_push'])) {
        // Send Notification using HTTP v1
        $title = $_POST['title'] ?? '';
        $body = $_POST['body'] ?? '';
        $enteredUrl = $_POST['image_url'] ?? '';
        $topic = $_POST['topic'] ?? 'all';
        $finalImageUrl = $enteredUrl;

        // Handle Image Upload
        if (isset($_FILES['notification_image']) && $_FILES['notification_image']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = '../uploads/notifications/';
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            
            $fileName = time() . '_' . basename($_FILES['notification_image']['name']);
            $targetPath = $uploadDir . $fileName;
            
            if (move_uploaded_file($_FILES['notification_image']['tmp_name'], $targetPath)) {
                // Construct URL
                // Assuming script is in /admin/push.php, we need root URL
                $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
                $host = $_SERVER['HTTP_HOST'];
                // Adjust path based on your deployment. For localhost it might be /simsosiminfobackend/
                // Ideally use the configured base URL, but we can construct it relative to current script
                
                // Hardcoding simso.sbs based on previous conversations, but fallback to dynamic
                if (strpos($host, 'simso.sbs') !== false) {
                     $finalImageUrl = "https://simso.sbs/uploads/notifications/" . $fileName;
                } else {
                     // Localhost or other
                     $path = dirname(dirname($_SERVER['PHP_SELF'])); // go up one level from /admin
                     $finalImageUrl = "$protocol://$host$path/uploads/notifications/$fileName";
                }
            } else {
                $error = "Failed to upload image.";
            }
        }
        
        if (empty($error)) {
            try {
                if (!file_exists($secureUploadPath)) {
                    throw new Exception("Service Account JSON not found. Please upload it first.");
                }
                
                // 1. Get Access Token
                $tokenData = GoogleAccessToken::getToken($secureUploadPath);
                $accessToken = $tokenData['access_token'];
                $projectId = $tokenData['project_id'];
                
                // 2. Prepare HTTP v1 Payload
                $url = "https://fcm.googleapis.com/v1/projects/{$projectId}/messages:send";
                
                $notificationPayload = [
                    'title' => $title,
                    'body' => $body
                ];
    
                // Add image if provided
                if (!empty($finalImageUrl)) {
                    $notificationPayload['image'] = $finalImageUrl;
                }
    
                $payload = [
                    'message' => [
                        'topic' => $topic,
                        'notification' => $notificationPayload,
                        'data' => [
                            'title' => $title,
                            'body' => $body,
                            'image' => $finalImageUrl,
                            'click_action' => 'FLUTTER_NOTIFICATION_CLICK'
                        ],
                        'android' => [
                            'priority' => 'high'
                        ]
                    ]
                ];
                
                // 3. Send Request
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_HTTPHEADER, [
                    'Authorization: Bearer ' . $accessToken,
                    'Content-Type: application/json'
                ]);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
                
                $result = curl_exec($ch);
                $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                
                if ($result === FALSE) {
                     throw new Exception("CURL Error: " . curl_error($ch));
                }
                
                curl_close($ch);
                
                $response = json_decode($result, true);
                
                if ($httpCode == 200 && isset($response['name'])) {
                    $message = "Notification sent successfully! (ID: " . $response['name'] . ")";
                } else {
                    $errorMsg = isset($response['error']['message']) ? $response['error']['message'] : $result;
                    throw new Exception("FCM Error ($httpCode): " . $errorMsg);
                }
                
            } catch (Exception $e) {
                $error = $e->getMessage();
            }
        }
    }
}

// Check status
$isConfigured = file_exists($secureUploadPath);
?>

<div class="container-fluid">
    <div class="row mb-4">
        <div class="col">
            <h2 class="fw-bold text-dark">Push Notifications (HTTP v1)</h2>
            <p class="text-muted">Send alerts using the new Firebase HTTP v1 API</p>
        </div>
    </div>

    <?php if ($message): ?>
        <div class="alert alert-success"><?php echo $message; ?></div>
    <?php endif; ?>
    <?php if ($error): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>

    <div class="row">
        <!-- Send Notification Card -->
        <div class="col-md-7">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Send Message</h5>
                    <?php if ($isConfigured): ?>
                        <span class="badge bg-success">System Ready</span>
                    <?php else: ?>
                        <span class="badge bg-danger">Not Configured</span>
                    <?php endif; ?>
                </div>
                <div class="card-body">
                    <?php if (!$isConfigured): ?>
                        <div class="alert alert-warning">
                            Please upload your Service Account JSON file in the Setup panel first.
                        </div>
                    <?php endif; ?>

                    <form method="POST" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label class="form-label">Title</label>
                            <input type="text" name="title" class="form-control" required placeholder="e.g. New Update Available!" <?php echo !$isConfigured ? 'disabled' : ''; ?>>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Message Body</label>
                            <textarea name="body" class="form-control" rows="4" required placeholder="Enter your message details..." <?php echo !$isConfigured ? 'disabled' : ''; ?>></textarea>
                        </div>
                        
                        <!-- Image Upload Section -->
                         <div class="mb-3">
                            <label class="form-label">Notification Image (Optional)</label>
                            <div class="card p-3 bg-light text-center border-dashed" style="border: 2px dashed #ccc; cursor: pointer;" onclick="document.getElementById('imgUpload').click()">
                                <i class="fas fa-cloud-upload-alt fa-2x text-muted mb-2"></i>
                                <p class="mb-0 text-muted">Click or Drag to Upload Image</p>
                                <input type="file" name="notification_image" id="imgUpload" class="d-none" accept="image/*" onchange="previewImage(this)">
                            </div>
                            <div id="imagePreview" class="mt-2 text-center d-none">
                                <img src="" style="max-height: 150px; border-radius: 8px; border: 1px solid #ddd;">
                                <p class="small text-success mt-1">Image selected</p>
                            </div>
                         </div>

                        <div class="mb-3">
                            <label class="form-label">Or Enter Image URL</label>
                            <input type="url" name="image_url" class="form-control" placeholder="https://example.com/banner.jpg" <?php echo !$isConfigured ? 'disabled' : ''; ?>>
                        </div>
                        
                        <div class="alert alert-info py-2">
                            <small><i class="fas fa-info-circle"></i> This will be sent to ALL users via the 'all' topic.</small>
                        </div>

                        <button type="submit" name="send_push" class="btn btn-primary" <?php echo !$isConfigured ? 'disabled' : ''; ?>>
                            <i class="fas fa-paper-plane me-2"></i> Send Notification
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Configuration Card -->
        <div class="col-md-5">
            <div class="card">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0">Setup (HTTP v1)</h5>
                </div>
                <div class="card-body">
                    <form method="POST" enctype="multipart/form-data">
                        <div class="mb-3 h-100">
                            <label class="form-label fw-bold">Upload Service Account JSON</label>
                            <p class="small text-muted mb-2">
                                1. Go to <a href="https://console.firebase.google.com/" target="_blank">Firebase Console</a> > Project Settings > Service accounts.<br>
                                2. Click "Generate new private key".<br>
                                3. Upload the downloaded JSON file here.
                            </p>
                            <input type="file" name="service_json" class="form-control" accept=".json" required>
                        </div>
                        
                        <?php if ($isConfigured): ?>
                            <div class="alert alert-success py-2 mb-3">
                                <small>✓ Service Key is currently installed.</small>
                            </div>
                        <?php endif; ?>

                        <button type="submit" name="upload_json" class="btn btn-secondary w-100">
                            <i class="fas fa-upload me-2"></i> Upload Key File
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function previewImage(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            var preview = document.getElementById('imagePreview');
            preview.querySelector('img').src = e.target.result;
            preview.classList.remove('d-none');
        }
        reader.readAsDataURL(input.files[0]);
    }
}
</script>

<?php include 'includes/footer.php'; ?>