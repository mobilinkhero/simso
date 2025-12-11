<?php
/**
 * Google OAuth2 Token Generator for FCM HTTP v1 API
 * Generates access tokens from Service Account JSON without external libraries.
 */

class GoogleAccessToken
{

    /**
     * Get Access Token from Service Account JSON file
     */
    public static function getToken($serviceAccountFile)
    {
        if (!file_exists($serviceAccountFile)) {
            throw new Exception("Service account file not found.");
        }

        $data = json_decode(file_get_contents($serviceAccountFile), true);

        if (!isset($data['client_email']) || !isset($data['private_key'])) {
            throw new Exception("Invalid service account JSON.");
        }

        $header = json_encode(['alg' => 'RS256', 'typ' => 'JWT']);
        $now = time();
        $claims = json_encode([
            'iss' => $data['client_email'],
            'scope' => 'https://www.googleapis.com/auth/firebase.messaging',
            'aud' => 'https://oauth2.googleapis.com/token',
            'exp' => $now + 3600,
            'iat' => $now
        ]);

        $base64Header = self::base64UrlEncode($header);
        $base64Claims = self::base64UrlEncode($claims);
        $signatureInput = $base64Header . "." . $base64Claims;

        $signature = '';
        if (!openssl_sign($signatureInput, $signature, $data['private_key'], 'SHA256')) {
            throw new Exception("Failed to sign JWT.");
        }

        $jwt = $signatureInput . "." . self::base64UrlEncode($signature);

        // Exchange JWT for Access Token
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://oauth2.googleapis.com/token');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
            'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
            'assertion' => $jwt
        ]));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode != 200) {
            throw new Exception("Failed to get access token: " . $response);
        }

        $tokenData = json_decode($response, true);
        return [
            'access_token' => $tokenData['access_token'],
            'project_id' => $data['project_id']
        ];
    }

    private static function base64UrlEncode($data)
    {
        return str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($data));
    }
}
?>