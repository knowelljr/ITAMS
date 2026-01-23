-- Add PO number and quotation file to asset_requests
IF NOT EXISTS (SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='asset_requests' AND COLUMN_NAME='po_number')
    ALTER TABLE asset_requests ADD po_number NVARCHAR(100);

IF NOT EXISTS (SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='asset_requests' AND COLUMN_NAME='quotation_file')
    ALTER TABLE asset_requests ADD quotation_file NVARCHAR(255);
