CREATE TABLE assets (
    id INT IDENTITY(1,1) PRIMARY KEY,
    asset_code NVARCHAR(255) UNIQUE NOT NULL,
    name NVARCHAR(255) NOT NULL,
    category NVARCHAR(255) NOT NULL,
    serial_number NVARCHAR(255),
    model NVARCHAR(255),
    description NVARCHAR(MAX),
    location NVARCHAR(255),
    cost DECIMAL(18, 2),
    quantity_onhand INT,
    quantity_issued INT,
    optimum_stock INT,
    max_stock INT,
    status NVARCHAR(50),
    created_at DATETIME DEFAULT GETDATE(),
    updated_at DATETIME DEFAULT GETDATE() ON UPDATE GETDATE()
);