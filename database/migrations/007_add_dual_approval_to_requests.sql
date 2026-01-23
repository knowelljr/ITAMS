-- Add dual approval workflow to asset_requests table

-- Add columns only if they don't exist
IF NOT EXISTS (SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='asset_requests' AND COLUMN_NAME='request_number')
    ALTER TABLE asset_requests ADD request_number NVARCHAR(50) UNIQUE;

IF NOT EXISTS (SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='asset_requests' AND COLUMN_NAME='department_manager_approval_status')
    ALTER TABLE asset_requests ADD department_manager_approval_status NVARCHAR(50) DEFAULT 'PENDING';

IF NOT EXISTS (SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='asset_requests' AND COLUMN_NAME='department_manager_approved_by')
    ALTER TABLE asset_requests ADD department_manager_approved_by INT FOREIGN KEY REFERENCES users(id);

IF NOT EXISTS (SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='asset_requests' AND COLUMN_NAME='department_manager_approved_at')
    ALTER TABLE asset_requests ADD department_manager_approved_at DATETIME;

IF NOT EXISTS (SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='asset_requests' AND COLUMN_NAME='department_manager_remarks')
    ALTER TABLE asset_requests ADD department_manager_remarks NVARCHAR(MAX);

IF NOT EXISTS (SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='asset_requests' AND COLUMN_NAME='it_manager_approval_status')
    ALTER TABLE asset_requests ADD it_manager_approval_status NVARCHAR(50) DEFAULT 'PENDING';

IF NOT EXISTS (SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='asset_requests' AND COLUMN_NAME='it_manager_approved_by')
    ALTER TABLE asset_requests ADD it_manager_approved_by INT FOREIGN KEY REFERENCES users(id);

IF NOT EXISTS (SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='asset_requests' AND COLUMN_NAME='it_manager_approved_at')
    ALTER TABLE asset_requests ADD it_manager_approved_at DATETIME;

IF NOT EXISTS (SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='asset_requests' AND COLUMN_NAME='it_manager_remarks')
    ALTER TABLE asset_requests ADD it_manager_remarks NVARCHAR(MAX);

IF NOT EXISTS (SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='asset_requests' AND COLUMN_NAME='date_needed')
    ALTER TABLE asset_requests ADD date_needed DATE;

IF NOT EXISTS (SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='asset_requests' AND COLUMN_NAME='asset_name')
    ALTER TABLE asset_requests ADD asset_name NVARCHAR(255);

IF NOT EXISTS (SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='asset_requests' AND COLUMN_NAME='asset_category')
    ALTER TABLE asset_requests ADD asset_category NVARCHAR(100);

IF NOT EXISTS (SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='asset_requests' AND COLUMN_NAME='quantity_requested')
    ALTER TABLE asset_requests ADD quantity_requested INT;

-- Add check constraints only if they don't exist
IF NOT EXISTS (SELECT * FROM INFORMATION_SCHEMA.TABLE_CONSTRAINTS WHERE CONSTRAINT_NAME='CHK_dept_mgr_approval')
    ALTER TABLE asset_requests ADD CONSTRAINT CHK_dept_mgr_approval CHECK (department_manager_approval_status IN ('PENDING', 'APPROVED', 'REJECTED'));

IF NOT EXISTS (SELECT * FROM INFORMATION_SCHEMA.TABLE_CONSTRAINTS WHERE CONSTRAINT_NAME='CHK_it_mgr_approval')
    ALTER TABLE asset_requests ADD CONSTRAINT CHK_it_mgr_approval CHECK (it_manager_approval_status IN ('PENDING', 'APPROVED', 'REJECTED'));

-- Update status check constraint - drop old one and add new one
BEGIN
    IF EXISTS (SELECT * FROM INFORMATION_SCHEMA.TABLE_CONSTRAINTS WHERE CONSTRAINT_NAME='CHK_request_status')
        ALTER TABLE asset_requests DROP CONSTRAINT CHK_request_status;
END;

IF NOT EXISTS (SELECT * FROM INFORMATION_SCHEMA.TABLE_CONSTRAINTS WHERE CONSTRAINT_NAME='CHK_request_status')
    ALTER TABLE asset_requests ADD CONSTRAINT CHK_request_status CHECK (status IN ('PENDING', 'DEPT_APPROVED', 'FULLY_APPROVED', 'REJECTED', 'ISSUED', 'CANCELLED'));

-- Generate request numbers for existing records without one
UPDATE asset_requests
SET request_number = 'REQ' + FORMAT(GETDATE(), 'yyyyMMdd') + RIGHT('0000' + CAST(id AS NVARCHAR), 4)
WHERE request_number IS NULL;
