<?php

class Schedule {
    private $conn;
    private $table = 'schedules';

    public function __construct($conn) {
        $this->conn = $conn;
    }

    // Get all schedules
    public function getAll() {
        $stmt = $this->conn->prepare(
            "SELECT s.*, h.address, h.zone as household_zone,
            u.name as owner_name
            FROM {$this->table} s
            LEFT JOIN households h ON s.household_id = h.id
            LEFT JOIN users u ON h.owner_user_id = u.id
            ORDER BY s.next_pickup_at ASC"
        );
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // Get single schedule by ID
    public function getById($id) {
        $stmt = $this->conn->prepare(
            "SELECT s.*, h.address, h.zone as household_zone,
            u.name as owner_name
            FROM {$this->table} s
            LEFT JOIN households h ON s.household_id = h.id
            LEFT JOIN users u ON h.owner_user_id = u.id
            WHERE s.id = ?"
        );
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    // Get schedule by household ID
    public function getByHouseholdId($householdId) {
        $stmt = $this->conn->prepare(
            "SELECT * FROM {$this->table}
            WHERE household_id = ?
            ORDER BY next_pickup_at ASC"
        );
        $stmt->bind_param('i', $householdId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // Create schedule
    public function create($data) {
        $stmt = $this->conn->prepare(
            "INSERT INTO {$this->table}
            (household_id, zone, recurrence, next_pickup_at)
            VALUES (?, ?, ?, ?)"
        );
        $stmt->bind_param(
            'isss',
            $data['household_id'],
            $data['zone'],
            $data['recurrence'],
            $data['next_pickup_at']
        );
        $stmt->execute();
        return $this->conn->insert_id;
    }

    // Update schedule
    public function update($id, $data) {
        $stmt = $this->conn->prepare(
            "UPDATE {$this->table}
            SET zone = ?, recurrence = ?, next_pickup_at = ?
            WHERE id = ?"
        );
        $stmt->bind_param(
            'sssi',
            $data['zone'],
            $data['recurrence'],
            $data['next_pickup_at'],
            $id
        );
        return $stmt->execute();
    }

    // Delete schedule
    public function delete($id) {
        $stmt = $this->conn->prepare(
            "DELETE FROM {$this->table} WHERE id = ?"
        );
        $stmt->bind_param('i', $id);
        return $stmt->execute();
    }
}