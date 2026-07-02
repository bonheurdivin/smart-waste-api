<?php
$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$requestUri = trim($requestUri);
if (empty($requestUri)) $requestUri = '/';

// Serve static files directly
if (file_exists(__DIR__ . $requestUri) && !is_dir(__DIR__ . $requestUri)) {
    return false;
}

// Route everything to index.php
require_once __DIR__ . '/index.php';