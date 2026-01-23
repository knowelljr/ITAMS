<?php
require 'vendor/autoload.php';

use App\Database\Connection;

try {
    $db = Connection::getInstance()->getConnection();
    
    // Check if there are any assets
    $count = $db->query("SELECT COUNT(*) as cnt FROM assets")->fetch();
    echo "Assets in database: {$count['cnt']}\n";
    
    if ($count['cnt'] == 0) {
        echo "Creating test assets...\n";
        
        // Insert test assets
        $assets = [
            ['asset_code' => 'LAPTOP-001', 'name' => 'Dell Laptop', 'category' => 'Computer', 'serial' => 'SN001', 'model' => 'XPS 13', 'location' => 'IT Room', 'cost' => 1200.00, 'qty_on_hand' => 5, 'opt_stock' => 2, 'max_stock' => 10],
            ['asset_code' => 'MONITOR-001', 'name' => 'Dell Monitor 24"', 'category' => 'Peripherals', 'serial' => 'SN002', 'model' => 'U2419', 'location' => 'IT Room', 'cost' => 300.00, 'qty_on_hand' => 10, 'opt_stock' => 5, 'max_stock' => 20],
            ['asset_code' => 'KEYBOARD-001', 'name' => 'Mechanical Keyboard', 'category' => 'Peripherals', 'serial' => 'SN003', 'model' => 'MX Keys', 'location' => 'Storage', 'cost' => 100.00, 'qty_on_hand' => 15, 'opt_stock' => 8, 'max_stock' => 25],
        ];
        
        foreach ($assets as $asset) {
            $stmt = $db->prepare("
                INSERT INTO assets (asset_code, name, category, serial_number, model, location, cost, quantity_onhand, quantity_issued, optimum_stock, max_stock, status)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, 0, ?, ?, 'AVAILABLE')
            ");
            $stmt->execute([
                $asset['asset_code'], $asset['name'], $asset['category'], $asset['serial'], 
                $asset['model'], $asset['location'], $asset['cost'], $asset['qty_on_hand'], 
                $asset['opt_stock'], $asset['max_stock']
            ]);
            echo "✓ Created {$asset['name']}\n";
        }
    }
    
    echo "\nCurrent Assets:\n";
    $assets = $db->query("SELECT id, asset_code, name, quantity_onhand, quantity_issued FROM assets ORDER BY id")->fetchAll();
    foreach ($assets as $asset) {
        $available = $asset['quantity_onhand'] - $asset['quantity_issued'];
        echo "ID: {$asset['id']}, Code: {$asset['asset_code']}, Name: {$asset['name']}, On-Hand: {$asset['quantity_onhand']}, Available: $available\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
