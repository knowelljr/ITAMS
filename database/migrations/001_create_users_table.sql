CREATE TABLE users (
    id INT PRIMARY KEY IDENTITY(1,1),
    name NVARCHAR(100) NOT NULL,
    email NVARCHAR(100) UNIQUE NOT NULL,
    password NVARCHAR(255) NOT NULL,
    employee_number NVARCHAR(50) UNIQUE,
    mobile_number NVARCHAR(20),
    department NVARCHAR(100),
    role NVARCHAR(50) DEFAULT 'REQUESTER',
    status NVARCHAR(20) DEFAULT 'ACTIVE',
    archived BIT DEFAULT 0,
    created_at DATETIME DEFAULT GETDATE(),
    updated_at DATETIME DEFAULT GETDATE()
);