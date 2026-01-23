# Enhanced Asset Receive Process - Implementation Guide

## Quick Summary

The asset receive process has been enhanced to include **Endorsement & Assignment** capabilities. When receiving an asset, users now select whether the asset should be:
- **Department** - Added to department's asset pool
- **Individual** - Assigned to a specific employee (with employee number)

Plus an optional remarks field for context.

---

## What's New

### 1. Updated Receive Form Modal

The receive modal now includes THREE new sections:

**Section A: Endorsement Type Selection**
```
Endorse To * (required)
○ Department (default)
○ Individual Employee
```

**Section B: Employee Number Field (Conditional)**
```
Employee Number * (only shown when "Individual Employee" selected)
[Enter employee number]
```

**Section C: Endorsement Remarks**
```
Endorsement Remarks (optional)
[Textarea for additional context]
```

### 2. Database Enhancements

Three new columns added to `asset_issuances` table:
- `endorsement_type` - "DEPARTMENT" or "INDIVIDUAL"
- `endorsed_employee_number` - Employee ID (when individual)
- `endorsement_remarks` - Context/notes about endorsement

### 3. Validation Rules

- **Employee number required** when "Individual Employee" is selected
- **Endorsement type must** be one of: DEPARTMENT, INDIVIDUAL
- All existing validations still apply (condition, notes, etc.)

---

## How to Use

### Step 1: Navigate to Receive Assets
Go to http://localhost:8000/assets/receive

### Step 2: View Issued Assets
List of all issued assets ready for receipt

### Step 3: Click "Receive" Button
Opens receive modal

### Step 4: Fill Out Receipt Form

#### If Department Endorsement:
1. Select "Department" radio button (default)
2. Choose condition (Good/Minor Damage/Major Damage/Unusable)
3. Add receipt notes if needed
4. Add endorsement remarks like "Department pool" or "Shared resource"
5. Click "Confirm Receipt"

#### If Individual Endorsement:
1. Select "Individual Employee" radio button
2. Employee number field appears → **enter employee ID** (e.g., "EMP004")
3. Choose condition
4. Add receipt notes if needed
5. Add endorsement remarks like "Project assignment" or "Personal use"
6. Click "Confirm Receipt"

### Step 5: Confirmation
- Asset marked as RECEIVED
- Endorsement data stored in database
- Success message displayed
- Asset appears in movement history with endorsement details

---

## Database Migration

**Status**: ✅ Already Applied

The migration file `013_add_endorsement_fields_to_issuances.sql` has been executed successfully.

Three columns added:
- ✓ endorsement_type
- ✓ endorsed_employee_number  
- ✓ endorsement_remarks

---

## File Changes

### Modified Files (3 Total)

#### 1. `resources/views/assets/receive.php`
**Changes**:
- Added endorsement type radio selector (Department/Individual)
- Added conditional employee_number input field
- Added endorsement_remarks textarea
- Added JavaScript `toggleEndorsementFields()` function
- Updated modal open/close handlers

**Lines Added**: ~60 lines

#### 2. `app/Controllers/AssetIssuanceController.php`
**Changes**:
- Enhanced `processReceipt()` method
- Added validation for endorsement_type
- Added validation for employee_number when Individual selected
- Updated database INSERT to store new fields
- Enhanced audit trail with endorsement info

**Lines Modified**: ~30 lines

#### 3. `database/migrations/013_add_endorsement_fields_to_issuances.sql` (NEW)
**Changes**:
- Migration script to add 3 columns to asset_issuances
- Includes column definitions and foreign keys

**Lines**: 15 lines

---

## Technical Details

### Form Behavior

**JavaScript Controls**:
```javascript
toggleEndorsementFields() {
  // Shows/hides employee field based on radio selection
  // Makes field required when Individual is selected
  // Clears field value when Department is selected
}

openReceiveModal() {
  // Resets endorsement to Department (default)
  // Hides employee field
  // Clears all input values
}
```

### Validation Flow

```
Form Submission
├─ Client-Side (JavaScript)
│  └─ If Individual: requires employee_number
└─ Server-Side (PHP)
   ├─ Validate endorsement_type ∈ [DEPARTMENT, INDIVIDUAL]
   ├─ If INDIVIDUAL: validate employee_number not empty
   ├─ Validate condition_status
   ├─ Save to database
   ├─ Create audit trail with endorsement info
   └─ Redirect to success/error
```

### Database Storage

```sql
UPDATE asset_issuances 
SET endorsement_type = 'DEPARTMENT' or 'INDIVIDUAL',
    endorsed_employee_number = 'EMP004' or NULL,
    endorsement_remarks = 'Optional context text',
    ...other fields...
WHERE id = ?
```

---

## Error Handling

### Validation Errors

| Error Message | Cause | Solution |
|--------------|-------|----------|
| "Employee number is required when endorsing to an individual" | Individual selected but no employee number | Enter employee number |
| "Invalid endorsement type" | Server received invalid type | Contact admin if persists |
| "Invalid issuance or already received" | Asset already received | Refresh page |

### Display

- Errors shown as red banner at top of receive form
- Form stays open so user can correct and resubmit
- Specific error message guides user

---

## Audit Trail

Every received asset now creates movement records that include:

```
Reason: "Asset received by requester - Condition: GOOD - Endorsed to Department"
        OR
Reason: "Asset received by requester - Condition: GOOD - Endorsed to Employee: EMP004"
```

This provides complete audit trail showing:
- What asset was received
- In what condition
- Who endorsed it
- To where/whom it was endorsed

---

## Testing Checklist

Use this to verify the feature works:

- [ ] Navigate to /assets/receive and see receive form loads
- [ ] Issue an asset first (go to /assets/issue if none exist)
- [ ] Open receive modal for an issued asset
- [ ] Verify "Department" is selected by default
- [ ] Verify employee number field is hidden initially
- [ ] Select "Individual Employee" radio button
- [ ] Verify employee number field appears
- [ ] Try to submit without employee number - should show error
- [ ] Enter employee number (e.g., "EMP004")
- [ ] Add endorsement remarks (e.g., "Project assignment")
- [ ] Select a condition (e.g., "Good")
- [ ] Click "Confirm Receipt"
- [ ] Verify success message appears
- [ ] Check database that endorsement_type, endorsed_employee_number, endorsement_remarks are stored
- [ ] Check asset_movements table that endorsement info appears in reason field

---

## FAQ

### Q: Can I receive an asset without selecting endorsement type?
**A**: No, endorsement type is required. Department is selected by default.

### Q: Is employee number validated against the database?
**A**: Currently it accepts any text. You can validate against employees table if needed.

### Q: Can I change endorsement after receiving?
**A**: Currently no. You would need to issue and receive again, or modify the database directly (not recommended).

### Q: What happens with the receipts if asset is damaged?
**A**: Condition is recorded separately. Damaged items are still endorsed properly, then marked as damaged in inventory.

### Q: Can multiple people receive the same asset?
**A**: No, once received (status = RECEIVED), it can't be received again unless reissued.

---

## Related Features

- **Asset Issuance**: /assets/issue - Issue assets from stores
- **Asset Receipt**: /assets/receive - Receive and endorse assets (NEW)
- **Movement History**: View all asset movements including endorsement info
- **Inventory Reports**: Filter assets by department vs. individual

---

## Support & Troubleshooting

### Employee Number Field Not Showing?
- Clear browser cache
- Verify JavaScript is enabled
- Check browser console for errors
- Restart PHP server

### Data Not Saving?
- Check database credentials
- Verify migration was applied (check for 3 new columns)
- Check PHP error logs

### Form Not Responding?
- Clear browser cache
- Try incognito window
- Check if JavaScript is enabled
- Verify PHP server is running

---

## Summary

✅ **Implementation Complete**

All components deployed and validated:
- ✓ Database schema updated
- ✓ Form UI enhanced  
- ✓ Controller logic updated
- ✓ Validation rules implemented
- ✓ Audit trail enhanced
- ✓ PHP syntax verified
- ✓ Ready for user testing

Start using the new endorsement feature by navigating to /assets/receive!

