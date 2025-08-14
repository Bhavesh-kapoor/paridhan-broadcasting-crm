-- SQL script to add employee fields to users table
-- This script can be run directly on the database if Laravel migrations are not working

-- Add date_of_birth field
ALTER TABLE users ADD COLUMN date_of_birth DATE NULL AFTER address;

-- Add status field with default value
ALTER TABLE users ADD COLUMN status ENUM('active', 'inactive') DEFAULT 'active' AFTER date_of_birth;

-- Add position field
ALTER TABLE users ADD COLUMN position VARCHAR(255) NULL AFTER status;

-- Add salary field
ALTER TABLE users ADD COLUMN salary DECIMAL(10,2) NULL AFTER position;

-- Add hire_date field
ALTER TABLE users ADD COLUMN hire_date DATE NULL AFTER salary;

-- Update existing users to have 'employee' role if they don't have one
-- (This assumes you want to convert existing users to employees)
-- UPDATE users SET role = 'employee' WHERE role IS NULL OR role = '';

-- Verify the changes
DESCRIBE users;
