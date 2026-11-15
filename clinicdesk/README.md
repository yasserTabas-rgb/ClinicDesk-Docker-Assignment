# ClinicDesk — Clinic Management Dashboard

> PHP Final Project · SDEV 2106 / WDMM 2010 / MOBC 2102 · Semester 2, 2025–2026
> Instructor: Eng. Mohammed Zuqlam · Islamic University of Gaza

---

## Overview

ClinicDesk is a private, login-protected clinic management system built in pure PHP (no framework).
It serves three roles — **Admin**, **Doctor**, and **Patient** — each with a dedicated dashboard and
access-controlled features.

---

## Tech Stack

| Layer       | Technology                          |
|-------------|-------------------------------------|
| Language    | PHP 8.1+                            |
| Database    | MySQL 8 / MariaDB 10.6+             |
| Frontend    | AdminLTE 3 + Bootstrap 4            |
| Icons       | Font Awesome 5                      |
| Tables      | DataTables (bundled in AdminLTE)    |
| Web server  | Apache 2.4 + mod_rewrite            |

---

## Features by Role

### Admin
- Full user CRUD (create patients, doctors, admins)
- Doctor profile management (specialization, fee, availability, photo)
- Specialization CRUD with safe-delete guard
- View & filter all appointments, update any status
- Generate appointment reports with CSV export

### Doctor
- View today's appointments at a glance on the dashboard
- Full schedule with date/status filters
- Confirm / Complete / Cancel appointments with notes
- Add prescriptions (with optional PDF upload) to completed appointments
- Edit own profile

### Patient
- Book appointments (conflict check + available-days validation)
- View & filter appointment history
- Cancel pending appointments
- Download own prescriptions (securely served through PHP)

---

## Security Implemented

| Item                  | Implementation                                               |
|-----------------------|--------------------------------------------------------------|
| Passwords             | `password_hash(PASSWORD_BCRYPT)` + `password_verify()`      |
| CSRF                  | Every POST carries a token; validated with `hash_equals()`  |
| SQL Injection         | Zero raw SQL — all queries use `mysqli` prepared statements  |
| XSS                   | Every output wrapped in `htmlspecialchars()` via `e()`       |
| Session Fixation      | `session_regenerate_id(true)` on every login                 |
| File Type Validation  | Images: `getimagesize()` · PDFs: `finfo_file()`             |
| File Access Control   | Prescriptions folder blocked via `.htaccess`; served via PHP |
| Directory Listing     | `index.php` guard in every upload subfolder                  |
| Role Enforcement      | `Auth::requireRole()` is the first line of every action      |
| Credentials in Git    | `config/database.php` is in `.gitignore`                    |

---

## Installation

### 1. Clone / Download

```bash
git clone <repo-url> clinicdesk
# OR extract the ZIP into your web root
```

### 2. Web Server

Place the project inside your Apache web root, e.g.:
```
/var/www/html/clinicdesk/
# or XAMPP: C:\xampp\htdocs\clinicdesk\
```

Ensure `mod_rewrite` is enabled and `AllowOverride All` is set for the directory.

### 3. Database

```bash
mysql -u root -p < clinicdesk_db.sql
```

This creates `clinicdesk_db`, all five tables, and seeds demo accounts.

### 4. Configuration

Edit `config/database.php` to match your MySQL credentials:

```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'clinicdesk_db');
define('DB_USER', 'root');
define('DB_PASS', 'your_password');
```

Edit `config/config.php` to set `BASE_URL`:

```php
define('BASE_URL', 'http://localhost/clinicdesk');
```

### 5. AdminLTE

Download AdminLTE 3.x from https://adminlte.io and extract it to:
```
public/assets/adminlte/
```

The directory should contain `dist/`, `plugins/`, etc.

### 6. Directory Permissions

```bash
chmod -R 775 public/uploads/
```

### 7. Open in Browser

```
http://localhost/clinicdesk/index.php?page=auth&action=login
```

---

## Demo Accounts

> **Note:** These are for development only. Change all passwords before any real deployment.

| Role    | Email                  | Password      |
|---------|------------------------|---------------|
| Admin   | admin@clinic.local     | Admin@1234    |
| Doctor  | doctor@clinic.local    | Doctor@1234   |
| Patient | patient@clinic.local   | Patient@1234  |

---

## Project Structure

```
clinicdesk/
├── index.php               ← Front controller (routes all requests)
├── .htaccess               ← Rewrites everything through index.php
├── clinicdesk_db.sql       ← Full schema + seed data
│
├── config/
│   ├── config.php          ← APP_NAME, BASE_URL, upload limits
│   └── database.php        ← DB credentials (gitignored)
│
├── core/
│   ├── Database.php        ← Singleton mysqli wrapper
│   ├── Auth.php            ← login / logout / requireRole
│   ├── CSRF.php            ← generateToken / validateToken
│   ├── Paginator.php       ← offset / totalPages / hasPrev / hasNext
│   └── helpers.php         ← redirect / e / flash / formatDate …
│
├── models/                 ← One class per table, all use prepared stmts
├── controllers/            ← Business logic; talk to models, load views
├── views/                  ← Pure HTML+PHP output; no DB calls
│   └── partials/           ← header / navbar / sidebar / footer / alerts
│
└── public/
    ├── assets/adminlte/    ← AdminLTE CSS/JS (download separately)
    └── uploads/
        ├── avatars/
        ├── doctor_photos/
        └── prescriptions/  ← Blocked from direct URL access
```

---

## Key Design Patterns Used

- **Singleton** — `Database::getInstance()` ensures one connection per request
- **Front Controller** — all requests flow through `index.php`
- **MVC separation** — Models own SQL, Controllers own logic, Views own output
- **BaseModel** — shared `execute()` method centralises error handling

---

## Git Commits Convention

```
feat: add appointment booking with conflict check
fix: correct CSRF token validation in logout
security: block prescription folder via .htaccess
refactor: extract filter-building into AppointmentModel::buildFilters
docs: update README with installation steps
```

---

## License

For academic use only. Islamic University of Gaza, 2025–2026.

## 🚀 Running with Docker

### 1. Build the Docker Image
Open your terminal inside the project directory and run the following command to build the image:
```bash
docker build -t clinicdesk-app .