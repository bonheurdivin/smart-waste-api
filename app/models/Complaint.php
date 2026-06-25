<?php

class Complaint {
    private $conn;
    private $table = 'complaints';

    public function __construct($conn) {
        $this->conn = $conn;
    }

    // Get all complaints
    public function getAll() {
        $stmt = $this->conn->prepare(
            "SELECT c.*, h.address, h.zone,
            u.name as owner_name, u.phone as owner_phone
            FROM {$this->table} c
            LEFT JOIN households h ON c.household_id = h.id
            LEFT JOIN users u ON h.owner_user_id = u.id
            ORDER BY c.created_at DESC"
        );
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // Get single complaint by ID
    public function getById($id) {
        $stmt = $this->conn->prepare(
            "SELECT c.*, h.address, h.zone,
            u.name as owner_name, u.phone as owner_phone
            FROM {$this->table} c
            LEFT JOIN households h ON c.household_id = h.id
            LEFT JOIN users u ON h.owner_user_id = u.id
            WHERE c.id = ?"
        );
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    // Get complaints by household ID
    public function getByHouseholdId($householdId) {
        $stmt = $this->conn->prepare(
            "SELECT * FROM {$this->table}
            WHERE household_id = ?
            ORDER BY created_at DESC"
        );
        $stmt->bind_param('i', $householdId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // Create complaint
    public function create($data) {
        $stmt = $this->conn->prepare(
            "INSERT INTO {$this->table}
            (household_id, type, description, photo_url, status)
            VALUES (?, ?, ?, ?, ?)"
        );
        $stmt->bind_param(
            'issss',
            $data['household_id'],
            $data['type'],
            $data['description'],
            $data['photo_url'],
            $data['status']
        );
        $stmt->execute();
        return $this->conn->insert_id;
    }

    // Update complaint
    public function update($id, $data) {
        $stmt = $this->conn->prepare(
            "UPDATE {$this->table}
            SET status = ?, resolved_at = ?
            WHERE id = ?"
        );
        $stmt->bind_param(
            'ssi',
            $data['status'],
            $data['resolved_at'],
            $id
        );
        return $stmt->execute();
    }

    // Delete complaint
    public function delete($id) {
        $stmt = $this->conn->prepare(
            "DELETE FROM {$this->table} WHERE id = ?"
        );
        $stmt->bind_param('i', $id);
        return $stmt->execute();
    }
}