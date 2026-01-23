<?php
require 'vendor/autoload.php';

use App\Database\Connection;

try {
    $db = Connection::getInstance()->getConnection();
    
    echo "All users with departments:\n";
    $result = $db->query("
        SELECT u.id, u.email, u.name, u.role, u.employee_number, d.department_name
        FROM users u 
        LEFT JOIN departments d ON u.department_id = d.id 
        ORDER BY u.id
    ")->fetchAll();
    
    foreach ($result as $user) {
        echo "ID: {$user['id']}, Email: {$user['email']}, Name: {$user['name']}, Role: {$user['role']}, Dept: {$user['department_name']}\n";
    }
    
    echo "\n\nDepartments:\n";
    $depts = $db->query("SELECT * FROM departments ORDER BY id")->fetchAll();
    foreach ($depts as $dept) {
        echo "ID: {$dept['id']}, Code: {$dept['department_code']}, Name: {$dept['department_name']}\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
