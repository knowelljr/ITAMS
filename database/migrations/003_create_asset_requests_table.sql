CREATE TABLE asset_requests (
    id INT PRIMARY KEY IDENTITY(1,1),
    requester_id INT NOT NULL FOREIGN KEY REFERENCES users(id),
    asset_id INT FOREIGN KEY REFERENCES assets(id),
    quantity_requested INT NOT NULL,
    priority NVARCHAR(50) DEFAULT 'normal',
    reason NVARCHAR(MAX),
    status NVARCHAR(50) DEFAULT 'PENDING',
    approved_by INT,
    approved_at DATETIME,
    issued_by INT,
    issued_at DATETIME,
    accepted_at DATETIME,
    created_at DATETIME DEFAULT GETDATE(),
    updated_at DATETIME DEFAULT GETDATE()
);