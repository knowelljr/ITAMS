<?php
require 'vendor/autoload.php';

use App\Database\Connection;

try {
    $db = Connection::getInstance()->getConnection();
    
    echo "=== ITAMS System Check ===\n\n";
    
    // 1. Check database connection
    echo "✓ Database connected\n";
    
    // 2. Check users
    $users = $db->query("SELECT COUNT(*) as cnt FROM users")->fetch();
    echo "✓ Users: {$users['cnt']}\n";
    
    // 3. Check assets
    $assets = $db->query("SELECT COUNT(*) as cnt FROM assets")->fetch();
    echo "✓ Assets: {$assets['cnt']}\n";
    
    // 4. Check asset issuances
    $issuances = $db->query("SELECT COUNT(*) as cnt FROM asset_issuances")->fetch();
    echo "✓ Asset Issuances: {$issuances['cnt']}\n";
    
    // 5. Check asset requests
    $requests = $db->query("SELECT COUNT(*) as cnt FROM asset_requests")->fetch();
    echo "✓ Asset Requests: {$requests['cnt']}\n";
    
    // 6. Check departments
    $depts = $db->query("SELECT COUNT(*) as cnt FROM departments")->fetch();
    echo "✓ Departments: {$depts['cnt']}\n";
    
    echo "\n=== Database Schema Validation ===\n\n";
    
    // Check critical columns exist
    $stmt = $db->query("
        SELECT COLUMN_NAME 
        FROM INFORMATION_SCHEMA.COLUMNS 
        WHERE TABLE_NAME = 'asset_issuances'
    ");
    $columns = $stmt->fetchAll();
    $columnNames = array_column($columns, 'COLUMN_NAME');
    
    $requiredCols = ['id', 'asset_id', 'issued_by', 'quantity', 'issuance_type', 'approval_status', 'status', 'created_at'];
    $missing = array_diff($requiredCols, $columnNames);
    
    if (empty($missing)) {
        echo "✓ asset_issuances table has all required columns\n";
    } else {
        echo "✗ asset_issuances missing columns: " . implode(', ', $missing) . "\n";
    }
    
    // Check asset_requests columns
    $stmt = $db->query("
        SELECT COLUMN_NAME 
        FROM INFORMATION_SCHEMA.COLUMNS 
        WHERE TABLE_NAME = 'asset_requests'
    ");
    $columns = $stmt->fetchAll();
    $columnNames = array_column($columns, 'COLUMN_NAME');
    
    $requiredCols = ['id', 'requester_id', 'asset_id', 'request_number', 'department_manager_approval_status', 'it_manager_approval_status', 'status'];
    $missing = array_diff($requiredCols, $columnNames);
    
    if (empty($missing)) {
        echo "✓ asset_requests table has all required columns\n";
    } else {
        echo "✗ asset_requests missing columns: " . implode(', ', $missing) . "\n";
    }
    
    echo "\n=== Test Data ===\n\n";
    
    // Show sample users
    echo "Users:\n";
    $users = $db->query("SELECT TOP 5 id, email, name, role FROM users ORDER BY id")->fetchAll();
    foreach ($users as $user) {
        echo "  - {$user['email']} ({$user['role']})\n";
    }
    
    // Show sample assets
    echo "\nAssets:\n";
    $assets = $db->query("SELECT TOP 5 id, asset_code, name, quantity_onhand FROM assets ORDER BY id")->fetchAll();
    foreach ($assets as $asset) {
        echo "  - {$asset['asset_code']}: {$asset['name']} (Qty: {$asset['quantity_onhand']})\n";
    }
    
    echo "\n✓ System ready for testing\n";
    
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
}
