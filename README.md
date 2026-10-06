# GlobeTrek Adventures - Web Application (CSE 5009)

[![PHP Version](https://img.shields.io/badge/PHP-8.2-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://php.net)
[![MySQL](https://img.shields.io/badge/MySQL-8.0-4479A1?style=for-the-badge&logo=mysql&logoColor=white)](https://mysql.com)
[![Bootstrap](https://img.shields.io/badge/Bootstrap-5.3-7952B3?style=for-the-badge&logo=bootstrap&logoColor=white)](https://getbootstrap.com)
[![Chart.js](https://img.shields.io/badge/Chart.js-4.0-FF6384?style=for-the-badge&logo=chartdotjs&logoColor=white)](https://chartjs.org)

An interactive, responsive travel and tourism web application developed for **GlobeTrek Adventures**, an inbound tour operating agency headquartered along Lewis Place, Negombo Beach, Sri Lanka.

---

## 🌟 System Highlights & Core Modules

### 1. Public & Traveler Experience
- **Interactive Landing Page**: Hero search widget, Negombo regional spotlight, and featured tour package showcases.
- **Faceted Tour Catalog & Instant Live Search**: Filter tours by keyword, category, duration, and dynamic budget slider.
- **Detailed Tour Presentation**: Day-by-day itineraries, inclusions vs. exclusions, hotel standards, transport fleet details, and customer reviews.
- **Dynamic Booking & Checkout Engine**: Live calculation (`Unit Price × Travelers`), date selector, dietary/special requests, and simulated secure card transactions.
- **Tailor-Made Trip Planner**: Bespoke multi-destination customized itinerary builder.
- **Traveler Portal**: Self-service reservation tracking, custom requests, question query desk, and printable official travel voucher receipts (`@media print`).

### 2. Travel Agency Staff Desk
- **Operations Dashboard**: Real-time KPI counters (Pending Bookings, Confirmed Tours, Active Packages, Open Inquiries).
- **Tour Package Management (CRUD)**: Create, edit, and deactivate tour packages with itineraries, images, and pricing.
- **Reservations & Logistics Coordinator**: Review traveler bookings, update statuses, and log hotel room and chauffeur coordination notes.
- **Inquiry Response Desk**: View incoming traveler queries and dispatch official agency replies.

### 3. Administrator Executive Portal
- **Executive Analytics Dashboard**: Gross paid sales, total bookings, registered customers, and an interactive **Chart.js** category revenue breakdown.
- **User & Staff Directory**: Provision agency staff accounts, toggle account statuses (Active/Inactive), and reset passwords.
- **Sales & Business Audit Reports**: Tour performance ranking, reservation pipeline distribution, and print-ready business reports.

---

## 🗄️ Database Architecture (`globetrek_db`)

The database consists of 9 normalized relational tables with foreign keys and cascading integrity rules:
1. `roles`: Role definitions (`admin`, `staff`, `customer`).
2. `users`: User profiles with Bcrypt-hashed credentials.
3. `categories`: Tour categories (Coastal & Lagoon, Cultural Heritage, Hill Country, Wildlife Safari, Adventure & Hiking).
4. `packages`: Tour packages with pricing, itineraries, and inclusions.
5. `bookings`: Reservation records with unique references (e.g., `GTA-2026-001`).
6. `payments`: Verified transaction records linked to bookings.
7. `custom_trips`: Tailor-made tour inquiry submissions.
8. `inquiries`: Customer questions and staff official responses.
9. `reviews`: Traveler package ratings (1 to 5 stars) and feedback.

---

## 🚀 Quick Setup Instructions

### Prerequisites
- [XAMPP](https://www.apachefriends.org/) (with **PHP 8.1+** and **MySQL / MariaDB**)

### Installation
1. Clone this repository into your web server directory:
   ```bash
   git clone https://github.com/teamcore38-droid/globetrek-PHP-Web-application-.git C:/xampp/htdocs/globetrek
   ```
2. Start **Apache** and **MySQL** from your XAMPP Control Panel.
3. Import the database using the automated one-click migration script:
   ```bash
   cd C:/xampp/htdocs/globetrek
   php database/migrate.php
   ```
   *(Alternatively, import `database/schema.sql` via phpMyAdmin at `http://localhost/phpmyadmin/`)*
4. Open your browser and navigate to:
   ```text
   http://localhost/globetrek/
   ```

---

## 🔐 Default Demo Accounts

The login page at `/views/login.php` includes **one-click autofill buttons** for quick evaluation:

| Role | Email | Password | Access Rights |
| :--- | :--- | :--- | :--- |
| **System Administrator** | `admin@globetrek.com` | `Admin@123` | Full system access, analytics charts, staff provisioning, sales reports |
| **Agency Staff** | `staff@globetrek.com` | `Staff@123` | Tour package CRUD, booking logistics, hotel/transport notes, inquiry reply desk |
| **Traveler (Customer)** | `traveler@gmail.com` | `Traveler@123` | Booking tours, custom trip planner, viewing and printing vouchers |

---

## 📐 Wireframes & Site Map (Figma Ready)

Low-fidelity UI wireframe mockups and a complete 3-tier hierarchical site map are located in `/wireframes/`:
- **Site Map**: `/wireframes/sitemap.html`
- **Master Wireframes Hub**: `/wireframes/index.html`
- Optimized for direct import into **Figma** using the **html.to.design** plugin.

---

## 📸 Screenshots Directory

All visual evidence, UI mockups, database schemas, and testing screenshots are organized inside the `/screenshots/` folder.
