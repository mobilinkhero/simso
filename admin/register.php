<?php
/**
 * One-Time Admin Registration
 * Create admin account without login
 * DELETE THIS FILE AFTER CREATING YOUR ADMIN ACCOUNT!
 */

require_once('../includes/AdminAuth.php');

$message = '';
$error = '';
$success = false;

// Handle registration
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    // Validation
    if (empty($username) || empty($password)) {
        $error = 'Username and password are required';
    } elseif (strlen($username) < 3) {
        $error = 'Username must be at least 3 characters';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters';
    } elseif ($password !== $confirmPassword) {
        $error = 'Passwords do not match';
    } else {
        // Create admin user
        $conn = include('../config/database.php');

        // Check if username already exists
        $stmt = $conn->prepare("SELECT id FROM admin_users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $error = 'Username already exists';
        } else {
            // Create password hash
            $passwordHash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);

            // Insert admin user
            $stmt = $conn->prepare("INSERT INTO admin_users (username, password_hash, is_active, created_at) VALUES (?, ?, 1, NOW())");
            $stmt->bind_param("ss", $username, $passwordHash);

            if ($stmt->execute()) {
                $success = true;
                $message = 'Admin account created successfully!';
            } else {
                $error = 'Failed to create admin account: ' . $conn->error;
            }
        }

        $conn->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Admin - SIMSO</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .register-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            width: 100%;
            max-width: 500px;
            overflow: hidden;
        }

        .register-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 40px 30px;
            text-align: center;
        }

        .register-header h1 {
            font-size: 32px;
            margin-bottom: 5px;
        }

        .register-header p {
            opacity: 0.9;
            font-size: 14px;
        }

        .warning-banner {
            background: #fff3cd;
            color: #856404;
            padding: 15px 30px;
            border-bottom: 2px solid #ffc107;
            font-size: 14px;
            font-weight: 600;
        }

        .register-body {
            padding: 40px 30px;
        }

        .form-group {
            margin-bottom: 25px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: 600;
            font-size: 14px;
        }

        .form-group input {
            width: 100%;
            padding: 14px 16px;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            font-size: 15px;
            transition: all 0.3s;
            outline: none;
        }

        .form-group input:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .form-group small {
            display: block;
            margin-top: 5px;
            color: #666;
            font-size: 12px;
        }

        .btn-register {
            width: 100%;
            padding: 16px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .btn-register:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(102, 126, 234, 0.3);
        }

        .btn-register:active {
            transform: translateY(0);
        }

        .alert {
            padding: 14px 16px;
            border-radius: 10px;
            margin-bottom: 20px;
            font-size: 14px;
        }

        .alert-error {
            background: #fee;
            color: #c33;
            border: 1px solid #fcc;
        }

        .alert-success {
            background: #efe;
            color: #3c3;
            border: 1px solid #cfc;
        }

        .success-actions {
            margin-top: 20px;
            text-align: center;
        }

        .success-actions a {
            display: inline-block;
            padding: 12px 30px;
            background: #667eea;
            color: white;
            text-decoration: none;
            border-radius: 10px;
            font-weight: 600;
            margin: 5px;
        }

        .success-actions a:hover {
            background: #5568d3;
        }

        .delete-warning {
            background: #f8d7da;
            color: #721c24;
            padding: 20px;
            border-radius: 10px;
            margin-top: 20px;
            font-size: 14px;
        }

        .delete-warning strong {
            display: block;
            margin-bottom: 10px;
            font-size: 16px;
        }

        @media (max-width: 480px) {
            .register-header h1 {
                font-size: 26px;
            }

            .register-body {
                padding: 30px 20px;
            }
        }
    </style>
</head>

<body>
    <div class="register-container">
        <div class="register-header">
            <h1>👤 Register Admin</h1>
            <p>Create Your Admin Account</p>
        </div>

        <div class="warning-banner">
            ⚠️ ONE-TIME USE ONLY - Delete this file after registration!
        </div>

        <div class="register-body">
            <?php if ($error): ?>
                <div class="alert alert-error">
                    ⚠️ <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="alert alert-success">
                    ✓ <?php echo htmlspecialchars($message); ?>
                </div>

                <div class="success-actions">
                    <a href="login.php">Go to Login</a>
                </div>

                <div class="delete-warning">
                    <strong>🔥 IMPORTANT - DELETE THIS FILE NOW!</strong>
                    <p>For security reasons, delete <code>register.php</code> immediately.</p>
                    <p>You can now login with your new credentials at <a href="login.php">login.php</a></p>
                </div>
            <?php else: ?>
                <form method="POST" action="">
                    <div class="form-group">
                        <label for="username">Username</label>
                        <input type="text" id="username" name="username" required autocomplete="username" autofocus
                            placeholder="Choose a username"
                            value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>">
                        <small>At least 3 characters</small>
                    </div>

                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" required autocomplete="new-password"
                            placeholder="Choose a strong password">
                        <small>At least 6 characters</small>
                    </div>

                    <div class="form-group">
                        <label for="confirm_password">Confirm Password</label>
                        <input type="password" id="confirm_password" name="confirm_password" required
                            autocomplete="new-password" placeholder="Re-enter your password">
                        <small>Must match the password above</small>
                    </div>

                    <button type="submit" class="btn-register">
                        Create Admin Account
                    </button>
                </form>

                <div class="delete-warning" style="margin-top: 30px;">
                    <strong>⚠️ Security Warning</strong>
                    <p>This page allows anyone to create an admin account without authentication.</p>
                    <p><strong>Delete this file immediately after creating your account!</strong></p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
        // Prevent multiple form submissions
        document.querySelector('form')?.addEventListener('submit', function (e) {
            const btn = document.querySelector('.btn-register');
            btn.disabled = true;
            btn.textContent = 'Creating account...';
        });

        // Password match validation
        const password = document.getElementById('password');
        const confirmPassword = document.getElementById('confirm_password');

        confirmPassword?.addEventListener('input', function () {
            if (this.value !== password.value) {
                this.setCustomValidity('Passwords do not match');
            } else {
                this.setCustomValidity('');
            }
        });
    </script>
</body>

</html>