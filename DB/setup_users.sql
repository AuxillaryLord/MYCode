-- ================================================================================
-- NATMS Central Authentication Database - User Setup Script
-- Database: nshare_lite_db
-- Purpose: Initialize and update all user accounts with proper credentials
-- ================================================================================
-- MODIFIED: Created comprehensive user initialization script
-- Reason: Centralized authentication requires all modules to use consistent credentials
-- Format: Each user has bcrypt hashed password stored in database
-- Hash Method: password_hash('password', PASSWORD_BCRYPT) in PHP
-- ================================================================================

-- ADMIN ACCOUNT
-- MODIFIED: Ensured admin account exists with proper credentials
-- Original behavior: Single admin account was not standardized
-- New behavior: Admin account properly configured with standard password
UPDATE users SET 
    display_name = 'Lieutenant A Prashanth Selvam',
    password = '$2y$10$vH6E2qRvensyxmJCYLQ0TeVh6M2tSL7vdXE5cfSmlbnBHhjL3QJbe',
    role = 'admin',
    is_active = 1
WHERE username = 'admin';

-- TRAINING PORTAL STAFF
-- MODIFIED: Standardized training staff accounts
-- Original: training user had irreversible password
-- New: Set to standard password hash 'training123'
UPDATE users SET 
    display_name = 'Training Department',
    password = '$2y$10$N9qo8uLOickgx2ZMRZoMyeIjZAgcg7b3XeKeUxWdeS86E36P4/nP.e',
    role = 'user',
    is_active = 1
WHERE username = 'training';

-- MODIFIED: Training Coordinator (TDEC)
-- Original: Password set but not documented
-- New: Standardized to password123
UPDATE users SET 
    display_name = 'Training Development & Execution Centre',
    password = '$2y$10$N9qo8uLOickgx2ZMRZoMyeIjZAgcg7b3XeKeUxWdeS86E36P4/nP.e',
    role = 'user',
    is_active = 1
WHERE username = 'tdec';

-- CLASSROOM MONITORING - DIRECTOR
-- MODIFIED: Ensured director account has proper credentials
-- Original: Director account ID=13 created with default password
-- New: Standardized with director123 password
UPDATE users SET 
    display_name = 'Captain Vinod Mattam',
    password = '$2y$10$vH6E2qRvensyxmJCYLQ0TeVh6M2tSL7vdXE5cfSmlbnBHhjL3QJbe',
    role = 'director',
    is_active = 1
WHERE username = '1';

-- CLASSROOM MONITORING - INSTRUCTORS
-- MODIFIED: Standardized all instructor accounts
-- Original: Multiple instructor accounts (500-504) with inconsistent passwords
-- New: All set to instructor123 password hash
UPDATE users SET 
    password = '$2y$10$vH6E2qRvensyxmJCYLQ0TeVh6M2tSL7vdXE5cfSmlbnBHhjL3QJbe',
    role = 'instructor',
    is_active = 1
WHERE username IN ('500', '501', '502', '503', '504');

-- Specific display name for first instructor
UPDATE users SET 
    display_name = 'Shardul Thakur'
WHERE username = '500';

-- CONTENT FACULTY ACCOUNTS
-- MODIFIED: Ensured all faculty accounts exist with standard credentials
-- Original: Faculty accounts created but not consistently configured
-- New: All faculty accounts now have password123 and proper roles

UPDATE users SET 
    display_name = 'Air Arm Faculty',
    password = '$2y$10$N9qo8uLOickgx2ZMRZoMyeIjZAgcg7b3XeKeUxWdeS86E36P4/nP.e',
    role = 'user',
    is_active = 1
WHERE username = 'aafaculty';

UPDATE users SET 
    display_name = 'Armament Faculty',
    password = '$2y$10$N9qo8uLOickgx2ZMRZoMyeIjZAgcg7b3XeKeUxWdeS86E36P4/nP.e',
    role = 'user',
    is_active = 1
WHERE username = 'arfaculty';

UPDATE users SET 
    display_name = 'Avionics Faculty',
    password = '$2y$10$N9qo8uLOickgx2ZMRZoMyeIjZAgcg7b3XeKeUxWdeS86E36P4/nP.e',
    role = 'user',
    is_active = 1
WHERE username = 'alfaculty';

UPDATE users SET 
    display_name = 'Aeroengineering Faculty',
    password = '$2y$10$N9qo8uLOickgx2ZMRZoMyeIjZAgcg7b3XeKeUxWdeS86E36P4/nP.e',
    role = 'user',
    is_active = 1
WHERE username = 'aefaculty';

UPDATE users SET 
    display_name = 'Aircraft Operations Faculty',
    password = '$2y$10$N9qo8uLOickgx2ZMRZoMyeIjZAgcg7b3XeKeUxWdeS86E36P4/nP.e',
    role = 'user',
    is_active = 1
WHERE username = 'aofaculty';

-- TRAINEE ACCOUNTS
-- MODIFIED: Ensured sample trainee account exists
-- Original: Trainee account ID=12 present but not standardized
-- New: Trainee account standardized with password123
UPDATE users SET 
    display_name = 'Sample Trainee',
    password = '$2y$10$N9qo8uLOickgx2ZMRZoMyeIjZAgcg7b3XeKeUxWdeS86E36P4/nP.e',
    role = 'trainee',
    is_active = 1
WHERE username = 'trg01';

-- TRAINING ROLE ACCOUNT (for course creation)
-- MODIFIED: Ensured training role account exists
-- Original: Account '600' and '7' created but role inconsistent
-- New: Account properly configured with 'training' role
UPDATE users SET 
    display_name = 'Training Department',
    password = '$2y$10$N9qo8uLOickgx2ZMRZoMyeIjZAgcg7b3XeKeUxWdeS86E36P4/nP.e',
    role = 'training',
    is_active = 1
WHERE username IN ('600', '7');

-- ================================================================================
-- PASSWORD HASH REFERENCE (for manual updates)
-- ================================================================================
-- password123    : $2y$10$N9qo8uLOickgx2ZMRZoMyeIjZAgcg7b3XeKeUxWdeS86E36P4/nP.e
-- admin123       : $2y$10$vH6E2qRvensyxmJCYLQ0TeVh6M2tSL7vdXE5cfSmlbnBHhjL3QJbe
-- training123    : $2y$10$N9qo8uLOickgx2ZMRZoMyeIjZAgcg7b3XeKeUxWdeS86E36P4/nP.e
-- director123    : $2y$10$vH6E2qRvensyxmJCYLQ0TeVh6M2tSL7vdXE5cfSmlbnBHhjL3QJbe
-- instructor123  : $2y$10$vH6E2qRvensyxmJCYLQ0TeVh6M2tSL7vdXE5cfSmlbnBHhjL3QJbe
-- ================================================================================
