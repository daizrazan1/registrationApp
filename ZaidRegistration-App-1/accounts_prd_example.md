# **Product Requirements Document (PRD)**

**Project:** User Registration & Profile System

Claude Share link [https://claude.ai/share/49e370a5-edfe-4746-8097-19c0c9be21b3](https://claude.ai/share/49e370a5-edfe-4746-8097-19c0c9be21b3)

Claude Chat link [https://claude.ai/chat/de6fd2bd-f0fc-4ee4-8378-42db4b876f5d](https://claude.ai/chat/de6fd2bd-f0fc-4ee4-8378-42db4b876f5d) 

---

## **1\. Summary & Learning Goals**

**Summary:** A simple web application that allows users to register accounts, log in securely, manage their profiles, and log out. This project focuses on understanding user authentication, session management, and data persistence.

**Learning Goals:**

* Learn to work with JSON files for data storage and manipulation  
* Understand PHP sessions for user authentication and state management  
* Practice form validation and user input sanitization  
* Explore the transition from file-based storage (JSON) to database storage (MySQL)  
* Build responsive web interfaces using HTML/CSS and Bootstrap

---

## **2\. Scope**

**In Scope (MVP):**

* Landing page (index.php) with app information and navigation to register/login  
* User registration with account creation  
* User login with username/password authentication  
* User logout functionality accessible from all authenticated pages  
* Profile page displaying user information (username, first name, last name, profile picture)  
* Profile editing capabilities (personal information and password)

**Out of Scope:**

* Email verification or password reset features  
* Advanced user roles or permissions  
* File upload functionality for profile pictures (use placeholder paths)

**Stretch Goals:**

* User directory page (users.php) showing all registered users  
* Enhanced profile customization options

---

## **3\. Users & User Stories**

**Roles:** Guest (unregistered), Registered User

**User Stories:**

* As a **Guest**, I can view the landing page to learn about the application  
* As a **Guest**, I can register for a new account so I can access the application  
* As a **Guest**, I can log in with my credentials to access my account  
* As a **Registered User**, I can view my profile information  
* As a **Registered User**, I can edit my profile details to keep information current  
* As a **Registered User**, I can change my password for security  
* As a **Registered User**, I can log out to secure my account

---

## **4\. Pages & User Flow**

**Core Pages:**

* **GET /index.php:** Landing page with app info, register/login options  
* **GET /register.php:** Registration form (username, email, password, first name, last name)  
* **POST /register.php:** Process registration → redirect to login on success  
* **GET /login.php:** Login form (username, password)  
* **POST /login.php:** Authenticate user → start session → redirect to profile  
* **POST /logout.php:** End session → redirect to landing page  
* **GET /profile.php:** Display user profile (requires authentication)  
* **GET /edit-profile.php:** Form to edit profile information (requires authentication)  
* **POST /edit-profile.php:** Update profile → show success message  
* **GET /change-password.php:** Form to change password (requires authentication)  
* **POST /change-password.php:** Update password → show confirmation

**Navigation Flow:**

* Unauthenticated users can only access: index.php, register.php, login.php  
* Authenticated users have logout option in top-right corner of all pages  
* Profile page links to edit-profile.php and change-password.php

---

## **5\. Data Model (JSON Structure)**

**File: users.json**

json  
\[  
  {  
    "uid": "unique\_id\_string",  
    "username": "string",  
    "email": "email@example.com",   
    "password": "plain\_text\_password"  
  }

\]

**File: profiles.json**

json  
\[  
  {  
    "uid": "unique\_id\_string",  
    "firstName": "string",  
    "lastName": "string",  
    "imgPath": "path/to/profile/image.jpg"  
  }

\]

**Test Data:** Create 5 sample user profiles with realistic data for testing purposes.

**Future MySQL Tables:**

* `users` table: id, username, email, password\_hash, created\_at  
* `profiles` table: id, user\_id, first\_name, last\_name, img\_path, updated\_at

---

## **6\. Security & Validation Plan**

**Authentication:**

* Use PHP sessions to track logged-in users  
* Start session after successful login  
* Destroy session on logout  
* Redirect unauthenticated users trying to access protected pages

**Password Security:**

* Store passwords in plain text (for learning purposes only)  
* Password requirements: minimum 1 uppercase, 1 lowercase, 1 number, 1 special character  
* Clear error messaging for failed login attempts

**Data Validation:**

* Unique username requirement (no duplicates)  
* Unique email address requirement (no duplicates)  
* Required field validation for all form inputs  
* Sanitize user input to prevent basic security issues

**Error Handling:**

* Specific error messages for registration validation failures  
* Generic error message for login failures  
* Success confirmations for profile updates

---

## **7\. Success Criteria (Testing Checklist)**

**Registration & Login:**

* I can successfully register a new account with valid information  
* I cannot register with a duplicate username or email  
* I can log in with correct credentials and access my profile  
* I see an appropriate error message when using wrong login credentials  
* Password validation rules are enforced during registration

**Profile Management:**

* Profile page displays my username, first name, last name, and profile picture  
* I can edit my profile information and see updates immediately  
* I can change my password successfully  
* Profile changes persist after logging out and back in

**Session Management:**

* I can log out from any authenticated page  
* After logout, I cannot access profile pages without logging in again  
* Unauthenticated users are redirected to login when accessing protected pages

**Data Persistence:**

* User data persists in JSON files between sessions  
* All five test profiles load correctly

---

## **8\. Technical Implementation Notes**

**File Structure:**

project/  
├── index.php (landing page)  
├── register.php (registration)  
├── login.php (authentication)    
├── logout.php (session termination)  
├── profile.php (view profile)  
├── edit-profile.php (edit profile)  
├── change-password.php (password change)  
├── data/  
│   ├── users.json  
│   └── profiles.json  
├── includes/  
│   ├── functions.php (helper functions)  
│   └── session.php (session management)  
└── assets/  
    ├── css/ (Bootstrap \+ custom styles)

    └── images/ (profile pictures)

**Key Learning Concepts:**

* JSON file reading/writing in PHP  
* PHP session management  
* Form processing and validation  
* Bootstrap responsive design  
* Preparing for database migration

---

## **9\. Migration Plan (JSON to MySQL)**

**Phase 1:** Complete JSON-based implementation **Phase 2:** Design MySQL schema **Phase 3:** Create database migration script to transfer JSON data **Phase 4:** Refactor code to use MySQL instead of JSON files **Phase 5:** Implement password hashing for production security

This approach allows students to understand both file-based and database storage methods while building the same application twice with different backends.

