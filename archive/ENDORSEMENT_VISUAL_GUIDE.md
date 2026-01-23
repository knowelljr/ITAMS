# Asset Receive Form - Visual Guide

## Receive Modal Layout

### Initial State (Department Default)

```
┌─────────────────────────────────────────────────────────────────┐
│ Receive Asset                                                   │
├─────────────────────────────────────────────────────────────────┤
│                                                                 │
│ Asset: Dell Laptop                                              │
│ Condition upon receipt                                          │
│                                                                 │
│ ┌─────────────────────────────────────────────────────────────┐ │
│ │ Condition *                                                 │ │
│ │ [▼ Good - No issues                                         │ │
│ │  ┌ Minor Damage - Will be repaired                          │ │
│ │  ┌ Major Damage - Will be scrapped                          │ │
│ │  └ Unusable - Dispose immediately                           │ │
│ └─────────────────────────────────────────────────────────────┘ │
│                                                                 │
│ ┌─────────────────────────────────────────────────────────────┐ │
│ │ Receipt Notes                                               │ │
│ │ [                                                           │ │
│ │  Any damage details, issues, or additional notes            │ │
│ │                                                             │ │
│ │ ]                                                           │ │
│ │ Document any damage, missing parts, or concerns             │ │
│ └─────────────────────────────────────────────────────────────┘ │
│                                                                 │
│ ───────────────────────────────────────────────────────────── │
│ [NEW SECTION - ENDORSEMENT]                                     │
│                                                                 │
│ Endorsement / Assignment                                        │
│                                                                 │
│ ┌─────────────────────────────────────────────────────────────┐ │
│ │ Endorse To *                                                │ │
│ │ ◉ Department                    <- Selected (Default)       │ │
│ │   └─ Choose if this asset belongs to the department         │ │
│ │                                                             │ │
│ │ ○ Individual Employee            <- Not Selected            │ │
│ │   └─ Choose if this asset is assigned to a specific emp.   │ │
│ │                                                             │ │
│ │ Choose if this asset belongs to the department or is        │ │
│ │ assigned to a specific employee                             │ │
│ └─────────────────────────────────────────────────────────────┘ │
│                                                                 │
│ ┌─────────────────────────────────────────────────────────────┐ │
│ │ Endorsement Remarks                                         │ │
│ │ [                                                           │ │
│ │  e.g., assigned for project, department pool, etc.          │ │
│ │                                                             │ │
│ │ ]                                                           │ │
│ │ Additional details about the endorsement or assignment      │ │
│ └─────────────────────────────────────────────────────────────┘ │
│                                                                 │
│                                                                 │
│ [Confirm Receipt]  [Cancel]                                    │
│                                                                 │
└─────────────────────────────────────────────────────────────────┘
```

---

### After Selecting "Individual Employee"

```
┌─────────────────────────────────────────────────────────────────┐
│ Receive Asset                                                   │
├─────────────────────────────────────────────────────────────────┤
│                                                                 │
│ Asset: Dell Laptop                                              │
│ Condition upon receipt                                          │
│                                                                 │
│ ┌─────────────────────────────────────────────────────────────┐ │
│ │ Condition *                                                 │ │
│ │ [▼ Good - No issues                                         │ │
│ └─────────────────────────────────────────────────────────────┘ │
│                                                                 │
│ ┌─────────────────────────────────────────────────────────────┐ │
│ │ Receipt Notes                                               │ │
│ │ [                                                           │ │
│ │  Any damage details, issues, or additional notes            │ │
│ │                                                             │ │
│ │ ]                                                           │ │
│ │ Document any damage, missing parts, or concerns             │ │
│ └─────────────────────────────────────────────────────────────┘ │
│                                                                 │
│ ───────────────────────────────────────────────────────────── │
│ [ENDORSEMENT SECTION - UPDATED]                                 │
│                                                                 │
│ Endorsement / Assignment                                        │
│                                                                 │
│ ┌─────────────────────────────────────────────────────────────┐ │
│ │ Endorse To *                                                │ │
│ │ ○ Department                      <- Now Not Selected       │ │
│ │   └─ Choose if this asset belongs to the department         │ │
│ │                                                             │ │
│ │ ◉ Individual Employee             <- Now Selected ★        │ │
│ │   └─ Choose if this asset is assigned to a specific emp.   │ │
│ │                                                             │ │
│ │ Choose if this asset belongs to the department or is        │ │
│ │ assigned to a specific employee                             │ │
│ └─────────────────────────────────────────────────────────────┘ │
│                                                                 │
│ ┌─────────────────────────────────────────────────────────────┐ │
│ │ Employee Number * [NOW VISIBLE & REQUIRED]  ★              │ │
│ │ [Enter employee number_________________]                    │ │
│ │ The employee this asset is being assigned to                │ │
│ └─────────────────────────────────────────────────────────────┘ │
│                                                                 │
│ ┌─────────────────────────────────────────────────────────────┐ │
│ │ Endorsement Remarks                                         │ │
│ │ [                                                           │ │
│ │  e.g., assigned for project, department pool, etc.          │ │
│ │                                                             │ │
│ │ ]                                                           │ │
│ │ Additional details about the endorsement or assignment      │ │
│ └─────────────────────────────────────────────────────────────┘ │
│                                                                 │
│ [Confirm Receipt]  [Cancel]                                    │
│                                                                 │
└─────────────────────────────────────────────────────────────────┘
```

---

## Form Sections Breakdown

### Section 1: Existing Receipt Fields (Unchanged)

```
Condition Selection:
├─ Good - No issues (default)
├─ Minor Damage - Will be repaired
├─ Major Damage - Will be scrapped
└─ Unusable - Dispose immediately

Receipt Notes:
└─ Textarea for damage documentation
```

### Section 2: NEW - Endorsement Type Selection

```
Endorse To (Required):
├─ Department (Default)
│  └─ Asset belongs to receiving department
│     └─ Becomes shared resource for department
└─ Individual Employee
   └─ Asset assigned to specific employee
      └─ Employee number required
```

### Section 3: NEW - Conditional Employee Number Field

```
State 1: Hidden (When Department Selected)
├─ Field ID: employee_field
├─ CSS Class: hidden
└─ Display: None

State 2: Visible (When Individual Selected)
├─ Field ID: employee_field
├─ CSS Class: removed (shows field)
├─ Required: true (makes field required)
└─ Display: Visible with red asterisk (*)
```

### Section 4: NEW - Endorsement Remarks

```
Endorsement Remarks (Optional):
├─ Always visible for both types
├─ Textarea field
├─ Placeholder examples:
│  ├─ "assigned for project"
│  ├─ "department pool"
│  ├─ "personal use"
│  ├─ "backup device"
│  └─ etc.
└─ Provides context for decision-makers
```

---

## Form State Transitions

### State Flow Diagram

```
[Open Receive Modal]
        ↓
┌─────────────────────┐
│ Department Selected │ (Default)
│ ✓ Condition        │
│ ✓ Receipt Notes    │
│ ✓ Remarks          │
│ ✗ Employee Field   │ (Hidden)
└────────┬────────────┘
         │
    User clicks "Individual Employee"
         ↓
┌─────────────────────┐
│ Individual Selected │
│ ✓ Condition        │
│ ✓ Receipt Notes    │
│ ✓ Employee Number  │ (Now visible & required)
│ ✓ Remarks          │
└────────┬────────────┘
         │
    User enters employee #
         ↓
┌─────────────────────┐
│ Ready to Submit     │
│ All required filled │
└────────┬────────────┘
         │
    User clicks "Confirm Receipt"
         ↓
┌─────────────────────┐
│ Processing...       │
│ - Validate fields   │
│ - Save to database  │
│ - Create movements  │
│ - Close modal       │
└────────┬────────────┘
         │
         ↓
[Success Message & Redirect]
```

---

## Validation Flow

### Client-Side Validation (JavaScript)

```
Form Submit Attempt
        ↓
Condition filled? ──NO──> Block Submit
        │
       YES
        ↓
Receipt Notes ok? ──NO──> Block Submit (optional, always ok)
        │
       YES
        ↓
Endorsement type = INDIVIDUAL?
        │
   ┌────┴────┐
   │         │
  YES        NO
   │         │
   ↓         ↓
Check    Allow
Employee  Submit
Number  ────────→ [Form Submitted to Server]
 filled?
   │
┌──┴──┐
│    NO
│    ↓
│ Show Error
│ "Employee number required"
│ Block Submit
│
└──YES
   ↓
Allow Submit
```

### Server-Side Validation (PHP)

```
Server Receives Form
        ↓
Extract Fields:
├─ endorsement_type
├─ endorsed_employee_number
├─ endorsement_remarks
└─ Other fields
        ↓
Validate endorsement_type in ['DEPARTMENT', 'INDIVIDUAL']
        │
   ┌────┴────┐
   │         │
 VALID    INVALID
   │         │
  YES        ↓
   │     Send Error
   │     "Invalid endorsement type"
   │
   ↓
Check if type = 'INDIVIDUAL'
        │
   ┌────┴────┐
   │         │
 YES        NO
   │         │
   ↓         │
Validate  ┌──┘
employee  │
number    │
not empty?│
   │      │
┌──┴──┐   │
│    NO    │
│    ↓     │
│ Send     │
│ Error    │
│ "Emp.#   │
│ required"│
│    ↓     │
│ Return   │
│ NO OK    │
│    ↓     │
  FAIL   ┌─┘
         │
        YES (all ok)
         ↓
Update Database
├─ endorsement_type
├─ endorsed_employee_number
├─ endorsement_remarks
└─ Other fields
         ↓
Create Movement Record
├─ Include endorsement info in reason
└─ Store in asset_movements
         ↓
Return Success
├─ Display success message
└─ Redirect to /assets/receive
```

---

## Example User Interactions

### Scenario A: Department Endorsement (Happy Path)

```
User Action                    → Form State                → Result
────────────────────────────────────────────────────────────────
Click "Receive" button         → Modal opens             → Department selected
                               → Condition: Good
                               → Receipt Notes: empty
                               → Employee: hidden
                               → Remarks: empty

Type receipt notes             → Receipt Notes: filled   → Ready to submit
Type remarks "IT pool"         → Remarks: filled         → Ready to submit

Click "Confirm Receipt"        → Server processes        → Success
                                 all validation passes    → Message shown
                                 data saved              → Modal closes
                                 movements recorded       → Redirected
```

### Scenario B: Individual Endorsement (Happy Path)

```
User Action                    → Form State                → Result
────────────────────────────────────────────────────────────────
Click "Receive" button         → Modal opens             → Department selected
                               → Employee: hidden

Click "Individual Employee"    → Individual: selected    → Employee field visible
                               → Employee: visible      → Field becomes required
                               → Field: focused

Type employee "EMP004"         → Employee: filled       → Validation passes

Type remarks "Project X"       → Remarks: filled        → Ready to submit

Click "Confirm Receipt"        → Server processes        → Success
                                 all validations pass     → Message shown
                                 data saved              → Modal closes
                                 movements recorded       → Redirected
```

### Scenario C: Individual Endorsement (Validation Error)

```
User Action                    → Form State                → Result
────────────────────────────────────────────────────────────────
Click "Individual Employee"    → Individual: selected    → Employee field visible
                               → Employee: visible      → Field required

Click "Confirm Receipt"        → JavaScript checks      → Validation error
WITHOUT filling employee#      → Employee: EMPTY       → Red error banner
                               → Form open             → "Employee # required"
                               
Type employee "EMP005"         → Employee: filled       → Error disappears

Click "Confirm Receipt"        → Server processes        → Success
                                 validation passes       → Proceeds normally
```

---

## CSS Classes Used

```css
/* Tailwind CSS Classes Applied */

/* Hidden State (Employee Field Initially) */
.hidden {
    display: none;
}

/* Form Fields */
.w-full          /* Full width */
.px-4            /* Horizontal padding */
.py-2            /* Vertical padding */
.border          /* Border */
.border-gray-300 /* Gray border */
.rounded-lg      /* Rounded corners */
.focus:outline-none
.focus:ring-2    /* Focus ring on input */
.focus:ring-blue-500

/* Labels */
.block           /* Display block */
.text-sm         /* Small font */
.font-medium     /* Medium weight */
.text-gray-700   /* Dark gray text */
.mb-2            /* Margin bottom */

/* Containers */
.mb-4            /* Margin bottom 4 units */
.space-y-2       /* Space between radio buttons */

/* Radio Buttons */
.w-4 .h-4        /* Size */
.text-blue-600   /* Blue color */
.cursor-pointer  /* Pointer cursor */

/* Buttons */
.bg-green-600    /* Green background */
.text-white      /* White text */
.hover:bg-green-700 /* Darker on hover */
.rounded         /* Rounded */
.flex-1          /* Take equal space */
.gap-2           /* Gap between buttons */
```

---

## Summary

The receive form now provides:

✅ **Department Endorsement**
- Default option
- No employee field needed
- Remarks optional
- Quick selection for shared assets

✅ **Individual Endorsement**
- Requires employee number
- Shows conditional field
- Remarks optional
- Specific assignment tracking

✅ **Validation**
- Client-side shows/hides fields
- Server-side validates all inputs
- Clear error messages
- Form prevents invalid submissions

✅ **Data Capture**
- Endorsement type stored
- Employee number stored (if individual)
- Remarks stored for context
- Full audit trail in movements

