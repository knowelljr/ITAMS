<?php
/**
 * Populate Store Inventory with Test Assets
 * 
 * This script adds sample assets to the store_inventory table
 * to enable testing of the issue/receive workflows
 */

require 'vendor/autoload.php';

use App\Database\Connection;

$db = Connection::getInstance()->getConnection();

echo "=== Populating Store Inventory ===\n\n";

try {
    // Get sample assets to add to stores
    $assetsStmt = $db->query("
        SELECT TOP 10 id, name, category FROM assets 
        WHERE status = 'AVAILABLE'
        ORDER BY id
    ");
    $assets = $assetsStmt->fetchAll();
    
    if (empty($assets)) {
        echo "ERROR: No active assets found. Run setup_test_assets.php first.\n";
        exit(1);
    }
    
    echo "Found " . count($assets) . " assets to distribute across stores.\n\n";
    
    // Get all stores
    $storesStmt = $db->query("SELECT id, store_name FROM inventory_stores WHERE is_active = 1");
    $stores = $storesStmt->fetchAll();
    
    if (empty($stores)) {
        echo "ERROR: No stores found. Run create_initial_stores.php first.\n";
        exit(1);
    }
    
    echo "Found " . count($stores) . " stores.\n\n";
    
    // Clear existing inventory to avoid duplicates
    $db->exec("DELETE FROM store_inventory");
    echo "✓ Cleared existing inventory\n";
    
    $totalAdded = 0;
    
    // Distribute assets across stores
    foreach ($assets as $assetIndex => $asset) {
        foreach ($stores as $storeIndex => $store) {
            // Vary quantities: first store gets most, last gets least
            $baseQty = 10 - ($storeIndex * 2);
            $quantity = max(1, $baseQty);
            
            $stmt = $db->prepare("
                INSERT INTO store_inventory 
                (store_id, asset_id, quantity_available, quantity_reserved, quantity_damaged, created_at, updated_at)
                VALUES (?, ?, ?, 0, 0, GETDATE(), GETDATE())
            ");
            
            $result = $stmt->execute([$store['id'], $asset['id'], $quantity]);
            
            if ($result) {
                $totalAdded++;
            }
        }
    }
    
    echo "\n✓ Added $totalAdded inventory records\n\n";
    
    // Display summary
    $summaryStmt = $db->query("
        SELECT 
            s.store_name,
            COUNT(*) as asset_count,
            SUM(si.quantity_available) as total_quantity
        FROM store_inventory si
        JOIN inventory_stores s ON si.store_id = s.id
        GROUP BY s.store_name
        ORDER BY s.store_name
    ");
    
    $summary = $summaryStmt->fetchAll();
    
    echo "=== Store Inventory Summary ===\n";
    echo str_pad("Store", 30) . str_pad("Assets", 15) . "Total Qty\n";
    echo str_repeat("-", 55) . "\n";
    
    foreach ($summary as $row) {
        echo str_pad($row['store_name'], 30) 
           . str_pad($row['asset_count'], 15) 
           . $row['total_quantity'] . "\n";
    }
    
    echo "\n✓ Store inventory populated successfully!\n";
    
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
    exit(1);
}

?>
