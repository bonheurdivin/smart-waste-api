<?php
define('DB_HOST', getenv('DB_HOST') ?: 'hayabusa.proxy.rlwy.net');
define('DB_USER', getenv('DB_USER') ?: 'root');
define('DB_PASS', getenv('DB_PASS') ?: 'cHqYYsBwNhMfwdwPdmUZUSXdtkLIoeTv');
define('DB_NAME', getenv('DB_NAME') ?: 'railway');

function getConnection() {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    
    if ($conn->connect_error) {
        die(json_encode([
            'status' => 'error',
            'message' => 'Database connection failed: ' . $conn->connect_error
        ]));
    }
    
    return $conn;
}