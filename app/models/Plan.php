<?php

class Plan {
    private $conn;
    private $table = 'plans';

    public function __construct($conn) {
        $this->conn = $conn;
    }

    // Get all plans
    public function getAll() {
        $stmt = $this->conn->prepare(
            "SELECT * FROM {$this->table} ORDER BY price ASC"
        );
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // Get single plan by ID
    public function getById($id) {
        $stmt = $this->conn->prepare(
            "SELECT * FROM {$this->table} WHERE id = ?"
        );
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    // Create plan
    public function create($data) {
        $stmt = $this->conn->prepare(
            "INSERT INTO {$this->table} (name, frequency, price)
            VALUES (?, ?, ?)"
        );
        $stmt->bind_param(
            'ssd',
            $data['name'],
            $data['frequency'],
            $data['price']
        );
        $stmt->execute();
        return $this->conn->insert_id;
    }

    // Update plan
    public function update($id, $data) {
        $stmt = $this->conn->prepare(
            "UPDATE {$this->table}
            SET name = ?, frequency = ?, price = ?
            WHERE id = ?"
        );
        $stmt->bind_param(
            'ssdi',
            $data['name'],
            $data['frequency'],
            $data['price'],
            $id
        );
        return $stmt->execute();
    }

    // Delete plan
    public function delete($id) {
        $stmt = $this->conn->prepare(
            "DELETE FROM {$this->table} WHERE id = ?"
        );
        $stmt->bind_param('i', $id);
        return $stmt->execute();
    }
}