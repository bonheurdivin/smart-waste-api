<?php

class Notification {
    private $conn;
    private $table = 'notifications';

    public function __construct($conn) {
        $this->conn = $conn;
    }

    // Get all notifications
    public function getAll() {
        $stmt = $this->conn->prepare(
            "SELECT n.*, u.name as recipient_name, u.phone as recipient_phone
            FROM {$this->table} n
            LEFT JOIN users u ON n.recipient_user_id = u.id
            ORDER BY n.created_at DESC"
        );
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // Get notifications by user ID
    public function getByUserId($userId) {
        $stmt = $this->conn->prepare(
            "SELECT * FROM {$this->table}
            WHERE recipient_user_id = ?
            ORDER BY created_at DESC"
        );
        $stmt->bind_param('i', $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // Create notification
    public function create($data) {
        $stmt = $this->conn->prepare(
            "INSERT INTO {$this->table}
            (recipient_user_id, channel, payload, status, sent_at)
            VALUES (?, ?, ?, ?, ?)"
        );
        $stmt->bind_param(
            'issss',
            $data['recipient_user_id'],
            $data['channel'],
            $data['payload'],
            $data['status'],
            $data['sent_at']
        );
        $stmt->execute();
        return $this->conn->insert_id;
    }
}