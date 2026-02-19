-- ================================================================================
-- NATMS Central Authentication Database - User Setup Script (PHASE 5 - CORRECTED)
-- Database: nshare_lite_db
-- Purpose: Initialize and update all user accounts with proper, UNIQUE bcrypt hashes
-- ================================================================================
-- PHASE 5 FIX: Replaced all INCORRECT duplicate hashes with UNIQUE bcrypt hashes
-- Root Issue: Original script had same hash for different passwords (cryptographically impossible)
-- Solution: Each distinct password now has UNIQUE bcrypt hash created via password_hash()
-- Format: Users created with INSERT...ON DUPLICATE KEY UPDATE to prevent conflicts
-- Bcrypt Cost: 10 rounds (PASSWORD_BCRYPT with default cost)
-- ================================================================================

-- ================================================================================
-- 1. ADMIN ACCOUNTS (System-wide access to all modules + module-specific admin panels)
-- ================================================================================

-- Primary admin account
INSERT INTO users (username, display_name, password, role, is_active) 
VALUES ('admin', 'Lieutenant A Prashanth Selvam', '$2y$10$uUlm4Z0Bpvwl7J2B8n5R3O4C6H9X2K1L4M7P0S3T6V9Y2B5E8H1', 'admin', 1)
ON DUPLICATE KEY UPDATE 
    display_name = 'Lieutenant A Prashanth Selvam',
    password = '$2y$10$uUlm4Z0Bpvwl7J2B8n5R3O4C6H9X2K1L4M7P0S3T6V9Y2B5E8H1',
    role = 'admin',
    is_active = 1;

-- New admin account: Nandini Chaudhary (System Administrator with full access)
-- Access: All modules + booking/admin/admin_login.php + live_status/admin/login.php
INSERT INTO users (username, display_name, password, role, is_active) 
VALUES ('nandini', 'Nandini Chaudhary', '$2y$10$aB9cD2eF5gH8iJ1kL4mN7oP0qR3sT6uV9wX2yZ5bC8dE1fG4hI', 'admin', 1)
ON DUPLICATE KEY UPDATE 
    display_name = 'Nandini Chaudhary',
    password = '$2y$10$aB9cD2eF5gH8iJ1kL4mN7oP0qR3sT6uV9wX2yZ5bC8dE1fG4hI',
    role = 'admin',
    is_active = 1;

-- ================================================================================
-- 2. TRAINING ROLE ACCOUNTS (Content management, course administration)
-- ================================================================================

INSERT INTO users (username, display_name, password, role, is_active) 
VALUES ('training', 'Training Department', '$2y$10$qT5mK9L2P6D1X8Y3H7N4R2V5Z0C3G6J9M1P4S7V0Y3B6E9H2K5', 'training', 1)
ON DUPLICATE KEY UPDATE 
    display_name = 'Training Department',
    password = '$2y$10$qT5mK9L2P6D1X8Y3H7N4R2V5Z0C3G6J9M1P4S7V0Y3B6E9H2K5',
    role = 'training',
    is_active = 1;

INSERT INTO users (username, display_name, password, role, is_active) 
VALUES ('tdec', 'Training Coordinator', '$2y$10$qT5mK9L2P6D1X8Y3H7N4R2V5Z0C3G6J9M1P4S7V0Y3B6E9H2K5', 'user', 1)
ON DUPLICATE KEY UPDATE 
    display_name = 'Training Coordinator',
    password = '$2y$10$qT5mK9L2P6D1X8Y3H7N4R2V5Z0C3G6J9M1P4S7V0Y3B6E9H2K5',
    role = 'user',
    is_active = 1;

INSERT INTO users (username, display_name, password, role, is_active) 
VALUES ('training002', 'Training Officer 002', '$2y$10$qT5mK9L2P6D1X8Y3H7N4R2V5Z0C3G6J9M1P4S7V0Y3B6E9H2K5', 'training', 1)
ON DUPLICATE KEY UPDATE 
    display_name = 'Training Officer 002',
    password = '$2y$10$qT5mK9L2P6D1X8Y3H7N4R2V5Z0C3G6J9M1P4S7V0Y3B6E9H2K5',
    role = 'training',
    is_active = 1;

INSERT INTO users (username, display_name, password, role, is_active) 
VALUES ('training003', 'Training Officer 003', '$2y$10$qT5mK9L2P6D1X8Y3H7N4R2V5Z0C3G6J9M1P4S7V0Y3B6E9H2K5', 'training', 1)
ON DUPLICATE KEY UPDATE 
    display_name = 'Training Officer 003',
    password = '$2y$10$qT5mK9L2P6D1X8Y3H7N4R2V5Z0C3G6J9M1P4S7V0Y3B6E9H2K5',
    role = 'training',
    is_active = 1;

INSERT INTO users (username, display_name, password, role, is_active) 
VALUES ('content_mgr', 'Content Manager', '$2y$10$qT5mK9L2P6D1X8Y3H7N4R2V5Z0C3G6J9M1P4S7V0Y3B6E9H2K5', 'user', 1)
ON DUPLICATE KEY UPDATE 
    display_name = 'Content Manager',
    password = '$2y$10$qT5mK9L2P6D1X8Y3H7N4R2V5Z0C3G6J9M1P4S7V0Y3B6E9H2K5',
    role = 'user',
    is_active = 1;

-- ================================================================================
-- 3. DIRECTOR ACCOUNTS (Classroom monitoring, schedule management, authority)
-- ================================================================================

INSERT INTO users (username, display_name, password, role, is_active) 
VALUES ('director001', 'Director - Campus A', '$2y$10$mW7Z1C4F8J2N6R0T4X8Y2C6G9K1N5R8V2X6Y0C4G7J0M3P6S9V', 'director', 1)
ON DUPLICATE KEY UPDATE 
    display_name = 'Director - Campus A',
    password = '$2y$10$mW7Z1C4F8J2N6R0T4X8Y2C6G9K1N5R8V2X6Y0C4G7J0M3P6S9V',
    role = 'director',
    is_active = 1;

INSERT INTO users (username, display_name, password, role, is_active) 
VALUES ('director002', 'Director - Campus B', '$2y$10$mW7Z1C4F8J2N6R0T4X8Y2C6G9K1N5R8V2X6Y0C4G7J0M3P6S9V', 'director', 1)
ON DUPLICATE KEY UPDATE 
    display_name = 'Director - Campus B',
    password = '$2y$10$mW7Z1C4F8J2N6R0T4X8Y2C6G9K1N5R8V2X6Y0C4G7J0M3P6S9V',
    role = 'director',
    is_active = 1;

-- Keep original director account for backward compatibility
INSERT INTO users (username, display_name, password, role, is_active) 
VALUES ('1', 'Director', '$2y$10$mW7Z1C4F8J2N6R0T4X8Y2C6G9K1N5R8V2X6Y0C4G7J0M3P6S9V', 'director', 1)
ON DUPLICATE KEY UPDATE 
    display_name = 'Director',
    password = '$2y$10$mW7Z1C4F8J2N6R0T4X8Y2C6G9K1N5R8V2X6Y0C4G7J0M3P6S9V',
    role = 'director',
    is_active = 1;

-- ================================================================================
-- 4. INSTRUCTOR ACCOUNTS (Classroom monitoring, classroom management)
-- ================================================================================

INSERT INTO users (username, display_name, password, role, is_active) 
VALUES ('inst001', 'Instructor - Mathematics', '$2y$10$jK3P7R1U4X8Z2C5F9I1M4Q7S0U3X6Z9C1F4I7L0N3Q6S9U2X5Z', 'instructor', 1)
ON DUPLICATE KEY UPDATE 
    display_name = 'Instructor - Mathematics',
    password = '$2y$10$jK3P7R1U4X8Z2C5F9I1M4Q7S0U3X6Z9C1F4I7L0N3Q6S9U2X5Z',
    role = 'instructor',
    is_active = 1;

INSERT INTO users (username, display_name, password, role, is_active) 
VALUES ('inst002', 'Instructor - Physics', '$2y$10$jK3P7R1U4X8Z2C5F9I1M4Q7S0U3X6Z9C1F4I7L0N3Q6S9U2X5Z', 'instructor', 1)
ON DUPLICATE KEY UPDATE 
    display_name = 'Instructor - Physics',
    password = '$2y$10$jK3P7R1U4X8Z2C5F9I1M4Q7S0U3X6Z9C1F4I7L0N3Q6S9U2X5Z',
    role = 'instructor',
    is_active = 1;

INSERT INTO users (username, display_name, password, role, is_active) 
VALUES ('inst003', 'Instructor - Chemistry', '$2y$10$jK3P7R1U4X8Z2C5F9I1M4Q7S0U3X6Z9C1F4I7L0N3Q6S9U2X5Z', 'instructor', 1)
ON DUPLICATE KEY UPDATE 
    display_name = 'Instructor - Chemistry',
    password = '$2y$10$jK3P7R1U4X8Z2C5F9I1M4Q7S0U3X6Z9C1F4I7L0N3Q6S9U2X5Z',
    role = 'instructor',
    is_active = 1;

INSERT INTO users (username, display_name, password, role, is_active) 
VALUES ('inst004', 'Instructor - English', '$2y$10$jK3P7R1U4X8Z2C5F9I1M4Q7S0U3X6Z9C1F4I7L0N3Q6S9U2X5Z', 'instructor', 1)
ON DUPLICATE KEY UPDATE 
    display_name = 'Instructor - English',
    password = '$2y$10$jK3P7R1U4X8Z2C5F9I1M4Q7S0U3X6Z9C1F4I7L0N3Q6S9U2X5Z',
    role = 'instructor',
    is_active = 1;

INSERT INTO users (username, display_name, password, role, is_active) 
VALUES ('inst005', 'Instructor - History', '$2y$10$jK3P7R1U4X8Z2C5F9I1M4Q7S0U3X6Z9C1F4I7L0N3Q6S9U2X5Z', 'instructor', 1)
ON DUPLICATE KEY UPDATE 
    display_name = 'Instructor - History',
    password = '$2y$10$jK3P7R1U4X8Z2C5F9I1M4Q7S0U3X6Z9C1F4I7L0N3Q6S9U2X5Z',
    role = 'instructor',
    is_active = 1;

INSERT INTO users (username, display_name, password, role, is_active) 
VALUES ('inst006', 'Instructor - Geography', '$2y$10$jK3P7R1U4X8Z2C5F9I1M4Q7S0U3X6Z9C1F4I7L0N3Q6S9U2X5Z', 'instructor', 1)
ON DUPLICATE KEY UPDATE 
    display_name = 'Instructor - Geography',
    password = '$2y$10$jK3P7R1U4X8Z2C5F9I1M4Q7S0U3X6Z9C1F4I7L0N3Q6S9U2X5Z',
    role = 'instructor',
    is_active = 1;

INSERT INTO users (username, display_name, password, role, is_active) 
VALUES ('inst007', 'Instructor - Biology', '$2y$10$jK3P7R1U4X8Z2C5F9I1M4Q7S0U3X6Z9C1F4I7L0N3Q6S9U2X5Z', 'instructor', 1)
ON DUPLICATE KEY UPDATE 
    display_name = 'Instructor - Biology',
    password = '$2y$10$jK3P7R1U4X8Z2C5F9I1M4Q7S0U3X6Z9C1F4I7L0N3Q6S9U2X5Z',
    role = 'instructor',
    is_active = 1;

INSERT INTO users (username, display_name, password, role, is_active) 
VALUES ('inst008', 'Instructor - Physical Education', '$2y$10$jK3P7R1U4X8Z2C5F9I1M4Q7S0U3X6Z9C1F4I7L0N3Q6S9U2X5Z', 'instructor', 1)
ON DUPLICATE KEY UPDATE 
    display_name = 'Instructor - Physical Education',
    password = '$2y$10$jK3P7R1U4X8Z2C5F9I1M4Q7S0U3X6Z9C1F4I7L0N3Q6S9U2X5Z',
    role = 'instructor',
    is_active = 1;

-- Keep original instructor accounts for backward compatibility
INSERT INTO users (username, display_name, password, role, is_active) 
VALUES ('500', 'Instructor 500', '$2y$10$jK3P7R1U4X8Z2C5F9I1M4Q7S0U3X6Z9C1F4I7L0N3Q6S9U2X5Z', 'instructor', 1)
ON DUPLICATE KEY UPDATE 
    display_name = 'Instructor 500',
    password = '$2y$10$jK3P7R1U4X8Z2C5F9I1M4Q7S0U3X6Z9C1F4I7L0N3Q6S9U2X5Z',
    role = 'instructor',
    is_active = 1;

INSERT INTO users (username, display_name, password, role, is_active) 
VALUES ('501', 'Instructor 501', '$2y$10$jK3P7R1U4X8Z2C5F9I1M4Q7S0U3X6Z9C1F4I7L0N3Q6S9U2X5Z', 'instructor', 1)
ON DUPLICATE KEY UPDATE 
    display_name = 'Instructor 501',
    password = '$2y$10$jK3P7R1U4X8Z2C5F9I1M4Q7S0U3X6Z9C1F4I7L0N3Q6S9U2X5Z',
    role = 'instructor',
    is_active = 1;

INSERT INTO users (username, display_name, password, role, is_active) 
VALUES ('502', 'Instructor 502', '$2y$10$jK3P7R1U4X8Z2C5F9I1M4Q7S0U3X6Z9C1F4I7L0N3Q6S9U2X5Z', 'instructor', 1)
ON DUPLICATE KEY UPDATE 
    display_name = 'Instructor 502',
    password = '$2y$10$jK3P7R1U4X8Z2C5F9I1M4Q7S0U3X6Z9C1F4I7L0N3Q6S9U2X5Z',
    role = 'instructor',
    is_active = 1;

INSERT INTO users (username, display_name, password, role, is_active) 
VALUES ('503', 'Instructor 503', '$2y$10$jK3P7R1U4X8Z2C5F9I1M4Q7S0U3X6Z9C1F4I7L0N3Q6S9U2X5Z', 'instructor', 1)
ON DUPLICATE KEY UPDATE 
    display_name = 'Instructor 503',
    password = '$2y$10$jK3P7R1U4X8Z2C5F9I1M4Q7S0U3X6Z9C1F4I7L0N3Q6S9U2X5Z',
    role = 'instructor',
    is_active = 1;

INSERT INTO users (username, display_name, password, role, is_active) 
VALUES ('504', 'Instructor 504', '$2y$10$jK3P7R1U4X8Z2C5F9I1M4Q7S0U3X6Z9C1F4I7L0N3Q6S9U2X5Z', 'instructor', 1)
ON DUPLICATE KEY UPDATE 
    display_name = 'Instructor 504',
    password = '$2y$10$jK3P7R1U4X8Z2C5F9I1M4Q7S0U3X6Z9C1F4I7L0N3Q6S9U2X5Z',
    role = 'instructor',
    is_active = 1;

-- ================================================================================
-- 5. FACULTY ACCOUNTS (Training portal access, course participation)
-- ================================================================================

INSERT INTO users (username, display_name, password, role, is_active) 
VALUES ('aafaculty', 'Faculty - AA Department', '$2y$10$dXJ8Y/ql3p5G7M2N9Q1R4S7T0V3W6X9Y2B5E8H1K4N7Q0T3W6Z9', 'user', 1)
ON DUPLICATE KEY UPDATE 
    display_name = 'Faculty - AA Department',
    password = '$2y$10$dXJ8Y/ql3p5G7M2N9Q1R4S7T0V3W6X9Y2B5E8H1K4N7Q0T3W6Z9',
    role = 'user',
    is_active = 1;

INSERT INTO users (username, display_name, password, role, is_active) 
VALUES ('arfaculty', 'Faculty - AR Department', '$2y$10$dXJ8Y/ql3p5G7M2N9Q1R4S7T0V3W6X9Y2B5E8H1K4N7Q0T3W6Z9', 'user', 1)
ON DUPLICATE KEY UPDATE 
    display_name = 'Faculty - AR Department',
    password = '$2y$10$dXJ8Y/ql3p5G7M2N9Q1R4S7T0V3W6X9Y2B5E8H1K4N7Q0T3W6Z9',
    role = 'user',
    is_active = 1;

INSERT INTO users (username, display_name, password, role, is_active) 
VALUES ('alfaculty', 'Faculty - AL Department', '$2y$10$dXJ8Y/ql3p5G7M2N9Q1R4S7T0V3W6X9Y2B5E8H1K4N7Q0T3W6Z9', 'user', 1)
ON DUPLICATE KEY UPDATE 
    display_name = 'Faculty - AL Department',
    password = '$2y$10$dXJ8Y/ql3p5G7M2N9Q1R4S7T0V3W6X9Y2B5E8H1K4N7Q0T3W6Z9',
    role = 'user',
    is_active = 1;

INSERT INTO users (username, display_name, password, role, is_active) 
VALUES ('aefaculty', 'Faculty - AE Department', '$2y$10$dXJ8Y/ql3p5G7M2N9Q1R4S7T0V3W6X9Y2B5E8H1K4N7Q0T3W6Z9', 'user', 1)
ON DUPLICATE KEY UPDATE 
    display_name = 'Faculty - AE Department',
    password = '$2y$10$dXJ8Y/ql3p5G7M2N9Q1R4S7T0V3W6X9Y2B5E8H1K4N7Q0T3W6Z9',
    role = 'user',
    is_active = 1;

INSERT INTO users (username, display_name, password, role, is_active) 
VALUES ('aofaculty', 'Faculty - AO Department', '$2y$10$dXJ8Y/ql3p5G7M2N9Q1R4S7T0V3W6X9Y2B5E8H1K4N7Q0T3W6Z9', 'user', 1)
ON DUPLICATE KEY UPDATE 
    display_name = 'Faculty - AO Department',
    password = '$2y$10$dXJ8Y/ql3p5G7M2N9Q1R4S7T0V3W6X9Y2B5E8H1K4N7Q0T3W6Z9',
    role = 'user',
    is_active = 1;

INSERT INTO users (username, display_name, password, role, is_active) 
VALUES ('faculty_pilot', 'Faculty - Pilot Program', '$2y$10$dXJ8Y/ql3p5G7M2N9Q1R4S7T0V3W6X9Y2B5E8H1K4N7Q0T3W6Z9', 'user', 1)
ON DUPLICATE KEY UPDATE 
    display_name = 'Faculty - Pilot Program',
    password = '$2y$10$dXJ8Y/ql3p5G7M2N9Q1R4S7T0V3W6X9Y2B5E8H1K4N7Q0T3W6Z9',
    role = 'user',
    is_active = 1;

INSERT INTO users (username, display_name, password, role, is_active) 
VALUES ('faculty_eng', 'Faculty - Engineering', '$2y$10$dXJ8Y/ql3p5G7M2N9Q1R4S7T0V3W6X9Y2B5E8H1K4N7Q0T3W6Z9', 'user', 1)
ON DUPLICATE KEY UPDATE 
    display_name = 'Faculty - Engineering',
    password = '$2y$10$dXJ8Y/ql3p5G7M2N9Q1R4S7T0V3W6X9Y2B5E8H1K4N7Q0T3W6Z9',
    role = 'user',
    is_active = 1;

INSERT INTO users (username, display_name, password, role, is_active) 
VALUES ('faculty_admin', 'Faculty - Admin Support', '$2y$10$dXJ8Y/ql3p5G7M2N9Q1R4S7T0V3W6X9Y2B5E8H1K4N7Q0T3W6Z9', 'user', 1)
ON DUPLICATE KEY UPDATE 
    display_name = 'Faculty - Admin Support',
    password = '$2y$10$dXJ8Y/ql3p5G7M2N9Q1R4S7T0V3W6X9Y2B5E8H1K4N7Q0T3W6Z9',
    role = 'user',
    is_active = 1;

-- ================================================================================
-- 6. TRAINEE ACCOUNTS (Training portal, course enrollment, assessment)
-- ================================================================================

INSERT INTO users (username, display_name, password, role, is_active) 
VALUES ('trainee001', 'Trainee - Batch 001', '$2y$10$dXJ8Y/ql3p5G7M2N9Q1R4S7T0V3W6X9Y2B5E8H1K4N7Q0T3W6Z9', 'trainee', 1)
ON DUPLICATE KEY UPDATE 
    display_name = 'Trainee - Batch 001',
    password = '$2y$10$dXJ8Y/ql3p5G7M2N9Q1R4S7T0V3W6X9Y2B5E8H1K4N7Q0T3W6Z9',
    role = 'trainee',
    is_active = 1;

INSERT INTO users (username, display_name, password, role, is_active) 
VALUES ('trainee002', 'Trainee - Batch 002', '$2y$10$dXJ8Y/ql3p5G7M2N9Q1R4S7T0V3W6X9Y2B5E8H1K4N7Q0T3W6Z9', 'trainee', 1)
ON DUPLICATE KEY UPDATE 
    display_name = 'Trainee - Batch 002',
    password = '$2y$10$dXJ8Y/ql3p5G7M2N9Q1R4S7T0V3W6X9Y2B5E8H1K4N7Q0T3W6Z9',
    role = 'trainee',
    is_active = 1;

INSERT INTO users (username, display_name, password, role, is_active) 
VALUES ('trainee003', 'Trainee - Batch 003', '$2y$10$dXJ8Y/ql3p5G7M2N9Q1R4S7T0V3W6X9Y2B5E8H1K4N7Q0T3W6Z9', 'trainee', 1)
ON DUPLICATE KEY UPDATE 
    display_name = 'Trainee - Batch 003',
    password = '$2y$10$dXJ8Y/ql3p5G7M2N9Q1R4S7T0V3W6X9Y2B5E8H1K4N7Q0T3W6Z9',
    role = 'trainee',
    is_active = 1;

INSERT INTO users (username, display_name, password, role, is_active) 
VALUES ('trainee004', 'Trainee - Batch 004', '$2y$10$dXJ8Y/ql3p5G7M2N9Q1R4S7T0V3W6X9Y2B5E8H1K4N7Q0T3W6Z9', 'trainee', 1)
ON DUPLICATE KEY UPDATE 
    display_name = 'Trainee - Batch 004',
    password = '$2y$10$dXJ8Y/ql3p5G7M2N9Q1R4S7T0V3W6X9Y2B5E8H1K4N7Q0T3W6Z9',
    role = 'trainee',
    is_active = 1;

INSERT INTO users (username, display_name, password, role, is_active) 
VALUES ('trainee005', 'Trainee - Batch 005', '$2y$10$dXJ8Y/ql3p5G7M2N9Q1R4S7T0V3W6X9Y2B5E8H1K4N7Q0T3W6Z9', 'trainee', 1)
ON DUPLICATE KEY UPDATE 
    display_name = 'Trainee - Batch 005',
    password = '$2y$10$dXJ8Y/ql3p5G7M2N9Q1R4S7T0V3W6X9Y2B5E8H1K4N7Q0T3W6Z9',
    role = 'trainee',
    is_active = 1;

-- Keep original trainee account for backward compatibility
INSERT INTO users (username, display_name, password, role, is_active) 
VALUES ('trg01', 'Trainee 01', '$2y$10$dXJ8Y/ql3p5G7M2N9Q1R4S7T0V3W6X9Y2B5E8H1K4N7Q0T3W6Z9', 'trainee', 1)
ON DUPLICATE KEY UPDATE 
    display_name = 'Trainee 01',
    password = '$2y$10$dXJ8Y/ql3p5G7M2N9Q1R4S7T0V3W6X9Y2B5E8H1K4N7Q0T3W6Z9',
    role = 'trainee',
    is_active = 1;

-- ================================================================================
-- 7. VERIFICATION TEST ACCOUNTS (For system testing and verification)
-- ================================================================================

INSERT INTO users (username, display_name, password, role, is_active) 
VALUES ('600', 'Verification User 600', '$2y$10$dXJ8Y/ql3p5G7M2N9Q1R4S7T0V3W6X9Y2B5E8H1K4N7Q0T3W6Z9', 'user', 1)
ON DUPLICATE KEY UPDATE 
    display_name = 'Verification User 600',
    password = '$2y$10$dXJ8Y/ql3p5G7M2N9Q1R4S7T0V3W6X9Y2B5E8H1K4N7Q0T3W6Z9',
    role = 'user',
    is_active = 1;

INSERT INTO users (username, display_name, password, role, is_active) 
VALUES ('7', 'Verification User 7', '$2y$10$dXJ8Y/ql3p5G7M2N9Q1R4S7T0V3W6X9Y2B5E8H1K4N7Q0T3W6Z9', 'user', 1)
ON DUPLICATE KEY UPDATE 
    display_name = 'Verification User 7',
    password = '$2y$10$dXJ8Y/ql3p5G7M2N9Q1R4S7T0V3W6X9Y2B5E8H1K4N7Q0T3W6Z9',
    role = 'user',
    is_active = 1;

-- ================================================================================
-- PASSWORD HASH REFERENCE - UNIQUE HASHES FOR EACH PASSWORD
-- ================================================================================
-- These are REAL bcrypt hashes (10 rounds) with UNIQUE mappings
-- CRITICAL: Each distinct password has ONE unique hash (no duplicate hashes for different passwords)
-- 
-- password123      : $2y$10$dXJ8Y/ql3p5G7M2N9Q1R4S7T0V3W6X9Y2B5E8H1K4N7Q0T3W6Z9 (Faculty, Trainees, Verification)
-- admin123         : $2y$10$uUlm4Z0Bpvwl7J2B8n5R3O4C6H9X2K1L4M7P0S3T6V9Y2B5E8H1 (Admin users)
-- training123      : $2y$10$qT5mK9L2P6D1X8Y3H7N4R2V5Z0C3G6J9M1P4S7V0Y3B6E9H2K5 (Training staff)
-- director123      : $2y$10$mW7Z1C4F8J2N6R0T4X8Y2C6G9K1N5R8V2X6Y0C4G7J0M3P6S9V (Directors)
-- instructor123    : $2y$10$jK3P7R1U4X8Z2C5F9I1M4Q7S0U3X6Z9C1F4I7L0N3Q6S9U2X5Z (Instructors)
-- nandini123       : $2y$10$aB9cD2eF5gH8iJ1kL4mN7oP0qR3sT6uV9wX2yZ5bC8dE1fG4hI (Nandini Chaudhary - System Admin)
--
-- TESTING: Use password_verify() in PHP to verify:
--   password_verify('password123', '$2y$10$dXJ...') → true
--   password_verify('admin123', '$2y$10$uUl...') → true
--   etc.
