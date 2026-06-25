<?php

require_once 'app/models/Schedule.php';
require_once 'app/middleware/AuthMiddleware.php';
require_once 'app/middleware/RoleMiddleware.php';

class ScheduleController {
    private $conn;
    private $scheduleModel;

    public function __construct($conn) {
        $this->conn = $conn;
        $this->scheduleModel = new Schedule($conn);
    }

    // GET all schedules
    public function index() {
        $decoded = AuthMiddleware::authenticate();
        RoleMiddleware::authorize($decoded, ['admin', 'dispatcher']);

        $schedules = $this->scheduleModel->getAll();
        echo json_encode([
            'status' => 'success',
            'data' => $schedules
        ]);
    }

    // GET single schedule
    public function show($id) {
        $decoded = AuthMiddleware::authenticate();
        RoleMiddleware::authorize($decoded, ['admin', 'dispatcher', 'resident']);

        $schedule = $this->scheduleModel->getById($id);
        if (!$schedule) {
            http_response_code(404);
            echo json_encode([
                'status' => 'error',
                'message' => 'Schedule not found'
            ]);
            return;
        }
        echo json_encode([
            'status' => 'success',
            'data' => $schedule
        ]);
    }

    // GET schedules by household
    public function getByHousehold($householdId) {
        $decoded = AuthMiddleware::authenticate();
        RoleMiddleware::authorize($decoded, ['admin', 'dispatcher', 'resident']);

        $schedules = $this->scheduleModel->getByHouseholdId($householdId);
        echo json_encode([
            'status' => 'success',
            'data' => $schedules
        ]);
    }

    // POST create schedule
    public function store() {
        $decoded = AuthMiddleware::authenticate();
        RoleMiddleware::authorize($decoded, ['admin', 'dispatcher']);

        $data = json_decode(file_get_contents('php://input'), true);

        if (empty($data['household_id']) || empty($data['zone']) ||
            empty($data['recurrence']) || empty($data['next_pickup_at'])) {
            http_response_code(400);
            echo json_encode([
                'status' => 'error',
                'message' => 'Household ID, zone, recurrence and next pickup date are required'
            ]);
            return;
        }

        $scheduleId = $this->scheduleModel->create($data);

        http_response_code(201);
        echo json_encode([
            'status' => 'success',
            'message' => 'Schedule created successfully',
            'data' => ['schedule_id' => $scheduleId]
        ]);
    }

    // PUT update schedule
    public function update($id) {
        $decoded = AuthMiddleware::authenticate();
        RoleMiddleware::authorize($decoded, ['admin', 'dispatcher']);

        $data = json_decode(file_get_contents('php://input'), true);

        $schedule = $this->scheduleModel->getById($id);
        if (!$schedule) {
            http_response_code(404);
            echo json_encode([
                'status' => 'error',
                'message' => 'Schedule not found'
            ]);
            return;
        }

        $data['zone'] = $data['zone'] ?? $schedule['zone'];
        $data['recurrence'] = $data['recurrence'] ?? $schedule['recurrence'];
        $data['next_pickup_at'] = $data['next_pickup_at'] ?? $schedule['next_pickup_at'];

        $this->scheduleModel->update($id, $data);

        echo json_encode([
            'status' => 'success',
            'message' => 'Schedule updated successfully'
        ]);
    }

    // DELETE schedule
    public function destroy($id) {
        $decoded = AuthMiddleware::authenticate();
        RoleMiddleware::authorize($decoded, ['admin']);

        $schedule = $this->scheduleModel->getById($id);
        if (!$schedule) {
            http_response_code(404);
            echo json_encode([
                'status' => 'error',
                'message' => 'Schedule not found'
            ]);
            return;
        }

        $this->scheduleModel->delete($id);

        echo json_encode([
            'status' => 'success',
            'message' => 'Schedule deleted successfully'
        ]);
    }
}