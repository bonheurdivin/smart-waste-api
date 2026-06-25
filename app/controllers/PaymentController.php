<?php

require_once 'app/models/Payment.php';
require_once 'app/middleware/AuthMiddleware.php';
require_once 'app/middleware/RoleMiddleware.php';

class PaymentController {
    private $conn;
    private $paymentModel;

    public function __construct($conn) {
        $this->conn = $conn;
        $this->paymentModel = new Payment($conn);
    }

    // GET all payments
    public function index() {
        $decoded = AuthMiddleware::authenticate();
        RoleMiddleware::authorize($decoded, ['admin', 'finance']);

        $payments = $this->paymentModel->getAll();
        echo json_encode([
            'status' => 'success',
            'data' => $payments
        ]);
    }

    // GET single payment
    public function show($id) {
        $decoded = AuthMiddleware::authenticate();
        RoleMiddleware::authorize($decoded, ['admin', 'finance', 'resident']);

        $payment = $this->paymentModel->getById($id);
        if (!$payment) {
            http_response_code(404);
            echo json_encode([
                'status' => 'error',
                'message' => 'Payment not found'
            ]);
            return;
        }
        echo json_encode([
            'status' => 'success',
            'data' => $payment
        ]);
    }

    // GET payments by household
    public function getByHousehold($householdId) {
        $decoded = AuthMiddleware::authenticate();
        RoleMiddleware::authorize($decoded, ['admin', 'finance', 'resident']);

        $payments = $this->paymentModel->getByHouseholdId($householdId);
        echo json_encode([
            'status' => 'success',
            'data' => $payments
        ]);
    }

    // POST create payment
    public function store() {
        $decoded = AuthMiddleware::authenticate();
        RoleMiddleware::authorize($decoded, ['admin', 'finance', 'resident']);

        $data = json_decode(file_get_contents('php://input'), true);

        if (empty($data['household_id']) || empty($data['amount']) ||
            empty($data['method'])) {
            http_response_code(400);
            echo json_encode([
                'status' => 'error',
                'message' => 'Household ID, amount and method are required'
            ]);
            return;
        }

        if ($decoded['role'] === 'resident') {
            $data['status'] = 'pending';
            $data['paid_at'] = null;
        } else {
        $data['status'] = $data['status'] ?? 'unpaid';
        $data['paid_at'] = $data['paid_at'] ?? null;
        }

        $data['reference'] = $data['reference'] ?? null;

        $paymentId = $this->paymentModel->create($data);

        http_response_code(201);
        echo json_encode([
            'status' => 'success',
            'message' => 'Payment submitted successfully. Awaiting admin approval.',
            'data' => ['payment_id' => $paymentId]
        ]);
    }

    // PUT update payment
    public function update($id) {
        $decoded = AuthMiddleware::authenticate();
        RoleMiddleware::authorize($decoded, ['admin', 'finance', 'resident']);

        $data = json_decode(file_get_contents('php://input'), true);

        $payment = $this->paymentModel->getById($id);
        if (!$payment) {
            http_response_code(404);
            echo json_encode([
                'status' => 'error',
                'message' => 'Payment not found'
            ]);
            return;
        }

        $data['amount'] = $data['amount'] ?? $payment['amount'];
        $data['method'] = $data['method'] ?? $payment['method'];
        $data['reference'] = $data['reference'] ?? $payment['reference'];
        $data['status'] = $data['status'] ?? $payment['status'];
        $data['paid_at'] = $data['paid_at'] ?? $payment['paid_at'];

        $this->paymentModel->update($id, $data);

        echo json_encode([
            'status' => 'success',
            'message' => 'Payment updated successfully'
        ]);
    }

    // DELETE payment
    public function destroy($id) {
        $decoded = AuthMiddleware::authenticate();
        RoleMiddleware::authorize($decoded, ['admin']);

        $payment = $this->paymentModel->getById($id);
        if (!$payment) {
            http_response_code(404);
            echo json_encode([
                'status' => 'error',
                'message' => 'Payment not found'
            ]);
            return;
        }

        $this->paymentModel->delete($id);

        echo json_encode([
            'status' => 'success',
            'message' => 'Payment deleted successfully'
        ]);
    }
}