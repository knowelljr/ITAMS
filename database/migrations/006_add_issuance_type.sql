-- Add issuance_type and approval fields to asset_issuances table
-- Only add columns if they don't exist
IF NOT EXISTS (SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='asset_issuances' AND COLUMN_NAME='issuance_type')
    ALTER TABLE asset_issuances ADD issuance_type NVARCHAR(50) DEFAULT 'REQUEST_BASED';

IF NOT EXISTS (SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='asset_issuances' AND COLUMN_NAME='approval_status')
    ALTER TABLE asset_issuances ADD approval_status NVARCHAR(50) DEFAULT 'APPROVED';

IF NOT EXISTS (SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='asset_issuances' AND COLUMN_NAME='approved_by')
    ALTER TABLE asset_issuances ADD approved_by INT FOREIGN KEY REFERENCES users(id);

IF NOT EXISTS (SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='asset_issuances' AND COLUMN_NAME='approved_at')
    ALTER TABLE asset_issuances ADD approved_at DATETIME;

IF NOT EXISTS (SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='asset_issuances' AND COLUMN_NAME='approval_remarks')
    ALTER TABLE asset_issuances ADD approval_remarks NVARCHAR(MAX);

-- Add check constraints (only if they don't exist)
IF NOT EXISTS (SELECT * FROM INFORMATION_SCHEMA.TABLE_CONSTRAINTS WHERE CONSTRAINT_NAME='CHK_issuance_type')
    ALTER TABLE asset_issuances ADD CONSTRAINT CHK_issuance_type CHECK (issuance_type IN ('REQUEST_BASED', 'UNPLANNED'));

IF NOT EXISTS (SELECT * FROM INFORMATION_SCHEMA.TABLE_CONSTRAINTS WHERE CONSTRAINT_NAME='CHK_approval_status')
    ALTER TABLE asset_issuances ADD CONSTRAINT CHK_approval_status CHECK (approval_status IN ('PENDING_APPROVAL', 'APPROVED', 'REJECTED'));

-- Update existing records to be REQUEST_BASED and APPROVED
UPDATE asset_issuances
SET issuance_type = 'REQUEST_BASED'
WHERE issuance_type IS NULL;

UPDATE asset_issuances
SET approval_status = 'APPROVED'
WHERE approval_status IS NULL;
