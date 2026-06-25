<?php

class RoleMiddleware {

    public static function authorize($decoded, $allowedRoles) {
        if (!in_array($decoded['role'], $allowedRoles)) {
            http_response_code(403);
            echo json_encode([
                'status' => 'error',
                'message' => 'Access denied. You do not have permission to perform this action'
            ]);
            exit();
        }
    }
}