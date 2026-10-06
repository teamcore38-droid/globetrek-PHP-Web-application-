# GlobeTrek Adventures - System Documentation & Testing Evaluation (CSE5009)

## 1. Practical Architecture Overview
- **Application Name**: GlobeTrek Adventures
- **Headquarters**: Lewis Place, Negombo Beach Road, Negombo, Sri Lanka
- **Technology Stack**:
  - **Server-Side**: PHP 8.2 (Object-Oriented & PDO prepared queries)
  - **Database**: MySQL / MariaDB 10.4 (`globetrek_db` with relational foreign keys & indexes)
  - **Client-Side**: HTML5, CSS3, Bootstrap 5.3.3, Bootstrap Icons, JavaScript (ES6+), SweetAlert2, Chart.js
  - **Local Server**: Apache 2.4 (via XAMPP) / Port 80 & 3306
  - **Access URL**: `http://localhost/globetrek/`

---

## 2. Default Test Credentials

| Role | Email | Password | Access Rights |
| :--- | :--- | :--- | :--- |
| **Administrator** | `admin@globetrek.com` | `Admin@123` | Full system oversight, executive dashboard, financial charts, user/staff accounts, reports. |
| **Travel Agency Staff** | `staff@globetrek.com` | `Staff@123` | Package management (CRUD), booking reservations, hotel/transport coordination, query replies. |
| **Customer / Traveler** | `traveler@gmail.com` | `Traveler@123` | Browse & search tours, customize itineraries, book packages, view & print PDF vouchers. |

*Note: The login page includes convenient "One-Click Quick Login" demo buttons.*

---

## 3. Comprehensive Test Cases (Task 03 Evidence)

| Test ID | Module | Test Scenario | Test Input / Action | Expected Result | Actual Result | Status |
| :--- | :--- | :--- | :--- | :--- | :--- | :--- |
| **TC-01** | User Auth | Customer Registration Validation | Submit registration form with empty email or mismatched passwords | Form rejects submission, displays error alert, prevents database insert | Password mismatch alert displayed | **PASSED** |
| **TC-02** | User Auth | Successful Registration & Auto-Login | Register with valid full name, unique email, and password (min 6 chars) | Account created with bcrypt hash, session started, redirected to Traveler Dashboard | User created, session started, redirected to dashboard | **PASSED** |
| **TC-03** | User Auth | Role-Based Access Restriction | Customer tries to directly access `admin/dashboard.php` | Access blocked by `auth_guard.php`, flash alert displayed, redirected to safe page | Access denied, redirected with danger flash alert | **PASSED** |
| **TC-04** | Catalog | Real-time Search & Filter | Type "Sigiriya" or "Negombo" into package search input | Tour cards filtered instantly without page reload | Matching tour cards displayed, non-matching hidden | **PASSED** |
| **TC-05** | Booking | Dynamic Price Calculation | Change travelers count from 1 to 3 on Package Detail page | Total estimated price updates immediately ($Unit Price * 3) | Price updates dynamically in real-time | **PASSED** |
| **TC-06** | Booking | Database Transaction & ACID Integrity | Submit booking form with selected date and travelers count | Booking record inserted, payment record inserted, unique booking reference (`GTA-2026-XXXX`) generated | Transaction committed, booking reference generated, redirected to invoice voucher | **PASSED** |
| **TC-07** | Staff Desk | Package Creation & Update (CRUD) | Staff adds new tour package or updates price in `staff/package_edit.php` | Database updated with new package information, reflected on public catalog | Package saved, slug generated, visible immediately in catalog | **PASSED** |
| **TC-08** | Staff Desk | Booking Status & Logistics Note | Staff updates booking status to 'Confirmed' and enters hotel/chauffeur notes | Status updated, customer dashboard reflects confirmed status with coordination notes | Status updated and visible on traveler invoice | **PASSED** |
| **TC-09** | Inquiry Desk | Question Submission & Staff Reply | Traveler submits query via `views/contact.php`; Staff replies via `staff/inquiries_manage.php` | Query logged with 'pending' status; Staff reply updates status to 'replied' and displays in traveler dashboard | Query received and reply displayed in customer portal | **PASSED** |
| **TC-10** | Security | SQL Injection & XSS Defense | Input `' OR 1=1 --` into login and `<script>alert(1)</script>` into query form | PDO parameterized statements prevent SQL injection; `htmlspecialchars()` escapes scripts | Attacks neutralized, inputs safely escaped | **PASSED** |

---

## 4. Key Site Map & URLs

- **Homepage**: `http://localhost/globetrek/index.php`
- **Tour Packages Catalog**: `http://localhost/globetrek/views/packages.php`
- **Package Details & Booking**: `http://localhost/globetrek/views/package_detail.php?id=1`
- **Tailor-Made Trip Planner**: `http://localhost/globetrek/views/custom_trip.php`
- **About Us & Fleet**: `http://localhost/globetrek/views/about.php`
- **Contact & Inquiries Desk**: `http://localhost/globetrek/views/contact.php`
- **Sign In**: `http://localhost/globetrek/views/login.php`
- **Sign Up**: `http://localhost/globetrek/views/register.php`
- **Customer Dashboard**: `http://localhost/globetrek/views/customer/dashboard.php`
- **Traveler Booking Voucher (Printable)**: `http://localhost/globetrek/views/customer/invoice.php?id=1`
- **Staff Operations Desk**: `http://localhost/globetrek/staff/dashboard.php`
- **Staff Package CRUD**: `http://localhost/globetrek/staff/packages_manage.php`
- **Staff Bookings Manager**: `http://localhost/globetrek/staff/bookings_manage.php`
- **Staff Inquiry Response Desk**: `http://localhost/globetrek/staff/inquiries_manage.php`
- **Admin Executive Dashboard**: `http://localhost/globetrek/admin/dashboard.php`
- **Admin User & Staff Management**: `http://localhost/globetrek/admin/users_manage.php`
- **Admin Sales & Performance Reports**: `http://localhost/globetrek/admin/reports.php`
