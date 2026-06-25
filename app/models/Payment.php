<?php

class Payment {
    private $conn;
    private $table = 'payments';

    public function __construct($conn) {
        $this->conn = $conn;
    }

    // Get all payments
    public function getAll() {
        $stmt = $this->conn->prepare(
            "SELECT p.*, h.address, h.zone,
            u.name as owner_name, u.phone as owner_phone
            FROM {$this->table} p
            LEFT JOIN households h ON p.household_id = h.id
            LEFT JOIN users u ON h.owner_user_id = u.id
            ORDER BY p.created_at DESC"
        );
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // Get single payment by ID
    public function getById($id) {
        $stmt = $this->conn->prepare(
            "SELECT p.*, h.address, h.zone,
            u.name as owner_name, u.phone as owner_phone
            FROM {$this->table} p
            LEFT JOIN households h ON p.household_id = h.id
            LEFT JOIN users u ON h.owner_user_id = u.id
            WHERE p.id = ?"
        );
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    // Get payments by household ID
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

    // Create payment
    public function create($data) {
        $stmt = $this->conn->prepare(
            "INSERT INTO {$this->table}
            (household_id, amount, method, reference, status, paid_at)
            VALUES (?, ?, ?, ?, ?, ?)"
        );
        $stmt->bind_param(
            'idssss',
            $data['household_id'],
            $data['amount'],
            $data['method'],
            $data['reference'],
            $data['status'],
            $data['paid_at']
        );
        $stmt->execute();
        return $this->conn->insert_id;
    }

    // Update payment
    public function update($id, $data) {
        $stmt = $this->conn->prepare(
            "UPDATE {$this->table}
            SET amount = ?, method = ?, reference = ?,
            status = ?, paid_at = ?
            WHERE id = ?"
        );
        $stmt->bind_param(
            'dssssi',
            $data['amount'],
            $data['method'],
            $data['reference'],
            $data['status'],
            $data['paid_at'],
            $id
        );
        return $stmt->execute();
    }

    // Delete payment
    public function delete($id) {
        $stmt = $this->conn->prepare(
            "DELETE FROM {$this->table} WHERE id = ?"
        );
        $stmt->bind_param('i', $id);
        return $stmt->execute();
    }
}