<?php
require 'vendor/autoload.php';

use App\Database\Connection;
use App\Helpers\Encryption;

try {
    $db = Connection::getInstance()->getConnection();
    
    // Create a department manager for HR
    $email = 'maria@xyz.com';
    $name = 'Maria Garcia';
    $role = 'IT_MANAGER';  // Use IT_MANAGER for now (we can adjust roles later)
    $password = 'password123';
    $employee_number = 'EMP004';
    $department_id = 2; // HR department
    $mobile_number = '+1-234-567-8901';
    
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
    
    $stmt = $db->prepare("
        INSERT INTO users (email, name, role, password, employee_number, department_id, mobile_number, password_reset_required)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)
    ");
    
    $stmt->execute([$email, $name, $role, $hashedPassword, $employee_number, $department_id, $mobile_number, 0]);
    
    echo "✓ Created IT_MANAGER user: $email\n";
    
    // Create another user as a different role that might be department manager
    $email2 = 'david@xyz.com';
    $name2 = 'David Rodriguez';
    $role2 = 'IT_MANAGER';
    $employee_number2 = 'EMP005';
    $department_id2 = 2; // HR
    
    $stmt->execute([$email2, $name2, $role2, $hashedPassword, $employee_number2, $department_id2, '+1-987-654-3210', 0]);
    
    echo "✓ Created second IT_MANAGER user: $email2\n";
    
    // List all users now
    echo "\nAll users:\n";
    $users = $db->query("SELECT id, email, name, role, department_id FROM users ORDER BY id")->fetchAll();
    foreach ($users as $user) {
        echo "ID: {$user['id']}, Email: {$user['email']}, Name: {$user['name']}, Role: {$user['role']}, DeptID: {$user['department_id']}\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
