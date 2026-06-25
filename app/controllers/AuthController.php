<?php

require_once 'app/models/User.php';
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

define('JWT_SECRET', 'smart_waste_secret_key_2026_secure_jwt_token_for_authentication_purposes');
define('JWT_EXPIRY', 3600); // 1 hour

class AuthController {
    private $conn;
    private $userModel;

    public function __construct($conn) {
        $this->conn = $conn;
        $this->userModel = new User($conn);
    }

    // REGISTER
    public function register() {
        $data = json_decode(file_get_contents('php://input'), true);

        // Validate required fields
        if (empty($data['name']) || empty($data['phone']) || 
            empty($data['password']) || empty($data['role'])) {
            http_response_code(400);
            echo json_encode([
                'status' => 'error',
                'message' => 'Name, phone, password and role are required'
            ]);
            return;
        }

        // Check if phone already exists
        if ($this->userModel->findByPhone($data['phone'])) {
            http_response_code(409);
            echo json_encode([
                'status' => 'error',
                'message' => 'Phone number already registered'
            ]);
            return;
        }

        // Hash the password
        $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT);

        // Create the user
        $userId = $this->userModel->create($data);

        http_response_code(201);
        echo json_encode([
            'status' => 'success',
            'message' => 'Account created successfully',
            'data' => ['user_id' => $userId]
        ]);
    }

    // LOGIN
    public function login() {
        $data = json_decode(file_get_contents('php://input'), true);

        // Validate required fields
        if (empty($data['phone']) || empty($data['password'])) {
            http_response_code(400);
            echo json_encode([
                'status' => 'error',
                'message' => 'Phone and password are required'
            ]);
            return;
        }

        // Find user by phone
        $user = $this->userModel->findByPhone($data['phone']);

        if (!$user) {
            http_response_code(401);
            echo json_encode([
                'status' => 'error',
                'message' => 'Invalid phone or password'
            ]);
            return;
        }

        // Verify password
        if (!password_verify($data['password'], $user['password'])) {
            http_response_code(401);
            echo json_encode([
                'status' => 'error',
                'message' => 'Invalid phone or password'
            ]);
            return;
        }

        // Generate JWT token
        $payload = [
            'iss' => 'smart-waste-api',
            'iat' => time(),
            'exp' => time() + JWT_EXPIRY,
            'user_id' => $user['id'],
            'role' => $user['role']
        ];

        $token = JWT::encode($payload, JWT_SECRET, 'HS256');

        http_response_code(200);
        echo json_encode([
            'status' => 'success',
            'message' => 'Login successful',
            'data' => [
                'token' => $token,
                'user' => [
                    'id' => $user['id'],
                    'name' => $user['name'],
                    'email' => $user['email'],
                    'phone' => $user['phone'],
                    'role' => $user['role']
                ]
            ]
        ]);
    }

    // GET all residents
    public function getResidents() {
        $decoded = AuthMiddleware::authenticate();
        RoleMiddleware::authorize($decoded, ['admin', 'dispatcher']);

        $stmt = $this->conn->prepare(
            "SELECT id, name, phone, email 
            FROM users
            WHERE role = 'resident'
            ORDER BY name ASC"
    );
        $stmt->execute();
        $result = $stmt->get_result();
        $residents = $result->fetch_all(MYSQLI_ASSOC);

        echo json_encode([
            'status' => 'success',
            'data' => $residents
     ]);
    }

    // GET all worker users
    public function getWorkerUsers() {
        $decoded = AuthMiddleware::authenticate();
        RoleMiddleware::authorize($decoded, ['admin']);

        $stmt = $this->conn->prepare(
            "SELECT id, name, phone, email 
            FROM users 
            WHERE role = 'worker'
            ORDER BY name ASC"
    );
        $stmt->execute();
        $result = $stmt->get_result();
        $workers = $result->fetch_all(MYSQLI_ASSOC);

        echo json_encode([
            'status' => 'success',
            'data' => $workers
    ]);
  }

  // Change password
  public function changePassword() {
      $decoded = AuthMiddleware::authenticate();

      $data = json_decode(file_get_contents('php://input'), true);

      // Validate required fields
      if (empty($data['current_password']) || empty($data['new_password'])) {
          http_response_code(400);
          echo json_encode([
              'status' => 'error',
              'message' => 'Current password and new password are required'
          ]);
          return;
      }

      // Get user
      $user = $this->userModel->findById($decoded['user_id']);

      // Verify current password
      if (!password_verify($data['current_password'], $user['password'])) {
          http_response_code(401);
          echo json_encode([
              'status' => 'error',
              'message' => 'Current password is incorrect'
          ]);
          return;
      }

      // Update password
      $newPassword = password_hash($data['new_password'], PASSWORD_BCRYPT);
      $stmt = $this->conn->prepare(
          "UPDATE users SET password = ? WHERE id = ?"
      );
      $stmt->bind_param("si", $newPassword, $decoded['id']);
      $stmt->execute();

      echo json_encode([
          'status' => 'success',
          'message' => 'Password changed successfully'
      ]);
  }
}