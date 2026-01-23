# Store-Based Inventory Workflow - Testing Guide

## Overview
This document provides a complete guide for testing the new store-based asset issuance and receipt workflow implemented in ITAMS.

## Architecture Summary

The system has been transformed from direct asset issuance to a **store-based inventory model** with these key components:

### Database Layer
- **inventory_stores**: Centralized store/warehouse locations (Main Store, Branch Store, Central Warehouse)
- **store_inventory**: Per-location asset stock with quantities (available, reserved, damaged)
- **asset_movements**: Complete audit trail of all asset movements between locations and users
- **asset_issuances**: Enhanced with store source, condition on receipt, receipt notes

### Application Layer
- **Store Model** (`app/Models/Store.php`): 12 methods for inventory management
- **AssetIssuanceController**: Refactored to use store inventory instead of direct assets
- **API Endpoints**: `/api/stores/:id/inventory` for dynamic asset loading

### User Interface
- **issue.php**: Store selection → Dynamic asset loading → Issue from inventory
- **receive.php**: Condition assessment (GOOD/MINOR_DAMAGE/MAJOR_DAMAGE/UNUSABLE) → Receipt notes
- Dynamic inventory loading via JavaScript fetch() API

## Pre-Testing Checklist

### Database Verification
```
✓ inventory_stores table exists with 3 stores:
  - Main Store (ID:1)
  - Branch Store (ID:2)
  - Central Warehouse (ID:3)
  - All assigned to IT Manager David Rodriguez (ID:5)

✓ store_inventory table created with columns:
  - id, store_id, asset_id, quantity_available, quantity_reserved, quantity_damaged

✓ asset_movements table created with 15 columns including:
  - movement_type (ISSUED, RECEIVED, RETURNED, DAMAGED, TRANSFERRED)
  - from_store_id, to_store_id, quantity, performed_by, created_at

✓ asset_issuances enhancements:
  - issued_from_store_id (source store)
  - issued_by_name (issuer name)
  - condition_on_receipt (condition assessment)
  - receipt_notes (damage/issue documentation)
  - received_at_location (final asset location)
```

### Views Verification
```
✓ issue.php updated:
  - Store selection dropdown present
  - Assets loaded dynamically via loadStoreInventory()
  - Form validation requires store_id

✓ receive.php updated:
  - Condition dropdown with schema-matching options
  - Receipt notes textarea for documentation
  - No quantity_returned field (full asset receipt model)
  - Modal clears and resets on each open
```

### API Endpoints
```
✓ /api/stores/:id/inventory - Returns JSON with store inventory
✓ /assets/issue/process - Creates issuance from store inventory
✓ /assets/receive/process - Records receipt with condition
```

## Step-by-Step Testing

### Test 1: Verify Store Data
**Objective**: Confirm stores are properly created and assigned

1. Open database management tool
2. Run: `SELECT id, name, manager_id, is_active FROM inventory_stores`
3. **Expected Result**: 3 rows with stores and manager_id = 5 (David Rodriguez)

### Test 2: Check Asset Inventory Population
**Objective**: Verify store_inventory is accessible (will be empty initially - need to populate with existing assets)

1. Run: `SELECT COUNT(*) FROM store_inventory`
2. **Expected Result**: 0 initially (normal - need migration of existing stock)
3. **Note**: Asset stock must be populated before testing full workflow

### Test 3: Test Issue Form (UI)
**Objective**: Verify store dropdown and dynamic asset loading work

**Steps:**
1. Navigate to `/assets/issue`
2. Verify "Store" dropdown displays all 3 stores
3. Select "Main Store"
4. **Expected Result**: Asset dropdown updates with message or shows loading (depends on store_inventory population)
5. Try other stores - verify dropdown updates each time

**What's happening:**
- Page calls `loadStoreInventory()` JavaScript function
- Function fetches `/api/stores/1/inventory` (for Main Store)
- API returns JSON with available assets
- Dropdown populated with options

### Test 4: Test API Endpoint
**Objective**: Verify the API endpoint returns correct data format

**Steps:**
1. Open browser console (F12)
2. Run: 
```javascript
fetch('/api/stores/1/inventory')
  .then(r => r.json())
  .then(d => console.log(d))
```
3. **Expected Result**: JSON object with `inventory` array containing assets (empty if store_inventory not populated)

**Example Response Format:**
```json
{
  "inventory": [
    {
      "asset_id": 1,
      "asset_code": "IT-001",
      "asset_name": "Dell Laptop",
      "category": "Computer",
      "quantity_available": 5,
      "cost": 75000
    }
  ]
}
```

### Test 5: Populate Test Data
**Objective**: Create sample data for testing complete workflow

**Steps:**
1. Create test script `test_issue_workflow.php`:

```php
<?php
require_once 'app/Database/Connection.php';
require_once 'app/Models/Store.php';

$db = new \App\Database\Connection();
$store = new \App\Models\Store($db->getConnection());

// Get a sample asset (assume ID:1 exists)
$assetId = 1;
$storeId = 1; // Main Store

// Populate store inventory with 10 units of asset
$stmt = $db->getConnection()->prepare("
    INSERT INTO store_inventory (store_id, asset_id, quantity_available, quantity_reserved, quantity_damaged)
    VALUES (?, ?, ?, 0, 0)
    ON CONFLICT (store_id, asset_id) DO UPDATE SET
        quantity_available = quantity_available + ?
");
$stmt->execute([$storeId, $assetId, 10, 10]);

echo "✓ Added 10 units of Asset ID $assetId to Store ID $storeId\n";
?>
```

2. Run: `php test_issue_workflow.php`
3. **Expected Result**: Confirmation message showing inventory updated

### Test 6: Issue Asset (Full Flow)
**Objective**: Test complete issuance from store inventory

**Prerequisites:**
- Store inventory populated with test assets (see Test 5)
- Asset request created and assigned to a requester

**Steps:**
1. Navigate to `/assets/issue`
2. Select store (e.g., "Main Store")
3. Verify assets dropdown now shows available items
4. Select an asset and quantity
5. Click "Issue"
6. **Expected Result**: Success message and issuance created

**Backend Verification:**
1. Check database:
   - `SELECT * FROM asset_issuances WHERE status = 'ISSUED'` - should show new record
   - `SELECT * FROM store_inventory WHERE asset_id = ?` - quantity_available decreased
   - `SELECT * FROM asset_movements WHERE movement_type = 'ISSUED'` - should show movement record

### Test 7: Receive Asset (Full Flow)
**Objective**: Test receipt with condition assessment

**Prerequisites:**
- Asset previously issued (see Test 6)

**Steps:**
1. Navigate to `/assets/receive`
2. Find issued asset in pending list
3. Click "Receive"
4. Modal opens showing:
   - Asset name
   - Condition dropdown with options (GOOD, MINOR_DAMAGE, MAJOR_DAMAGE, UNUSABLE)
   - Receipt notes textarea
5. Select condition "GOOD"
6. Add notes (optional): "Received in excellent condition"
7. Click "Confirm Receipt"
8. **Expected Result**: Success message, issuance status changes to RECEIVED

**Backend Verification:**
1. Check database:
   - `SELECT condition_on_receipt, receipt_notes FROM asset_issuances WHERE id = ?` - should show selected values
   - `SELECT * FROM asset_movements WHERE movement_type = 'RECEIVED'` - should show receipt movement
   - `SELECT * FROM asset_movements WHERE movement_type IN ('RECEIVED', 'RETURNED', 'DAMAGED')` - verify movement recorded

### Test 8: Damaged Item Handling (MINOR_DAMAGE)
**Objective**: Verify minor damage items are returned to store for repair

**Steps:**
1. Issue new asset
2. Receive with condition "MINOR_DAMAGE"
3. Add notes: "Screen has minor scratches"
4. Submit
5. **Expected Result**: Success message

**Backend Verification:**
1. Asset movement should show:
   - RECEIVED movement (condition: MINOR_DAMAGE)
   - RETURNED movement (reason: Minor damage - returned for repair)
2. Store inventory should show:
   - quantity_available incremented (back in store for repair)

### Test 9: Damaged Item Handling (MAJOR_DAMAGE/UNUSABLE)
**Objective**: Verify major damage items are marked as damaged inventory

**Steps:**
1. Issue new asset
2. Receive with condition "MAJOR_DAMAGE"
3. Add notes: "LCD screen completely broken"
4. Submit
5. **Expected Result**: Success message

**Backend Verification:**
1. Asset movement should show:
   - RECEIVED movement (condition: MAJOR_DAMAGE)
   - DAMAGED movement (item marked as damaged)
2. Store inventory should show:
   - quantity_damaged incremented
   - quantity_available unchanged (not available for reissue)

### Test 10: Audit Trail
**Objective**: Verify complete movement history is recorded

**Steps:**
1. Query asset movements:
```sql
SELECT 
    am.id,
    am.movement_type,
    am.quantity,
    am.reason,
    am.created_at,
    u.username as performer
FROM asset_movements am
LEFT JOIN users u ON am.performed_by = u.id
WHERE am.asset_id = 1
ORDER BY am.created_at DESC
```

2. **Expected Result**: Complete chronological history showing:
   - ISSUED (by IT Manager when issuing from store)
   - RECEIVED (by Requester when accepting)
   - RETURNED or DAMAGED (based on condition)

## Troubleshooting

### Issue: "404 Not Found" on /assets/issue
**Solution**: Verify route exists in `routes/web.php`
- Check: `grep -r "assets/issue" routes/`
- Should show route handler mapped to `AssetIssuanceController->issueForm()`

### Issue: Store dropdown shows but no stores display
**Solution**: Check store data in database
- Run: `SELECT * FROM inventory_stores`
- If empty, run: `php create_initial_stores.php`

### Issue: Asset dropdown not updating when store selected
**Solution**: Check browser console for JavaScript errors
- Open F12 Developer Tools → Console tab
- Verify `loadStoreInventory()` function exists
- Check if `/api/stores/:id/inventory` returns valid JSON

### Issue: Form submission fails with "Invalid condition status"
**Solution**: Verify condition values match schema
- Form should send: GOOD, MINOR_DAMAGE, MAJOR_DAMAGE, or UNUSABLE
- Check receive.php form for correct option values
- Controller validates against this list in `processReceipt()`

### Issue: Asset quantity not decreasing in store_inventory
**Solution**: Verify transaction handling in `processIssuance()`
- Check database transaction logs
- Ensure `Store->updateInventory()` is called
- Verify no try/catch blocks silently swallowing errors

## Performance Considerations

1. **Inventory Queries**: `store_inventory` should be indexed on (store_id, asset_id)
2. **Movement History**: Large `asset_movements` table benefits from index on (asset_id, created_at)
3. **API Response**: JSON endpoint can handle hundreds of store items efficiently

## Next Steps After Testing

1. ✅ **Basic workflow tests** (Tests 1-7) - Validates core functionality
2. ⏭️ **Create store management interface** - UI for CRUD operations on stores
3. ⏭️ **Build inventory reports** - Views for store stock levels and asset locations
4. ⏭️ **Populate existing inventory** - Migration script to add current assets to store_inventory
5. ⏭️ **Asset movement history UI** - Audit trail viewing for compliance

## Success Criteria

✅ **Testing Successful When:**
- Store inventory loads without errors
- Assets are deducted from store_inventory on issue
- Condition properly recorded on receipt
- Damaged items handled according to condition
- Complete audit trail recorded in asset_movements
- All PHP files pass syntax validation
- Database transactions ensure consistency

---

**Last Updated**: Phase 6 (Views Update) - Store-Based Workflow Complete
**Status**: Ready for Testing
