<?php

require_once 'app/models/Worker.php';
require_once 'app/middleware/AuthMiddleware.php';
require_once 'app/middleware/RoleMiddleware.php';

class WorkerController {
    private $conn;
    private $workerModel;

    public function __construct($conn) {
        $this->conn = $conn;
        $this->workerModel = new Worker($conn);
    }

    // GET all workers
    public function index() {
        $decoded = AuthMiddleware::authenticate();
        RoleMiddleware::authorize($decoded, ['admin', 'dispatcher']);

        $workers = $this->workerModel->getAll();
        echo json_encode([
            'status' => 'success',
            'data' => $workers
        ]);
    }

    // GET single worker
    public function show($id) {
        $decoded = AuthMiddleware::authenticate();
        RoleMiddleware::authorize($decoded, ['admin', 'dispatcher']);

        $worker = $this->workerModel->getById($id);
        if (!$worker) {
            http_response_code(404);
            echo json_encode([
                'status' => 'error',
                'message' => 'Worker not found'
            ]);
            return;
        }
        echo json_encode([
            'status' => 'success',
            'data' => $worker
        ]);
    }

    // POST create worker (admin only)
    public function store() {
        $decoded = AuthMiddleware::authenticate();
        RoleMiddleware::authorize($decoded, ['admin']);

        $data = json_decode(file_get_contents('php://input'), true);

        if (empty($data['user_id']) || empty($data['zone'])) {
            http_response_code(400);
            echo json_encode([
                'status' => 'error',
                'message' => 'User ID and zone are required'
            ]);
            return;
        }

        $data['status'] = $data['status'] ?? 'active';

        $workerId = $this->workerModel->create($data);

        http_response_code(201);
        echo json_encode([
            'status' => 'success',
            'message' => 'Worker created successfully',
            'data' => ['worker_id' => $workerId]
        ]);
    }

    // POST create worker with user account
    public function storeWithUser() {
    $decoded = AuthMiddleware::authenticate();
    RoleMiddleware::authorize($decoded, ['admin']);

    $data = json_decode(file_get_contents('php://input'), true);

    if (empty($data['name']) || empty($data['phone']) ||
        empty($data['password']) || empty($data['zone'])) {
        http_response_code(400);
        echo json_encode([
            'status' => 'error',
            'message' => 'Name, phone, password and zone are required'
        ]);
        return;
    }

    // Check if phone already exists
    $userModel = new User($this->conn);
    if ($userModel->findByPhone($data['phone'])) {
        http_response_code(409);
        echo json_encode([
            'status' => 'error',
            'message' => 'Phone number already registered'
        ]);
        return;
    }

    // Create user account
    $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT);
    $userId = $userModel->create([
        'name' => $data['name'],
        'phone' => $data['phone'],
        'email' => $data['email'] ?? null,
        'password' => $data['password'],
        'role' => 'worker',
    ]);

    // Create worker profile
    $workerId = $this->workerModel->create([
        'user_id' => $userId,
        'zone' => $data['zone'],
        'status' => $data['status'] ?? 'active',
    ]);

    http_response_code(201);
    echo json_encode([
        'status' => 'success',
        'message' => 'Worker created successfully',
        'data' => [
            'user_id' => $userId,
            'worker_id' => $workerId
        ]
    ]);
}

    // PUT update worker (admin only)
    public function update($id) {
    $decoded = AuthMiddleware::authenticate();
    RoleMiddleware::authorize($decoded, ['admin']);

        $data = json_decode(file_get_contents('php://input'), true);

        $worker = $this->workerModel->getById($id);
        if (!$worker) {
            http_response_code(404);
            echo json_encode([
                'status' => 'error',
                'message' => 'Worker not found'
            ]);
            return;
        }

        $data['zone'] = $data['zone'] ?? $worker['zone'];
        $data['status'] = $data['status'] ?? $worker['status'];

        $this->workerModel->update($id, $data);

        echo json_encode([
            'status' => 'success',
            'message' => 'Worker updated successfully'
        ]);
    }

    // DELETE worker (admin only)
    public function destroy($id) {
        $decoded = AuthMiddleware::authenticate();
        RoleMiddleware::authorize($decoded, ['admin']);

        $worker = $this->workerModel->getById($id);
        if (!$worker) {
            http_response_code(404);
            echo json_encode([
                'status' => 'error',
                'message' => 'Worker not found'
            ]);
            return;
        }

        $this->workerModel->delete($id);

        echo json_encode([
            'status' => 'success',
            'message' => 'Worker deleted successfully'
        ]);
    }
}