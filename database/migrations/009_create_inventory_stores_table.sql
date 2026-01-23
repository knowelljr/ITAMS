-- Create inventory_stores table if it doesn't exist
IF NOT EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[dbo].[inventory_stores]') AND type in (N'U'))
BEGIN
    CREATE TABLE inventory_stores (
        id INT PRIMARY KEY IDENTITY(1,1),
        store_code NVARCHAR(50) NOT NULL UNIQUE,
        store_name NVARCHAR(255) NOT NULL,
        location NVARCHAR(255),
        description NVARCHAR(MAX),
        manager_id INT,
        is_active BIT DEFAULT 1,
        created_at DATETIME DEFAULT GETDATE(),
        updated_at DATETIME DEFAULT GETDATE(),
        FOREIGN KEY (manager_id) REFERENCES users(id)
    );
    
    PRINT 'Table inventory_stores created successfully';
END
ELSE
BEGIN
    PRINT 'Table inventory_stores already exists';
END;
