-- Add endorsement tracking columns to asset_issuances table
IF NOT EXISTS (SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = 'asset_issuances' AND COLUMN_NAME = 'endorsement_type')
BEGIN
    ALTER TABLE asset_issuances
    ADD endorsement_type NVARCHAR(50), -- DEPARTMENT or INDIVIDUAL
        endorsed_employee_number NVARCHAR(50), -- For individual endorsements
        endorsement_remarks NVARCHAR(MAX); -- Additional remarks about endorsement
    
    PRINT 'Endorsement columns added to asset_issuances table successfully';
END
ELSE
BEGIN
    PRINT 'Endorsement columns already exist in asset_issuances table';
END;
