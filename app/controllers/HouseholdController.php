<?php

require_once 'app/models/Household.php';
require_once 'app/middleware/AuthMiddleware.php';
require_once 'app/middleware/RoleMiddleware.php';

class HouseholdController {
    private $conn;
    private $householdModel;

    public function __construct($conn) {
        $this->conn = $conn;
        $this->householdModel = new Household($conn);
    }

    // GET all households (admin/dispatcher only)
    public function index() {
        $decoded = AuthMiddleware::authenticate();
        RoleMiddleware::authorize($decoded, ['admin', 'dispatcher', 'finance']);

        $households = $this->householdModel->getAll();

        echo json_encode([
            'status' => 'success',
            'data' => $households
        ]);
    }

    // GET single household
    public function show($id) {
        $decoded = AuthMiddleware::authenticate();
        RoleMiddleware::authorize($decoded, ['admin', 'dispatcher', 'finance', 'resident']);

        $household = $this->householdModel->getById($id);

        if (!$household) {
            http_response_code(404);
            echo json_encode([
                'status' => 'error',
                'message' => 'Household not found'
            ]);
            return;
        }

        echo json_encode([
            'status' => 'success',
            'data' => $household
        ]);
    }

    // GET my household (resident)
    public function myHousehold() {
        $decoded = AuthMiddleware::authenticate();
        RoleMiddleware::authorize($decoded, ['resident']);

        $household = $this->householdModel->getByUserId($decoded['user_id']);

        if (!$household) {
            http_response_code(404);
            echo json_encode([
                'status' => 'error',
                'message' => 'No household found for this user'
            ]);
            return;
        }

        echo json_encode([
            'status' => 'success',
            'data' => $household
        ]);
    }

    // POST create household
    public function store() {
        $decoded = AuthMiddleware::authenticate();
        RoleMiddleware::authorize($decoded, ['admin', 'resident']);

        $data = json_decode(file_get_contents('php://input'), true);

        if (empty($data['address']) || empty($data['zone'])) {
            http_response_code(400);
            echo json_encode([
                'status' => 'error',
                'message' => 'Address and zone are required'
            ]);
            return;
        }

        // Set defaults
        $data['owner_user_id'] = $decoded['user_id'];
        $data['gps_lat'] = $data['gps_lat'] ?? null;
        $data['gps_lng'] = $data['gps_lng'] ?? null;
        $data['plan_id'] = $data['plan_id'] ?? null;
        $data['occupants'] = $data['occupants'] ?? 1;

        $householdId = $this->householdModel->create($data);

        http_response_code(201);
        echo json_encode([
            'status' => 'success',
            'message' => 'Household created successfully',
            'data' => ['household_id' => $householdId]
        ]);
    }

    // PUT update household
    public function update($id) {
        $decoded = AuthMiddleware::authenticate();
        RoleMiddleware::authorize($decoded, ['admin', 'resident']);

        $data = json_decode(file_get_contents('php://input'), true);

        $household = $this->householdModel->getById($id);

        if (!$household) {
            http_response_code(404);
            echo json_encode([
                'status' => 'error',
                'message' => 'Household not found'
            ]);
            return;
        }

        // Set defaults from existing data
        $data['address'] = $data['address'] ?? $household['address'];
        $data['zone'] = $data['zone'] ?? $household['zone'];
        $data['gps_lat'] = $data['gps_lat'] ?? $household['gps_lat'];
        $data['gps_lng'] = $data['gps_lng'] ?? $household['gps_lng'];
        $data['plan_id'] = $data['plan_id'] ?? $household['plan_id'];
        $data['occupants'] = $data['occupants'] ?? $household['occupants'];
        $data['status'] = $data['status'] ?? $household['status'];

        $this->householdModel->update($id, $data);

        echo json_encode([
            'status' => 'success',
            'message' => 'Household updated successfully'
        ]);
    }

    // DELETE household
    public function destroy($id) {
        $decoded = AuthMiddleware::authenticate();
        RoleMiddleware::authorize($decoded, ['admin']);

        $household = $this->householdModel->getById($id);

        if (!$household) {
            http_response_code(404);
            echo json_encode([
                'status' => 'error',
                'message' => 'Household not found'
            ]);
            return;
        }

        $this->householdModel->delete($id);

        echo json_encode([
            'status' => 'success',
            'message' => 'Household deleted successfully'
        ]);
    }
}