<?php

class Worker {
    private $conn;
    private $table = 'workers';

    public function __construct($conn) {
        $this->conn = $conn;
    }

    // Get all workers
    public function getAll() {
        $stmt = $this->conn->prepare(
            "SELECT w.*, u.name, u.phone, u.email
            FROM {$this->table} w
            LEFT JOIN users u ON w.user_id = u.id
            ORDER BY w.created_at DESC"
        );
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // Get single worker by ID
    public function getById($id) {
        $stmt = $this->conn->prepare(
            "SELECT w.*, u.name, u.phone, u.email
            FROM {$this->table} w
            LEFT JOIN users u ON w.user_id = u.id
            WHERE w.id = ?"
        );
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    // Create worker
    public function create($data) {
        $stmt = $this->conn->prepare(
            "INSERT INTO {$this->table} (user_id, zone, status)
            VALUES (?, ?, ?)"
        );
        $stmt->bind_param(
            'iss',
            $data['user_id'],
            $data['zone'],
            $data['status']
        );
        $stmt->execute();
        return $this->conn->insert_id;
    }

    // Update worker
    public function update($id, $data) {
        $stmt = $this->conn->prepare(
            "UPDATE {$this->table}
            SET zone = ?, status = ?
            WHERE id = ?"
        );
        $stmt->bind_param(
            'ssi',
            $data['zone'],
            $data['status'],
            $id
        );
        return $stmt->execute();
    }

    // Delete worker
    public function delete($id) {
        $stmt = $this->conn->prepare(
            "DELETE FROM {$this->table} WHERE id = ?"
        );
        $stmt->bind_param('i', $id);
        return $stmt->execute();
    }
}