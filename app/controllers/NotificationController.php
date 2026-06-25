<?php

require_once 'app/models/Notification.php';
require_once 'app/middleware/AuthMiddleware.php';
require_once 'app/middleware/RoleMiddleware.php';

class NotificationController {
    private $conn;
    private $notificationModel;

    public function __construct($conn) {
        $this->conn = $conn;
        $this->notificationModel = new Notification($conn);
    }

    // GET all notifications (admin only)
    public function index() {
        $decoded = AuthMiddleware::authenticate();
        RoleMiddleware::authorize($decoded, ['admin']);

        $notifications = $this->notificationModel->getAll();
        echo json_encode([
            'status' => 'success',
            'data' => $notifications
        ]);
    }

    // GET my notifications (resident)
    public function myNotifications() {
        $decoded = AuthMiddleware::authenticate();
        RoleMiddleware::authorize($decoded, ['resident', 'admin', 'dispatcher']);

        $notifications = $this->notificationModel->getByUserId($decoded['user_id']);
        echo json_encode([
            'status' => 'success',
            'data' => $notifications
        ]);
    }

    // POST send notification (admin only)
    public function send() {
        $decoded = AuthMiddleware::authenticate();
        RoleMiddleware::authorize($decoded, ['admin', 'dispatcher']);

        $data = json_decode(file_get_contents('php://input'), true);

        if (empty($data['recipient_user_id']) || empty($data['channel']) ||
            empty($data['payload'])) {
            http_response_code(400);
            echo json_encode([
                'status' => 'error',
                'message' => 'Recipient user ID, channel and payload are required'
            ]);
            return;
        }

        // Log the notification
        $data['status'] = 'sent';
        $data['sent_at'] = date('Y-m-d H:i:s');

        $notificationId = $this->notificationModel->create($data);

        // In a real system we would send FCM push or SMS here
        // For now we just log it to the database

        http_response_code(201);
        echo json_encode([
            'status' => 'success',
            'message' => 'Notification sent successfully',
            'data' => ['notification_id' => $notificationId]
        ]);
    }
}