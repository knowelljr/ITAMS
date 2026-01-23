<?php

namespace App\Controllers;

use App\Database\Connection;

class DepartmentController
{
    private $db;

    public function __construct()
    {
        $this->db = Connection::getInstance()->getConnection();
    }

    /**
     * Display list of all departments
     */
    public function index()
    {
        try {
            $stmt = $this->db->query("
                SELECT id, department_code, department_name, created_at 
                FROM departments 
                ORDER BY department_name ASC
            ");
            $departments = $stmt->fetchAll();

            $activePage = 'departments';
            include __DIR__ . '/../../resources/views/admin/departments/index.php';
        } catch (\Exception $e) {
            $_SESSION['error'] = 'Failed to fetch departments: ' . $e->getMessage();
            header('Location: /dashboard');
            exit;
        }
    }

    /**
     * Show create department form
     */
    public function create()
    {
        $activePage = 'departments';
        include __DIR__ . '/../../resources/views/admin/departments/create.php';
    }

    /**
     * Store new department
     */
    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /departments');
            exit;
        }

        $code = strtoupper(trim($_POST['department_code'] ?? ''));
        $name = trim($_POST['department_name'] ?? '');

        // Validation
        if (empty($code) || empty($name)) {
            $_SESSION['error'] = 'Department code and name are required';
            header('Location: /departments/create');
            exit;
        }

        if (strlen($code) > 10) {
            $_SESSION['error'] = 'Department code must not exceed 10 characters';
            header('Location: /departments/create');
            exit;
        }

        if (strlen($name) > 80) {
            $_SESSION['error'] = 'Department name must not exceed 80 characters';
            header('Location: /departments/create');
            exit;
        }

        // Check if code already exists
        try {
            $stmt = $this->db->prepare("SELECT id FROM departments WHERE department_code = ?");
            $stmt->execute([$code]);
            if ($stmt->fetch()) {
                $_SESSION['error'] = 'Department code already exists';
                header('Location: /departments/create');
                exit;
            }

            // Create department
            $stmt = $this->db->prepare("
                INSERT INTO departments (department_code, department_name, created_at, updated_at) 
                VALUES (?, ?, GETDATE(), GETDATE())
            ");
            $stmt->execute([$code, $name]);

            $_SESSION['success'] = 'Department created successfully';
            header('Location: /departments');
            exit;
        } catch (\Exception $e) {
            $_SESSION['error'] = 'Failed to create department: ' . $e->getMessage();
            header('Location: /departments/create');
            exit;
        }
    }

    /**
     * Show edit department form
     */
    public function edit($id)
    {
        try {
            $stmt = $this->db->prepare("SELECT * FROM departments WHERE id = ?");
            $stmt->execute([$id]);
            $department = $stmt->fetch();

            if (!$department) {
                $_SESSION['error'] = 'Department not found';
                header('Location: /departments');
                exit;
            }

            $activePage = 'departments';
            include __DIR__ . '/../../resources/views/admin/departments/edit.php';
        } catch (\Exception $e) {
            $_SESSION['error'] = 'Failed to fetch department: ' . $e->getMessage();
            header('Location: /departments');
            exit;
        }
    }

    /**
     * Update department
     */
    public function update($id)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /departments');
            exit;
        }

        $code = strtoupper(trim($_POST['department_code'] ?? ''));
        $name = trim($_POST['department_name'] ?? '');

        // Validation
        if (empty($code) || empty($name)) {
            $_SESSION['error'] = 'Department code and name are required';
            header('Location: /departments/edit/' . $id);
            exit;
        }

        if (strlen($code) > 10) {
            $_SESSION['error'] = 'Department code must not exceed 10 characters';
            header('Location: /departments/edit/' . $id);
            exit;
        }

        if (strlen($name) > 80) {
            $_SESSION['error'] = 'Department name must not exceed 80 characters';
            header('Location: /departments/edit/' . $id);
            exit;
        }

        try {
            // Check if code exists for another department
            $stmt = $this->db->prepare("SELECT id FROM departments WHERE department_code = ? AND id != ?");
            $stmt->execute([$code, $id]);
            if ($stmt->fetch()) {
                $_SESSION['error'] = 'Department code already exists';
                header('Location: /departments/edit/' . $id);
                exit;
            }

            // Update department
            $stmt = $this->db->prepare("
                UPDATE departments 
                SET department_code = ?, department_name = ?, updated_at = GETDATE() 
                WHERE id = ?
            ");
            $stmt->execute([$code, $name, $id]);

            $_SESSION['success'] = 'Department updated successfully';
            header('Location: /departments');
            exit;
        } catch (\Exception $e) {
            $_SESSION['error'] = 'Failed to update department: ' . $e->getMessage();
            header('Location: /departments/edit/' . $id);
            exit;
        }
    }

    /**
     * Delete department
     */
    public function delete($id)
    {
        try {
            // Check if department has users
            $stmt = $this->db->prepare("SELECT COUNT(*) as count FROM users WHERE department_id = ?");
            $stmt->execute([$id]);
            $result = $stmt->fetch();

            if ($result['count'] > 0) {
                $_SESSION['error'] = 'Cannot delete department with assigned users';
                header('Location: /departments');
                exit;
            }

            $stmt = $this->db->prepare("DELETE FROM departments WHERE id = ?");
            $stmt->execute([$id]);

            $_SESSION['success'] = 'Department deleted successfully';
            header('Location: /departments');
            exit;
        } catch (\Exception $e) {
            $_SESSION['error'] = 'Failed to delete department: ' . $e->getMessage();
            header('Location: /departments');
            exit;
        }
    }

    /**
     * Get all departments for dropdown
     */
    public static function getAllDepartments()
    {
        try {
            $db = Connection::getInstance()->getConnection();
            $stmt = $db->query("SELECT id, department_code, department_name FROM departments ORDER BY department_name ASC");
            return $stmt->fetchAll();
        } catch (\Exception $e) {
            return [];
        }
    }
}
