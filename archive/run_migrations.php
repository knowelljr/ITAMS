<?php
require 'vendor/autoload.php';

use App\Database\Connection;

try {
    $db = Connection::getInstance()->getConnection();
    
    echo "Running migrations...\n\n";
    
    // Read and execute migration 006
    echo "Running migration 006_add_issuance_type.sql...\n";
    $sql006 = file_get_contents('database/migrations/006_add_issuance_type.sql');
    // Split by GO statements and execute each
    $statements = array_filter(array_map('trim', preg_split('/\bGO\b/i', $sql006)));
    foreach ($statements as $statement) {
        if (!empty($statement)) {
            try {
                $db->exec($statement);
                echo "✓ Statement executed\n";
            } catch (Exception $e) {
                echo "✗ Error: " . $e->getMessage() . "\n";
            }
        }
    }
    
    echo "\nRunning migration 007_add_dual_approval_to_requests.sql...\n";
    $sql007 = file_get_contents('database/migrations/007_add_dual_approval_to_requests.sql');
    $statements = array_filter(array_map('trim', preg_split('/\bGO\b/i', $sql007)));
    foreach ($statements as $statement) {
        if (!empty($statement)) {
            try {
                $db->exec($statement);
                echo "✓ Statement executed\n";
            } catch (Exception $e) {
                echo "✗ Error: " . $e->getMessage() . "\n";
            }
        }
    }
    
    echo "\nRunning migration 008_add_po_and_quotation_to_requests.sql...\n";
    $sql008 = file_get_contents('database/migrations/008_add_po_and_quotation_to_requests.sql');
    $statements = array_filter(array_map('trim', preg_split('/\bGO\b/i', $sql008)));
    foreach ($statements as $statement) {
        if (!empty($statement)) {
            try {
                $db->exec($statement);
                echo "✓ Statement executed\n";
            } catch (Exception $e) {
                echo "✗ Error: " . $e->getMessage() . "\n";
            }
        }
    }

    echo "\nRunning migration 009_create_inventory_stores_table.sql...\n";
    $sql009 = file_get_contents('database/migrations/009_create_inventory_stores_table.sql');
    $statements = array_filter(array_map('trim', preg_split('/\bGO\b/i', $sql009)));
    foreach ($statements as $statement) {
        if (!empty($statement)) {
            try {
                $db->exec($statement);
                echo "✓ Statement executed\n";
            } catch (Exception $e) {
                echo "✗ Error: " . $e->getMessage() . "\n";
            }
        }
    }

    echo "\nRunning migration 010_create_store_inventory_table.sql...\n";
    $sql010 = file_get_contents('database/migrations/010_create_store_inventory_table.sql');
    $statements = array_filter(array_map('trim', preg_split('/\bGO\b/i', $sql010)));
    foreach ($statements as $statement) {
        if (!empty($statement)) {
            try {
                $db->exec($statement);
                echo "✓ Statement executed\n";
            } catch (Exception $e) {
                echo "✗ Error: " . $e->getMessage() . "\n";
            }
        }
    }

    echo "\nRunning migration 011_create_asset_movements_table.sql...\n";
    $sql011 = file_get_contents('database/migrations/011_create_asset_movements_table.sql');
    $statements = array_filter(array_map('trim', preg_split('/\bGO\b/i', $sql011)));
    foreach ($statements as $statement) {
        if (!empty($statement)) {
            try {
                $db->exec($statement);
                echo "✓ Statement executed\n";
            } catch (Exception $e) {
                echo "✗ Error: " . $e->getMessage() . "\n";
            }
        }
    }

    echo "\nRunning migration 012_add_store_fields_to_issuances.sql...\n";
    $sql012 = file_get_contents('database/migrations/012_add_store_fields_to_issuances.sql');
    $statements = array_filter(array_map('trim', preg_split('/\bGO\b/i', $sql012)));
    foreach ($statements as $statement) {
        if (!empty($statement)) {
            try {
                $db->exec($statement);
                echo "✓ Statement executed\n";
            } catch (Exception $e) {
                echo "✗ Error: " . $e->getMessage() . "\n";
            }
        }
    }

    echo "\n✓ Migrations completed!\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
