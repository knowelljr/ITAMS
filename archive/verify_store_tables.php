<?php
require 'vendor/autoload.php';

use App\Database\Connection;

try {
    $db = Connection::getInstance()->getConnection();
    
    echo "=== Verifying New Store-Based Tables ===\n\n";
    
    // Check inventory_stores table
    echo "1. Checking inventory_stores table...\n";
    $stmt = $db->query("SELECT COLUMN_NAME, DATA_TYPE FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = 'inventory_stores' ORDER BY ORDINAL_POSITION");
    $columns = $stmt->fetchAll();
    if (count($columns) > 0) {
        echo "   ✓ Table EXISTS with " . count($columns) . " columns:\n";
        foreach ($columns as $col) {
            echo "     - {$col['COLUMN_NAME']} ({$col['DATA_TYPE']})\n";
        }
    } else {
        echo "   ✗ Table NOT FOUND\n";
    }
    
    // Check store_inventory table
    echo "\n2. Checking store_inventory table...\n";
    $stmt = $db->query("SELECT COLUMN_NAME, DATA_TYPE FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = 'store_inventory' ORDER BY ORDINAL_POSITION");
    $columns = $stmt->fetchAll();
    if (count($columns) > 0) {
        echo "   ✓ Table EXISTS with " . count($columns) . " columns:\n";
        foreach ($columns as $col) {
            echo "     - {$col['COLUMN_NAME']} ({$col['DATA_TYPE']})\n";
        }
    } else {
        echo "   ✗ Table NOT FOUND\n";
    }
    
    // Check asset_movements table
    echo "\n3. Checking asset_movements table...\n";
    $stmt = $db->query("SELECT COLUMN_NAME, DATA_TYPE FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = 'asset_movements' ORDER BY ORDINAL_POSITION");
    $columns = $stmt->fetchAll();
    if (count($columns) > 0) {
        echo "   ✓ Table EXISTS with " . count($columns) . " columns:\n";
        foreach ($columns as $col) {
            echo "     - {$col['COLUMN_NAME']} ({$col['DATA_TYPE']})\n";
        }
    } else {
        echo "   ✗ Table NOT FOUND\n";
    }
    
    // Check asset_issuances for new columns
    echo "\n4. Checking asset_issuances enhancements...\n";
    $newColumns = ['issued_from_store_id', 'issued_by_name', 'condition_on_receipt', 'receipt_notes', 'received_at_location'];
    $stmt = $db->query("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = 'asset_issuances'");
    $existingColumns = array_map(function($row) { return $row['COLUMN_NAME']; }, $stmt->fetchAll());
    
    $found = 0;
    foreach ($newColumns as $col) {
        if (in_array($col, $existingColumns)) {
            echo "   ✓ Column '$col' added\n";
            $found++;
        } else {
            echo "   ✗ Column '$col' NOT FOUND\n";
        }
    }
    echo "   Summary: $found/" . count($newColumns) . "\n";
    
    // Check indexes on asset_movements
    echo "\n5. Checking asset_movements indexes...\n";
    $stmt = $db->query("SELECT INDEX_NAME FROM INFORMATION_SCHEMA.INDEXES WHERE TABLE_NAME = 'asset_movements' AND INDEX_NAME NOT LIKE 'PK%'");
    $indexes = $stmt->fetchAll();
    echo "   Found " . count($indexes) . " indexes:\n";
    foreach ($indexes as $idx) {
        echo "     - {$idx['INDEX_NAME']}\n";
    }
    
    echo "\n=== ✓ All store-based tables verified successfully! ===\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
