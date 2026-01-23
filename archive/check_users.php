<?php
require 'vendor/autoload.php';

use App\Database\Connection;

try {
    $db = Connection::getInstance()->getConnection();
    
    echo "Users in database:\n";
    $result = $db->query("SELECT id, employee_number, email, name, role, password_reset_required FROM users ORDER BY id")->fetchAll();
    
    foreach ($result as $user) {
        echo "ID: {$user['id']}, Email: {$user['email']}, Name: {$user['name']}, Role: {$user['role']}, Password Reset: {$user['password_reset_required']}\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
