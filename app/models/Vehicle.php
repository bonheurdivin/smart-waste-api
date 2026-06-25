<?php

class Vehicle {
    private $conn;
    private $table = 'vehicles';

    public function __construct($conn) {
        $this->conn = $conn;
    }

    // Get all vehicles
    public function getAll() {
        $stmt = $this->conn->prepare(
            "SELECT v.*, u.name as driver_name
            FROM {$this->table} v
            LEFT JOIN workers w ON v.assigned_driver_id = w.id
            LEFT JOIN users u ON w.user_id = u.id
            ORDER BY v.created_at DESC"
        );
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // Get single vehicle by ID
    public function getById($id) {
        $stmt = $this->conn->prepare(
            "SELECT v.*, u.name as driver_name
            FROM {$this->table} v
            LEFT JOIN workers w ON v.assigned_driver_id = w.id
            LEFT JOIN users u ON w.user_id = u.id
            WHERE v.id = ?"
        );
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    // Create vehicle
    public function create($data) {
        $stmt = $this->conn->prepare(
            "INSERT INTO {$this->table}
            (plate, capacity, status, assigned_driver_id)
            VALUES (?, ?, ?, ?)"
        );
        $stmt->bind_param(
            'sssi',
            $data['plate'],
            $data['capacity'],
            $data['status'],
            $data['assigned_driver_id']
        );
        $stmt->execute();
        return $this->conn->insert_id;
    }

    // Update vehicle
    public function update($id, $data) {
        $stmt = $this->conn->prepare(
            "UPDATE {$this->table}
            SET plate = ?, capacity = ?, status = ?, assigned_driver_id = ?
            WHERE id = ?"
        );
        $stmt->bind_param(
            'sssii',
            $data['plate'],
            $data['capacity'],
            $data['status'],
            $data['assigned_driver_id'],
            $id
        );
        return $stmt->execute();
    }

    // Delete vehicle
    public function delete($id) {
        $stmt = $this->conn->prepare(
            "DELETE FROM {$this->table} WHERE id = ?"
        );
        $stmt->bind_param('i', $id);
        return $stmt->execute();
    }
}