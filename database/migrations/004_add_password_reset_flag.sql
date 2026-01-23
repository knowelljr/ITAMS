-- Add password_reset_required flag to users table
ALTER TABLE users ADD password_reset_required BIT DEFAULT 0;
