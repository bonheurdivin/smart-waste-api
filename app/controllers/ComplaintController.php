<?php

require_once 'app/models/Complaint.php';
require_once 'app/middleware/AuthMiddleware.php';
require_once 'app/middleware/RoleMiddleware.php';

class ComplaintController {
    private $conn;
    private $complaintModel;

    public function __construct($conn) {
        $this->conn = $conn;
        $this->complaintModel = new Complaint($conn);
    }

    // GET all complaints
    public function index() {
        $decoded = AuthMiddleware::authenticate();
        RoleMiddleware::authorize($decoded, ['admin', 'dispatcher']);

        $complaints = $this->complaintModel->getAll();
        echo json_encode([
            'status' => 'success',
            'data' => $complaints
        ]);
    }

    // GET single complaint
    public function show($id) {
        $decoded = AuthMiddleware::authenticate();
        RoleMiddleware::authorize($decoded, ['admin', 'dispatcher', 'resident']);

        $complaint = $this->complaintModel->getById($id);
        if (!$complaint) {
            http_response_code(404);
            echo json_encode([
                'status' => 'error',
                'message' => 'Complaint not found'
            ]);
            return;
        }
        echo json_encode([
            'status' => 'success',
            'data' => $complaint
        ]);
    }

    // GET complaints by household
    public function getByHousehold($householdId) {
        $decoded = AuthMiddleware::authenticate();
        RoleMiddleware::authorize($decoded, ['admin', 'dispatcher', 'resident']);

        $complaints = $this->complaintModel->getByHouseholdId($householdId);
        echo json_encode([
            'status' => 'success',
            'data' => $complaints
        ]);
    }

    // POST create complaint
    public function store() {
        $decoded = AuthMiddleware::authenticate();
        RoleMiddleware::authorize($decoded, ['resident']);

        $data = json_decode(file_get_contents('php://input'), true);

        if (empty($data['household_id']) || empty($data['type']) ||
            empty($data['description'])) {
            http_response_code(400);
            echo json_encode([
                'status' => 'error',
                'message' => 'Household ID, type and description are required'
            ]);
            return;
        }

        $data['photo_url'] = $data['photo_url'] ?? null;
        $data['status'] = 'open';

        $complaintId = $this->complaintModel->create($data);

        http_response_code(201);
        echo json_encode([
            'status' => 'success',
            'message' => 'Complaint submitted successfully',
            'data' => ['complaint_id' => $complaintId]
        ]);
    }

    // PUT resolve complaint
    public function update($id) {
        $decoded = AuthMiddleware::authenticate();
        RoleMiddleware::authorize($decoded, ['admin', 'dispatcher']);

        $data = json_decode(file_get_contents('php://input'), true);

        $complaint = $this->complaintModel->getById($id);
        if (!$complaint) {
            http_response_code(404);
            echo json_encode([
                'status' => 'error',
                'message' => 'Complaint not found'
            ]);
            return;
        }

        $data['status'] = $data['status'] ?? $complaint['status'];
        $data['resolved_at'] = $data['status'] === 'resolved' 
            ? date('Y-m-d H:i:s') 
            : null;

        $this->complaintModel->update($id, $data);

        echo json_encode([
            'status' => 'success',
            'message' => 'Complaint updated successfully'
        ]);
    }

    // DELETE complaint
    public function destroy($id) {
        $decoded = AuthMiddleware::authenticate();
        RoleMiddleware::authorize($decoded, ['admin']);

        $complaint = $this->complaintModel->getById($id);
        if (!$complaint) {
            http_response_code(404);
            echo json_encode([
                'status' => 'error',
                'message' => 'Complaint not found'
            ]);
            return;
        }

        $this->complaintModel->delete($id);

        echo json_encode([
            'status' => 'success',
            'message' => 'Complaint deleted successfully'
        ]);
    }
}