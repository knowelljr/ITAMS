-- Add employee fields to users table
ALTER TABLE users ADD employee_number NVARCHAR(50) UNIQUE;
ALTER TABLE users ADD mobile_number NVARCHAR(20);
ALTER TABLE users ADD department NVARCHAR(100);
ALTER TABLE users ADD archived BIT DEFAULT 0;
