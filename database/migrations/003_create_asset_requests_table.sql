CREATE TABLE asset_requests (
    id INT PRIMARY KEY IDENTITY(1,1),
    requester_id INT FOREIGN KEY REFERENCES users(id),
    asset_id INT FOREIGN KEY REFERENCES assets(id),
    quantity_requested INT NOT NULL,
    priority VARCHAR(50) NOT NULL,
    reason TEXT,
    status VARCHAR(20) NOT NULL,
    approved_by INT,
    approved_at DATETIME,
    issued_by INT,
    issued_at DATETIME,
    accepted_by INT,
    accepted_at DATETIME,
    created_at DATETIME DEFAULT GETDATE(),
    updated_at DATETIME DEFAULT GETDATE() ON UPDATE GETDATE()
);
