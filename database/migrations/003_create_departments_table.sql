-- Create departments table
CREATE TABLE departments (
    id INT PRIMARY KEY IDENTITY(1,1),
    department_code NVARCHAR(10) UNIQUE NOT NULL,
    department_name NVARCHAR(80) NOT NULL,
    created_at DATETIME DEFAULT GETDATE(),
    updated_at DATETIME DEFAULT GETDATE()
);

-- Modify users table to link to departments
-- First, drop the old department column if it exists
IF EXISTS (SELECT * FROM sys.columns WHERE object_id = OBJECT_ID(N'users') AND name = 'department')
BEGIN
    ALTER TABLE users DROP COLUMN department;
END

-- Add department_id foreign key
ALTER TABLE users ADD department_id INT;
ALTER TABLE users ADD CONSTRAINT FK_users_departments FOREIGN KEY (department_id) REFERENCES departments(id);
