<?php

class Pickup {
    private $conn;
    private $table = 'pickups';

    public function __construct($conn) {
        $this->conn = $conn;
    }

    // Get all pickups
    public function getAll() {
        $stmt = $this->conn->prepare(
            "SELECT p.*, h.address, h.zone,
            u.name as owner_name,
            w.id as worker_id,
            wu.name as worker_name,
            v.plate as vehicle_plate
            FROM {$this->table} p
            LEFT JOIN households h ON p.household_id = h.id
            LEFT JOIN users u ON h.owner_user_id = u.id
            LEFT JOIN workers w ON p.worker_id = w.id
            LEFT JOIN users wu ON w.user_id = wu.id
            LEFT JOIN vehicles v ON p.vehicle_id = v.id
            ORDER BY p.scheduled_at DESC"
        );
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // Get single pickup by ID
    public function getById($id) {
        $stmt = $this->conn->prepare(
            "SELECT p.*, h.address, h.zone,
            u.name as owner_name,
            wu.name as worker_name,
            v.plate as vehicle_plate
            FROM {$this->table} p
            LEFT JOIN households h ON p.household_id = h.id
            LEFT JOIN users u ON h.owner_user_id = u.id
            LEFT JOIN workers w ON p.worker_id = w.id
            LEFT JOIN users wu ON w.user_id = wu.id
            LEFT JOIN vehicles v ON p.vehicle_id = v.id
            WHERE p.id = ?"
        );
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    // Get pickups by household ID
    public function getByHouseholdId($householdId) {
        $stmt = $this->conn->prepare(
            "SELECT p.*, wu.name as worker_name,
            v.plate as vehicle_plate
            FROM {$this->table} p
            LEFT JOIN workers w ON p.worker_id = w.id
            LEFT JOIN users wu ON w.user_id = wu.id
            LEFT JOIN vehicles v ON p.vehicle_id = v.id
            WHERE p.household_id = ?
            ORDER BY p.scheduled_at DESC"
        );
        $stmt->bind_param('i', $householdId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // Create pickup
    public function create($data) {
        $stmt = $this->conn->prepare(
            "INSERT INTO {$this->table}
            (household_id, scheduled_at, worker_id, vehicle_id, status, notes)
            VALUES (?, ?, ?, ?, ?, ?)"
        );
        $stmt->bind_param(
            'issiis',
            $data['household_id'],
            $data['scheduled_at'],
            $data['worker_id'],
            $data['vehicle_id'],
            $data['status'],
            $data['notes']
        );
        $stmt->execute();
        return $this->conn->insert_id;
    }

    // Update pickup
    public function update($id, $data) {
        $stmt = $this->conn->prepare(
            "UPDATE {$this->table}
            SET worker_id = ?, vehicle_id = ?, status = ?,
            scheduled_at = ?, completed_at = ?, notes = ?
            WHERE id = ?"
        );
        $stmt->bind_param(
            'iissssi',
            $data['worker_id'],
            $data['vehicle_id'],
            $data['status'],
            $data['scheduled_at'],
            $data['completed_at'],
            $data['notes'],
            $id
        );
        return $stmt->execute();
    }

    // Rate pickup
    public function rate($id, $rating) {
        $stmt = $this->conn->prepare(
            "UPDATE {$this->table} SET rating = ? WHERE id = ?"
        );
        $stmt->bind_param('ii', $rating, $id);
        return $stmt->execute();
    }

    // Delete pickup
    public function delete($id) {
        $stmt = $this->conn->prepare(
            "DELETE FROM {$this->table} WHERE id = ?"
        );
        $stmt->bind_param('i', $id);
        return $stmt->execute();
    }
}