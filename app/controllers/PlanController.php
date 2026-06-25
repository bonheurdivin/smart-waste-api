<?php

require_once 'app/models/Plan.php';
require_once 'app/middleware/AuthMiddleware.php';
require_once 'app/middleware/RoleMiddleware.php';

class PlanController {
    private $conn;
    private $planModel;

    public function __construct($conn) {
        $this->conn = $conn;
        $this->planModel = new Plan($conn);
    }

    // GET all plans (public)
    public function index() {
        $plans = $this->planModel->getAll();
        echo json_encode([
            'status' => 'success',
            'data' => $plans
        ]);
    }

    // GET single plan
    public function show($id) {
        $plan = $this->planModel->getById($id);
        if (!$plan) {
            http_response_code(404);
            echo json_encode([
                'status' => 'error',
                'message' => 'Plan not found'
            ]);
            return;
        }
        echo json_encode([
            'status' => 'success',
            'data' => $plan
        ]);
    }

    // POST create plan (admin only)
    public function store() {
        $decoded = AuthMiddleware::authenticate();
        RoleMiddleware::authorize($decoded, ['admin']);

        $data = json_decode(file_get_contents('php://input'), true);

        if (empty($data['name']) || empty($data['frequency']) || empty($data['price'])) {
            http_response_code(400);
            echo json_encode([
                'status' => 'error',
                'message' => 'Name, frequency and price are required'
            ]);
            return;
        }

        $planId = $this->planModel->create($data);

        http_response_code(201);
        echo json_encode([
            'status' => 'success',
            'message' => 'Plan created successfully',
            'data' => ['plan_id' => $planId]
        ]);
    }

    // PUT update plan (admin only)
    public function update($id) {
        $decoded = AuthMiddleware::authenticate();
        RoleMiddleware::authorize($decoded, ['admin']);

        $data = json_decode(file_get_contents('php://input'), true);

        $plan = $this->planModel->getById($id);
        if (!$plan) {
            http_response_code(404);
            echo json_encode([
                'status' => 'error',
                'message' => 'Plan not found'
            ]);
            return;
        }

        $data['name'] = $data['name'] ?? $plan['name'];
        $data['frequency'] = $data['frequency'] ?? $plan['frequency'];
        $data['price'] = $data['price'] ?? $plan['price'];

        $this->planModel->update($id, $data);

        echo json_encode([
            'status' => 'success',
            'message' => 'Plan updated successfully'
        ]);
    }

    // DELETE plan (admin only)
    public function destroy($id) {
        $decoded = AuthMiddleware::authenticate();
        RoleMiddleware::authorize($decoded, ['admin']);

        $plan = $this->planModel->getById($id);
        if (!$plan) {
            http_response_code(404);
            echo json_encode([
                'status' => 'error',
                'message' => 'Plan not found'
            ]);
            return;
        }

        $this->planModel->delete($id);

        echo json_encode([
            'status' => 'success',
            'message' => 'Plan deleted successfully'
        ]);
    }
}