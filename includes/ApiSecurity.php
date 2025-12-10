<?php
/**
 * API Security Middleware
 * Protects API endpoints from browser access
 * Only allows requests from the mobile app
 */

class ApiSecurity
{

    // Secret API key - change this to something unique
    private static $API_KEY = 'SIMSO_SECRET_KEY_2025_XYZ123';

    // Allowed User-Agent patterns (your app's user agent)
    private static $ALLOWED_USER_AGENTS = [
        'okhttp',           // OkHttp (used by Retrofit)
        'Dalvik',           // Android Dalvik
        'SIMSO-Android',    // Custom user agent
    ];

    /**
     * Check if request is from browser
     */
    public static function isBrowserRequest()
    {
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';

        // Common browser user agents
        $browserPatterns = [
            'Mozilla',
            'Chrome',
            'Safari',
            'Firefox',
            'Edge',
            'Opera',
            'MSIE',
            'Trident'
        ];

        foreach ($browserPatterns as $pattern) {
            if (stripos($userAgent, $pattern) !== false) {
                // Check if it's NOT from allowed app agents
                $isAppRequest = false;
                foreach (self::$ALLOWED_USER_AGENTS as $appAgent) {
                    if (stripos($userAgent, $appAgent) !== false) {
                        $isAppRequest = true;
                        break;
                    }
                }

                if (!$isAppRequest) {
                    return true; // It's a browser
                }
            }
        }

        return false;
    }

    /**
     * Validate API key from request header
     */
    public static function validateApiKey()
    {
        $apiKey = $_SERVER['HTTP_X_API_KEY'] ?? '';
        return $apiKey === self::$API_KEY;
    }

    /**
     * Check if request is from allowed app
     */
    public static function isAllowedRequest()
    {
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';

        foreach (self::$ALLOWED_USER_AGENTS as $allowedAgent) {
            if (stripos($userAgent, $allowedAgent) !== false) {
                return true;
            }
        }

        return false;
    }

    /**
     * Block browser access - show 404
     */
    public static function blockBrowserAccess()
    {
        if (self::isBrowserRequest()) {
            self::show404();
        }
    }

    /**
     * Protect API endpoint
     * Requires either valid API key OR allowed user agent
     */
    public static function protectEndpoint()
    {
        // Allow if valid API key
        if (self::validateApiKey()) {
            return true;
        }

        // Allow if from app user agent
        if (self::isAllowedRequest()) {
            return true;
        }

        // Block everything else
        self::show404();
    }

    /**
     * Show fake 404 page
     */
    public static function show404()
    {
        http_response_code(404);
        header('Content-Type: text/html; charset=utf-8');
        ?>
        <!DOCTYPE html>
        <html>

        <head>
            <title>404 Not Found</title>
            <style>
                body {
                    font-family: Arial, sans-serif;
                    background: #f5f5f5;
                    display: flex;
                    justify-content: center;
                    align-items: center;
                    height: 100vh;
                    margin: 0;
                }

                .error-container {
                    text-align: center;
                    background: white;
                    padding: 50px;
                    border-radius: 10px;
                    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
                }

                h1 {
                    font-size: 72px;
                    margin: 0;
                    color: #333;
                }

                p {
                    font-size: 18px;
                    color: #666;
                }
            </style>
        </head>

        <body>
            <div class="error-container">
                <h1>404</h1>
                <p>Not Found</p>
                <p style="font-size: 14px; color: #999;">The requested resource could not be found on this server.</p>
            </div>
        </body>

        </html>
        <?php
        exit;
    }

    /**
     * Get API key for app configuration
     */
    public static function getApiKey()
    {
        return self::$API_KEY;
    }
}
?>