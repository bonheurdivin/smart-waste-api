<?php

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class AuthMiddleware {

    public static function authenticate() {
        // Get Authorization header (XAMPP fix)
        $authHeader = '';

        if (isset($_SERVER['HTTP_AUTHORIZATION'])) {
            $authHeader = $_SERVER['HTTP_AUTHORIZATION'];
        } elseif (isset($_SERVER['REDIRECT_HTTP_AUTHORIZATION'])) {
            $authHeader = $_SERVER['REDIRECT_HTTP_AUTHORIZATION'];
        } elseif (function_exists('getallheaders')) {
            $headers = getallheaders();
            if (isset($headers['Authorization'])) {
                $authHeader = $headers['Authorization'];
            }
        }

        if (empty($authHeader)) {
            http_response_code(401);
            echo json_encode([
                'status' => 'error',
                'message' => 'Authorization header missing'
            ]);
            exit();
        }

        // Extract the token
        $token = str_replace('Bearer ', '', $authHeader);

        try {
            // Decode the token
            $decoded = JWT::decode($token, new Key(JWT_SECRET, 'HS256'));
            return (array) $decoded;
        } catch (Exception $e) {
            http_response_code(401);
            echo json_encode([
                'status' => 'error',
                'message' => 'Invalid or expired token: ' . $e->getMessage()
            ]);
            exit();
        }
    }
}