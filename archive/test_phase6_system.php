<?php
/**
 * Phase 6 System Testing - Complete Workflow Validation
 * 
 * This script tests the complete store-based inventory system:
 * 1. Database schema verification
 * 2. Store data verification
 * 3. Inventory loading
 * 4. Simulated issue workflow
 * 5. Simulated receipt workflow
 * 6. Audit trail verification
 */

require 'vendor/autoload.php';

use App\Database\Connection;

class Phase6Tester {
    private $db;
    private $testsPassed = 0;
    private $testsFailed = 0;
    
    public function __construct() {
        $this->db = Connection::getInstance()->getConnection();
    }
    
    public function test($name, $condition, $details = '') {
        if ($condition) {
            echo "✓ PASS: $name\n";
            if ($details) echo "       $details\n";
            $this->testsPassed++;
        } else {
            echo "✗ FAIL: $name\n";
            if ($details) echo "       $details\n";
            $this->testsFailed++;
        }
    }
    
    public function runAllTests() {
        echo "\n=== PHASE 6 SYSTEM TESTING ===\n\n";
        
        $this->testDatabaseSchema();
        $this->testStoreData();
        $this->testInventoryData();
        $this->testAPI();
        $this->testWorkflows();
        
        echo "\n=== TEST SUMMARY ===\n";
        echo "Passed: " . $this->testsPassed . "\n";
        echo "Failed: " . $this->testsFailed . "\n";
        
        if ($this->testsFailed === 0) {
            echo "\n✓ ALL TESTS PASSED! System ready for deployment.\n";
            return 0;
        } else {
            echo "\n✗ Some tests failed. Review details above.\n";
            return 1;
        }
    }
    
    private function testDatabaseSchema() {
        echo "1. DATABASE SCHEMA VALIDATION\n";
        echo str_repeat("-", 60) . "\n";
        
        // Check tables exist
        $tables = ['inventory_stores', 'store_inventory', 'asset_movements', 'asset_issuances'];
        foreach ($tables as $table) {
            try {
                $result = $this->db->query("SELECT COUNT(*) FROM $table")->fetch();
                $this->test("Table exists: $table", isset($result[0]));
            } catch (\Exception $e) {
                $this->test("Table exists: $table", false, "Error: " . $e->getMessage());
            }
        }
        
        // Check key columns in asset_issuances
        $requiredColumns = ['issued_from_store_id', 'condition_on_receipt', 'receipt_notes'];
        foreach ($requiredColumns as $col) {
            try {
                $result = $this->db->query("SELECT $col FROM asset_issuances LIMIT 1")->fetch();
                $this->test("Column exists: asset_issuances.$col", true);
            } catch (\Exception $e) {
                $this->test("Column exists: asset_issuances.$col", false, "Missing column");
            }
        }
        
        echo "\n";
    }
    
    private function testStoreData() {
        echo "2. STORE DATA VALIDATION\n";
        echo str_repeat("-", 60) . "\n";
        
        // Check stores exist
        $storesStmt = $this->db->query("SELECT COUNT(*) as cnt FROM inventory_stores WHERE is_active = 1");
        $storesCount = $storesStmt->fetch()['cnt'];
        $this->test("Stores created", $storesCount >= 3, "$storesCount active stores found");
        
        // Check store names
        $storeNames = ['Main Store', 'Branch Store', 'Central Warehouse'];
        $foundNames = [];
        $result = $this->db->query("SELECT store_name FROM inventory_stores WHERE is_active = 1");
        while ($row = $result->fetch()) {
            $foundNames[] = $row['store_name'];
        }
        foreach ($storeNames as $name) {
            $this->test("Store exists: $name", in_array($name, $foundNames));
        }
        
        // Check manager assignment
        $managerStmt = $this->db->query("SELECT COUNT(*) as cnt FROM inventory_stores WHERE manager_id IS NOT NULL AND is_active = 1");
        $storesWithManager = $managerStmt->fetch()['cnt'];
        $this->test("All stores have managers", $storesWithManager === $storesCount);
        
        echo "\n";
    }
    
    private function testInventoryData() {
        echo "3. INVENTORY DATA VALIDATION\n";
        echo str_repeat("-", 60) . "\n";
        
        // Check inventory records
        $invStmt = $this->db->query("SELECT COUNT(*) as cnt FROM store_inventory");
        $invCount = $invStmt->fetch()['cnt'];
        $this->test("Inventory records created", $invCount > 0, "$invCount records in store_inventory");
        
        // Check quantities are positive
        $qtyStmt = $this->db->query("
            SELECT COUNT(*) as cnt FROM store_inventory 
            WHERE quantity_available > 0
        ");
        $qtyCount = $qtyStmt->fetch()['cnt'];
        $this->test("Inventory has positive quantities", $qtyCount > 0, "$qtyCount items with stock > 0");
        
        // Check all stores have inventory
        $storesWithInv = $this->db->query("
            SELECT COUNT(DISTINCT store_id) as cnt FROM store_inventory
        ")->fetch()['cnt'];
        $this->test("All stores have inventory", $storesWithInv >= 3, "$storesWithInv stores have inventory");
        
        echo "\n";
    }
    
    private function testAPI() {
        echo "4. API ENDPOINT VALIDATION\n";
        echo str_repeat("-", 60) . "\n";
        
        // Check Store model can be instantiated
        try {
            $store = new \App\Models\Store($this->db);
            $this->test("Store model loads", true);
        } catch (\Exception $e) {
            $this->test("Store model loads", false, $e->getMessage());
            echo "\n";
            return;
        }
        
        // Check getStoreInventory method works
        try {
            $inventory = $store->getStoreInventory(1);
            $this->test("getStoreInventory(1) works", is_array($inventory), count($inventory) . " items returned");
        } catch (\Exception $e) {
            $this->test("getStoreInventory(1) works", false, $e->getMessage());
        }
        
        // Check getAllStores method
        try {
            $allStores = $store->getAllStores();
            $this->test("getAllStores() works", is_array($allStores), count($allStores) . " stores returned");
        } catch (\Exception $e) {
            $this->test("getAllStores() works", false, $e->getMessage());
        }
        
        echo "\n";
    }
    
    private function testWorkflows() {
        echo "5. WORKFLOW SIMULATION\n";
        echo str_repeat("-", 60) . "\n";
        
        try {
            $store = new \App\Models\Store($this->db);
            
            // Get first asset from inventory
            $invStmt = $this->db->query("
                SELECT TOP 1 si.*, a.name as asset_name
                FROM store_inventory si
                JOIN assets a ON si.asset_id = a.id
                WHERE si.quantity_available > 0
            ");
            $inventory = $invStmt->fetch();
            
            if ($inventory) {
                $storeId = $inventory['store_id'];
                $assetId = $inventory['asset_id'];
                $availableQty = $inventory['quantity_available'];
                $assetName = $inventory['asset_name'];
                
                $this->test("Found test asset", true, "$assetName in store $storeId");
                
                // Test movement recording
                try {
                    $store->recordMovement([
                        'asset_id' => $assetId,
                        'movement_type' => 'TEST_ISSUED',
                        'from_store_id' => $storeId,
                        'quantity' => 1,
                        'performed_by' => 1,
                        'reason' => 'Test workflow validation',
                        'notes' => 'Automated test'
                    ]);
                    
                    // Verify movement was recorded
                    $moveStmt = $this->db->query("
                        SELECT COUNT(*) as cnt FROM asset_movements 
                        WHERE asset_id = ? AND movement_type = 'TEST_ISSUED'
                    ")->fetch();
                    
                    $this->test("recordMovement() works", $moveStmt['cnt'] > 0, "Movement recorded in audit trail");
                } catch (\Exception $e) {
                    $this->test("recordMovement() works", false, $e->getMessage());
                }
                
                // Test inventory update
                try {
                    $updated = $store->updateInventory($storeId, $assetId, -1, 'available');
                    $this->test("updateInventory() deducts stock", true);
                    
                    // Verify quantity changed
                    $verifyStmt = $this->db->query("
                        SELECT quantity_available FROM store_inventory 
                        WHERE store_id = ? AND asset_id = ?
                    ", [$storeId, $assetId])->fetch();
                    
                    $newQty = $verifyStmt['quantity_available'];
                    $this->test("Stock deducted correctly", $newQty === ($availableQty - 1), "Qty: $availableQty → $newQty");
                    
                    // Restore for other tests
                    $store->updateInventory($storeId, $assetId, 1, 'available');
                } catch (\Exception $e) {
                    $this->test("updateInventory() deducts stock", false, $e->getMessage());
                }
                
            } else {
                $this->test("Found test asset", false, "No inventory data available");
            }
            
        } catch (\Exception $e) {
            $this->test("Workflow simulation", false, $e->getMessage());
        }
        
        echo "\n";
    }
}

// Run tests
$tester = new Phase6Tester();
exit($tester->runAllTests());

?>
