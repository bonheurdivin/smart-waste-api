<?php

// Allow cross-origin requests (needed for React and Flutter to call our API)
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Content-Type: application/json');

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Load Composer autoloader
require_once 'vendor/autoload.php';

// Load database config
require_once 'config/database.php';

// Load middleware
require_once 'app/middleware/AuthMiddleware.php';
require_once 'app/middleware/RoleMiddleware.php';

// Get the request path and method
$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$requestUri = str_replace('/smart-waste-api', '', $requestUri);
$requestUri = trim($requestUri);
$requestMethod = $_SERVER['REQUEST_METHOD'];

// Load routes

require_once 'routes/api.php';