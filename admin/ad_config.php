<?php include 'includes/header.php'; ?>

<?php
$message = '';
$error = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $conn = include('../config/database.php');

    // Config keys
    $configs = [
        'enable_ads' => isset($_POST['enable_ads']) ? '1' : '0',
        'admob_app_id' => $_POST['admob_app_id'] ?? '',
        'banner_ad_id' => $_POST['banner_ad_id'] ?? '',
        'rewarded_ad_id' => $_POST['rewarded_ad_id'] ?? '',
        'interstitial_ad_id' => $_POST['interstitial_ad_id'] ?? ''
    ];

    $success = true;
    $stmt = $conn->prepare("INSERT INTO settings (setting_key, setting_value) VALUES (?, ?) ON DUPLICATE KEY UPDATE setting_value = ?");

    foreach ($configs as $key => $value) {
        $stmt->bind_param("sss", $key, $value, $value);
        if (!$stmt->execute()) {
            $success = false;
            $error = "Error saving $key: " . $conn->error;
        }
    }

    if ($success) {
        $message = "Ad configuration saved successfully!";
    }
}

// Fetch current values
$conn = include('../config/database.php');
$settings = [];
$result = $conn->query("SELECT setting_key, setting_value FROM settings WHERE setting_key IN ('enable_ads', 'admob_app_id', 'banner_ad_id', 'rewarded_ad_id', 'interstitial_ad_id')");
while ($row = $result->fetch_assoc()) {
    $settings[$row['setting_key']] = $row['setting_value'];
}

// Defaults (Test IDs if not set)
$enable_ads = $settings['enable_ads'] ?? '0';
$admob_app_id = $settings['admob_app_id'] ?? 'ca-app-pub-3940256099942544~3347511713';
$banner_ad_id = $settings['banner_ad_id'] ?? 'ca-app-pub-3940256099942544/6300978111';
$rewarded_ad_id = $settings['rewarded_ad_id'] ?? 'ca-app-pub-3940256099942544/5224354917';
$interstitial_ad_id = $settings['interstitial_ad_id'] ?? 'ca-app-pub-3940256099942544/1033173712'; // Test ID
?>

<div class="container-fluid">
    <div class="row mb-4">
        <div class="col">
            <h2 class="fw-bold text-dark">Ad Configuration</h2>
            <p class="text-muted">Manage AdMob settings and Ad Units dynamically.</p>
        </div>
    </div>

    <?php if ($message): ?>
        <div class="alert alert-success"><?php echo $message; ?></div>
    <?php endif; ?>
    <?php if ($error): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>

    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 text-primary"><i class="fas fa-bullhorn me-2"></i>AdMob Settings</h5>
                </div>
                <div class="card-body p-4">
                    <form method="POST">

                        <!-- Global Toggle -->
                        <div class="form-check form-switch mb-4 p-3 bg-light rounded border">
                            <input class="form-check-input" type="checkbox" id="enable_ads" name="enable_ads" <?php echo $enable_ads == '1' ? 'checked' : ''; ?>
                                style="width: 3em; height: 1.5em; margin-right: 1em;">
                            <label class="form-check-label fw-bold mt-1" for="enable_ads">Enable Ads in App</label>
                            <div class="form-text ms-1">Turn this OFF to instantly hide all ads in the app (e.g. during
                                Play Store review).</div>
                        </div>

                        <!-- App ID -->
                        <div class="mb-4">
                            <label class="form-label fw-bold">AdMob App ID</label>
                            <input type="text" name="admob_app_id" class="form-control"
                                value="<?php echo htmlspecialchars($admob_app_id); ?>" required>
                            <div class="form-text">Found in AdMob > App Settings. Use
                                <code>ca-app-pub-3940256099942544~3347511713</code> for testing.</div>
                        </div>

                        <hr class="my-4">

                        <!-- Banner Ad -->
                        <div class="mb-3">
                            <label class="form-label fw-bold">Banner Ad Unit ID</label>
                            <input type="text" name="banner_ad_id" class="form-control"
                                value="<?php echo htmlspecialchars($banner_ad_id); ?>">
                            <div class="form-text">Bottom of the screen. Test ID:
                                <code>ca-app-pub-3940256099942544/6300978111</code></div>
                        </div>

                        <!-- Rewarded Ad -->
                        <div class="mb-3">
                            <label class="form-label fw-bold">Rewarded Ad Unit ID</label>
                            <input type="text" name="rewarded_ad_id" class="form-control"
                                value="<?php echo htmlspecialchars($rewarded_ad_id); ?>">
                            <div class="form-text">Triggers when clicking "Check Number". Test ID:
                                <code>ca-app-pub-3940256099942544/5224354917</code></div>
                        </div>

                        <!-- Interstitial Ad -->
                        <div class="mb-4">
                            <label class="form-label fw-bold">Interstitial Ad Unit ID</label>
                            <input type="text" name="interstitial_ad_id" class="form-control"
                                value="<?php echo htmlspecialchars($interstitial_ad_id); ?>">
                            <div class="form-text">Full screen ad (optional usage). Test ID:
                                <code>ca-app-pub-3940256099942544/1033173712</code></div>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-save me-2"></i> Save Configuration
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>