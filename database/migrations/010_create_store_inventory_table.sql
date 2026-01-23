-- Create store_inventory table if it doesn't exist
IF NOT EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[dbo].[store_inventory]') AND type in (N'U'))
BEGIN
    CREATE TABLE store_inventory (
        id INT PRIMARY KEY IDENTITY(1,1),
        store_id INT NOT NULL,
        asset_id INT NOT NULL,
        quantity_available INT DEFAULT 0,
        quantity_reserved INT DEFAULT 0,
        quantity_damaged INT DEFAULT 0,
        last_counted_at DATETIME,
        created_at DATETIME DEFAULT GETDATE(),
        updated_at DATETIME DEFAULT GETDATE(),
        FOREIGN KEY (store_id) REFERENCES inventory_stores(id),
        FOREIGN KEY (asset_id) REFERENCES assets(id),
        UNIQUE (store_id, asset_id)
    );
    
    PRINT 'Table store_inventory created successfully';
END
ELSE
BEGIN
    PRINT 'Table store_inventory already exists';
END;
