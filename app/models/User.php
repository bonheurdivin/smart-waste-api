<?php

class User {
    private $conn;
    private $table = 'users';

    public function __construct($conn) {
        $this->conn = $conn;
    }

    // Find user by phone
    public function findByPhone($phone) {
        $stmt = $this->conn->prepare(
            "SELECT * FROM {$this->table} WHERE phone = ?"
        );
        $stmt->bind_param('s', $phone);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    // Find user by email
    public function findByEmail($email) {
        $stmt = $this->conn->prepare(
            "SELECT * FROM {$this->table} WHERE email = ?"
        );
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    // Find user by ID
    public function findById($id) {
        $stmt = $this->conn->prepare(
            "SELECT * FROM {$this->table} WHERE id = ?"
        );
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    // Create new user
    public function create($data) {
        $stmt = $this->conn->prepare(
            "INSERT INTO {$this->table} 
            (name, email, phone, password, role) 
            VALUES (?, ?, ?, ?, ?)"
        );
        $stmt->bind_param(
            'sssss',
            $data['name'],
            $data['email'],
            $data['phone'],
            $data['password'],
            $data['role']
        );
        $stmt->execute();
        return $this->conn->insert_id;
    }
}