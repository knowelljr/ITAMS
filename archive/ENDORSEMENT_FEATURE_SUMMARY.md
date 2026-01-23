# Asset Receive Process Enhancement - Endorsement Feature

## Overview
Enhanced the asset receive process to include comprehensive endorsement tracking. When receiving an asset, users can now specify whether the asset will be:
1. **Department Level** - Asset belongs to the receiving department's asset pool
2. **Individual Level** - Asset is assigned to a specific employee (requires employee number)

Additionally, a remarks field captures context about the endorsement/assignment.

## Changes Made

### 1. Database Schema Update
**Migration File**: `database/migrations/013_add_endorsement_fields_to_issuances.sql`

Added 3 new columns to `asset_issuances` table:
- `endorsement_type` (NVARCHAR(50)) - Stores "DEPARTMENT" or "INDIVIDUAL"
- `endorsed_employee_number` (NVARCHAR(50)) - Employee number when assigned to individual
- `endorsement_remarks` (NVARCHAR(MAX)) - Additional context about the endorsement

**Migration Status**: ✅ Successfully applied

### 2. Form Updates
**File**: `resources/views/assets/receive.php`

#### New Form Sections Added:

**A. Endorsement Type Selection**
- Radio button group with two options:
  - "Department" (default)
  - "Individual Employee"
- Users select where/to whom the asset is being endorsed

**B. Conditional Employee Number Field**
- Only appears when "Individual Employee" is selected
- Field becomes required when Individual is selected
- Placeholder: "Enter employee number"
- JavaScript handles show/hide logic

**C. Endorsement Remarks Box**
- Textarea field for additional context
- Examples provided: "assigned for project", "department pool", "personal use", etc.
- Optional field
- Appears for both department and individual endorsements

#### JavaScript Enhancements:
- `toggleEndorsementFields()` - Shows/hides employee field based on endorsement type
- `openReceiveModal()` - Resets all endorsement fields when opening modal
- Form validation ensures employee number is provided when "Individual" is selected

### 3. Controller Updates
**File**: `app/Controllers/AssetIssuanceController.php`

#### Enhanced `processReceipt()` Method:

**Input Processing**:
```php
$endorsementType = $_POST['endorsement_type'] ?? 'DEPARTMENT';
$endorsedEmployeeNumber = $_POST['endorsed_employee_number'] ?? '';
$endorsementRemarks = $_POST['endorsement_remarks'] ?? '';
```

**Validation Logic**:
- Validates endorsement_type is either "DEPARTMENT" or "INDIVIDUAL"
- Ensures employee_number is provided when endorsement_type is "INDIVIDUAL"
- Provides clear error messages for validation failures

**Database Update**:
```sql
UPDATE asset_issuances 
SET endorsement_type = ?,
    endorsed_employee_number = ?,
    endorsement_remarks = ?,
    ... other fields ...
WHERE id = ?
```

**Audit Trail Enhancement**:
- Movement record now includes endorsement information in the reason field
- Examples:
  - "Asset received by requester - Condition: GOOD - Endorsed to Department"
  - "Asset received by requester - Condition: GOOD - Endorsed to Employee: EMP001"

## User Workflow

### Step-by-Step Process:

1. **Navigate to Asset Receipt**
   - User goes to /assets/receive
   - Views list of issued assets ready to be received

2. **Open Receive Modal**
   - Clicks "Receive" button for an asset
   - Modal opens with:
     - Asset name and details
     - Condition selection (Good/Minor Damage/Major Damage/Unusable)
     - Receipt notes textarea
     - **NEW**: Endorsement type selector (Department/Individual)
     - **NEW**: Conditional employee number field
     - **NEW**: Endorsement remarks box

3. **Select Endorsement Type**
   - **Option A - Department**:
     - Radio button "Department" (default)
     - Form shows: Condition, Receipt Notes, Endorsement Remarks
     - Employee field remains hidden
     - Click "Confirm Receipt"
   
   - **Option B - Individual**:
     - Click radio button "Individual Employee"
     - Employee number field appears and becomes required
     - Must enter employee number
     - Can add endorsement remarks for context
     - Click "Confirm Receipt"

4. **Confirmation**
   - Asset is marked as RECEIVED
   - Condition is recorded
   - Endorsement details are stored
   - Movement history records the full transaction with endorsement info
   - User sees success message

## Data Structure

### Asset Issuances Table Schema (Enhanced)

| Column | Type | Purpose |
|--------|------|---------|
| id | INT | Primary key |
| asset_id | INT | FK to assets |
| issued_to_user_id | INT | FK to users (original recipient) |
| status | NVARCHAR(50) | ISSUED → RECEIVED |
| condition_on_receipt | NVARCHAR(50) | GOOD/MINOR_DAMAGE/MAJOR_DAMAGE/UNUSABLE |
| receipt_notes | NVARCHAR(MAX) | Damage/issues documentation |
| **endorsement_type** | **NVARCHAR(50)** | **NEW: DEPARTMENT or INDIVIDUAL** |
| **endorsed_employee_number** | **NVARCHAR(50)** | **NEW: Employee ID if individual** |
| **endorsement_remarks** | **NVARCHAR(MAX)** | **NEW: Context about endorsement** |
| created_at | DATETIME | Record creation |
| updated_at | DATETIME | Last modification |

## Business Logic

### Endorsement Rules:

1. **Department Endorsement**
   - Asset becomes part of department's asset pool
   - Department can redistribute to users as needed
   - Tracked in audit trail as "Endorsed to Department"
   - employee_number field is NULL

2. **Individual Endorsement**
   - Asset is specifically assigned to an employee
   - Employee number must be provided
   - Tracked in audit trail with specific employee ID
   - Endorsement remarks can specify purpose (project, personal use, etc.)

3. **Audit Trail**
   - All endorsement data flows to movement history
   - Movement reason includes endorsement type and employee (if applicable)
   - Provides complete audit trail for asset lifecycle

## API Data Storage

When asset is received, the following data is persisted:

```sql
INSERT INTO asset_movements (
    asset_id, movement_type, quantity, 
    reason, performed_by, reference_number, created_at
) VALUES (
    123, 'RECEIVED', 5,
    'Asset received by requester - Condition: GOOD - Endorsed to Employee: EMP001',
    456, 'RECEIPT_789', GETDATE()
);
```

## Validation Rules

### Client-Side (JavaScript):
- Employee field shows/hides based on selection
- Employee number becomes required when "Individual" is selected

### Server-Side (PHP):
- Endorsement type must be "DEPARTMENT" or "INDIVIDUAL"
- If "INDIVIDUAL": endorsed_employee_number must not be empty
- Condition status must be valid
- Issuance must exist and have status "ISSUED"

### Error Handling:
- "Invalid endorsement type" - endorsement_type not recognized
- "Employee number is required when endorsing to an individual" - Individual selected but no employee number
- "Issuance ID is required" - No issuance ID provided
- "Invalid issuance or already received" - Asset already received or invalid ID

## Testing Checklist

- [ ] Verify modal shows both endorsement options
- [ ] Employee number field hidden by default
- [ ] Employee number field shows when "Individual" selected
- [ ] Employee number field hides when "Department" selected
- [ ] Form validation prevents submit without employee number (Individual mode)
- [ ] Asset receives with Department endorsement stores correctly
- [ ] Asset receives with Individual endorsement stores employee number
- [ ] Endorsement remarks are captured for both types
- [ ] Movement history shows endorsement info in reason field
- [ ] Data persists in database correctly
- [ ] All previous receive functionality still works (condition, receipt notes, returns, etc.)

## Files Modified

1. **`database/migrations/013_add_endorsement_fields_to_issuances.sql`** (NEW)
   - Migration script to add endorsement columns

2. **`resources/views/assets/receive.php`** (UPDATED)
   - Added endorsement type selector
   - Added conditional employee number field
   - Added endorsement remarks textarea
   - Added JavaScript for form control

3. **`app/Controllers/AssetIssuanceController.php`** (UPDATED)
   - Enhanced `processReceipt()` method
   - Added endorsement field validation
   - Added endorsement data to database update
   - Enhanced audit trail with endorsement info

4. **`run_endorsement_migration.php`** (NEW)
   - Migration runner script
   - Verifies columns were added successfully

## Status

✅ **All Changes Implemented and Validated**

- Database migration: ✅ Applied successfully
- Form UI: ✅ Created with conditional logic
- Controller logic: ✅ Updated with validation and storage
- PHP Syntax: ✅ No errors detected
- Database Schema: ✅ Columns verified as created

## Next Steps

1. **Browser Testing**: Navigate to /assets/receive and test the new endorsement workflow
2. **Create Test Case**: Issue an asset, then receive it with both endorsement types
3. **Verify Data**: Check database to confirm endorsement data is stored correctly
4. **Verify Audit Trail**: Confirm movement history shows endorsement information
5. **User Training**: Inform users about the new endorsement selection feature

## Technical Notes

### JavaScript Functionality:
- `toggleEndorsementFields()` is called:
  - On radio button change
  - When modal is opened (to reset to default state)
- Employee field uses `required` attribute dynamically
- Form validation prevents submission without required employee number

### Database Performance:
- New columns are indexed on endorsement_type for quick filtering
- employee_number is searchable for audits
- endorsement_remarks is unlimited text for detailed context

### Backward Compatibility:
- Existing received assets have NULL values in new columns
- Existing workflows not affected
- All previous fields (condition, receipt_notes) continue to work unchanged

