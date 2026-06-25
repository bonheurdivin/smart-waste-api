<?php

require_once 'app/models/Vehicle.php';
require_once 'app/middleware/AuthMiddleware.php';
require_once 'app/middleware/RoleMiddleware.php';

class VehicleController {
    private $conn;
    private $vehicleModel;

    public function __construct($conn) {
        $this->conn = $conn;
        $this->vehicleModel = new Vehicle($conn);
    }

    // GET all vehicles
    public function index() {
        $decoded = AuthMiddleware::authenticate();
        RoleMiddleware::authorize($decoded, ['admin', 'dispatcher']);

        $vehicles = $this->vehicleModel->getAll();
        echo json_encode([
            'status' => 'success',
            'data' => $vehicles
        ]);
    }

    // GET single vehicle
    public function show($id) {
        $decoded = AuthMiddleware::authenticate();
        RoleMiddleware::authorize($decoded, ['admin', 'dispatcher']);

        $vehicle = $this->vehicleModel->getById($id);
        if (!$vehicle) {
            http_response_code(404);
            echo json_encode([
                'status' => 'error',
                'message' => 'Vehicle not found'
            ]);
            return;
        }
        echo json_encode([
            'status' => 'success',
            'data' => $vehicle
        ]);
    }

    // POST create vehicle
    public function store() {
        $decoded = AuthMiddleware::authenticate();
        RoleMiddleware::authorize($decoded, ['admin']);

        $data = json_decode(file_get_contents('php://input'), true);

        if (empty($data['plate']) || empty($data['capacity'])) {
            http_response_code(400);
            echo json_encode([
                'status' => 'error',
                'message' => 'Plate and capacity are required'
            ]);
            return;
        }

        $data['status'] = $data['status'] ?? 'available';
        $data['assigned_driver_id'] = $data['assigned_driver_id'] ?? null;

        $vehicleId = $this->vehicleModel->create($data);

        http_response_code(201);
        echo json_encode([
            'status' => 'success',
            'message' => 'Vehicle created successfully',
            'data' => ['vehicle_id' => $vehicleId]
        ]);
    }

    // PUT update vehicle
    public function update($id) {
        $decoded = AuthMiddleware::authenticate();
        RoleMiddleware::authorize($decoded, ['admin']);

        $data = json_decode(file_get_contents('php://input'), true);

        $vehicle = $this->vehicleModel->getById($id);
        if (!$vehicle) {
            http_response_code(404);
            echo json_encode([
                'status' => 'error',
                'message' => 'Vehicle not found'
            ]);
            return;
        }

        $data['plate'] = $data['plate'] ?? $vehicle['plate'];
        $data['capacity'] = $data['capacity'] ?? $vehicle['capacity'];
        $data['status'] = $data['status'] ?? $vehicle['status'];
        $data['assigned_driver_id'] = $data['assigned_driver_id'] ?? $vehicle['assigned_driver_id'];

        $this->vehicleModel->update($id, $data);

        echo json_encode([
            'status' => 'success',
            'message' => 'Vehicle updated successfully'
        ]);
    }

    // DELETE vehicle
    public function destroy($id) {
        $decoded = AuthMiddleware::authenticate();
        RoleMiddleware::authorize($decoded, ['admin']);

        $vehicle = $this->vehicleModel->getById($id);
        if (!$vehicle) {
            http_response_code(404);
            echo json_encode([
                'status' => 'error',
                'message' => 'Vehicle not found'
            ]);
            return;
        }

        $this->vehicleModel->delete($id);

        echo json_encode([
            'status' => 'success',
            'message' => 'Vehicle deleted successfully'
        ]);
    }
}