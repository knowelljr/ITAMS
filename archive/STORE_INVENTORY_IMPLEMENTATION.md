# Store-Based Inventory Management System Implementation

## Overview
The ITAMS system has been enhanced with a centralized store/warehouse inventory management system. All asset issuances now flow through IT-managed stores rather than directly from the general assets table.

## Key Benefits

### 1. **Complete Audit Trail**
- Every asset movement is tracked with timestamps and performer information
- Movements include: RECEIVED, STORED, ISSUED, RETURNED, DAMAGED, TRANSFERRED, COUNTED
- Full history visible for compliance and reconciliation

### 2. **Clear Accountability**
- Each store has a designated manager
- Clear separation of asset location and status
- Know exactly where each asset is and who handled it

### 3. **Better Control**
- Prevent over-issuance with explicit store stock checks
- Handle damaged items separately (MINOR_DAMAGE, MAJOR_DAMAGE, UNUSABLE)
- Support multiple store locations

### 4. **Inventory Accuracy**
- Store inventory tracks: Available, Reserved, Damaged quantities
- Easy stock reconciliation
- Prevent system/physical count mismatches

## Architecture

### New Tables

#### `inventory_stores`
```sql
- id (Primary Key)
- store_code (NVARCHAR(50), UNIQUE)
- store_name (NVARCHAR(255))
- location (NVARCHAR(255))
- description (NVARCHAR(MAX))
- manager_id (Foreign Key → users)
- is_active (BIT)
- created_at, updated_at
```

Purpose: Define physical store/warehouse locations and their managers.

#### `store_inventory`
```sql
- id (Primary Key)
- store_id (Foreign Key → inventory_stores)
- asset_id (Foreign Key → assets)
- quantity_available (INT)
- quantity_reserved (INT)
- quantity_damaged (INT)
- last_counted_at (DATETIME)
- created_at, updated_at
- UNIQUE(store_id, asset_id)
```

Purpose: Track exact quantities of each asset at each store.

#### `asset_movements`
```sql
- id (Primary Key)
- asset_id (Foreign Key → assets)
- movement_type (NVARCHAR(50)): RECEIVED, STORED, ISSUED, RETURNED, DAMAGED, TRANSFERRED, COUNTED
- from_location (NVARCHAR(255))
- to_location (NVARCHAR(255))
- from_store_id (Foreign Key → inventory_stores)
- to_store_id (Foreign Key → inventory_stores)
- quantity (INT)
- asset_request_id (Foreign Key → asset_requests)
- user_id (Foreign Key → users) - Who received/used the asset
- performed_by (Foreign Key → users) - Who performed the action
- reason (NVARCHAR(MAX))
- notes (NVARCHAR(MAX))
- reference_number (NVARCHAR(100))
- created_at (DATETIME)
- Indexes: asset_id, movement_type, user_id, created_at
```

Purpose: Complete audit trail of all asset movements for compliance and analysis.

#### Enhanced `asset_issuances`
New columns added:
- `issued_from_store_id` - Which store the asset was issued from
- `issued_by_name` - Name of the IT staff member who issued
- `condition_on_receipt` - GOOD, MINOR_DAMAGE, MAJOR_DAMAGE, UNUSABLE
- `receipt_notes` - Notes captured when asset was received
- `received_at_location` - Where asset was received (typically USER_INVENTORY)

## Asset Flow

### 1. Asset Reception (IT Staff → Store)
```
Physical asset arrives
    ↓
IT staff creates receipt record
    ↓
Asset added to store_inventory (quantity_available)
    ↓
Movement recorded: type=RECEIVED, from_location=null, to_store_id=store_id
```

### 2. Asset Storage (Store Ready for Issue)
```
Asset in store inventory
    ↓
Status: quantity_available = X
    ↓
Movement recorded: type=STORED (when stock arrives)
```

### 3. Asset Issuance (Store → Requester)
```
IT Staff selects asset from store inventory
    ↓
Check store_inventory.quantity_available >= requested
    ↓
Create issuance record (issued_from_store_id recorded)
    ↓
Deduct from store_inventory (quantity_available -= qty)
    ↓
Movement recorded: type=ISSUED, user_id=recipient, from_store_id=store_id
    ↓
Requester receives asset
```

### 4. Asset Receipt (Requester Confirms)
```
Requester receives asset from IT Staff
    ↓
Requester selects condition: GOOD / MINOR_DAMAGE / MAJOR_DAMAGE / UNUSABLE
    ↓
Update issuance: status=RECEIVED, condition_on_receipt=X, receipt_notes=notes
    ↓
Movement recorded: type=RECEIVED, condition noted
    ↓
If GOOD: Asset with requester (inventory tracked via issuance)
    ↓
If MINOR/MAJOR_DAMAGE: Asset returns to store for repair/disposal
    ↓
If UNUSABLE: Asset marked as damaged in store inventory
```

### 5. Asset Return (Requester → Store)
```
Requester no longer needs asset
    ↓
Returns to IT Staff
    ↓
IT Staff processes return
    ↓
Update issuance: status=RETURNED
    ↓
Add back to store_inventory (quantity_available += qty)
    ↓
Movement recorded: type=RETURNED, to_store_id=store_id
    ↓
Asset available for re-issuance
```

## Store Model Methods

### Core Operations
- `getAllStores()` - Get all active stores
- `getStoreById($id)` - Get store details with manager info
- `getStoreInventory($storeId)` - Get all assets in store with quantities
- `getAvailableQuantity($storeId, $assetId)` - Check if asset available in store

### Inventory Management
- `updateInventory($storeId, $assetId, $quantityChange, $type)` - Adjust quantities
  - Types: 'available', 'reserved', 'damaged'
- `recordMovement($data)` - Log asset movement for audit trail

### Reporting
- `getAssetMovementHistory($assetId, $limit)` - Complete movement history
- `getUserMovementHistory($userId, $limit)` - Assets received by specific user
- `getStoreStats($storeId)` - Store inventory summary and value

## Updated AssetIssuanceController

### Enhanced processIssuance()
✓ Now requires store_id parameter
✓ Checks store inventory instead of global assets table
✓ Records complete movement with audit trail
✓ Validates request fully approved before issue
✓ Supports REQUEST_BASED and UNPLANNED issuances
✓ All operations within transaction for data consistency

### Enhanced processReceipt()
✓ Captures condition status when received
✓ Records receipt movement with condition notes
✓ Handles damaged items:
  - MINOR_DAMAGE: Returns to store for repair
  - MAJOR_DAMAGE/UNUSABLE: Marked as damaged in store
  - GOOD: Stays with requester (ownership via issuance record)
✓ Logs all movements for audit trail
✓ Requester access control preserved

## Migration Execution

Run migrations in order:
1. `009_create_inventory_stores_table.sql` - Create stores table
2. `010_create_store_inventory_table.sql` - Create store inventory tracking
3. `011_create_asset_movements_table.sql` - Create movement audit trail
4. `012_add_store_fields_to_issuances.sql` - Enhance asset_issuances table

**Via Web:** POST to `/admin/run-migrations` (ADMIN only)
**Via CLI:** `php run_migrations.php`

## Next Steps Required

1. **Create/Update Views**
   - Update `assets/issue.php` - Add store selection dropdown
   - Update `assets/receive.php` - Add condition capture and notes
   - Create `stores/list.php` - Store management interface
   - Create `stores/inventory.php` - View store stock levels
   - Create `movements/history.php` - View asset movement audit trail

2. **Add Store Management Routes**
   - `GET /stores` - List stores
   - `GET /stores/:id` - View store details
   - `GET /stores/:id/inventory` - View store inventory
   - `GET /movements/history/:assetId` - View asset movement history
   - `POST /stores/create` - Create new store
   - `POST /stores/update/:id` - Update store
   - `GET /stores/:id/report` - Store inventory report

3. **Create Store/Admin Controllers**
   - StoreController for store CRUD operations
   - MovementController for viewing audit trails

4. **Create Initial Store Data**
   - Add default "Main Store" via migration or fixture
   - Assign to IT Manager
   - Migrate existing assets to main store inventory

5. **Testing**
   - Test complete issuance flow with store selection
   - Test receipt with condition capture
   - Verify audit trail recorded correctly
   - Test damaged item handling
   - Verify access controls work

## Reporting Capabilities

Now possible:
- Asset location tracking: Where is asset X?
- User inventory: What assets did User Y receive?
- Store inventory: What stock levels in Store Z?
- Audit trail: Complete history of asset movement
- Reconciliation: Physical count vs system count
- Damaged items: What assets need repair/disposal?
- Movement analysis: Who issued, when, to whom, why?

## Data Consistency Features

- Transactions ensure all operations complete or all rollback
- Foreign keys prevent orphaned records
- Unique constraints prevent duplicate store inventory records
- Prepared statements prevent SQL injection
- Role-based access control at routes and controller level
- Ownership verification (requesters only see their assets)

## Benefits Over Previous System

| Aspect | Before | After |
|--------|--------|-------|
| Asset Location | Generic "on hand" | Specific store location |
| Accountability | Implicit | Explicit (per IT staff) |
| Audit Trail | Limited | Complete with timestamps |
| Return Handling | Basic status | Condition-aware with tracking |
| Damaged Items | Mixed with available | Separate tracking |
| Over-issuance | Possible | Prevented by store check |
| Reconciliation | Difficult | Easy with store counts |
| Scalability | Single location | Multiple stores supported |
| Compliance | Limited evidence | Full audit trail |

## Conclusion

This store-based inventory system provides enterprise-grade asset management with complete auditability, prevents data inconsistencies, and enables comprehensive reporting. The modular design supports future enhancements like multi-store transfers, asset depreciation tracking, and advanced analytics.
