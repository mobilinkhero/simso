<?php
/**
 * Advanced Admin Authentication System
 * Multi-layer security with session management
 */

class AdminAuth
{

    private static $SESSION_TIMEOUT = 3600; // 1 hour
    private static $MAX_LOGIN_ATTEMPTS = 5;
    private static $LOCKOUT_TIME = 900; // 15 minutes

    /**
     * Initialize secure session
     */
    public static function initSession()
    {
        if (session_status() === PHP_SESSION_NONE) {
            // Secure session configuration
            ini_set('session.cookie_httponly', 1);
            ini_set('session.cookie_secure', 0); // Set to 1 if using HTTPS
            ini_set('session.use_only_cookies', 1);
            ini_set('session.cookie_samesite', 'Strict');

            session_name('SIMSO_ADMIN_SESSION');
            session_start();

            // Regenerate session ID periodically
            if (!isset($_SESSION['created'])) {
                $_SESSION['created'] = time();
            } else if (time() - $_SESSION['created'] > 1800) {
                session_regenerate_id(true);
                $_SESSION['created'] = time();
            }
        }
    }

    /**
     * Verify admin credentials with password hashing
     */
    public static function verifyCredentials($username, $password)
    {
        $conn = include(__DIR__ . '/../config/database.php');

        // Check if account is locked
        if (self::isAccountLocked($username)) {
            return [
                'success' => false,
                'message' => 'Account temporarily locked due to multiple failed attempts. Try again in 15 minutes.'
            ];
        }

        // Prepare statement to prevent SQL injection
        $stmt = $conn->prepare("SELECT id, username, password_hash, is_active FROM admin_users WHERE username = ? LIMIT 1");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            self::recordFailedAttempt($username);
            return [
                'success' => false,
                'message' => 'Invalid credentials'
            ];
        }

        $user = $result->fetch_assoc();

        // Check if account is active
        if ($user['is_active'] != 1) {
            return [
                'success' => false,
                'message' => 'Account is disabled'
            ];
        }

        // Verify password
        if (password_verify($password, $user['password_hash'])) {
            // Clear failed attempts
            self::clearFailedAttempts($username);

            // Create secure session
            self::createSession($user);

            // Log successful login
            self::logActivity($user['id'], 'login', 'Successful login');

            return [
                'success' => true,
                'message' => 'Login successful'
            ];
        } else {
            self::recordFailedAttempt($username);
            return [
                'success' => false,
                'message' => 'Invalid credentials'
            ];
        }
    }

    /**
     * Create secure admin session
     */
    private static function createSession($user)
    {
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_id'] = $user['id'];
        $_SESSION['admin_username'] = $user['username'];
        $_SESSION['login_time'] = time();
        $_SESSION['last_activity'] = time();
        $_SESSION['ip_address'] = $_SERVER['REMOTE_ADDR'];
        $_SESSION['user_agent'] = $_SERVER['HTTP_USER_AGENT'];

        // Generate CSRF token
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    /**
     * Check if user is authenticated
     */
    public static function isAuthenticated()
    {
        self::initSession();

        if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
            return false;
        }

        // Check session timeout
        if (time() - $_SESSION['last_activity'] > self::$SESSION_TIMEOUT) {
            self::logout();
            return false;
        }

        // Verify IP address hasn't changed
        if ($_SESSION['ip_address'] !== $_SERVER['REMOTE_ADDR']) {
            self::logout();
            return false;
        }

        // Verify user agent hasn't changed
        if ($_SESSION['user_agent'] !== $_SERVER['HTTP_USER_AGENT']) {
            self::logout();
            return false;
        }

        // Update last activity
        $_SESSION['last_activity'] = time();

        return true;
    }

    /**
     * Require authentication - redirect if not logged in
     */
    public static function requireAuth()
    {
        if (!self::isAuthenticated()) {
            header('Location: login.php');
            exit;
        }
    }

    /**
     * Logout and destroy session
     */
    public static function logout()
    {
        self::initSession();

        // Log logout activity
        if (isset($_SESSION['admin_id'])) {
            self::logActivity($_SESSION['admin_id'], 'logout', 'User logged out');
        }

        // Destroy session
        $_SESSION = array();

        if (isset($_COOKIE[session_name()])) {
            setcookie(session_name(), '', time() - 3600, '/');
        }

        session_destroy();
    }

    /**
     * Validate CSRF token
     */
    public static function validateCsrfToken($token)
    {
        self::initSession();
        return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
    }

    /**
     * Get CSRF token
     */
    public static function getCsrfToken()
    {
        self::initSession();
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    /**
     * Record failed login attempt
     */
    private static function recordFailedAttempt($username)
    {
        $conn = include(__DIR__ . '/../config/database.php');

        $ip = $_SERVER['REMOTE_ADDR'];
        $stmt = $conn->prepare("INSERT INTO login_attempts (username, ip_address, attempt_time) VALUES (?, ?, NOW())");
        $stmt->bind_param("ss", $username, $ip);
        $stmt->execute();
    }

    /**
     * Clear failed login attempts
     */
    private static function clearFailedAttempts($username)
    {
        $conn = include(__DIR__ . '/../config/database.php');

        $stmt = $conn->prepare("DELETE FROM login_attempts WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
    }

    /**
     * Check if account is locked
     */
    private static function isAccountLocked($username)
    {
        $conn = include(__DIR__ . '/../config/database.php');

        $ip = $_SERVER['REMOTE_ADDR'];
        $lockoutTime = date('Y-m-d H:i:s', time() - self::$LOCKOUT_TIME);

        $stmt = $conn->prepare("SELECT COUNT(*) as attempts FROM login_attempts WHERE username = ? AND ip_address = ? AND attempt_time > ?");
        $stmt->bind_param("sss", $username, $ip, $lockoutTime);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();

        return $row['attempts'] >= self::$MAX_LOGIN_ATTEMPTS;
    }

    /**
     * Log admin activity
     */
    private static function logActivity($adminId, $action, $description)
    {
        $conn = include(__DIR__ . '/../config/database.php');

        $ip = $_SERVER['REMOTE_ADDR'];
        $userAgent = $_SERVER['HTTP_USER_AGENT'];

        $stmt = $conn->prepare("INSERT INTO admin_activity_log (admin_id, action, description, ip_address, user_agent, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
        $stmt->bind_param("issss", $adminId, $action, $description, $ip, $userAgent);
        $stmt->execute();
    }

    /**
     * Create default admin user (run once)
     */
    public static function createDefaultAdmin($username, $password)
    {
        $conn = include(__DIR__ . '/../config/database.php');

        $passwordHash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);

        $stmt = $conn->prepare("INSERT INTO admin_users (username, password_hash, is_active, created_at) VALUES (?, ?, 1, NOW())");
        $stmt->bind_param("ss", $username, $passwordHash);

        return $stmt->execute();
    }
}
?>