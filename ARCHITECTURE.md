# NATMS (Naval Aviation Training Management System) - Architecture & Status Report

## 1. High-Level Overview

**Application Name:** NATMS (Naval Aviation Training Management System)

**Description:** NATMS is a multi-module web-based training and facility management platform designed for the Indian Navy's aviation training operations. It provides integrated systems for managing facility bookings, classroom attendance monitoring, training material distribution, network status tracking, and document management. The system serves training faculty, instructors, directors, and administrators with role-based access controls.

**Business/Domain Problem:** The system addresses the need for centralized management of multiple critical training operations: scheduling facility usage (auditoriums, exam halls), monitoring instructor presence in classrooms, organizing and distributing training materials (courses, PPTs, question banks, videos), tracking network infrastructure health, and managing shared documents.

**Application Type:** Multi-module web application (PHP backend with MySQL database, frontend with HTML/CSS/JavaScript)

**Key Users/Roles:**
- **Admins**: System-wide management, user creation, content management
- **Training Faculty**: Manage courses, upload materials, monitor classrooms
- **Instructors**: Check-in for sessions, view schedules
- **Directors**: Dashboard access, oversight of training operations
- **Trainees**: Access training materials
- **Regular Users**: Access to facility booking, training portals

---

## 2. Technology Stack

| Category | Technology |
|----------|-----------|
| **Backend Language** | PHP 8.1+ |
| **Database** | MySQL 10.4.32 (MariaDB) |
| **Frontend Framework** | Tailwind CSS, Vanilla JavaScript |
| **ORM/Database Access** | PDO (PHP Data Objects), MySQLi |
| **Date Picker Library** | Pikaday.js |
| **CSS Framework** | Tailwind CSS (minified) |
| **Build/Package Tools** | None (traditional PHP) |
| **Runtime Environment** | XAMPP (Apache + PHP + MySQL) |
| **Web Server** | Apache 2.4 |
| **Development OS** | Ubuntu 24.04.3 LTS |

### Key Configuration Files:
- Database connection files exist in each module (e.g., `booking/includes/db.php`, `training-portal/db.php`, `classroom_monitoring/db.php`)
- Session management in login files
- No `package.json`, `composer.json`, or `.env` files found
- No Docker/containerization setup

---

## 3. Project Structure & Modules

### 3.1 Directory Tree (High-Level)

```
/workspaces/MYCode/
├── booking/                    # Facility booking system
│   ├── admin/                 # Admin panel & approval workflow
│   │   ├── actions/          # Admin actions (block slots, manage users/roles)
│   ├── includes/             # DB connection & utilities
│   ├── css/                  # Tailwind & Pikaday CSS
│   ├── js/                   # Booking form logic, Pikaday
│   ├── sql/                  # Database schema
│   └── index.php            # Main booking interface
│
├── classroom_monitoring/       # Classroom attendance & schedule
│   ├── routine/              # Check-in, dashboards, schedules
│   ├── db.php               # Database connection
│   └── [admin/director/instructor dashboards]
│
├── training-portal/           # Training material management
│   ├── admin/               # Upload handlers (PPT, video, CBT, etc.)
│   ├── uploads/            # Uploaded course materials
│   ├── manage_*.php         # Course/subject/material management pages
│   ├── db.php              # Database connection
│   └── index.php           # Main portal interface
│
├── live_status/             # Network device monitoring
│   ├── admin/              # Login & admin controls
│   ├── helpers.php         # Color & emoji helper functions
│   ├── status.php          # Device status API
│   └── index.php           # Dashboard with AJAX refresh
│
├── niatcloud/              # Cloud file/document storage
│   ├── php/               # Utilities
│   ├── css/               # Styling
│   ├── login.php          # Central authentication
│   ├── browse.php         # File browser
│   ├── upload.php         # File upload handler
│   └── Central login used by other modules
│
├── dashboard/             # Bitnami XAMPP welcome page
│   ├── javascripts/       # Modernizr.js, feature detection
│   ├── stylesheets/       # Foundation CSS framework
│   └── FAQ & documentation pages
│
├── DB/                   # SQL schema files
│   ├── booking.sql
│   ├── classroom_monitoring.sql
│   ├── training_portal.sql
│   ├── live_network.sql
│   └── nshare_lite_db.sql
│
├── img/                 # Static images
├── index.php            # Redirect to /dashboard/
└── applications.html    # Bitnami module placeholder
```

### 3.2 Modules / Components

#### **Module 1: Facility Booking System** (`booking/`)
- **Purpose:** Allow users to book facilities (Auditorium, Examination Hall) and manage facility reservations
- **Key Responsibilities:**
  - User booking submission with date/time/facility selection
  - Admin approval/rejection workflow
  - Block slots for maintenance or special events
  - View available slots with real-time availability checking
  - Manage roles and users
- **Key Files:**
  - `index.php` - Public booking interface
  - `admin/admin_panel.php` - Admin dashboard with pending approvals
  - `includes/book_slot.php` - Booking submission endpoint
  - `includes/get_slots.php` - Slot availability API
  - `admin/actions/block_slot.php` - Block time slots
  - `admin/actions/manage_user.php`, `manage_role.php` - User management
- **Dependencies:** Booking database, session management
- **Status:** Appears mostly complete (admin approval workflow functional)

#### **Module 2: Classroom Monitoring** (`classroom_monitoring/`)
- **Purpose:** Track instructor attendance and manage classroom schedules
- **Key Responsibilities:**
  - Instructor login and session check-in
  - Attendance tracking (manned/unmanned status)
  - Weekly schedule management
  - Classroom, instructor, and course inventory management
  - Admin, director, and instructor dashboards with different views
- **Key Files:**
  - `routine/index.php` - Weekly routine manager entry point
  - `routine/login.php` - Instructor/faculty login (uses niatcloud central auth)
  - `routine/admin_dashboard.php` - Admin oversight dashboard
  - `routine/director_dashboard.php` - Director monitoring dashboard
  - `routine/weekly_schedule.php` - Schedule management
  - `routine/add_classroom.php`, `add_instructor.php` - Admin management pages
- **Dependencies:** Classroom_monitoring database, niatcloud login session
- **Status:** Partially complete; core check-in appears functional, some admin actions incomplete

#### **Module 3: Training Portal** (`training-portal/`)
- **Purpose:** Centralized distribution of training materials (courses, PPTs, videos, question banks, lesson plans, CBTs, TOS)
- **Key Responsibilities:**
  - Course and subject management
  - Upload and organize training materials
  - User role-based material access
  - Material deletion and management
- **Key Files:**
  - `index.php` - Course selection interface
  - `subjects.php` - Subject listing for selected course
  - `materials.php` - Material display interface
  - `admin_manage.php` - Admin dashboard
  - `manage_courses.php`, `manage_subjects.php` - Course/subject management
  - `manage_ppts.php`, `manage_cbts.php`, `manage_lesson_plans.php`, etc. - Material management
  - `admin/upload_ppt.php`, `upload_video.php`, etc. - Upload handlers
- **Dependencies:** Training_portal database, niatcloud central login
- **Status:** Core functionality appears complete; modular design allows independent material management

#### **Module 4: Live Network Status** (`live_status/`)
- **Purpose:** Monitor network device availability (PCs, printers, switches)
- **Key Responsibilities:**
  - Display device status (up/down) across locations
  - Filter by device type and location
  - Real-time AJAX refresh (every 30 seconds)
  - Device location and color-coding
- **Key Files:**
  - `index.php` - Dashboard with filters and AJAX refresh
  - `status.php` - Device status API endpoint
  - `helpers.php` - Utility functions (color lookup, emoji indicators)
  - `partial.php` - AJAX response handler
- **Dependencies:** live_network database, admin authentication
- **Status:** Appears functional; uses AJAX for live updates

#### **Module 5: NIAT Cloud** (`niatcloud/`)
- **Purpose:** Centralized file/document storage and sharing platform
- **Key Responsibilities:**
  - User authentication (central login for all modules)
  - File/folder upload and browsing
  - Folder management and ordering
  - Role-based file access
- **Key Files:**
  - `login.php` - Central authentication (used by other modules)
  - `index.php` - File browser interface
  - `browse.php` - Folder navigation
  - `upload.php` - File upload handler
  - `folder_view.php` - Folder display
- **Dependencies:** nshare_lite_db database
- **Status:** Core file management appears functional; central login session system

---

## 4. Application Behaviour

### 4.1 Entry Points

| File Path | Purpose | Startup Actions |
|-----------|---------|-----------------|
| **`/index.php`** | Root redirect | Redirects to `/dashboard/` |
| **`/dashboard/index.html`** | Web portal home | Bitnami XAMPP welcome page with module links |
| **`/booking/index.php`** | Facility booking | Loads facilities from DB, displays booking form |
| **`/classroom_monitoring/routine/login.php`** | Classroom monitoring entry | Login form for training faculty/directors |
| **`/training-portal/index.php`** | Training materials | Displays available courses from DB |
| **`/live_status/index.php`** | Network monitoring | Initializes device list, sets AJAX refresh interval |
| **`/niatcloud/login.php`** | Cloud file system | Central login (used by other modules) |

### 4.2 Backend / API Layer

#### Booking System APIs
| Method | Endpoint | Purpose |
|--------|----------|---------|
| POST | `/booking/includes/book_slot.php` | Submit booking request |
| GET | `/booking/includes/get_slots.php` | Fetch available slots for date/facility |
| POST | `/booking/admin/approve_reject_request.php` | Approve/reject booking requests |
| POST | `/booking/admin/actions/block_slot.php` | Block time slots |

#### Classroom Monitoring
| Method | Endpoint | Purpose |
|--------|----------|---------|
| POST | `/classroom_monitoring/routine/login.php` | User authentication |
| POST | `/classroom_monitoring/routine/weekly_schedule.php` | Save schedule entries |
| GET/POST | `/classroom_monitoring/routine/admin_dashboard.php` | Admin data retrieval |

#### Training Portal
| Method | Endpoint | Purpose |
|--------|----------|---------|
| POST | `/training-portal/admin/upload_ppt.php` | Upload PPT file |
| POST | `/training-portal/admin/upload_video.php` | Upload training video |
| POST | `/training-portal/admin/upload_cbts.php` | Upload CBT module |
| GET | `/training-portal/manage_courses.php` | List/manage courses |

#### Live Network
| Method | Endpoint | Purpose |
|--------|----------|---------|
| POST | `/live_status/admin/` | Admin login |
| GET | `/live_status/status.php` | Get device status (AJAX) |
| GET | `/live_status/index.php?partial=1` | Partial content for AJAX refresh |

### 4.3 Frontend / UI Layer

**Framework:** Vanilla HTML5, CSS (Tailwind), JavaScript (no frontend framework)

**Key Pages/Views:**
- Booking system: Facility selection → date/time/slot selection → booking request
- Classroom monitoring: Login → dashboard (role-specific) → check-in or weekly schedule
- Training portal: Course selection → subject list → material list → material download
- Live status: Device list with filters → real-time status updates
- NIAT Cloud: Login → folder browser → upload/download interface

**Authentication:** Session-based (`$_SESSION`), central authentication via niatcloud login

### 4.4 Data & Domain Logic

**Core Domain Models:**

| Entity | Key Fields | Related Module |
|--------|-----------|-----------------|
| **Bookings** | id, facility_id, start_date, end_date, start_time, end_time, status (pending/approved/rejected), requester_name, unit | Booking |
| **Facilities** | id, name | Booking |
| **Roles** | id, role_name, access_level | Booking |
| **Classrooms** | id, code, status (active/inactive) | Classroom Monitoring |
| **Instructors** | id, name, username | Classroom Monitoring |
| **Checkins** | id, instructor_id, classroom_id, date, session_start, session_end, status (manned/unmanned) | Classroom Monitoring |
| **Courses** | id, name | Training Portal, Classroom Monitoring |
| **Subjects** | id, course_id, name | Training Portal |
| **Materials (PPT, Video, CBT, etc.)** | id, subject_id, title, file_path | Training Portal |
| **Devices** | id, name, type (PC/Printer/Switch), ip_address, location, status (up/down) | Live Status |
| **Users** | id, username, password_hash, role, status | Multiple modules |

**Business Logic:**
- Slot availability checking with overlap detection
- Admin blocking of time slots
- Role-based access control and dashboards
- Session check-in with time validation
- File upload handling with unique filename generation

---

## 5. Data Model & Persistence

### Database Architecture

**5 Separate Databases:**
1. **`booking`** - Facility booking management
2. **`classroom_monitoring`** - Classroom & attendance tracking
3. **`training_portal`** - Training materials catalog
4. **`live_network`** - Network device inventory & status
5. **`nshare_lite_db`** - User authentication & cloud storage

### Schema Overview

#### `booking` Database Tables:
- `admins` - Admin credentials with bcrypt password hashing
- `bookings` - Booking requests (pending/approved/rejected)
- `facilities` - Facility inventory (Auditorium, Exam Hall, etc.)
- `blocked_slots` - Blocked date/time ranges
- `roles` - Role definitions with access levels

#### `classroom_monitoring` Database Tables:
- `users` - Training faculty, directors, admins
- `classrooms` - Classroom inventory
- `instructors` - Instructor registry
- `courses` - Course definitions
- `checkins` - Session attendance records
- `weekly_schedule` - Scheduled sessions per classroom

#### `training_portal` Database Tables:
- `courses` - Course catalog
- `subjects` - Subjects within courses
- `ppts` - PowerPoint presentations
- `videos` - Training videos
- `cbts` - Computer-Based Training modules
- `lesson_plans` - Lesson plan documents
- `question_banks` - Question bank files
- `tos` - Table of Specification documents
- `users` - Training system users

#### `live_network` Database Tables:
- `devices` - Network devices with IP and status
- `locations` - Physical locations with color coding
- `users` - Admin users for live status system

#### `nshare_lite_db` Database Tables:
- `users` - Central user registry (admin, user, trainee, director, instructor, training roles)

### Database Connection Details

**Connection Method:** PDO (for booking) and MySQLi (for training-portal, classroom_monitoring, live_status)

**Credentials (Hardcoded in each module):**
```
Host: localhost
User: root
Password: (empty)
Charset: utf8mb4
```

**Notable:** No migrations or version control for schema changes. Schemas provided as `.sql` dumps only.

---

## 6. Configuration, Environments & Deployment

### Configuration Strategy

**Current Approach:** Database credentials are hardcoded in PHP files:
- `booking/includes/db.php`
- `training-portal/db.php`
- `classroom_monitoring/routine/login.php` (inline connection)
- `live_status/index.php` (inline connection)

**No `.env` file or environment-based configuration**

**Session Management:** PHP native `session_start()` with `$_SESSION` superglobal

### Deployment Setup

**Development Environment:**
- XAMPP (Apache + PHP 8.1 + MySQL 10.4)
- Direct file execution via web server
- No build process required

**Production Deployment Concerns:**
- Credentials are hardcoded (security risk)
- No environment separation
- Error reporting enabled in some files (`ini_set('display_errors', 1)`)
- No HTTPS enforcement visible
- No database connection pooling

---

## 7. Testing & Quality

### Testing Coverage

**Status:** No automated tests found
- No test directories (`tests/`, `__tests__/`, `spec/`)
- No testing framework files (PHPUnit, Jest, etc.)
- No test configuration files

### Code Quality Tools

**Linting/Formatting:** None detected
- No `.eslintrc`, `prettier.config.js`, `.php-cs-fixer`, `phpstan.neon`
- No GitHub Actions or CI/CD pipeline files

### Quality Issues Observed

1. **Security:**
   - Hardcoded database credentials
   - No input validation on file uploads (accepting arbitrary extensions)
   - Direct SQL queries in some files (though mostly using prepared statements)
   - Weak password storage in some legacy tables
   - No CSRF token protection visible

2. **Code Organization:**
   - Database connections defined in multiple files (not DRY)
   - Mixed inline HTML and PHP (no templating engine)
   - Session management is basic session-based, no JWT or modern authentication

3. **Missing Error Handling:**
   - File uploads lack size/type validation
   - Some endpoints don't validate HTTP method
   - Error messages logged but not consistently handled

---

## 8. Current Status & Incomplete Work

### Implemented / Appears Complete

✅ **Booking System:**
- Public booking form with facility/date/time selection
- Admin panel with approval/rejection workflow
- Slot availability checking
- Admin blocking of time slots
- Role-based admin functions

✅ **Classroom Monitoring:**
- Instructor login and check-in
- Classroom, instructor, course inventory management
- Weekly schedule creation
- Admin/director dashboards

✅ **Training Portal:**
- Course browsing interface
- Subject management
- Material upload (PPT, video, CBT, lesson plans, question banks, TOS)
- Admin material management pages
- User management for training access

✅ **Live Network Status:**
- Device listing with type/location filters
- Real-time AJAX refresh every 30 seconds
- Admin authentication
- Location-based color coding

✅ **NIAT Cloud:**
- File upload and storage
- Folder browsing
- Central login system (used by other modules)
- File ordering and management

### Partially Implemented

⚠️ **Booking Admin Actions:**
- `booking/admin/approve.php` is **empty** (no implementation)
- Action files in `booking/admin/actions/` reference non-existent user/role management tables in some flows

⚠️ **Classroom Monitoring:**
- Logout functionality exists but may not fully clear sessions
- Some dashboard pages (`directors_dashboard.php`) may be duplicates or partially complete

⚠️ **Training Portal:**
- `manage_users.php` has basic user creation/deletion but may lack update functionality
- No detailed error messages in upload handlers (uses generic alerts)

⚠️ **File Upload Handlers:**
- No file size validation
- No MIME type validation
- No quarantine/scanning for malicious files

### Not Implemented / Planned

❌ **Missing Features:**
- No automated testing framework
- No centralized error logging or monitoring
- No API documentation or Swagger/OpenAPI specs
- No email notifications for booking approvals/rejections
- No database backup/restore procedures
- No audit logging for admin actions
- No rate limiting on API endpoints
- No image optimization for uploaded files
- No pagination on large data sets (e.g., material lists)
- No search functionality across training materials
- No bulk import/export for users or schedules
- No analytics or reporting dashboards
- No two-factor authentication
- No API key management for third-party integrations

### Known Issues & Bugs

1. **Session Redirect Inconsistency:** Different modules redirect to different login pages; classroom_monitoring redirects to `/niatcloud/login.php` but that may not be configured correctly in all cases.

2. **Database Isolation:** Five separate databases with duplicate user tables create synchronization issues. Changes in one user table aren't reflected in others.

3. **File Upload Storage:** Training portal and NIAT Cloud both have upload directories; unclear if they're synchronized or if uploads are duplicated.

4. **Date Handling:** Pikaday date picker in booking system uses custom date format handling that may not handle all edge cases.

5. **Empty Admin Approve File:** `booking/admin/approve.php` exists but is completely empty—likely stub from refactoring.

---

## 9. How to Run the Application

### Prerequisites

- **XAMPP** (or Apache + MySQL + PHP 8.1+)
- **MySQL 10.4+** (MariaDB)
- **PHP PDO extension** (for PDO connections)
- **Web browser** (Chrome, Firefox, Safari, Edge)

### Installation Steps

1. **Clone/Copy Codebase:**
   ```bash
   git clone <repo-url> /path/to/project
   # or XAMPP htdocs
   cp -r MYCode /xampp/htdocs/
   ```

2. **Start XAMPP:**
   ```bash
   sudo /opt/lampp/lampp start  # or use XAMPP Control Panel
   ```

3. **Import Databases:**
   ```sql
   -- Access phpMyAdmin at http://localhost/phpmyadmin
   -- Import each SQL file:
   -- booking.sql
   -- classroom_monitoring.sql
   -- training_portal.sql
   -- live_network.sql
   -- nshare_lite_db.sql
   ```
   Or via command line:
   ```bash
   mysql -u root < /path/to/booking.sql
   mysql -u root < /path/to/classroom_monitoring.sql
   # ... repeat for other databases
   ```

4. **Verify Database Connections:**
   - Test by visiting `http://localhost/booking/index.php`
   - Check browser console for connection errors

### Running in Development

```bash
# Start Apache and MySQL (XAMPP)
/opt/lampp/ctlscript.sh start

# Access the application
# Main portal: http://localhost/dashboard/
# Booking: http://localhost/booking/
# Classroom monitoring: http://localhost/classroom_monitoring/routine/login.php
# Training portal: http://localhost/training-portal/
# Live status: http://localhost/live_status/
# NIAT Cloud: http://localhost/niatcloud/
```

### Running Tests

No automated tests present. Manual testing recommended.

### Running in Production

⚠️ **NOT RECOMMENDED IN CURRENT STATE**

Before deploying to production:
1. **Move credentials to environment variables** (create `.env` file)
2. **Enable HTTPS** (SSL/TLS certificates)
3. **Set strong MySQL password** (remove root user with no password)
4. **Disable debug mode** (set `display_errors = Off`)
5. **Implement proper logging** (error_log to file, not stdout)
6. **Add input validation** on all forms and file uploads
7. **Set restrictive file permissions** on uploads directory
8. **Implement rate limiting** on API endpoints
9. **Add CSRF protection** (token validation on POST requests)
10. **Use a reverse proxy** (Nginx) in front of Apache
11. **Set up database backups** (automated daily/weekly)
12. **Implement monitoring** (error tracking, uptime monitoring)

---

## 10. Recommended Way Forward (From a Tech Lead Perspective)

### Status Summary

**Overall Assessment:** Core modules are functional but the application is in a **proof-of-concept/early production state** with significant technical debt and security gaps.

- ✅ Core business logic is implemented (booking, classroom monitoring, training materials, network status)
- ⚠️ Architecture shows signs of rapid development without centralized patterns
- ❌ Security is weak (hardcoded credentials, no validation, no HTTPS)
- ❌ No automated tests, no CI/CD pipeline
- ❌ Database schema not versioned; user sync issues across modules
- ❌ No monitoring, logging, or error tracking in production

### Prioritized Next Steps

#### **Short-Term (1–2 sprints) - Critical for Basic Security & Stability**

1. **Implement Environment-Based Configuration**
   - Create `.env` file with database credentials
   - Use `php-dotenv` library or native `getenv()`
   - Remove all hardcoded credentials from source code
   - **Impact:** Massive security improvement, easier deployment

2. **Remove Empty/Stub Files**
   - Delete or complete `booking/admin/approve.php`
   - Audit all action files to ensure they're implemented
   - **Impact:** Code clarity, reduced confusion

3. **Centralize Database Connection**
   - Create shared database abstraction layer
   - Single point for connection pooling, logging, error handling
   - **Impact:** DRY principle, easier to debug, maintain

4. **Add Basic Input Validation**
   - Validate all file uploads (size, MIME type, extension whitelist)
   - Sanitize all user inputs (trim, escape, type-cast)
   - **Impact:** Block common injection attacks, prevent file upload abuse

5. **Set Up Centralized User Management**
   - Merge duplicate user tables across databases
   - Implement single sign-on (SSO) via niatcloud
   - Sync roles and permissions across modules
   - **Impact:** Consistency, easier user administration

#### **Medium-Term (2–4 sprints) - Quality & Monitoring**

6. **Add Error Logging & Monitoring**
   - Implement centralized logging (Monolog or similar)
   - Send errors to file (not stdout in production)
   - Set up email alerts for critical errors
   - Integrate APM tool (e.g., New Relic, Datadog) for monitoring
   - **Impact:** Production visibility, faster incident response

7. **Implement CSRF Protection**
   - Generate and validate CSRF tokens on all POST requests
   - Use SameSite cookie attribute
   - **Impact:** Block cross-site attacks

8. **Add Rate Limiting & API Keys**
   - Implement rate limiting on booking and upload endpoints
   - Add API key management for third-party access (if needed)
   - **Impact:** Prevent abuse, API security

9. **Version Database Schema**
   - Use migration tool (Alembic, Flyway, or custom PHP migrations)
   - Version control all schema changes
   - **Impact:** Easier rollbacks, schema drift prevention

10. **Create Comprehensive Admin Dashboard**
    - Unify admin views across modules
    - Add user role management
    - System health monitoring (database connection, disk space)
    - **Impact:** Better administration, reduces support overhead

#### **Longer-Term (1–2 quarters) - Architecture & Best Practices**

11. **Migrate to Lightweight Framework (Optional)**
    - Consider Laravel, Symfony, or Slim for better structure
    - Or implement simple MVC pattern manually (less work)
    - **Impact:** Better separation of concerns, easier testing

12. **Implement Automated Testing**
    - Unit tests for business logic (PHPUnit)
    - Integration tests for database operations
    - E2E tests for critical user workflows (Selenium/Cypress)
    - Target: 70%+ code coverage
    - **Impact:** Prevents regressions, enables refactoring confidence

13. **Set Up CI/CD Pipeline**
    - GitHub Actions or GitLab CI for automated testing
    - Automated deployment to staging/production
    - Pre-commit hooks for linting and formatting
    - **Impact:** Faster, safer releases

14. **Improve Frontend Architecture**
    - Consider modern framework (Vue, React) for complex pages
    - Or implement HTMX for interactive features without heavy SPA
    - Separate frontend build from backend
    - **Impact:** Better UX, easier frontend testing

15. **Add Comprehensive Documentation**
    - Architecture Decision Records (ADRs)
    - API documentation (Swagger/OpenAPI)
    - Deployment & runbook documentation
    - **Impact:** Faster onboarding, fewer production surprises

16. **Implement Backup & Disaster Recovery**
    - Automated daily database backups
    - Test restore procedures monthly
    - Off-site backup storage
    - **Impact:** Business continuity

17. **Enable HTTPS & Security Hardening**
    - SSL/TLS certificates (Let's Encrypt for free)
    - HSTS headers, CSP (Content Security Policy)
    - XSS and SQL injection protection (review parameterized queries)
    - **Impact:** Data in transit protection

### Nice-to-Have Features (After MVP Stability)

- 📊 Analytics dashboards (facility utilization, training completion rates)
- 📧 Email notifications for booking approvals, class reminders
- 📱 Mobile app or responsive mobile interface
- 🔍 Full-text search across training materials
- 📄 Bulk import/export (courses, users, schedules)
- 🏆 Training completion certificates
- 🔐 Two-factor authentication
- 🗂️ Advanced file sharing & permissions in NIAT Cloud
- 🎓 Reporting portal for directors/faculty
- 📞 Automated SMS/Slack notifications

---

**Recommendation:** Focus on **Short-Term items first** (environment config, centralized DB, input validation, logging). These provide the largest security/stability gains with moderate effort. Once those are in place, the application can be safely run in production with confidence. Medium and longer-term items can be addressed in subsequent releases as the codebase matures.
