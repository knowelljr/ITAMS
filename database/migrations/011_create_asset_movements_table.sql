-- Create asset_movements table for complete audit trail if it doesn't exist
IF NOT EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[dbo].[asset_movements]') AND type in (N'U'))
BEGIN
    CREATE TABLE asset_movements (
        id INT PRIMARY KEY IDENTITY(1,1),
        asset_id INT NOT NULL,
        movement_type NVARCHAR(50) NOT NULL, -- RECEIVED, STORED, ISSUED, RETURNED, DAMAGED, TRANSFERRED, COUNTED
        from_location NVARCHAR(255),
        to_location NVARCHAR(255),
        from_store_id INT,
        to_store_id INT,
        quantity INT NOT NULL,
        asset_request_id INT,
        user_id INT, -- Who received/issued/returned the asset
        performed_by INT NOT NULL, -- Who performed the action (IT staff, etc.)
        reason NVARCHAR(MAX),
        notes NVARCHAR(MAX),
        reference_number NVARCHAR(100),
        created_at DATETIME DEFAULT GETDATE(),
        FOREIGN KEY (asset_id) REFERENCES assets(id),
        FOREIGN KEY (from_store_id) REFERENCES inventory_stores(id),
        FOREIGN KEY (to_store_id) REFERENCES inventory_stores(id),
        FOREIGN KEY (asset_request_id) REFERENCES asset_requests(id),
        FOREIGN KEY (user_id) REFERENCES users(id),
        FOREIGN KEY (performed_by) REFERENCES users(id)
    );
    
    -- Create index for faster queries
    CREATE INDEX idx_asset_movements_asset ON asset_movements(asset_id);
    CREATE INDEX idx_asset_movements_type ON asset_movements(movement_type);
    CREATE INDEX idx_asset_movements_user ON asset_movements(user_id);
    CREATE INDEX idx_asset_movements_date ON asset_movements(created_at);
    
    PRINT 'Table asset_movements created successfully with indexes';
END
ELSE
BEGIN
    PRINT 'Table asset_movements already exists';
END;
