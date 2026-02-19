# NATMS RBAC Integration Guide

**Purpose:** Complete guide for implementing unified RBAC across all NATMS modules  
**Version:** 2.0  
**Last Updated:** February 19, 2026  
**Status:** Ready for Implementation

---

## 📋 Overview

NATMS now has a **unified Role-Based Access Control (RBAC) system** that:
- ✅ Centralizes authentication through `/dashboard/login.php`
- ✅ Uses consistent session variables across all modules
- ✅ Provides a global error handler (`/dashboard/error.php`)
- ✅ Implements fine-grained access control (`/dashboard/php/rbac.php`)
- ✅ Routes users to correct modules based on role
- ✅ Logs security events for audit trails
- ✅ Implements session timeouts for security

---

## 🔐 Authentication Flow

### **Entry Point: Dashboard Login**
```
1. User visits any URL
   ↓
2. If NOT authenticated → redirect to /dashboard/login.php
   ↓
3. User submits credentials
   ↓
4. /dashboard/php/login_central.php validates against nshare_lite_db.users
   ↓
5. If valid → Create session with:
   - logged_in = true
   - user = username
   - user_id = id
   - role = role
   - display_name = full name
   - login_time = timestamp
   ↓
6. Redirect to /dashboard/portals.php
   ↓
7. Dashboard shows available modules for user's role
   ↓
8. User clicks module bubble
   ↓
9. Module checks RBAC before granting access
```

---

## 📁 File Structure & Changes

### **New Files Created**

#### 1. **`/dashboard/error.php`** (550+ lines)
- **Purpose:** Global error page for entire system
- **When used:** 
  - Authentication failures (401)
  - Authorization failures (403)  
  - Resource not found (404)
  - Server errors (500)
- **Features:**
  - Professional error display with navy/gold theme
  - Contextual error messages
  - Troubleshooting suggestions
  - Technical details (collapsible)
  - Navigation buttons (Return/Go Back)
  - Error logging

**Usage Example:**
```php
header('Location: /dashboard/error.php?code=403&type=permission&message=' . 
       urlencode('Access denied') . 
       '&redirect=/dashboard/portals.php&redirect_text=Return to Dashboard');
exit();
```

#### 2. **`/dashboard/php/rbac.php`** (400+ lines)
- **Purpose:** Unified RBAC system and function library
- **Key Functions:**
  - `is_authenticated()` - Check if user logged in
  - `has_module_access($module, $role)` - Check module access
  - `can_perform_action($module, $action, $role)` - Fine-grained control
  - `require_auth()` - Enforce authentication
  - `require_module_access($module, $allowed_roles)` - Enforce authorization
  - `require_action_permission($module, $action)` - Enforce action control
  - `get_user_info()` - Get standardized user data
  - `log_security_event($event, $severity)` - Audit logging
  - `check_session_timeout($timeout_minutes)` - Session timeout

**RBAC Matrix Defined:**
```php
admin       → [dashboard, training, classroom, booking, niatcloud, network, admin]
user        → [dashboard, training, booking, niatcloud, network]
training    → [dashboard, training, booking, niatcloud]
director    → [dashboard, training, classroom, booking, niatcloud]
instructor  → [dashboard, training, classroom, booking, niatcloud]
trainee     → [dashboard, training, classroom, niatcloud]
```

#### 3. **`/dashboard/admin_panel.php`** (370 lines)
- **Purpose:** Centralized admin dashboard
- **Access:** Admin role only
- **Features:**
  - System statistics display
  - Links to module-specific admin panels
  - Quick reference for roles and users
  - Documentation links
  - Professional theme matching NATMS branding

#### 4. **Created Directory: `/niatcloud/uploads/`**
- **Purpose:** Cloud file storage directory
- **Permissions:** Read/Write for web server
- **Auto-created:** If missing when niatcloud/index.php loads

### **Modified Files**

#### 1. **`/niatcloud/index.php`** (210 lines)
**Changes Made:**
- ✅ Added error handling for missing `uploads/` directory
- ✅ Directory auto-creation with permission checks
- ✅ Graceful error messages instead of fatal errors
- ✅ Better session variable documentation
- ✅ Comprehensive code comments explaining changes

**Before:** 
```php
$folders = array_filter(scandir($baseDir), ...);  // Fails if directory missing
```

**After:**
```php
// Create directory if missing
if (!file_exists($baseDir)) {
    if (!@mkdir($baseDir, 0755, true)) {
        header('Location: /dashboard/error.php?code=500...');
        exit();
    }
}

// Safely scan with error handling
$scan_result = @scandir($baseDir);
if ($scan_result === false) {
    header('Location: /dashboard/error.php?code=500...');
    exit();
}
```

#### 2. **`/dashboard/portals.php`** (471 lines)
**Changes Made:**
- ✅ Fixed admin panel link (was pointing to non-existent file)
- ✅ Updated to `/dashboard/admin_panel.php`

**Before:**
```php
'url' => '/training-portal/admin/admin_panel.php',  // ❌ Doesn't exist
```

**After:**
```php
'url' => '/dashboard/admin_panel.php',  // ✅ Centralized admin dashboard
```

---

## 🔧 HOW TO USE RBAC IN YOUR MODULE

### **Step 1: Include RBAC at Module Entry Point**

```php
<?php
// At the very top of your module's entry point (index.php or admin.php)
session_start();

// Include the RBAC library
require_once('/app/dashboard/php/rbac.php');

// Now you can use all RBAC functions
// Continue with your code...
```

### **Step 2: Require Authentication**

```php
<?php
require_once('/app/dashboard/php/rbac.php');

// MODIFIED: Require user to be logged in
// Reason: Prevent unauthorized access
// Original: Each module had its own check
// New: Unified authentication check
// Benefit: Consistent behavior across modules
require_auth();

// If we got here, user is logged in
```

### **Step 3: Require Module Access**

```php
<?php
require_once('/app/dashboard/php/rbac.php');

// MODIFIED: Require access to specific module
// Reason: Check user's role against RBAC matrix
// Original: No centralized access control
// New: Unified authorization check
// Benefit: Automatic redirection to error page if unauthorized
require_module_access('training-portal');

// If we got here, user is logged in AND has module access
```

### **Step 4: (Optional) Fine-Grained Action Control**

```php
<?php
require_once('/app/dashboard/php/rbac.php');

require_module_access('training-portal');

// MODIFIED: Check if user can delete courses
// Reason: Some roles can view but not delete
// Original: No action-level control
// New: Fine-grained permission checking
// Benefit: Different actions for different roles
if ($_POST['action'] === 'delete_course') {
    require_action_permission('training-portal', 'delete');
    // Proceed with deletion
}
```

### **Step 5: Get User Information**

```php
<?php
require_once('/app/dashboard/php/rbac.php');

require_auth();

// MODIFIED: Get user info from session
// Reason: Standardized user data structure
// Original: Each module accessed $_SESSION directly
// New: Single function with validation
// Benefit: Consistent data structure across modules
$user = get_user_info();

echo "Welcome, " . $user['display_name'];
echo "Your role is: " . $user['role'];
```

---

## 📚 RBAC Matrix - Detailed Breakdown

### **MODULE ACCESS**

| Role | Dashboard | Training | Classroom | Booking | NIAT Cloud | Network | Admin |
|------|-----------|----------|-----------|---------|------------|---------|-------|
| **admin** | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ |
| **user** | ✅ | ✅ | ❌ | ✅ | ✅ | ✅ | ❌ |
| **training** | ✅ | ✅ | ❌ | ✅ | ✅ | ❌ | ❌ |
| **director** | ✅ | ✅ | ✅ | ✅ | ✅ | ❌ | ❌ |
| **instructor** | ✅ | ✅ | ✅ | ✅ | ✅ | ❌ | ❌ |
| **trainee** | ✅ | ✅ | ✅ | ❌ | ✅ | ❌ | ❌ |

### **ACTION PERMISSIONS**

#### Training Portal:
```
admin     → [view, create, edit, delete, approve]
user      → [view, create, edit]
training  → [view, create, edit]
director  → [view, approve]
instructor→ [view]
trainee   → [view]         // Read-only
```

#### Classroom Monitoring:
```
admin      → [view, create, edit, delete]
director   → [view]
instructor → [view]        // Only own classes
trainee    → [view]        // Only own class
```

#### Booking System:
```
admin    → [view, create, edit, delete, approve, reject]
user     → [view, create]
training → [view, create]
director → [view, create]
```

#### NIAT Cloud:
```
admin      → [view, upload, delete, share]
user       → [view, upload, delete, share]
training   → [view, upload, delete, share]
director   → [view, upload, delete, share]
instructor → [view, upload, delete, share]
trainee    → [view, upload, share]        // No delete
```

#### Live Status:
```
admin → [view, create, edit, delete]
user  → [view]
```

---

## ⚠️ Error Handling

### **Authentication Error (401)**
```php
// User not logged in or session expired
header('Location: /dashboard/error.php?code=401&type=auth&message=' . 
       urlencode('Please log in to continue'));
exit();
```

### **Authorization Error (403)**
```php
// User logged in but lacks permission
header('Location: /dashboard/error.php?code=403&type=permission&message=' . 
       urlencode('You do not have permission to access this module'));
exit();
```

### **Not Found Error (404)**
```php
// Resource doesn't exist
header('Location: /dashboard/error.php?code=404&type=notfound&message=' . 
       urlencode('The resource you requested was not found'));
exit();
```

### **Server Error (500)**
```php
// Unexpected error
header('Location: /dashboard/error.php?code=500&type=error&message=' . 
       urlencode('An unexpected error occurred') . 
       '&details=' . urlencode('Database connection failed'));
exit();
```

---

## 🔒 Security Features

### **1. Authentication**
- ✅ Bcrypt password hashing (10 rounds)
- ✅ Prepared statements (SQL injection prevention)
- ✅ Password verification with `password_verify()`
- ✅ Account active status checking
- ✅ Session-based authentication

### **2. Authorization**
- ✅ Role-based access control (RBAC)
- ✅ Module-level access checks
- ✅ Action-level permission checks
- ✅ Consistent across all modules
- ✅ Centralized permission matrix

### **3. Input Validation**
- ✅ htmlspecialchars() for XSS prevention
- ✅ Input validation on all forms
- ✅ SQL injection prevention via prepared statements
- ✅ File path validation

### **4. Session Management**
- ✅ Session timeout (configurable, default 30 minutes)
- ✅ Session variables validation
- ✅ Login time tracking
- ✅ Activity-based timeout reset

### **5. Audit Logging**
- ✅ Security event logging
- ✅ Timestamp recording
- ✅ IP address tracking
- ✅ User identification
- ✅ Severity levels (low/medium/high)

### **6. Error Handling**
- ✅ Global error page (professional appearance)
- ✅ User-friendly error messages
- ✅ No sensitive information exposed
- ✅ Technical details available (collapsible)
- ✅ Troubleshooting suggestions

---

## 📊 Session Variables (Standard)

All modules must use these session variables ONLY:

```php
$_SESSION['logged_in']     // boolean - Authentication flag
$_SESSION['user']          // string - Username
$_SESSION['user_id']       // integer - Database ID
$_SESSION['role']          // string - User role (admin|user|training|director|instructor|trainee)
$_SESSION['display_name']  // string - User's full name
$_SESSION['login_time']    // integer - Session start timestamp

// NOT RECOMMENDED (for backward compatibility only):
// $_SESSION['admin_logged_in']  // DON'T USE - Use $logged_in instead
// $_SESSION['user']['role']     // DON'T USE - Use $_SESSION['role'] instead
// $_SESSION['pno']              // DON'T USE - Use $_SESSION['user_id'] instead
```

---

## 🚀 Implementation Checklist for Modules

For EACH module entry point, follow this checklist:

- [ ] Add `session_start()` at the very top
- [ ] Add `require_once('/app/dashboard/php/rbac.php')`
- [ ] Add `require_auth()` to check authentication
- [ ] Add `require_module_access('module-name')` for module check
- [ ] Use `get_user_info()` to retrieve user data
- [ ] Use `log_security_event()` for important actions
- [ ] Replace direct `$_SESSION` access with `get_user_info()`
- [ ] Update error redirects to use `/dashboard/error.php`
- [ ] Add comments explaining MODIFIED code changes
- [ ] Test access with different roles

---

## 🧪 Testing RBAC

### **Test Authentication:**
Visit: `/dashboard/debug_test.php`
- Shows all users in database
- Test any username/password combination
- Displays session information
- Verifies hash matching

### **Test Module Access:**
```php
// In your browser console or test script:
<?php
require_once('/app/dashboard/php/rbac.php');

// Simulate different roles
$roles = ['admin', 'user', 'training', 'director', 'instructor', 'trainee'];
$module = 'training-portal';

foreach ($roles as $role) {
    $has_access = has_module_access($module, $role);
    echo "$role can access $module: " . ($has_access ? 'YES' : 'NO') . "\n";
}
```

### **Test Session Timeout:**
```php
// Check if session has timed out
if (!check_session_timeout(30)) {  // 30 minutes
    echo "Session expired, please log in again";
}
```

---

## 📝 Code Comment Standards

All code changes must include comments in this format:

```php
// MODIFIED: What you changed
// Reason: Why you changed it
// Original: What was there before (show old code)
// New: What's new (show new code)
// Benefit: Why it's better
```

Example:
```php
// MODIFIED: Check session timeout
// Reason: Implement security timeout for inactive sessions
// Original: Sessions persisted indefinitely
// New: Auto-logout after 30 minutes of inactivity
// Benefit: Improved security, prevents unauthorized access
if (!check_session_timeout(30)) {
    header('Location: /dashboard/login.php?error=Session expired');
    exit();
}
```

---

## 🔄 Migration Guide - Existing Modules

### **Module: Training Portal**
**Current:** Uses `/niatcloud/login.php` or local login  
**Required Changes:**
1. Replace login check with `/dashboard/php/rbac.php`
2. Use `require_auth()` and `require_module_access('training-portal')`
3. Update all redirect URLs to use centralized system
4. Remove module-specific session variables
5. Use `get_user_info()` instead of direct `$_SESSION` access

### **Module: Classroom Monitoring**
**Current:** Has its own `/classroom_monitoring/routine/login.php`  
**Required Changes:**
1. Remove local login page
2. Redirect to `/dashboard/login.php`
3. Update entry points to use RBAC
4. Migrate all session variable names
5. Update redirection logic to match portal system

### **Module: Booking System**
**Current:** Has `/booking/admin/admin_login.php` with `admin_logged_in` flag  
**Required Changes:**
1. Remove `admin_login.php`
2. Redirect all to `/dashboard/login.php`
3. Check for `admin` role instead of `admin_logged_in` flag
4. Update admin_panel.php to use centralized auth
5. Update all internal redirects

### **Module: Live Status**
**Current:** Has `/live_status/admin/login.php` with nested role structure  
**Required Changes:**
1. Remove local login
2. Update session variable access (from `$_SESSION['user']['role']` to `$_SESSION['role']`)
3. Add RBAC checks to admin pages
4. Update redirects and error handling

### **Module: NIAT Cloud**
**Current:** Have fixed `/niatcloud/index.php` for directory handling  
**Required Changes:**
1. ✅ DONE - Already fixed directory handling
2. Continue to use centralized auth (already in place)
3. Monitor upload directory for issues

---

## 📞 Support & Troubleshooting

**Issue:** "Access Denied" error  
**Solution:** Check that user's role has access to module in RBAC matrix

**Issue:** "Session expired" error  
**Solution:** Session timeout (30 mins). Log in again.

**Issue:** Uploads directory permission error  
**Solution:** Check `/niatcloud/uploads/` exists and is writable

**Issue:** Module-specific login still used  
**Solution:** Remove module login, redirect to `/dashboard/login.php`

**Issue:** Inconsistent session variable names  
**Solution:** Use `get_user_info()` function instead

---

## 📋 Rollout Plan

### **Phase 1: Core System (DONE)**
- ✅ Create error handler
- ✅ Create RBAC system
- ✅ Create centralized admin dashboard
- ✅ Fix niatcloud directory issue
- ✅ Fix portals.php admin link
- ✅ Create implementation guide

### **Phase 2: Module Updates (TODO)**
- [ ] Update Training Portal entry points
- [ ] Update Classroom Monitoring entry points
- [ ] Update Booking System entry points
- [ ] Update Live Status entry points
- [ ] Add RBAC checks to admin pages
- [ ] Test all module access by role

### **Phase 3: Testing & Validation (TODO)**
- [ ] Test each role's module access
- [ ] Test action-level permissions
- [ ] Test session timeout
- [ ] Test error page display
- [ ] Test audit logging
- [ ] Security review

### **Phase 4: Documentation (TODO)**
- [ ] Update all module README files
- [ ] Create admin quick-start guide
- [ ] Document RBAC policies
- [ ] Create troubleshooting guide
- [ ] Finalize user manual

---

**Document Version:** 2.0  
**Last Updated:** February 19, 2026  
**Next Review:** After Phase 2 completion
