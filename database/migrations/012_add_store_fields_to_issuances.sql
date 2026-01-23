-- Add store-related columns to asset_issuances table if they don't exist
IF NOT EXISTS (SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = 'asset_issuances' AND COLUMN_NAME = 'issued_from_store_id')
BEGIN
    ALTER TABLE asset_issuances
    ADD issued_from_store_id INT,
        issued_by_name NVARCHAR(255),
        condition_on_receipt NVARCHAR(50), -- GOOD, MINOR_DAMAGE, MAJOR_DAMAGE, UNUSABLE
        receipt_notes NVARCHAR(MAX),
        received_at_location NVARCHAR(255);
    
    ALTER TABLE asset_issuances
    ADD FOREIGN KEY (issued_from_store_id) REFERENCES inventory_stores(id);
    
    PRINT 'Columns added to asset_issuances table successfully';
END
ELSE
BEGIN
    PRINT 'Columns already exist in asset_issuances table';
END;
