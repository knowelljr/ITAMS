<?php
/**
 * Create initial store data for testing
 * Creates a default "Main Store" if it doesn't exist
 */

require 'vendor/autoload.php';

use App\Database\Connection;

try {
    $db = Connection::getInstance()->getConnection();
    
    echo "Creating initial store data...\n\n";
    
    // Get IT Manager user (use first IT_MANAGER if exists, otherwise use first ADMIN)
    $stmt = $db->query("
        SELECT TOP 1 id, name 
        FROM users 
        WHERE role IN ('IT_MANAGER', 'ADMIN') AND archived = 0
        ORDER BY role DESC
    ");
    $manager = $stmt->fetch();
    
    if (!$manager) {
        echo "✗ No IT Manager or Admin user found. Please create at least one admin user first.\n";
        exit;
    }
    
    $managerId = $manager['id'];
    $managerName = $manager['name'];
    
    echo "Using manager: {$managerName} (ID: {$managerId})\n\n";
    
    // Check if Main Store already exists
    $checkStmt = $db->prepare("SELECT id FROM inventory_stores WHERE store_code = 'MAIN_STORE'");
    $checkStmt->execute([]);
    $existing = $checkStmt->fetch();
    
    if ($existing) {
        echo "✓ Main Store already exists (ID: {$existing['id']})\n";
    } else {
        // Create Main Store
        $insertStmt = $db->prepare("
            INSERT INTO inventory_stores (store_code, store_name, location, description, manager_id, is_active, created_at, updated_at)
            VALUES (?, ?, ?, ?, ?, 1, GETDATE(), GETDATE())
        ");
        
        $insertStmt->execute([
            'MAIN_STORE',
            'Main Store',
            'IT Department - Ground Floor',
            'Primary inventory store for IT asset distribution',
            $managerId
        ]);
        
        $storeId = $db->lastInsertId();
        echo "✓ Main Store created (ID: {$storeId})\n";
    }
    
    // Optional: Create additional stores for large organizations
    $stores = [
        ['BRANCH_STORE', 'Branch Store', 'Building B - IT Office'],
        ['WAREHOUSE', 'Central Warehouse', 'Off-site storage facility']
    ];
    
    foreach ($stores as $store) {
        $checkStmt = $db->prepare("SELECT id FROM inventory_stores WHERE store_code = ?");
        $checkStmt->execute([$store[0]]);
        $existing = $checkStmt->fetch();
        
        if (!$existing) {
            $insertStmt = $db->prepare("
                INSERT INTO inventory_stores (store_code, store_name, location, description, manager_id, is_active, created_at, updated_at)
                VALUES (?, ?, ?, ?, ?, 1, GETDATE(), GETDATE())
            ");
            
            $insertStmt->execute([
                $store[0],
                $store[1],
                $store[2],
                'Additional inventory store',
                $managerId
            ]);
            
            $newStoreId = $db->lastInsertId();
            echo "✓ {$store[1]} created (ID: {$newStoreId})\n";
        } else {
            echo "✓ {$store[1]} already exists\n";
        }
    }
    
    echo "\n✓ Initial store data completed!\n";
    
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
    exit(1);
}
?>
