<?php
// Security: Block browser access
require_once('../includes/ApiSecurity.php');
ApiSecurity::blockBrowserAccess();

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

$conn = include('../config/database.php');

// Fetch app configuration settings
$result = $conn->query("SELECT setting_key, setting_value FROM settings WHERE setting_key IN (
    'home_title', 'home_subtitle', 'input_label', 'input_hint', 'button_text',
    'alt_home_title', 'alt_home_subtitle', 'alt_input_label', 'alt_input_hint', 'alt_button_text',
    'whitelist_error_title', 'whitelist_error_msg', 'whitelist_action_text', 'whitelist_action_url',
    'alt_error_title', 'alt_error_msg', 'whitelisted_countries',
    'whitelist_banner_image', 'whitelist_banner_url', 'alt_banner_image', 'alt_banner_url',
    'enable_ads', 'admob_app_id', 'banner_ad_id', 'rewarded_ad_id', 'interstitial_ad_id'
)");

$settings = [];
while ($row = $result->fetch_assoc()) {
    $settings[$row['setting_key']] = $row['setting_value'];
}

// Determine user's country
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

// Check if whitelisted
$whitelistStr = $settings['whitelisted_countries'] ?? 'PK';
$whitelist = array_map('trim', explode(',', strtoupper($whitelistStr)));
$isWhitelisted = in_array($countryCode, $whitelist);

// Return appropriate config based on geo-location
$config = [];

if ($isWhitelisted) {
    // Whitelisted countries get default config
    $config['home_title'] = $settings['home_title'] ?? 'SIMSO';
    $config['home_subtitle'] = $settings['home_subtitle'] ?? 'SIM INFORMATION CHECKER';
    $config['input_label'] = $settings['input_label'] ?? 'Enter Phone Number';
    $config['input_hint'] = $settings['input_hint'] ?? 'e.g. 03001234567';
    $config['button_text'] = $settings['button_text'] ?? 'Check Sim Info';
    $config['error_title'] = $settings['whitelist_error_title'] ?? 'Record Not Found';
    $config['error_msg'] = $settings['whitelist_error_msg'] ?? 'We could not find any details for this number.';
    $config['action_text'] = $settings['whitelist_action_text'] ?? '';
    $config['action_url'] = $settings['whitelist_action_url'] ?? '';
    $config['banner_image'] = $settings['whitelist_banner_image'] ?? '';
    $config['banner_url'] = $settings['whitelist_banner_url'] ?? '';
} else {
    // Non-whitelisted countries get alternative config
    $config['home_title'] = $settings['alt_home_title'] ?? 'SIMSO 124';
    $config['home_subtitle'] = $settings['alt_home_subtitle'] ?? 'This is a test message 1';
    $config['input_label'] = $settings['alt_input_label'] ?? 'Enter Phone Number or cnic';
    $config['input_hint'] = $settings['alt_input_hint'] ?? 'e.g. 03001234567';
    $config['button_text'] = $settings['alt_button_text'] ?? 'Check Sim Info';
    $config['error_title'] = $settings['alt_error_title'] ?? 'No Network Data';
    $config['error_msg'] = $settings['alt_error_msg'] ?? 'Network information is currently unavailable for this number.';
    $config['action_text'] = ''; // No action button for non-whitelisted
    $config['action_url'] = '';
    $config['banner_image'] = $settings['alt_banner_image'] ?? '';
    $config['banner_image'] = $settings['alt_banner_image'] ?? '';
    $config['banner_url'] = $settings['alt_banner_url'] ?? '';
}

// Attach Ad Settings (Global)
$config['enable_ads'] = $settings['enable_ads'] == '1';
$config['admob_app_id'] = $settings['admob_app_id'] ?? 'ca-app-pub-3940256099942544~3347511713';
$config['banner_ad_id'] = $settings['banner_ad_id'] ?? 'ca-app-pub-3940256099942544/6300978111';
$config['rewarded_ad_id'] = $settings['rewarded_ad_id'] ?? 'ca-app-pub-3940256099942544/5224354917';
$config['interstitial_ad_id'] = $settings['interstitial_ad_id'] ?? 'ca-app-pub-3940256099942544/1033173712';

$conn->close();

echo json_encode([
    'success' => true,
    'config' => $config
]);
?>