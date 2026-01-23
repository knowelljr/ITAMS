<?php

namespace App\Controllers;

use App\Database\Connection;

class AdminController
{
    private $db;

    public function __construct()
    {
        $this->db = Connection::getInstance()->getConnection();
    }

    /**
     * Display list of all users
     */
    public function index()
    {
        try {
            $stmt = $this->db->query("
                SELECT u.id, u.name, u.email, u.employee_number, u.mobile_number, d.department_name as department, u.role, u.archived, u.created_at 
                FROM users u
                LEFT JOIN departments d ON u.department_id = d.id
                ORDER BY u.archived ASC, u.created_at DESC
            ");
            $users = $stmt->fetchAll();

            $activePage = 'users';
            include __DIR__ . '/../../resources/views/admin/users/index.php';
        } catch (\Exception $e) {
            $_SESSION['error'] = 'Failed to fetch users: ' . $e->getMessage();
            header('Location: /dashboard');
            exit;
        }
    }

    /**
     * Show create user form
     */
    public function create()
    {
        $activePage = 'users';
        include __DIR__ . '/../../resources/views/admin/users/create.php';
    }

    /**
     * Store new user
     */
    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /users');
            exit;
        }

        $name = $_POST['name'] ?? '';
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        $employeeNumber = $_POST['employee_number'] ?? '';
        $mobileNumber = $_POST['mobile_number'] ?? '';
        $departmentId = $_POST['department_id'] ?? null;
        $role = $_POST['role'] ?? 'REQUESTER';

        // Validation
        if (empty($name) || empty($email) || empty($password) || empty($employeeNumber)) {
            $_SESSION['error'] = 'Name, email, password and employee number are required';
            header('Location: /users/create');
            exit;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['error'] = 'Invalid email format';
            header('Location: /users/create');
            exit;
        }

        if (strlen($password) < 6) {
            $_SESSION['error'] = 'Password must be at least 6 characters';
            header('Location: /users/create');
            exit;
        }

        // Check if email already exists
        try {
            $stmt = $this->db->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->execute([$email]);
            if ($stmt->fetch()) {
                $_SESSION['error'] = 'Email already exists';
                header('Location: /users/create');
                exit;
            }

            // Check if employee number already exists
            $stmt = $this->db->prepare("SELECT id FROM users WHERE employee_number = ?");
            $stmt->execute([$employeeNumber]);
            if ($stmt->fetch()) {
                $_SESSION['error'] = 'Employee number already exists';
                header('Location: /users/create');
                exit;
            }

            // Create user
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $this->db->prepare("
                INSERT INTO users (name, email, password, employee_number, mobile_number, department_id, role, created_at) 
                VALUES (?, ?, ?, ?, ?, ?, ?, GETDATE())
            ");
            $stmt->execute([$name, $email, $hashedPassword, $employeeNumber, $mobileNumber, $departmentId, $role]);

            $_SESSION['success'] = 'User created successfully';
            header('Location: /users');
            exit;
        } catch (\Exception $e) {
            $_SESSION['error'] = 'Failed to create user: ' . $e->getMessage();
            header('Location: /users/create');
            exit;
        }
    }

    /**
     * Show edit user form
     */
    public function edit($id)
    {
        try {
            $stmt = $this->db->prepare("SELECT u.*, d.department_name FROM users u LEFT JOIN departments d ON u.department_id = d.id WHERE u.id = ?");
            $stmt->execute([$id]);
            $user = $stmt->fetch();

            if (!$user) {
                $_SESSION['error'] = 'User not found';
                header('Location: /users');
                exit;
            }

            $activePage = 'users';
            include __DIR__ . '/../../resources/views/admin/users/edit.php';
        } catch (\Exception $e) {
            $_SESSION['error'] = 'Failed to fetch user: ' . $e->getMessage();
            header('Location: /users');
            exit;
        }
    }

    /**
     * Update user
     */
    public function update($id)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /users');
            exit;
        }

        $name = $_POST['name'] ?? '';
        $email = $_POST['email'] ?? '';
        $employeeNumber = $_POST['employee_number'] ?? '';
        $mobileNumber = $_POST['mobile_number'] ?? '';
        $departmentId = $_POST['department_id'] ?? null;
        $role = $_POST['role'] ?? 'REQUESTER';
        $password = $_POST['password'] ?? '';

        // Validation
        if (empty($name) || empty($email) || empty($employeeNumber)) {
            $_SESSION['error'] = 'Name, email and employee number are required';
            header('Location: /users/edit/' . $id);
            exit;
        }

        try {
            // Check if email exists for another user
            $stmt = $this->db->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
            $stmt->execute([$email, $id]);
            if ($stmt->fetch()) {
                $_SESSION['error'] = 'Email already exists';
                header('Location: /users/edit/' . $id);
                exit;
            }

            // Check if employee number exists for another user
            $stmt = $this->db->prepare("SELECT id FROM users WHERE employee_number = ? AND id != ?");
            $stmt->execute([$employeeNumber, $id]);
            if ($stmt->fetch()) {
                $_SESSION['error'] = 'Employee number already exists';
                header('Location: /users/edit/' . $id);
                exit;
            }

            // Update user
            if (!empty($password)) {
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $this->db->prepare("
                    UPDATE users 
                    SET name = ?, email = ?, password = ?, employee_number = ?, mobile_number = ?, department_id = ?, role = ? 
                    WHERE id = ?
                ");
                $stmt->execute([$name, $email, $hashedPassword, $employeeNumber, $mobileNumber, $departmentId, $role, $id]);
            } else {
                $stmt = $this->db->prepare("
                    UPDATE users 
                    SET name = ?, email = ?, employee_number = ?, mobile_number = ?, department_id = ?, role = ? 
                    WHERE id = ?
                ");
                $stmt->execute([$name, $email, $employeeNumber, $mobileNumber, $departmentId, $role, $id]);
            }

            $_SESSION['success'] = 'User updated successfully';
            header('Location: /users');
            exit;
        } catch (\Exception $e) {
            $_SESSION['error'] = 'Failed to update user: ' . $e->getMessage();
            header('Location: /users/edit/' . $id);
            exit;
        }
    }

    /**
     * Archive/Unarchive user
     */
    public function toggleArchive($id)
    {
        // Don't allow archiving yourself
        if ($id == $_SESSION['user_id']) {
            $_SESSION['error'] = 'Cannot archive your own account';
            header('Location: /users');
            exit;
        }

        try {
            // Get current archived status
            $stmt = $this->db->prepare("SELECT archived FROM users WHERE id = ?");
            $stmt->execute([$id]);
            $user = $stmt->fetch();

            if (!$user) {
                $_SESSION['error'] = 'User not found';
                header('Location: /users');
                exit;
            }

            // Toggle archived status
            $newStatus = $user['archived'] ? 0 : 1;
            $stmt = $this->db->prepare("UPDATE users SET archived = ? WHERE id = ?");
            $stmt->execute([$newStatus, $id]);

            $message = $newStatus ? 'User archived successfully' : 'User unarchived successfully';
            $_SESSION['success'] = $message;
            header('Location: /users');
            exit;
        } catch (\Exception $e) {
            $_SESSION['error'] = 'Failed to archive user: ' . $e->getMessage();
            header('Location: /users');
            exit;
        }
    }

    /**
     * Delete user
     */
    public function delete($id)
    {
        // Don't allow deleting yourself
        if ($id == $_SESSION['user_id']) {
            $_SESSION['error'] = 'Cannot delete your own account';
            header('Location: /users');
            exit;
        }

        try {
            $stmt = $this->db->prepare("DELETE FROM users WHERE id = ?");
            $stmt->execute([$id]);

            $_SESSION['success'] = 'User deleted successfully';
            header('Location: /users');
            exit;
        } catch (\Exception $e) {
            $_SESSION['error'] = 'Failed to delete user: ' . $e->getMessage();
            header('Location: /users');
            exit;
        }
    }

    /**
     * Trigger password reset for a user
     */
    public function resetPassword($id)
    {
        // Don't allow resetting your own password this way
        if ($id == $_SESSION['user_id']) {
            $_SESSION['error'] = 'Cannot reset your own password. Use change password instead.';
            header('Location: /users');
            exit;
        }

        try {
            $stmt = $this->db->prepare("UPDATE users SET password_reset_required = 1 WHERE id = ?");
            $stmt->execute([$id]);

            $_SESSION['success'] = 'Password reset flag set. User will be prompted to change password on next login.';
            header('Location: /users');
            exit;
        } catch (\Exception $e) {
            $_SESSION['error'] = 'Failed to trigger password reset: ' . $e->getMessage();
            header('Location: /users');
            exit;
        }
    }
}
