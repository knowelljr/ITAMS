# Endorsement Feature - Usage Examples

## Example 1: Department Endorsement

### Scenario
A Dell Laptop is issued to a user from the IT Department. Upon receipt, the IT Manager determines this laptop should be part of the department's asset pool for shared use across multiple team members.

### Workflow

**Issue Stage:**
- Asset: Dell Laptop (Code: DELL-LT-001)
- Issued To: David Rodriguez (IT Manager)
- Issued From: Main Store
- Quantity: 1

**Receive Stage:**
```
Modal Form Displayed:
├─ Asset: Dell Laptop
├─ Condition: GOOD ✓ (selected)
├─ Receipt Notes: "No visible damage, all accessories included"
├─ Endorsement Type: DEPARTMENT ✓ (selected)
│  └─ Employee field: (hidden - not applicable)
├─ Endorsement Remarks: "Added to IT dept shared pool for project teams"
└─ Confirm Receipt [Submit]
```

**Database Result:**
```sql
asset_issuances record:
{
  id: 1001,
  asset_id: 5,
  asset_code: "DELL-LT-001",
  status: "RECEIVED",
  condition_on_receipt: "GOOD",
  receipt_notes: "No visible damage, all accessories included",
  endorsement_type: "DEPARTMENT",
  endorsed_employee_number: NULL,
  endorsement_remarks: "Added to IT dept shared pool for project teams",
  created_at: "2026-01-22 14:30:00"
}
```

**Audit Trail Entry:**
```
Movement Record:
Movement Type: RECEIVED
Asset: Dell Laptop (DELL-LT-001)
Quantity: 1
Reason: "Asset received by requester - Condition: GOOD - Endorsed to Department"
Timestamp: 2026-01-22 14:30:00
Performed By: David Rodriguez (ID: 5)
```

---

## Example 2: Individual Endorsement - Project Assignment

### Scenario
A Mechanical Keyboard is issued to a user. Upon receipt, the user assigns it to a specific employee (Michael Chen, Employee #EMP004) for an ongoing development project.

### Workflow

**Issue Stage:**
- Asset: Mechanical Keyboard (Code: KBD-MCH-015)
- Issued To: Sarah Johnson (Project Lead)
- Issued From: Branch Store
- Quantity: 1

**Receive Stage:**
```
Modal Form Displayed:
├─ Asset: Mechanical Keyboard
├─ Condition: GOOD ✓ (selected)
├─ Receipt Notes: "In good working order"
├─ Endorsement Type: INDIVIDUAL ✓ (selected)
│  └─ Employee Number: [EMP004________________] ✓ (visible, required)
├─ Endorsement Remarks: "Assigned to Michael Chen for Q1 development project"
└─ Confirm Receipt [Submit]
```

**Database Result:**
```sql
asset_issuances record:
{
  id: 1002,
  asset_id: 8,
  asset_code: "KBD-MCH-015",
  status: "RECEIVED",
  condition_on_receipt: "GOOD",
  receipt_notes: "In good working order",
  endorsement_type: "INDIVIDUAL",
  endorsed_employee_number: "EMP004",
  endorsement_remarks: "Assigned to Michael Chen for Q1 development project",
  created_at: "2026-01-22 15:45:00"
}
```

**Audit Trail Entry:**
```
Movement Record:
Movement Type: RECEIVED
Asset: Mechanical Keyboard (KBD-MCH-015)
Quantity: 1
Reason: "Asset received by requester - Condition: GOOD - Endorsed to Employee: EMP004"
Timestamp: 2026-01-22 15:45:00
Performed By: Sarah Johnson (ID: 12)
```

---

## Example 3: Individual Endorsement - Damaged Item

### Scenario
A Dell Monitor is issued but arrives with minor damage. Upon receipt, the user assigns it to the IT Department's repair team (Employee #EMP007 - Repair Specialist) for assessment and repair.

### Workflow

**Issue Stage:**
- Asset: Dell Monitor (Code: MON-DELL-032)
- Issued To: Robert Mills (Department Manager)
- Issued From: Central Warehouse
- Quantity: 3

**Receive Stage:**
```
Modal Form Displayed:
├─ Asset: Dell Monitor
├─ Condition: MINOR_DAMAGE ✓ (selected)
├─ Receipt Notes: "Screen appears to have dust inside bezel, will need cleaning"
├─ Endorsement Type: INDIVIDUAL ✓ (selected)
│  └─ Employee Number: [EMP007________________] ✓ (visible, required)
├─ Endorsement Remarks: "Assigned to Repair Team for inspection and cleaning"
└─ Confirm Receipt [Submit]
```

**Database Result:**
```sql
asset_issuances record:
{
  id: 1003,
  asset_id: 6,
  asset_code: "MON-DELL-032",
  status: "RECEIVED",
  condition_on_receipt: "MINOR_DAMAGE",
  receipt_notes: "Screen appears to have dust inside bezel, will need cleaning",
  endorsement_type: "INDIVIDUAL",
  endorsed_employee_number: "EMP007",
  endorsement_remarks: "Assigned to Repair Team for inspection and cleaning",
  created_at: "2026-01-22 16:20:00"
}
```

**Audit Trail Entry:**
```
Movement Record:
Movement Type: RECEIVED
Asset: Dell Monitor (MON-DELL-032)
Quantity: 3
Reason: "Asset received by requester - Condition: MINOR_DAMAGE - Endorsed to Employee: EMP007"
Timestamp: 2026-01-22 16:20:00
Performed By: Robert Mills (ID: 8)

Additional Movement (Damage Tracking):
Movement Type: DAMAGED
Asset: Dell Monitor (MON-DELL-032)
Quantity: 3
Reason: "Damaged item from issuance - MINOR_DAMAGE"
To Store: Central Warehouse
```

---

## Example 4: Department Endorsement - Multi-Unit Receipt

### Scenario
5 USB Cables are issued to a support team. Upon receipt, all cables are endorsed to the Support Department as a shared resource to be distributed as needed.

### Workflow

**Issue Stage:**
- Asset: USB Type-C Cable (Code: USB-C-CABLE)
- Issued To: Lisa Wong (Support Manager)
- Issued From: Main Store
- Quantity: 5

**Receive Stage:**
```
Modal Form Displayed:
├─ Asset: USB Type-C Cable (Qty: 5)
├─ Condition: GOOD ✓ (selected)
├─ Receipt Notes: "All cables inspected and functioning properly"
├─ Endorsement Type: DEPARTMENT ✓ (selected)
│  └─ Employee field: (hidden - not applicable)
├─ Endorsement Remarks: "Stock for support team's equipment maintenance"
└─ Confirm Receipt [Submit]
```

**Database Result:**
```sql
asset_issuances record:
{
  id: 1004,
  asset_id: 15,
  asset_code: "USB-C-CABLE",
  status: "RECEIVED",
  condition_on_receipt: "GOOD",
  receipt_notes: "All cables inspected and functioning properly",
  endorsement_type: "DEPARTMENT",
  endorsed_employee_number: NULL,
  endorsement_remarks: "Stock for support team's equipment maintenance",
  created_at: "2026-01-22 17:00:00"
}
```

**Audit Trail Entry:**
```
Movement Record:
Movement Type: RECEIVED
Asset: USB Type-C Cable (USB-C-CABLE)
Quantity: 5
Reason: "Asset received by requester - Condition: GOOD - Endorsed to Department"
Timestamp: 2026-01-22 17:00:00
Performed By: Lisa Wong (ID: 15)
```

---

## Form Validation Examples

### Example A: Validation Failure - Individual without Employee Number

**User Action:**
1. Opens receive modal
2. Selects "Individual Employee" radio button
3. Employee number field appears and becomes required (red asterisk shows)
4. Tries to submit form WITHOUT entering employee number

**System Response:**
```
Error Message: "Employee number is required when endorsing to an individual"
Modal remains open
Form NOT submitted
User must enter employee number before proceeding
```

---

### Example B: Successful Submission - Individual with Employee Number

**User Action:**
1. Opens receive modal
2. Selects "Individual Employee" radio button
3. Employee number field appears
4. Enters "EMP004"
5. Adds endorsement remarks: "For training purposes"
6. Clicks "Confirm Receipt"

**System Response:**
```
✓ All validations pass
✓ Data saved to database
✓ Movement record created with employee info
✓ Success message displayed
✓ User redirected to /assets/receive
```

---

## Reporting & Audit Scenarios

### Finding All Assets Endorsed to Specific Employee

```sql
SELECT 
    ai.id,
    a.asset_code,
    a.name as asset_name,
    ai.endorsed_employee_number,
    ai.endorsement_remarks,
    ai.created_at
FROM asset_issuances ai
JOIN assets a ON ai.asset_id = a.id
WHERE ai.endorsement_type = 'INDIVIDUAL'
  AND ai.endorsed_employee_number = 'EMP004'
  AND ai.status = 'RECEIVED'
ORDER BY ai.created_at DESC;
```

**Result**: All assets currently assigned to employee EMP004

---

### Department Asset Pool Report

```sql
SELECT 
    ai.id,
    a.asset_code,
    a.name as asset_name,
    a.category,
    ai.condition_on_receipt,
    ai.endorsement_remarks,
    COUNT(*) as quantity,
    ai.created_at
FROM asset_issuances ai
JOIN assets a ON ai.asset_id = a.id
WHERE ai.endorsement_type = 'DEPARTMENT'
  AND ai.status = 'RECEIVED'
GROUP BY ai.id, a.asset_code, a.name, a.category, ai.condition_on_receipt, ai.endorsement_remarks, ai.created_at
ORDER BY ai.created_at DESC;
```

**Result**: All assets in department pools

---

## Business Benefits

1. **Clear Ownership Tracking**
   - Knows exactly which assets belong to departments vs. individuals
   - Enables targeted inventory audits

2. **Accountability**
   - Individual endorsements create clear accountability
   - Employee number links asset to specific person

3. **Flexible Asset Distribution**
   - Department endorsements allow flexible team-based sharing
   - Individual endorsements ensure dedicated resources

4. **Rich Audit Trail**
   - Endorsement info stored in every asset lifecycle entry
   - Remarks provide context for decision-making and audits

5. **Compliance & Reporting**
   - Easy to generate "assets assigned to employee X" reports
   - Easy to generate "department asset pools" reports
   - Full history of who endorsed what and when

