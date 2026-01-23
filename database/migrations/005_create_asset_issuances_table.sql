-- Create asset_issuances table for tracking issued assets
CREATE TABLE asset_issuances (
    id INT PRIMARY KEY IDENTITY(1,1),
    asset_request_id INT FOREIGN KEY REFERENCES asset_requests(id),
    asset_id INT NOT NULL FOREIGN KEY REFERENCES assets(id),
    issued_to_user_id INT NOT NULL FOREIGN KEY REFERENCES users(id),
    issued_by_user_id INT NOT NULL FOREIGN KEY REFERENCES users(id),
    quantity_issued INT NOT NULL,
    gatepass_number NVARCHAR(50) UNIQUE,
    qr_code NVARCHAR(MAX),
    purpose NVARCHAR(MAX),
    expected_return_date DATE,
    actual_return_date DATE,
    security_out_signature NVARCHAR(255),
    security_out_datetime DATETIME,
    security_in_signature NVARCHAR(255),
    security_in_datetime DATETIME,
    status NVARCHAR(50) DEFAULT 'ISSUED',
    remarks NVARCHAR(MAX),
    created_at DATETIME DEFAULT GETDATE(),
    updated_at DATETIME DEFAULT GETDATE()
);

-- Create asset_receipts table for tracking returns
CREATE TABLE asset_receipts (
    id INT PRIMARY KEY IDENTITY(1,1),
    asset_issuance_id INT FOREIGN KEY REFERENCES asset_issuances(id),
    asset_id INT NOT NULL FOREIGN KEY REFERENCES assets(id),
    returned_by_user_id INT NOT NULL FOREIGN KEY REFERENCES users(id),
    received_by_user_id INT NOT NULL FOREIGN KEY REFERENCES users(id),
    quantity_returned INT NOT NULL,
    condition_status NVARCHAR(50),
    remarks NVARCHAR(MAX),
    created_at DATETIME DEFAULT GETDATE()
);
