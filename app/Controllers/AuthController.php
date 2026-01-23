<?php

namespace App\Controllers;

use App\Models\User;

class AuthController
{
    /**
     * Handle user registration
     */
    public static function register()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }

        $name = $_POST['full_name'] ?? '';
        $email = $_POST['email'] ??  '';
        $password = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';
    $employeeNumber = $_POST['employee_number'] ?? '';
    $mobileNumber = $_POST['mobile_number'] ?? '';
    $departmentId = $_POST['department_id'] ?? '';

        // Validation
        if (empty($name) || empty($email) || empty($password) || empty($employeeNumber) || empty($departmentId)) {
            $_SESSION['error'] = 'All required fields must be filled';
            header('Location: /register');
            exit;
        }

        if ($password !== $confirmPassword) {
            $_SESSION['error'] = 'Passwords do not match';
            header('Location: /register');
            exit;
        }

        if (strlen($password) < 6) {
            $_SESSION['error'] = 'Password must be at least 6 characters';
            header('Location: /register');
            exit;
        }

        // Check if email exists
        $user = new User();
        if ($user->findByEmail($email)) {
            $_SESSION['error'] = 'Email already exists';
            header('Location: /register');
            exit;
        }

        // Create new user
        try {
            $user->create([
                'name' => $name,
                'email' => $email,
                'password' => $password,
                                'employee_number' => $employeeNumber,
                                'mobile_number' => $mobileNumber,
                                'department_id' => $departmentId,
                'role' => 'REQUESTER'
            ]);

            $_SESSION['success'] = 'Registration successful! Please login. ';
            header('Location: /login');
            exit;
        } catch (\Exception $e) {
            $_SESSION['error'] = 'Registration failed: ' .  $e->getMessage();
            header('Location: /register');
            exit;
        }
    }

    /**
     * Handle user login
     */
    public static function login()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }

        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';

        if (empty($email) || empty($password)) {
            $_SESSION['error'] = 'Email and password are required';
            header('Location: /login');
            exit;
        }

        $user = new User();
        
        if (! $user->verifyPassword($email, $password)) {
            $_SESSION['error'] = 'Invalid email or password';
            header('Location: /login');
            exit;
        }

        // Get user details
        $userData = $user->findByEmail($email);
        
        if (! $userData) {
            $_SESSION['error'] = 'User not found';
            header('Location: /login');
            exit;
        }

        // Check if user is archived
        if (isset($userData['archived']) && $userData['archived'] == 1) {
            $_SESSION['error'] = 'Your account has been archived. Please contact the administrator.';
            header('Location: /login');
            exit;
        }

        // Set session
        $_SESSION['user_id'] = $userData['id'];
        $_SESSION['user_name'] = $userData['name'];
        $_SESSION['user_email'] = $userData['email'];
        $_SESSION['user_role'] = $userData['role'];

        // Check if password reset is required
        if (isset($userData['password_reset_required']) && $userData['password_reset_required'] == 1) {
            $_SESSION['info'] = 'You must reset your password before continuing.';
            header('Location: /change-password');
            exit;
        }

        $_SESSION['success'] = 'Login successful!';
        header('Location: /dashboard');
        exit;
    }

    /**
     * Handle logout
     */
    public static function logout()
    {
        session_destroy();
        header('Location: /login');
        exit;
    }

    /**
     * Handle password change (forced or voluntary)
     */
    public static function changePassword()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }

        if (!isset($_SESSION['user_id'])) {
            $_SESSION['error'] = 'You must be logged in to change password';
            header('Location: /login');
            exit;
        }

        $currentPassword = $_POST['current_password'] ?? '';
        $newPassword = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        // Validation
        if (empty($currentPassword) || empty($newPassword) || empty($confirmPassword)) {
            $_SESSION['error'] = 'All fields are required';
            header('Location: /change-password');
            exit;
        }

        if ($newPassword !== $confirmPassword) {
            $_SESSION['error'] = 'New passwords do not match';
            header('Location: /change-password');
            exit;
        }

        if (strlen($newPassword) < 6) {
            $_SESSION['error'] = 'Password must be at least 6 characters';
            header('Location: /change-password');
            exit;
        }

        // Verify current password
        $user = new User();
        if (!$user->verifyPassword($_SESSION['user_email'], $currentPassword)) {
            $_SESSION['error'] = 'Current password is incorrect';
            header('Location: /change-password');
            exit;
        }

        // Update password and clear reset flag
        try {
            $db = \App\Database\Connection::getInstance()->getConnection();
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            $stmt = $db->prepare("UPDATE users SET password = ?, password_reset_required = 0, updated_at = GETDATE() WHERE id = ?");
            $stmt->execute([$hashedPassword, $_SESSION['user_id']]);

            $_SESSION['success'] = 'Password changed successfully!';
            header('Location: /dashboard');
            exit;
        } catch (\Exception $e) {
            $_SESSION['error'] = 'Failed to change password: ' . $e->getMessage();
            header('Location: /change-password');
            exit;
        }
    }
}
?>