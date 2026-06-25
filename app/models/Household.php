<?php

class Household {
    private $conn;
    private $table = 'households';

    public function __construct($conn) {
        $this->conn = $conn;
    }

    // Get all households
    public function getAll() {
        $stmt = $this->conn->prepare(
            "SELECT h.*, u.name as owner_name, u.phone as owner_phone, 
            u.email as owner_email, p.name as plan_name, p.price as plan_price
            FROM {$this->table} h
            LEFT JOIN users u ON h.owner_user_id = u.id
            LEFT JOIN plans p ON h.plan_id = p.id
            ORDER BY h.created_at DESC"
        );
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // Get single household by ID
    public function getById($id) {
        $stmt = $this->conn->prepare(
            "SELECT h.*, u.name as owner_name, u.phone as owner_phone,
            u.email as owner_email, p.name as plan_name, p.price as plan_price
            FROM {$this->table} h
            LEFT JOIN users u ON h.owner_user_id = u.id
            LEFT JOIN plans p ON h.plan_id = p.id
            WHERE h.id = ?"
        );
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    // Get household by owner user ID
    public function getByUserId($userId) {
        $stmt = $this->conn->prepare(
            "SELECT h.*, u.name as owner_name, u.phone as owner_phone,
            u.email as owner_email, p.name as plan_name, p.price as plan_price
            FROM {$this->table} h
            LEFT JOIN users u ON h.owner_user_id = u.id
            LEFT JOIN plans p ON h.plan_id = p.id
            WHERE h.owner_user_id = ?"
        );
        $stmt->bind_param('i', $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    // Create household
    public function create($data) {
        $stmt = $this->conn->prepare(
            "INSERT INTO {$this->table}
            (owner_user_id, address, zone, gps_lat, gps_lng, plan_id, occupants)
            VALUES (?, ?, ?, ?, ?, ?, ?)"
        );
        $stmt->bind_param(
            'issddii',
            $data['owner_user_id'],
            $data['address'],
            $data['zone'],
            $data['gps_lat'],
            $data['gps_lng'],
            $data['plan_id'],
            $data['occupants']
        );
        $stmt->execute();
        return $this->conn->insert_id;
    }

    // Update household
    public function update($id, $data) {
        $stmt = $this->conn->prepare(
            "UPDATE {$this->table}
            SET address = ?, zone = ?, gps_lat = ?, gps_lng = ?, 
            plan_id = ?, occupants = ?, status = ?
            WHERE id = ?"
        );
        $stmt->bind_param(
            'ssddiisi',
            $data['address'],
            $data['zone'],
            $data['gps_lat'],
            $data['gps_lng'],
            $data['plan_id'],
            $data['occupants'],
            $data['status'],
            $id
        );
        return $stmt->execute();
    }

    // Delete household
    public function delete($id) {
        $stmt = $this->conn->prepare(
            "DELETE FROM {$this->table} WHERE id = ?"
        );
        $stmt->bind_param('i', $id);
        return $stmt->execute();
    }
}