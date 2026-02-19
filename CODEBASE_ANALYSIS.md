# NATMS Codebase Architecture Analysis & Routing Issues

**Analysis Date:** February 19, 2026  
**Status:** Comprehensive Review Complete

---

## 📋 Executive Summary

The NATMS codebase has **multiple overlapping authentication/routing systems** that create conflicts and inconsistencies:
- 5 different login pages with different session patterns
- Inconsistent RBAC implementations across modules
- Missing directory structures (uploads/)
- Broken routing links (admin_panel.php doesn't exist)
- No centralized error handling

---

## 🏗️ Current Module Structure & Routing

### 1. **Dashboard Module** (`/dashboard/`)
**Entry Point:** `/dashboard/login.php`  
**Backend:** `/dashboard/php/login_central.php`  
**Authentication Database:** `nshare_lite_db.users`  
**Session Variables:** `logged_in`, `user`, `user_id`, `role`, `display_name`  
**Portal:** `/dashboard/portals.php` (Role-based module display)

**Current Issues:**
- ✅ Centralized authentication (GOOD)
- ✅ Using bcrypt (GOOD)
- ⚠️ Links to `/training-portal/admin/admin_panel.php` (doesn't exist)
- ⚠️ Some modules have their own login pages (creates confusion)

**RBAC Implementation:**
```php
// Admin: All modules + admin panel
// user: Training Portal, Booking, Network, Cloud
// training: Training Portal, Booking, Cloud
// trainee: Training Portal (read-only), Classroom Monitoring
// director: Classroom Monitoring (full)
// instructor: Classroom Monitoring (own class), Training Portal
```

---

### 2. **NIAT Cloud Module** (`/niatcloud/`)
**Entry Point:** `/niatcloud/login.php`  
**Current Issues:**
- ⚠️ `scandir('uploads/')` - Directory doesn't exist
- ⚠️ No error handling for missing directory
- ⚠️ Duplicate login page (should use centralized)
- ⚠️ Session uses `user` instead of following central pattern

**Location:** `/niatcloud/index.php` lines 8-10
```php
$uploadDir = 'uploads/';
$baseDir = 'uploads/';
$folders = array_filter(scandir($baseDir), ...);  // ❌ FAILS if directory missing
```

---

### 3. **Training Portal Module** (`/training-portal/`)
**Status:** Mixed routing
- `/training-portal/main.php` → checks `/dashboard/php/login_central.php` session
- `/training-portal/admin_manage.php` → redirects to `/niatcloud/login.php`
- `/training-portal/admin/` → Has upload scripts but NO admin_panel.php

**Current Issues:**
- ⚠️ Inconsistent redirect targets
- ❌ Missing `/training-portal/admin/admin_panel.php` (referenced in portals.php line ~395)
- ⚠️ Different modules checking different session variables

---

### 4. **Classroom Monitoring Module** (`/classroom_monitoring/routine/`)
**Entry Point:** `/classroom_monitoring/routine/login.php` (MODULE-SPECIFIC)  
**Database:** `classroom_monitoring.users`  
**Session Variables:** `user_id`, `pno`, `name`, `role`  
**Routing by Role:**
```
training_faculty → /index.php
director → /director_dashboard.php
admin → /admin_dashboard.php
instructor → /instructor_dashboard.php
```

**Current Issues:**
- ⚠️ Uses module-specific database (not centralized)
- ⚠️ Uses local session variables (different names)
- ⚠️ Separate login page (conflicts with unified approach)
- ⚠️ Some pages redirect to `/niatcloud/login.php` (mixed approach)

---

### 5. **Booking System Module** (`/booking/`)
**Entry Point:** `/booking/admin/admin_login.php` (ADMIN-ONLY)  
**Admin Session:** `$_SESSION['admin_logged_in'] = true`  
**Current Issues:**
- ❌ Only has admin login, no general user entry
- ⚠️ Uses different session variable (`admin_logged_in` vs `logged_in`)
- ⚠️ Separate from centralized authentication
- ⚠️ No role-based access control

---

### 6. **Live Status Module** (`/live_status/`)
**Entry Point:** `/live_status/admin/login.php` (ADMIN-ONLY)  
**Session Check:** `$_SESSION['user']['role'] !== 'admin'`  
**Current Issues:**
- ⚠️ Uses nested session structure (`$_SESSION['user']['role']`)
- ⚠️ Only admin login available
- ⚠️ Different from all other modules' session structure

---

## 🔄 Session Variable Inconsistencies

**Dashboard/Central Auth Uses:**
```php
$_SESSION['logged_in']    // boolean
$_SESSION['user']         // username string
$_SESSION['user_id']      // integer
$_SESSION['role']         // role string
$_SESSION['display_name'] // display name
```

**Classroom Monitoring Uses:**
```php
$_SESSION['user_id']  // integer
$_SESSION['pno']      // personnel number
$_SESSION['name']     // user name
$_SESSION['role']     // role string (different values: training_faculty, director, admin, instructor)
```

**Live Status Uses:**
```php
$_SESSION['user']['role']  // NESTED structure!
```

**Booking Admin Uses:**
```php
$_SESSION['admin_logged_in']  // boolean (NOT 'logged_in')
```

---

## 📊 RBAC Role Definitions (Conflicting)

### Central Dashboard Definition:
- `admin`: All modules + admin panel
- `user`: Training Portal, Booking, Network, Cloud
- `training`: Training Portal, Booking, Cloud
- `trainee`: Training Portal, Classroom Monitoring
- `director`: Classroom Monitoring
- `instructor`: Classroom Monitoring, Training Portal

### Classroom Monitoring Definition:
- `training_faculty`: index.php access
- `director`: director_dashboard.php
- `admin`: admin_dashboard.php
- `instructor`: instructor_dashboard.php

**Problem:** Same role names map to different things in different modules!

---

## 🚫 Broken Links & Missing Resources

### Missing Files:
- ❌ `/training-portal/admin/admin_panel.php` - Referenced in portals.php but doesn't exist
- ❌ `/niatcloud/uploads/` - Directory referenced in niatcloud/index.php but doesn't exist
- ❌ No global error handler

### Broken Redirects:
- ❌ `/dashboard/portals.php` line ~395 links to non-existent admin_panel.php
- ❌ Multiple modules redirect to `/niatcloud/login.php` instead of `/dashboard/login.php`

### Database Inconsistencies:
- Some modules use `nshare_lite_db.users` (centralized)
- Some modules use local databases (classroom_monitoring.users, live_network.users)
- Conflicting user schemas and role names

---

## 🎯 Module Features & Scope Analysis

| Module | Purpose | Users | Features | DB Used |
|--------|---------|-------|----------|---------|
| **Training Portal** | Course & content management | Admin, Faculty, Trainees | Courses, Materials (PPT, Video, CBT, QBank), Lessons | training_portal |
| **Classroom Monitor** | Class tracking & monitoring | Director, Instructors, Trainees | Schedule, Attendance, Analytics | classroom_monitoring |
| **Booking System** | Resource & slot booking | All Users | Slot booking, Approval workflow | booking |
| **NIAT Cloud** | Central file storage | All Users | File upload, folders, sharing | nshare_lite_db |
| **Live Status** | Device/network monitoring | Admin, Staff | Device status, health checks | live_network |
| **Admin Panel** | System administration | Admin only | User management, config | various |

---

## ✅ Requirements for Proper Integration

### 1. **Unified Authentication**
- ✅ Single login page: `/dashboard/login.php`
- ✅ Single auth backend: `/dashboard/php/login_central.php`
- ✅ Single database: `nshare_lite_db.users`
- ✅ Consistent session variables across all modules
- ✅ Role-based access control at module entry points

### 2. **Consistent RBAC**
- Define role hierarchy: Admin > Director > User/Training > Instructor > Trainee
- Map each role to accessible modules
- Implement access check at entry point of each module
- Consistent session variable naming

### 3. **Error Handling**
- Global error page for authentication failures
- Graceful handling of missing directories
- Clear error messages for unauthorized access
- Logging for security audit trail

### 4. **Directory Structure**
- Create missing upload directories
- Ensure all required folders exist before use
- Set proper permissions

---

## 🔐 Current Security Assessment

### ✅ Good:
- Bcrypt password hashing implemented
- Prepared statements used (SQL injection prevention)
- Input validation on most forms
- Session-based authentication
- htmlspecialchars() used for XSS prevention

### ⚠️ Needs Improvement:
- Multiple login pages increase attack surface
- No centralized error handling
- Inconsistent RBAC makes bugs easier
- Session variables could be compromised by module confusion
- No audit logging for failed attempts
- No session timeout enforcement
- Missing CSRF tokens on some forms

---

## 📝 Next Steps Required

1. **Create Global Error Handler** (`/dashboard/error.php`)
   - Handle authentication errors
   - Handle authorization errors
   - Handle database errors
   - Provide user-friendly error messages

2. **Create Unified Routing System** (`/dashboard/php/routing.php`)
   - Centralized RBAC checks
   - Consistent module access control
   - Role-based portal generation

3. **Fix NIAT Cloud Module** (`/niatcloud/index.php`)
   - Add error handling for missing uploads directory
   - Create directory if missing
   - Use centralized authentication

4. **Consolidate Authentication**
   - Remove module-specific login pages
   - All modules use `/dashboard/login.php`
   - All modules check `/dashboard/php/login_central.php` session
   - Standardize all session variables

5. **Create Missing Admin Panel** (`/training-portal/admin/admin_panel.php`)
   - Or remove link from portals.php
   - Clarify what admin panel should do

6. **Update All Module Entry Points**
   - Use consistent session checks
   - Use consistent role names
   - Use unified error handler

---

## 📚 Module-by-Module Access Matrix

```
                    Admin | User | Training | Director | Instructor | Trainee
Training Portal      ✓      ✓      ✓         ✓          ✓           ✓(RO)
Classroom Monitor    ✓      -      -         ✓          ✓(Own)       ✓(Own)
Booking System       ✓      ✓      ✓         ✓          -            -
NIAT Cloud          ✓      ✓      ✓         ✓          ✓            ✓
Live Status         ✓      ✓      -         -          -            -
Admin Panel         ✓      -      -         -          -            -
```

---

## 🔧 Configuration Recommendations

### Session Management:
```php
// All modules should use these variables
$_SESSION['logged_in']        // true/false
$_SESSION['user']            // username
$_SESSION['user_id']         // database id
$_SESSION['role']            // admin|user|training|director|instructor|trainee
$_SESSION['display_name']    // user's full name
$_SESSION['login_time']      // timestamp for timeout
```

### RBAC Table:
```php
$RBAC = [
    'admin' => ['training', 'classroom', 'booking', 'niatcloud', 'network', 'admin_panel'],
    'user' => ['training', 'booking', 'niatcloud', 'network'],
    'training' => ['training', 'booking', 'niatcloud'],
    'director' => ['training', 'classroom', 'booking', 'niatcloud'],
    'instructor' => ['training', 'classroom', 'booking', 'niatcloud'],
    'trainee' => ['training', 'classroom', 'niatcloud'],
];
```

---

## 📋 Code Quality Standards for Fixes

All code changes must include:
1. **Comments explaining why change was made**
2. **Original code shown (commented out)**
3. **New code implementation**
4. **RBAC checks at module entry**
5. **Error handling with fallback**
6. **Session validation before use**
7. **User feedback for authorization failures**

---

**Document Version:** 1.0  
**Last Updated:** February 19, 2026  
**Prepared By:** Code Analysis System
