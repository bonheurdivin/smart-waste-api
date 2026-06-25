<?php

require_once 'app/models/Pickup.php';
require_once 'app/middleware/AuthMiddleware.php';
require_once 'app/middleware/RoleMiddleware.php';

class PickupController {
    private $conn;
    private $pickupModel;

    public function __construct($conn) {
        $this->conn = $conn;
        $this->pickupModel = new Pickup($conn);
    }

    // GET all pickups
    public function index() {
        $decoded = AuthMiddleware::authenticate();
        RoleMiddleware::authorize($decoded, ['admin', 'dispatcher']);

        $pickups = $this->pickupModel->getAll();
        echo json_encode([
            'status' => 'success',
            'data' => $pickups
        ]);
    }

    // GET single pickup
    public function show($id) {
        $decoded = AuthMiddleware::authenticate();
        RoleMiddleware::authorize($decoded, ['admin', 'dispatcher', 'resident']);

        $pickup = $this->pickupModel->getById($id);
        if (!$pickup) {
            http_response_code(404);
            echo json_encode([
                'status' => 'error',
                'message' => 'Pickup not found'
            ]);
            return;
        }
        echo json_encode([
            'status' => 'success',
            'data' => $pickup
        ]);
    }

    // GET pickups by household
    public function getByHousehold($householdId) {
        $decoded = AuthMiddleware::authenticate();
        RoleMiddleware::authorize($decoded, ['admin', 'dispatcher', 'resident']);

        $pickups = $this->pickupModel->getByHouseholdId($householdId);
        echo json_encode([
            'status' => 'success',
            'data' => $pickups
        ]);
    }

    // POST create pickup
    public function store() {
        $decoded = AuthMiddleware::authenticate();
        RoleMiddleware::authorize($decoded, ['admin', 'dispatcher', 'resident']);

        $data = json_decode(file_get_contents('php://input'), true);

        if (empty($data['household_id']) || empty($data['scheduled_at'])) {
            http_response_code(400);
            echo json_encode([
                'status' => 'error',
                'message' => 'Household ID and scheduled date are required'
            ]);
            return;
        }

        $data['worker_id'] = $data['worker_id'] ?? null;
        $data['vehicle_id'] = $data['vehicle_id'] ?? null;
        $data['status'] = $data['status'] ?? 'scheduled';
        $data['notes'] = $data['notes'] ?? null;

        $pickupId = $this->pickupModel->create($data);

        http_response_code(201);
        echo json_encode([
            'status' => 'success',
            'message' => 'Pickup created successfully',
            'data' => ['pickup_id' => $pickupId]
        ]);
    }

    // PUT update pickup
    public function update($id) {
        $decoded = AuthMiddleware::authenticate();
        RoleMiddleware::authorize($decoded, ['admin', 'dispatcher']);

        $data = json_decode(file_get_contents('php://input'), true);

        $pickup = $this->pickupModel->getById($id);
        if (!$pickup) {
            http_response_code(404);
            echo json_encode([
                'status' => 'error',
                'message' => 'Pickup not found'
            ]);
            return;
        }

        $data['worker_id'] = $data['worker_id'] ?? $pickup['worker_id'];
        $data['vehicle_id'] = $data['vehicle_id'] ?? $pickup['vehicle_id'];
        $data['status'] = $data['status'] ?? $pickup['status'];
        $data['scheduled_at'] = $data['scheduled_at'] ?? $pickup['scheduled_at'];
        $data['completed_at'] = $data['completed_at'] ?? $pickup['completed_at'];
        $data['notes'] = $data['notes'] ?? $pickup['notes'];

        $this->pickupModel->update($id, $data);

        echo json_encode([
            'status' => 'success',
            'message' => 'Pickup updated successfully'
        ]);
    }

    // POST rate pickup
    public function rate($id) {
        $decoded = AuthMiddleware::authenticate();
        RoleMiddleware::authorize($decoded, ['resident']);

        $data = json_decode(file_get_contents('php://input'), true);

        if (empty($data['rating']) || $data['rating'] < 1 || $data['rating'] > 5) {
            http_response_code(400);
            echo json_encode([
                'status' => 'error',
                'message' => 'Rating must be between 1 and 5'
            ]);
            return;
        }

        $this->pickupModel->rate($id, $data['rating']);

        echo json_encode([
            'status' => 'success',
            'message' => 'Pickup rated successfully'
        ]);
    }

    // DELETE pickup
    public function destroy($id) {
        $decoded = AuthMiddleware::authenticate();
        RoleMiddleware::authorize($decoded, ['admin']);

        $pickup = $this->pickupModel->getById($id);
        if (!$pickup) {
            http_response_code(404);
            echo json_encode([
                'status' => 'error',
                'message' => 'Pickup not found'
            ]);
            return;
        }

        $this->pickupModel->delete($id);

        echo json_encode([
            'status' => 'success',
            'message' => 'Pickup deleted successfully'
        ]);
    }
}