<?php
// Require authentication for all admin pages
require_once(__DIR__ . '/../../includes/AdminAuth.php');
AdminAuth::requireAuth();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIMSO Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #6B4CE6;
            --secondary-color: #F3E5F5;
            --text-dark: #2D3436;
        }

        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .sidebar {
            height: 100vh;
            background: white;
            box-shadow: 2px 0 10px rgba(0, 0, 0, 0.05);
            position: fixed;
            left: 0;
            top: 0;
            width: 250px;
            z-index: 1000;
        }

        .main-content {
            margin-left: 250px;
            padding: 30px;
        }

        .nav-link {
            color: #666;
            padding: 12px 20px;
            border-radius: 8px;
            margin-bottom: 5px;
            transition: all 0.3s;
            display: flex;
            align-items: center;
        }

        .nav-link:hover,
        .nav-link.active {
            background-color: var(--secondary-color);
            color: var(--primary-color);
            font-weight: 500;
        }

        .nav-link i {
            width: 25px;
            font-size: 1.1em;
        }

        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            margin-bottom: 20px;
            background: white;
        }

        .card-header {
            background: white;
            border-bottom: 1px solid #eee;
            border-radius: 15px 15px 0 0 !important;
            padding: 20px;
        }

        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            padding: 10px 20px;
            border-radius: 8px;
        }

        .btn-primary:hover {
            background-color: #5a3dc9;
        }

        /* Dashboard Specific */
        .stat-card {
            transition: transform 0.2s;
        }

        .stat-card:hover {
            transform: translateY(-5px);
        }

        .stat-icon {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin-bottom: 15px;
        }
    </style>
</head>

<body>

    <!-- Sidebar -->
    <div class="sidebar p-3">
        <div class="d-flex align-items-center mb-5 px-2 mt-2">
            <i class="fas fa-sim-card fa-2x text-primary me-2" style="color: var(--primary-color) !important;"></i>
            <h4 class="m-0 fw-bold" style="color: var(--primary-color);">SIMSO</h4>
        </div>

        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>"
                    href="index.php">
                    <i class="fas fa-chart-line"></i> Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'config.php' ? 'active' : ''; ?>"
                    href="config.php">
                    <i class="fas fa-mobile-alt"></i> App Config
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'popup_config.php' ? 'active' : ''; ?>"
                    href="popup_config.php">
                    <i class="fas fa-exclamation-circle"></i> Popup Config
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'banner_config.php' ? 'active' : ''; ?>"
                    href="banner_config.php">
                    <i class="fas fa-image"></i> Banner Config
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'push.php' ? 'active' : ''; ?>"
                    href="push.php">
                    <i class="fas fa-bell"></i> Push Notifications
                </a>
            </li>
            <li class="nav-item mt-auto" style="margin-top: auto; padding-top: 20px; border-top: 1px solid #eee;">
                <a class="nav-link text-danger" href="logout.php">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </li>
        </ul>
    </div>

    <!-- Main Content Start -->
    <div class="main-content">