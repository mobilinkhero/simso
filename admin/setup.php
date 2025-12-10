<?php
/**
 * Admin Authentication Setup Script
 * Run this ONCE to create database tables and default admin user
 */

require_once('../config/database.php');

echo "<h2>SIMSO Admin Authentication Setup</h2>";
echo "<p>Setting up admin authentication system...</p>";

// Read SQL file
$sql = file_get_contents(__DIR__ . '/../database/admin_auth.sql');

// Split into individual queries
$queries = array_filter(array_map('trim', explode(';', $sql)));

$success = 0;
$errors = 0;

foreach ($queries as $query) {
    if (empty($query))
        continue;

    if ($conn->query($query)) {
        $success++;
        echo "<p style='color: green;'>✓ Query executed successfully</p>";
    } else {
        $errors++;
        echo "<p style='color: red;'>✗ Error: " . $conn->error . "</p>";
    }
}

echo "<hr>";
echo "<h3>Setup Complete!</h3>";
echo "<p><strong>Success:</strong> $success queries</p>";
echo "<p><strong>Errors:</strong> $errors queries</p>";

if ($errors == 0) {
    echo "<div style='background: #d4edda; padding: 20px; border-radius: 10px; margin-top: 20px;'>";
    echo "<h4 style='color: #155724;'>✓ Setup Successful!</h4>";
    echo "<p><strong>Default Login Credentials:</strong></p>";
    echo "<p>Username: <code>admin</code></p>";
    echo "<p>Password: <code>Admin@123</code></p>";
    echo "<p style='color: #856404; background: #fff3cd; padding: 10px; border-radius: 5px;'>";
    echo "<strong>⚠️ IMPORTANT:</strong> Change the default password immediately after first login!";
    echo "</p>";
    echo "<p><a href='login.php' style='display: inline-block; background: #667eea; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; margin-top: 10px;'>Go to Login Page</a></p>";
    echo "</div>";

    echo "<hr>";
    echo "<p style='color: #856404;'><strong>Security Note:</strong> Delete this setup.php file after setup is complete!</p>";
} else {
    echo "<div style='background: #f8d7da; padding: 20px; border-radius: 10px; margin-top: 20px;'>";
    echo "<h4 style='color: #721c24;'>✗ Setup Failed</h4>";
    echo "<p>Please check the errors above and try again.</p>";
    echo "</div>";
}

$conn->close();
?>