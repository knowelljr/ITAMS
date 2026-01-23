<?php

namespace App\Models;

use App\Database\Connection;
use App\Helpers\Encryption;

class User
{
    private $db;

    public function __construct()
    {
        try {
            $this->db = Connection::getInstance()->getConnection();
        } catch (\Exception $e) {
            die("Database Error: " . $e->getMessage());
        }
    }

    /**
     * Create a new user
     */
    public function create($data)
    {
        try {
            $stmt = $this->db->prepare("
                INSERT INTO users (name, email, password, employee_number, mobile_number, department, role, created_at, updated_at)
                VALUES (?, ?, ?, ?, ?, ?, ?, GETDATE(), GETDATE())
            ");

            $hashedPassword = Encryption::hashPassword($data['password']);
            $role = $data['role'] ?? 'REQUESTER';

            return $stmt->execute([
                $data['name'],
                $data['email'],
                $hashedPassword,
                $data['employee_number'] ?? null,
                $data['mobile_number'] ?? null,
                $data['department'] ?? null,
                $role
            ]);
        } catch (\Exception $e) {
            throw new \Exception('Error creating user: ' . $e->getMessage());
        }
    }

    /**
     * Find user by email
     */
    public function findByEmail($email)
    {
        try {
            $stmt = $this->db->prepare("SELECT * FROM users WHERE email = ?");
            $stmt->execute([$email]);
            return $stmt->fetch();
        } catch (\Exception $e) {
            throw new \Exception('Error finding user: ' .  $e->getMessage());
        }
    }

    /**
     * Find user by ID
     */
    public function findById($id)
    {
        try {
            $stmt = $this->db->prepare("SELECT * FROM users WHERE id = ?");
            $stmt->execute([$id]);
            return $stmt->fetch();
        } catch (\Exception $e) {
            throw new \Exception('Error finding user: ' . $e->getMessage());
        }
    }

    /**
     * Get all users
     */
    public function all()
    {
        try {
            $stmt = $this->db->query("SELECT id, name, email, role, created_at FROM users ORDER BY created_at DESC");
            return $stmt->fetchAll();
        } catch (\Exception $e) {
            throw new \Exception('Error fetching users: ' .  $e->getMessage());
        }
    }

    /**
     * Update user
     */
    public function update($id, $data)
    {
        try {
            $fields = [];
            $values = [];

            foreach ($data as $key => $value) {
                if ($key !== 'id') {
                    $fields[] = "$key = ?";
                    $values[] = $value;
                }
            }

            if (empty($fields)) {
                return false;
            }

            $values[] = $id;
            $sql = "UPDATE users SET " . implode(', ', $fields) . ", updated_at = GETDATE() WHERE id = ?";

            $stmt = $this->db->prepare($sql);
            return $stmt->execute($values);
        } catch (\Exception $e) {
            throw new \Exception('Error updating user: ' . $e->getMessage());
        }
    }

    /**
     * Delete user
     */
    public function delete($id)
    {
        try {
            $stmt = $this->db->prepare("DELETE FROM users WHERE id = ?");
            return $stmt->execute([$id]);
        } catch (\Exception $e) {
            throw new \Exception('Error deleting user: ' . $e->getMessage());
        }
    }

    /**
     * Verify password
     */
    public function verifyPassword($email, $password)
    {
        try {
            $user = $this->findByEmail($email);
            if (!$user) {
                return false;
            }
            return Encryption::verifyPassword($password, $user['password']);
        } catch (\Exception $e) {
            throw new \Exception('Error verifying password: ' . $e->getMessage());
        }
    }
}
?>