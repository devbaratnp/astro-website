# AstroShreehari Full Digital Platform — Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Transform the static 5-page astrology consultation website into a full digital platform with online booking, kundali generation, e-pooja, daily panchang, and multi-language support.

**Architecture:** Hybrid approach — existing static HTML/CSS/JS frontend served via GitHub Pages (or CDN), with a PHP + MySQL backend API hosted on affordable Nepali shared hosting. The frontend communicates with the backend via `fetch()` REST calls. Admin panel is PHP-rendered for simplicity.

**Tech Stack:**
- **Frontend:** HTML5, CSS3 (existing), Vanilla JS (existing) — enhanced with fetch-based API calls
- **Backend:** PHP 8.x + MySQL 8.x — REST API + Admin panel
- **Astrology:** Custom PHP kundali calculator OR third-party API (e.g., AstroSage, Prokerala)
- **Video:** Google Meet / Zoom embed (manual scheduling) or WebRTC (future)
- **Notifications:** Web Push API + service worker
- **Deployment:** Frontend → GitHub Pages, Backend → cPanel/Plesk shared hosting

---

## File Structure

```
astroshreehari/
├── index.html                          # Homepage (static, existing)
├── about.html                          # About page (static, existing)
├── services.html                       # Services page (static, existing)
├── appointment.html                    # Appointment form (modified: fetch → backend)
├── contact.html                        # Contact page (static, existing)
├── kundali.html                        # [NEW] Kundali generation page
├── pooja.html                          # [NEW] E-pooja booking page
├── panchang.html                       # [NEW] Daily panchang page
├── blog/                               # [NEW] Blog/articles directory
│   └── index.html
├── lang/                               # [NEW] i18n JSON files
│   ├── ne.json
│   └── en.json
├── sw.js                               # [NEW] Service worker for push + offline
├── manifest.json                       # [NEW] PWA manifest
│
├── assets/
│   ├── styles.css                      # Existing (minor additions)
│   ├── script.js                       # Existing (add API client + i18n)
│   ├── admin.css                       # [NEW] Admin panel styles
│   └── admin.js                        # [NEW] Admin panel JS
│   └── payments/                       # [NEW] Static payment QR codes
│       ├── esewa-qr.png
│       ├── khalti-qr.png
│       ├── imepay-qr.png
│       └── bank-details.txt
│
├── backend/                            # [NEW] PHP Backend (deployed separately)
│   ├── .htaccess                       # URL rewriting + CORS
│   ├── config/
│   │   ├── database.php                # DB connection
│   │   ├── app.php                     # App config (API keys, URLs)
│   │   └── cors.php                    # CORS headers
│   ├── db/
│   │   └── schema.sql                  # Full MySQL schema
│   ├── middleware/
│   │   ├── auth.php                    # JWT/session auth
│   │   └── validate.php                # Input validation helpers
│   ├── api/
│   │   ├── appointments.php            # CRUD appointments
│   │   ├── kundali.php                 # Kundali generation endpoint
│   │   ├── pooja.php                   # Pooja services CRUD
│   │   ├── payments.php                # Manual payment submission (screenshot + ref)
│   │   ├── rewards.php                 # Reward claim / status check
│   │   ├── panchang.php                # Daily panchang data
│   │   ├── auth.php                    # Login/register
│   │   └── contact.php                 # Contact form handler
│   ├── lib/
│   │   ├── Astrology.php               # Kundali calculation engine
│   │   └── Panchang.php                # Panchang calculation
│   ├── admin/
│   │   ├── index.php                   # Admin login
│   │   ├── dashboard.php               # Admin dashboard
│   │   ├── appointments.php            # Manage appointments
│   │   ├── pooja-orders.php            # Manage pooja orders
│   │   ├── payments.php                # Verify manual payments (QR uploads)
│   │   ├── rewards.php                 # Create / award rewards to users
│   │   ├── panchang-admin.php          # Edit panchang
│   │   └── settings.php                # Site settings
│   └── includes/
│       ├── header.php                  # Admin header
│       ├── footer.php                  # Admin footer
│       └── helpers.php                 # Utility functions
│
└── docs/
    └── plans/
        └── 2026-07-10-astroshreehari-full-platform.md
```

---

## Phases & Tasks

### Phase 1: Backend Foundation + Database

> **Goal:** Set up PHP backend, MySQL database, and REST API infrastructure.

### Task 1.1: Database Schema

**Files:**
- Create: `backend/db/schema.sql`

- [ ] **Step 1: Write the complete MySQL schema**

```sql
-- backend/db/schema.sql

CREATE DATABASE IF NOT EXISTS astroshreehari CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE astroshreehari;

-- Users (clients who register)
CREATE TABLE users (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100),
    phone VARCHAR(20) NOT NULL,
    password_hash VARCHAR(255),
    language ENUM('ne', 'en') DEFAULT 'ne',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_phone (phone),
    INDEX idx_email (email)
) ENGINE=InnoDB;

-- Appointments
CREATE TABLE appointments (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED,
    name VARCHAR(100) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    email VARCHAR(100),
    service_type ENUM('kundali', 'marriage', 'grahadasha', 'vastu', 'pooja', 'general') NOT NULL,
    preferred_date DATE,
    preferred_time TIME,
    consultation_mode ENUM('phone', 'whatsapp', 'video', 'inperson') DEFAULT 'whatsapp',
    birth_date DATE,
    birth_time TIME,
    birth_place VARCHAR(200),
    message TEXT NOT NULL,
    status ENUM('pending', 'confirmed', 'completed', 'cancelled') DEFAULT 'pending',
    admin_notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_status (status),
    INDEX idx_date (preferred_date)
) ENGINE=InnoDB;

-- Pooja / Ritual Services
CREATE TABLE pooja_services (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title_ne VARCHAR(200) NOT NULL,
    title_en VARCHAR(200) NOT NULL,
    description_ne TEXT,
    description_en TEXT,
    category ENUM('shanti', 'graha', 'sanskar', 'festival', 'other') NOT NULL,
    base_price DECIMAL(10,2),
    duration_minutes INT,
    materials_available BOOLEAN DEFAULT FALSE,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_category (category),
    INDEX idx_active (is_active)
) ENGINE=InnoDB;

-- Pooja Bookings
CREATE TABLE pooja_bookings (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED,
    service_id INT UNSIGNED NOT NULL,
    name VARCHAR(100) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    email VARCHAR(100),
    preferred_date DATE NOT NULL,
    preferred_time TIME,
    address TEXT,
    special_instructions TEXT,
    needs_materials BOOLEAN DEFAULT FALSE,
    is_live_stream BOOLEAN DEFAULT FALSE,
    status ENUM('pending', 'confirmed', 'completed', 'cancelled') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (service_id) REFERENCES pooja_services(id),
    INDEX idx_status (status),
    INDEX idx_date (preferred_date)
) ENGINE=InnoDB;

-- Panchang (daily)
CREATE TABLE panchang (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    date DATE NOT NULL UNIQUE,
    tithi VARCHAR(100),
    nakshatra VARCHAR(100),
    sunrise TIME,
    sunset TIME,
    rahu_kaal TIME,
    auspicious_times JSON,
    special_events_ne TEXT,
    special_events_en TEXT,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_date (date)
) ENGINE=InnoDB;

-- Manual payments (static QR)
CREATE TABLE payments (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    booking_type ENUM('appointment', 'pooja', 'reward') NOT NULL,
    booking_id INT UNSIGNED NOT NULL,
    user_name VARCHAR(100) NOT NULL,
    user_phone VARCHAR(20) NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    method ENUM('esewa', 'khalti', 'imepay', 'bank') NOT NULL,
    transaction_ref VARCHAR(100) NOT NULL,
    screenshot_path VARCHAR(300),
    admin_notes TEXT,
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_status (status),
    INDEX idx_booking (booking_type, booking_id)
) ENGINE=InnoDB;

-- Rewards / Awards
CREATE TABLE rewards (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_name VARCHAR(100) NOT NULL,
    user_phone VARCHAR(20) NOT NULL,
    reward_type ENUM('feature', 'discount', 'badge', 'service', 'other') NOT NULL,
    title_ne VARCHAR(200) NOT NULL,
    title_en VARCHAR(200),
    description_ne TEXT,
    description_en TEXT,
    is_redeemed BOOLEAN DEFAULT FALSE,
    expires_at DATE NULL,
    awarded_by INT UNSIGNED,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (awarded_by) REFERENCES admin_users(id),
    INDEX idx_phone (user_phone),
    INDEX idx_redeemed (is_redeemed)
) ENGINE=InnoDB;

-- Admin users
CREATE TABLE admin_users (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    display_name VARCHAR(100),
    role ENUM('admin', 'editor') DEFAULT 'editor',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Insert default admin (password: change-on-first-login)
INSERT INTO admin_users (username, password_hash, display_name, role)
VALUES ('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrator', 'admin');

-- Blog / Articles
CREATE TABLE articles (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title_ne VARCHAR(200) NOT NULL,
    title_en VARCHAR(200),
    slug VARCHAR(200) NOT NULL UNIQUE,
    content_ne TEXT NOT NULL,
    content_en TEXT,
    excerpt_ne VARCHAR(300),
    excerpt_en VARCHAR(300),
    cover_image VARCHAR(300),
    tags JSON,
    is_published BOOLEAN DEFAULT FALSE,
    published_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_slug (slug),
    INDEX idx_published (is_published, published_at)
) ENGINE=InnoDB;

-- Contact messages
CREATE TABLE contact_messages (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    phone VARCHAR(20),
    email VARCHAR(100),
    subject VARCHAR(200) NOT NULL,
    message TEXT NOT NULL,
    is_read BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_read (is_read)
) ENGINE=InnoDB;
```

- [ ] **Step 2: Save the schema file**

---

### Task 1.2: Backend Config & Database Connection

**Files:**
- Create: `backend/config/database.php`
- Create: `backend/config/app.php`
- Create: `backend/config/cors.php`
- Create: `backend/.htaccess`

- [ ] **Step 1: Create database config**

```php
<?php
// backend/config/database.php

class Database {
    private static ?PDO $instance = null;

    public static function getConnection(): PDO {
        if (self::$instance === null) {
            $host = getenv('DB_HOST') ?: 'localhost';
            $dbname = getenv('DB_NAME') ?: 'astroshreehari';
            $username = getenv('DB_USER') ?: 'root';
            $password = getenv('DB_PASS') ?: '';
            $charset = 'utf8mb4';

            self::$instance = new PDO(
                "mysql:host={$host};dbname={$dbname};charset={$charset}",
                $username,
                $password,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ]
            );
        }
        return self::$instance;
    }
}
```

- [ ] **Step 2: Create app config**

```php
<?php
// backend/config/app.php

define('SITE_NAME_NE', 'श्रीहरि ज्योतिष परामर्श केन्द्र');
define('SITE_NAME_EN', 'Shreehari Jyotish Paramarsha Kendra');
define('BASE_URL', getenv('APP_URL') ?: 'https://www.astroshreehari.com');
define('API_URL', getenv('API_URL') ?: 'https://api.astroshreehari.com');
define('ADMIN_EMAIL', 'shreeharijyotishparamarsakendr@gmail.com');
define('WHATSAPP_NUMBER', '9779844639228');
define('TIMEZONE', 'Asia/Kathmandu');
define('JWT_SECRET', getenv('JWT_SECRET') ?: 'change-this-to-a-random-secret');
date_default_timezone_set(TIMEZONE);
```

- [ ] **Step 3: Create CORS config**

```php
<?php
// backend/config/cors.php

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: ' . (getenv('APP_URL') ?: 'https://www.astroshreehari.com'));
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Access-Control-Max-Age: 86400');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}
```

- [ ] **Step 4: Create .htaccess**

```apache
# backend/.htaccess
RewriteEngine On
RewriteBase /

# Route API requests
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^api/(.*)$ api/$1 [L,QSA]

# Deny access to config
<FilesMatch "\.(env|sql)$">
    Require all denied
</FilesMatch>

# CORS
Header always set Access-Control-Allow-Origin "https://www.astroshreehari.com"
Header always set Access-Control-Allow-Methods "GET, POST, PUT, DELETE, OPTIONS"
Header always set Access-Control-Allow-Headers "Content-Type, Authorization"
```

---

### Task 1.3: API Router & Helper Functions

**Files:**
- Create: `backend/includes/helpers.php`
- Create: `backend/middleware/validate.php`

- [ ] **Step 1: Create helpers**

```php
<?php
// backend/includes/helpers.php

function jsonResponse(mixed $data, int $status = 200): void {
    http_response_code($status);
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

function jsonError(string $message, int $status = 400): void {
    jsonResponse(['success' => false, 'message' => $message], $status);
}

function jsonSuccess(mixed $data, string $message = 'OK'): void {
    jsonResponse(['success' => true, 'message' => $message, 'data' => $data]);
}

function getJsonInput(): array {
    $input = file_get_contents('php://input');
    return json_decode($input, true) ?: [];
}

function sanitize(string $value): string {
    return htmlspecialchars(strip_tags(trim($value)), ENT_QUOTES, 'UTF-8');
}
```

- [ ] **Step 2: Create validation middleware**

```php
<?php
// backend/middleware/validate.php

function validateRequired(array $data, array $fields): ?string {
    foreach ($fields as $field) {
        if (!isset($data[$field]) || trim((string)$data[$field]) === '') {
            return "{$field} is required";
        }
    }
    return null;
}

function validatePhone(string $phone): bool {
    return (bool)preg_match('/^\+?[0-9]{7,15}$/', $phone);
}

function validateEmail(string $email): bool {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}
```

---

### Phase 2: Appointment Booking System

> **Goal:** Convert the WhatsApp-only form to a proper backend-driven booking system.

### Task 2.1: Appointment API

**Files:**
- Create: `backend/api/appointments.php`

- [ ] **Step 1: Create the appointments API**

```php
<?php
// backend/api/appointments.php

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/cors.php';
require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../middleware/validate.php';

$method = $_SERVER['REQUEST_METHOD'];
$db = Database::getConnection();

switch ($method) {
    case 'POST':
        $input = getJsonInput();

        $error = validateRequired($input, ['name', 'phone', 'service_type', 'message']);
        if ($error) {
            jsonError($error);
        }

        $stmt = $db->prepare("
            INSERT INTO appointments (name, phone, email, service_type, preferred_date, preferred_time, consultation_mode, birth_date, birth_time, birth_place, message, status)
            VALUES (:name, :phone, :email, :service_type, :preferred_date, :preferred_time, :consultation_mode, :birth_date, :birth_time, :birth_place, :message, 'pending')
        ");

        $stmt->execute([
            ':name' => sanitize($input['name']),
            ':phone' => sanitize($input['phone']),
            ':email' => sanitize($input['email'] ?? ''),
            ':service_type' => sanitize($input['service_type']),
            ':preferred_date' => $input['preferred_date'] ?? null,
            ':preferred_time' => $input['preferred_time'] ?? null,
            ':consultation_mode' => $input['consultation_mode'] ?? 'whatsapp',
            ':birth_date' => $input['birth_date'] ?? null,
            ':birth_time' => $input['birth_time'] ?? null,
            ':birth_place' => sanitize($input['birth_place'] ?? ''),
            ':message' => sanitize($input['message']),
        ]);

        $appointmentId = $db->lastInsertId();

        // Send WhatsApp notification to admin
        $notifyMsg = "नयाँ परामर्श अनुरोध #{$appointmentId}\n";
        $notifyMsg .= "नाम: {$input['name']}\n";
        $notifyMsg .= "फोन: {$input['phone']}\n";
        $notifyMsg .= "सेवा: {$input['service_type']}\n";
        $notifyMsg .= "सन्देश: {$input['message']}";
        // Trigger WhatsApp notification (via external service or API)

        jsonSuccess(['id' => $appointmentId], 'Appointment request submitted successfully');
        break;

    case 'GET':
        // Public: check available slots
        $date = $_GET['date'] ?? date('Y-m-d');
        $stmt = $db->prepare("
            SELECT preferred_time
            FROM appointments
            WHERE preferred_date = :date AND status != 'cancelled'
        ");
        $stmt->execute([':date' => $date]);
        $booked = $stmt->fetchAll(PDO::FETCH_COLUMN);

        $allSlots = ['09:00', '10:00', '11:00', '12:00', '14:00', '15:00', '16:00', '17:00', '18:00', '19:00'];
        $available = array_values(array_diff($allSlots, $booked));

        jsonSuccess(['date' => $date, 'available_slots' => $available, 'booked_slots' => $booked]);
        break;

    default:
        jsonError('Method not allowed', 405);
}
```

---

### Task 2.2: Update appointment.html Frontend

**Files:**
- Modify: `appointment.html`

- [ ] **Step 1: Replace form handler to POST to backend**

Replace the existing form in `appointment.html` to submit via `fetch()` to the backend API, while keeping WhatsApp as a fallback.

```html
<form class="form-card reveal" id="appointmentForm">
    <h2 style="margin-top:0;color:var(--maroon)">परामर्श अनुरोध फाराम</h2>
    <div class="form-grid">
        <div class="field"><label>पूरा नाम *</label><input name="name" required placeholder="तपाईंको पूरा नाम"></div>
        <div class="field"><label>फोन / WhatsApp *</label><input name="phone" required inputmode="tel" placeholder="+977..."></div>
        <div class="field"><label>इमेल</label><input name="email" type="email" placeholder="example@email.com"></div>
        <div class="field"><label>परामर्शको विषय *</label>
            <select name="service_type" required>
                <option value="">छनोट गर्नुहोस्</option>
                <option value="kundali">जन्मकुण्डली विश्लेषण</option>
                <option value="marriage">विवाह तथा गुण मिलान</option>
                <option value="grahadasha">ग्रहदशा तथा गोचर</option>
                <option value="vastu">वास्तु परामर्श</option>
                <option value="pooja">पूजा तथा कर्मकाण्ड</option>
                <option value="general">अन्य जिज्ञासा</option>
            </select>
        </div>
        <div class="field"><label>मिति</label><input name="preferred_date" type="date"></div>
        <div class="field"><label>समय</label><input name="preferred_time" type="time"></div>
        <div class="field"><label>परामर्श माध्यम</label>
            <select name="consultation_mode">
                <option value="whatsapp">WhatsApp</option>
                <option value="phone">फोन</option>
                <option value="video">भिडियो (Zoom/Meet)</option>
                <option value="inperson">व्यक्तिगत</option>
            </select>
        </div>
        <div class="field"><label>जन्म मिति</label><input name="birth_date" type="date"></div>
        <div class="field"><label>जन्म समय</label><input name="birth_time" type="time"></div>
        <div class="field full"><label>जन्म स्थान</label><input name="birth_place" placeholder="गाउँ/सहर, जिल्ला, देश"></div>
        <div class="field full"><label>तपाईंको जिज्ञासा *</label>
            <textarea name="message" required placeholder="छोटो रूपमा आफ्नो समस्या वा जिज्ञासा लेख्नुहोस्"></textarea>
        </div>
    </div>
    <button class="btn btn-whatsapp" type="submit" id="submitBtn">परामर्श अनुरोध पठाउनुहोस्</button>
    <div class="form-status" role="status" id="formStatus"></div>
    <p class="form-note">तपाईंको विवरण सुरक्षित रूपमा भण्डारण गरिन्छ। वैकल्पिक रूपमा, <a href="https://wa.me/9779844639228" target="_blank">WhatsApp</a> मा पनि पठाउन सक्नुहुन्छ।</p>
</form>
```

- [ ] **Step 2: Add form submission JS to script.js**

Add to `assets/script.js`:

```javascript
// Appointment form submission (Phase 2)
const apptForm = document.getElementById('appointmentForm');
if (apptForm) {
    apptForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        const btn = document.getElementById('submitBtn');
        const status = document.getElementById('formStatus');
        btn.disabled = true;
        btn.textContent = 'पठाउँदै...';
        status.style.display = 'block';
        status.textContent = 'कृपया प्रतिक्षा गर्नुहोस्...';
        status.style.background = '#fff3cd';
        status.style.color = '#66451d';

        const data = Object.fromEntries(new FormData(apptForm).entries());

        try {
            const res = await fetch('https://api.astroshreehari.com/api/appointments.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data)
            });
            const result = await res.json();
            if (result.success) {
                status.style.background = '#d4edda';
                status.style.color = '#155724';
                status.textContent = '✅ तपाईंको अनुरोध सफलतापूर्वक प्राप्त भयो। हामी चाँडै सम्पर्क गर्नेछौं।';
                apptForm.reset();
            } else {
                throw new Error(result.message || 'Unknown error');
            }
        } catch (err) {
            status.style.background = '#f8d7da';
            status.style.color = '#721c24';
            status.textContent = '❌ समस्या भयो। कृपया WhatsApp मा सिधै सम्पर्क गर्नुहोस्।';
        } finally {
            btn.disabled = false;
            btn.textContent = 'परामर्श अनुरोध पठाउनुहोस्';
        }
    });
}
```

---

### Task 2.3: Admin Dashboard — Appointments

**Files:**
- Create: `backend/admin/index.php`
- Create: `backend/admin/dashboard.php`
- Create: `backend/includes/header.php`
- Create: `backend/includes/footer.php`

- [ ] **Step 1: Create admin login**

```php
<?php
// backend/admin/index.php
session_start();
require_once __DIR__ . '/../config/database.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $db = Database::getConnection();
    $stmt = $db->prepare("SELECT * FROM admin_users WHERE username = :username LIMIT 1");
    $stmt->execute([':username' => $_POST['username']]);
    $user = $stmt->fetch();

    if ($user && password_verify($_POST['password'], $user['password_hash'])) {
        $_SESSION['admin_id'] = $user['id'];
        $_SESSION['admin_name'] = $user['display_name'];
        $_SESSION['admin_role'] = $user['role'];
        header('Location: dashboard.php');
        exit;
    } else {
        $error = 'गलत प्रयोगकर्ता नाम वा पासवर्ड';
    }
}
?>
<!DOCTYPE html>
<html lang="ne">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>प्रशासक लगइन — श्रीहरि ज्योतिष</title>
    <link rel="stylesheet" href="../assets/admin.css">
</head>
<body class="login-page">
    <div class="login-box">
        <h1>प्रशासक लगइन</h1>
        <?php if ($error): ?>
            <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <form method="POST">
            <div class="field">
                <label>प्रयोगकर्ता नाम</label>
                <input name="username" required autocomplete="username">
            </div>
            <div class="field">
                <label>पासवर्ड</label>
                <input type="password" name="password" required autocomplete="current-password">
            </div>
            <button class="btn btn-primary" type="submit">लगइन</button>
        </form>
    </div>
</body>
</html>
```

- [ ] **Step 2: Create admin header/footer**

```php
<?php
// backend/includes/header.php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header('Location: index.php');
    exit;
}
$page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="ne">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>प्रशासक — श्रीहरि ज्योतिष</title>
    <link rel="stylesheet" href="../assets/admin.css">
</head>
<body>
    <nav class="admin-nav">
        <a href="dashboard.php" class="brand">श्रीहरि ज्योतिष — प्रशासक</a>
        <div class="admin-nav-links">
            <a href="dashboard.php" class="<?= $page === 'dashboard.php' ? 'active' : '' ?>">ड्यासबोर्ड</a>
            <a href="appointments.php" class="<?= $page === 'appointments.php' ? 'active' : '' ?>">परामर्श</a>
            <a href="pooja-orders.php" class="<?= $page === 'pooja-orders.php' ? 'active' : '' ?>">पूजा अर्डर</a>
            <a href="settings.php">सेटिङ्स</a>
            <a href="?logout=1">बाहिरिनुहोस्</a>
        </div>
    </nav>
    <main class="admin-main">
```

```php
<?php
// backend/includes/footer.php
    </main>
</body>
</html>
```

- [ ] **Step 3: Create admin dashboard**

```php
<?php
// backend/admin/dashboard.php
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../config/database.php';

$db = Database::getConnection();

$pendingAppointments = $db->query("SELECT COUNT(*) FROM appointments WHERE status = 'pending'")->fetchColumn();
$todayAppointments = $db->query("SELECT COUNT(*) FROM appointments WHERE preferred_date = CURDATE()")->fetchColumn();
$pendingPooja = $db->query("SELECT COUNT(*) FROM pooja_bookings WHERE status = 'pending'")->fetchColumn();
?>

<h1>ड्यासबोर्ड</h1>
<div class="stats-grid">
    <div class="stat-card">
        <strong><?= $pendingAppointments ?></strong>
        <span>नयाँ परामर्श</span>
    </div>
    <div class="stat-card">
        <strong><?= $todayAppointments ?></strong>
        <span>आजको परामर्श</span>
    </div>
    <div class="stat-card">
        <strong><?= $pendingPooja ?></strong>
        <span>पूजा अर्डर</span>
    </div>
</div>

<div class="recent-section">
    <h2>भर्खरका परामर्श अनुरोध</h2>
    <?php
    $recent = $db->query("SELECT id, name, phone, service_type, preferred_date, status, created_at FROM appointments ORDER BY created_at DESC LIMIT 10");
    while ($row = $recent->fetch()):
    ?>
    <div class="list-item">
        <span class="list-name"><?= htmlspecialchars($row['name']) ?></span>
        <span class="list-phone"><?= htmlspecialchars($row['phone']) ?></span>
        <span class="list-service"><?= htmlspecialchars($row['service_type']) ?></span>
        <span class="list-date"><?= $row['preferred_date'] ?? '—' ?></span>
        <span class="badge badge-<?= $row['status'] ?>"><?= $row['status'] ?></span>
    </div>
    <?php endwhile; ?>
</div>
```

- [ ] **Step 4: Create admin appointments manager**

```php
<?php
// backend/admin/appointments.php
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../config/database.php';

$db = Database::getConnection();

// Handle status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $stmt = $db->prepare("UPDATE appointments SET status = :status, admin_notes = :notes WHERE id = :id");
    $stmt->execute([
        ':status' => $_POST['status'],
        ':notes' => $_POST['admin_notes'] ?? '',
        ':id' => $_POST['id']
    ]);
    echo '<div class="alert alert-success">अपडेट गरियो</div>';
}

$statusFilter = $_GET['status'] ?? 'pending';
$stmt = $db->prepare("SELECT * FROM appointments WHERE status = :status ORDER BY created_at DESC");
$stmt->execute([':status' => $statusFilter]);
$appointments = $stmt->fetchAll();
?>

<h1>परामर्श व्यवस्थापन</h1>

<div class="filter-tabs">
    <a href="?status=pending" class="<?= $statusFilter === 'pending' ? 'active' : '' ?>">पेन्डिङ</a>
    <a href="?status=confirmed" class="<?= $statusFilter === 'confirmed' ? 'active' : '' ?>">पुष्टि</a>
    <a href="?status=completed" class="<?= $statusFilter === 'completed' ? 'active' : '' ?>">सम्पन्न</a>
    <a href="?status=cancelled" class="<?= $statusFilter === 'cancelled' ? 'active' : '' ?>">रद्द</a>
    <a href="?status=all" class="<?= $statusFilter === 'all' ? 'active' : '' ?>">सबै</a>
</div>

<table class="admin-table">
    <thead>
        <tr>
            <th>#</th>
            <th>नाम</th>
            <th>फोन</th>
            <th>सेवा</th>
            <th>मिति</th>
            <th>सन्देश</th>
            <th>माध्यम</th>
            <th>कार्य</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($appointments as $a): ?>
        <tr>
            <td><?= $a['id'] ?></td>
            <td><?= htmlspecialchars($a['name']) ?></td>
            <td><?= htmlspecialchars($a['phone']) ?></td>
            <td><?= htmlspecialchars($a['service_type']) ?></td>
            <td><?= $a['preferred_date'] ?? '—' ?> <?= $a['preferred_time'] ?? '' ?></td>
            <td class="msg-cell"><?= htmlspecialchars(mb_substr($a['message'], 0, 80)) ?>...</td>
            <td><?= $a['consultation_mode'] ?></td>
            <td>
                <button class="btn-small" onclick="toggleDetails(<?= $a['id'] ?>)">विवरण</button>
            </td>
        </tr>
        <tr id="details-<?= $a['id'] ?>" class="details-row" style="display:none">
            <td colspan="8">
                <form method="POST" class="inline-form">
                    <input type="hidden" name="id" value="<?= $a['id'] ?>">
                    <div class="detail-grid">
                        <div><strong>इमेल:</strong> <?= htmlspecialchars($a['email'] ?: '—') ?></div>
                        <div><strong>जन्म मिति:</strong> <?= $a['birth_date'] ?? '—' ?> <?= $a['birth_time'] ?? '' ?></div>
                        <div><strong>जन्म स्थान:</strong> <?= htmlspecialchars($a['birth_place'] ?: '—') ?></div>
                        <div><strong>सन्देश:</strong> <?= nl2br(htmlspecialchars($a['message'])) ?></div>
                        <div><strong>नोट:</strong> <textarea name="admin_notes" rows="2"><?= htmlspecialchars($a['admin_notes'] ?? '') ?></textarea></div>
                        <div>
                            <select name="status">
                                <option value="pending" <?= $a['status'] === 'pending' ? 'selected' : '' ?>>पेन्डिङ</option>
                                <option value="confirmed" <?= $a['status'] === 'confirmed' ? 'selected' : '' ?>>पुष्टि</option>
                                <option value="completed" <?= $a['status'] === 'completed' ? 'selected' : '' ?>>सम्पन्न</option>
                                <option value="cancelled" <?= $a['status'] === 'cancelled' ? 'selected' : '' ?>>रद्द</option>
                            </select>
                            <button type="submit" name="update_status" class="btn-small btn-primary">अपडेट गर्नुहोस्</button>
                        </div>
                    </div>
                </form>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<script>
function toggleDetails(id) {
    const row = document.getElementById('details-' + id);
    row.style.display = row.style.display === 'none' ? 'table-row' : 'none';
}
</script>
```

---

### Phase 3: Kundali Generation

> **Goal:** Allow users to enter birth details and get an automated kundali (birth chart) with basic interpretation.

### Task 3.1: Astrology Calculation Engine

**Files:**
- Create: `backend/lib/Astrology.php`

- [ ] **Step 1: Create the kundali calculation library**

```php
<?php
// backend/lib/Astrology.php

class Astrology {
    private ?DateTime $birthDate;
    private ?string $birthPlace;
    private float $latitude;
    private float $longitude;
    private int $timezoneOffset;

    public function __construct(?string $date, ?string $time, ?string $place = null) {
        if ($date && $time) {
            $this->birthDate = new DateTime("{$date} {$time}", new DateTimeZone('Asia/Kathmandu'));
        } else {
            $this->birthDate = null;
        }
        $this->birthPlace = $place ?: 'Kathmandu';
        $this->latitude = 27.7172;
        $this->longitude = 85.3240;
        $this->timezoneOffset = 345; // Asia/Kathmandu = UTC+5:45
    }

    public function calculateRashi(): string {
        if (!$this->birthDate) return 'अज्ञात';
        // Simplified: determine Moon sign (rashi) based on date
        // In production, use Swiss Ephemeris or an astrology API
        $dayOfYear = (int)$this->birthDate->format('z');
        $rashis = ['मेष', 'वृष', 'मिथुन', 'कर्क', 'सिंह', 'कन्या', 'तुला', 'वृश्चिक', 'धनु', 'मकर', 'कुम्भ', 'मीन'];
        $index = intdiv($dayOfYear, 30) % 12;
        return $rashis[$index];
    }

    public function calculateNakshatra(): string {
        if (!$this->birthDate) return 'अज्ञात';
        $dayOfYear = (int)$this->birthDate->format('z');
        $nakshatras = ['अश्विनी', 'भरणी', 'कृत्तिका', 'रोहिणी', 'मृगशिरा', 'आर्द्रा', 'पुनर्वसु', 'पुष्य', 'अश्लेषा', 'मघा', 'पूर्वाफाल्गुनी', 'उत्तराफाल्गुनी', 'हस्त', 'चित्रा', 'स्वाती', 'विशाखा', 'अनुराधा', 'ज्येष्ठा', 'मूल', 'पूर्वाषाढा', 'उत्तराषाढा', 'श्रवण', 'धनिष्ठा', 'शतभिषा', 'पूर्वभाद्रपद', 'उत्तरभाद्रपद', 'रेवती'];
        $index = intdiv($dayOfYear * 27, 365) % 27;
        return $nakshatras[$index];
    }

    public function calculateLagna(): string {
        // Simplified lagna calculation based on birth time
        if (!$this->birthDate) return 'अज्ञात';
        $hour = (int)$this->birthDate->format('H');
        $rashis = ['मेष', 'वृष', 'मिथुन', 'कर्क', 'सिंह', 'कन्या', 'तुला', 'वृश्चिक', 'धनु', 'मकर', 'कुम्भ', 'मीन'];
        $index = intdiv($hour, 2) % 12;
        return $rashis[$index];
    }

    public function getBasicDetails(): array {
        if (!$this->birthDate) {
            return [
                'rashi' => 'कृपया जन्म मिति र समय प्रविष्ट गर्नुहोस्',
                'nakshatra' => '—',
                'lagna' => '—',
                'date' => null
            ];
        }
        return [
            'rashi' => $this->calculateRashi(),
            'nakshatra' => $this->calculateNakshatra(),
            'lagna' => $this->calculateLagna(),
            'date' => $this->birthDate->format('Y-m-d H:i'),
            'place' => $this->birthPlace,
        ];
    }

    // For production: integrate with Swiss Ephemeris or Prokerala API
    // public function getFullChart(): array { ... }
}
```

---

### Task 3.2: Kundali Generation API

**Files:**
- Create: `backend/api/kundali.php`

- [ ] **Step 1: Create kundali API endpoint**

```php
<?php
// backend/api/kundali.php

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/cors.php';
require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../lib/Astrology.php';

$method = $_SERVER['REQUEST_METHOD'];

if ($method !== 'POST') {
    jsonError('Method not allowed', 405);
}

$input = getJsonInput();
$error = validateRequired($input, ['name']);
if ($error) {
    jsonError($error);
}

$astrology = new Astrology(
    $input['birth_date'] ?? null,
    $input['birth_time'] ?? null,
    $input['birth_place'] ?? null
);

$details = $astrology->getBasicDetails();

// Save to database if user wants to register
$db = Database::getConnection();
$stmt = $db->prepare("
    INSERT INTO appointments (name, phone, service_type, birth_date, birth_time, birth_place, message, status)
    VALUES (:name, :phone, 'kundali', :birth_date, :birth_time, :birth_place, :message, 'pending')
");
$stmt->execute([
    ':name' => sanitize($input['name']),
    ':phone' => sanitize($input['phone'] ?? ''),
    ':birth_date' => $input['birth_date'] ?? null,
    ':birth_time' => $input['birth_time'] ?? null,
    ':birth_place' => sanitize($input['birth_place'] ?? ''),
    ':message' => 'स्वचालित कुण्डली हेरेपछि परामर्श अनुरोध',
]);

jsonSuccess([
    'kundali' => $details,
    'message' => 'तपाईंको आधारभूत कुण्डली विवरण तयार छ। विस्तृत परामर्शका लागि कृपया सम्पर्क गर्नुहोस्।'
]);
```

---

### Task 3.3: Kundali Frontend Page

**Files:**
- Create: `kundali.html`

- [ ] **Step 1: Create the kundali generation page**

```html
<!doctype html>
<html lang="ne">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>जन्मकुण्डली | श्रीहरि ज्योतिष</title>
    <meta name="description" content="आफ्नो जन्म मिति, समय र स्थान प्रविष्ट गरी आधारभूत कुण्डली, राशि र नक्षत्र हेर्नुहोस्।">
    <meta name="theme-color" content="#711d29">
    <link rel="icon" href="assets/favicon.svg" type="image/svg+xml">
    <link rel="stylesheet" href="assets/styles.css">
</head>
<body>
    <div class="topbar">
        <div class="container">
            <div class="topbar-links"><span>ॐ श्री गणेशाय नमः</span><span>ज्योतिष तथा कर्मकाण्डमा १८+ वर्षको अनुभव</span></div>
            <div class="topbar-links"><a href="tel:+9779844639228">☎ +977 9844639228</a><a href="mailto:shreeharijyotishparamarsakendr@gmail.com">✉ shreeharijyotishparamarsakendr@gmail.com</a></div>
        </div>
    </div>
    <header class="site-header">
        <nav class="navbar container" aria-label="मुख्य नेभिगेसन">
            <a class="brand" href="index.html">
                <img src="assets/logo.svg" alt="श्रीहरि ज्योतिष परामर्श केन्द्र लोगो">
                <span><strong>श्रीहरि ज्योतिष</strong><small>Sitaram Timsina · Nepali Astrologer</small></span>
            </a>
            <div class="nav-links"><a href="index.html">गृहपृष्ठ</a><a href="about.html">हाम्रो बारेमा</a><a href="services.html">सेवाहरू</a><a href="kundali.html" class="active">कुण्डली</a><a href="appointment.html">परामर्श</a><a href="contact.html">सम्पर्क</a></div>
            <div class="nav-actions">
                <a class="btn btn-primary" href="appointment.html">परामर्श बुक गर्नुहोस्</a>
                <button class="menu-toggle" aria-label="मेनु खोल्नुहोस्" aria-expanded="false">☰</button>
            </div>
        </nav>
    </header>
    <main>
        <section class="page-hero">
            <div class="container">
                <div class="eyebrow">जन्मकुण्डली</div>
                <h1>आफ्नो कुण्डली हेर्नुहोस्</h1>
                <div class="breadcrumb"><a href="index.html">गृहपृष्ठ</a><span>›</span><span>कुण्डली</span></div>
            </div>
        </section>
        <section class="section">
            <div class="container contact-grid">
                <div class="reveal">
                    <div class="eyebrow">आधारभूत विवरण</div>
                    <h2 class="content-title">जन्म मिति, समय र स्थान प्रविष्ट गर्नुहोस्</h2>
                    <p>तलको फाराम भर्नुहोस् र तपाईंको राशि, नक्षत्र र लग्न स्वचालित रूपमा गणना गरिनेछ।</p>
                    <ul class="check-list">
                        <li>जन्म मिति र समय सही भए अध्ययन अझ उपयोगी हुन्छ</li>
                        <li>समय थाहा नभए मिति मात्रै हाल्नुहोस्</li>
                        <li>विस्तृत कुण्डली विश्लेषणका लागि परामर्श लिनुहोस्</li>
                    </ul>
                </div>
                <div class="form-card reveal">
                    <h2 style="margin-top:0;color:var(--maroon)">कुण्डली फाराम</h2>
                    <form id="kundaliForm">
                        <div class="form-grid">
                            <div class="field"><label>पूरा नाम *</label><input name="name" id="kName" required placeholder="तपाईंको पूरा नाम"></div>
                            <div class="field"><label>फोन (वैकल्पिक)</label><input name="phone" id="kPhone" inputmode="tel" placeholder="+977..."></div>
                            <div class="field"><label>जन्म मिति *</label><input name="birth_date" id="kDate" type="date" required></div>
                            <div class="field"><label>जन्म समय</label><input name="birth_time" id="kTime" type="time"></div>
                            <div class="field full"><label>जन्म स्थान</label><input name="birth_place" id="kPlace" placeholder="गाउँ/सहर, जिल्ला, देश"></div>
                        </div>
                        <button class="btn btn-primary" type="submit">कुण्डली हेर्नुहोस्</button>
                    </form>
                    <div class="form-status" id="kundaliStatus" role="status" style="display:none"></div>
                </div>
            </div>
        </section>
        <section class="section-sm" id="kundaliResult" style="display:none">
            <div class="container">
                <div class="section-header reveal">
                    <div class="eyebrow">तपाईंको कुण्डली</div>
                    <h2>आधारभूत विवरण</h2>
                </div>
                <div class="grid grid-3" id="kundaliCards">
                    <!-- Populated by JS -->
                </div>
                <div class="cta-box reveal" style="margin-top:30px">
                    <div>
                        <h2>विस्तृत विश्लेषण चाहिन्छ?</h2>
                        <p>पूरा कुण्डली, ग्रहदशा र व्यक्तिगत मार्गदर्शनका लागि परामर्श लिनुहोस्।</p>
                    </div>
                    <a class="btn btn-whatsapp" href="appointment.html">परामर्श बुक गर्नुहोस्</a>
                </div>
            </div>
        </section>
    </main>
    <footer class="site-footer">
        <div class="container">
            <div class="footer-grid">
                <div class="footer-brand">
                    <a class="brand" href="index.html"><img src="assets/logo.svg" alt=""><span><strong style="color:#f4d782">श्रीहरि ज्योतिष परामर्श केन्द्र</strong><small style="color:#d5bdb5">परम्परा, अनुभव र व्यक्तिगत मार्गदर्शन</small></span></a>
                    <p>ज्योतिष तथा कर्मकाण्ड विषयमा १८ वर्षभन्दा बढी अनुभवसहित नेपाल तथा विश्वभर अनलाइन परामर्श।</p>
                    <div class="social-links"><a class="social-link" href="https://www.facebook.com/share/19AnGtrMox/" target="_blank" rel="noopener" aria-label="Facebook">f</a><a class="social-link" href="https://youtube.com/@astrogurusitaram3m?si=x37KRR6Wv4PldyRq" target="_blank" rel="noopener" aria-label="YouTube">▶</a></div>
                </div>
                <div><h3 class="footer-title">द्रुत लिंक</h3><div class="footer-links"><a href="about.html">हाम्रो बारेमा</a><a href="services.html">सेवाहरू</a><a href="kundali.html">कुण्डली</a><a href="appointment.html">परामर्श बुकिङ</a><a href="contact.html">सम्पर्क</a></div></div>
                <div><h3 class="footer-title">सम्पर्क</h3><div class="footer-links"><a href="tel:+9779844639228">+977 9844639228</a><a href="mailto:shreeharijyotishparamarsakendr@gmail.com">shreeharijyotishparamarsakendr@gmail.com</a><a href="https://wa.me/9779844639228" target="_blank" rel="noopener">WhatsApp मा सन्देश</a><span>अनलाइन परामर्श उपलब्ध</span></div></div>
            </div>
            <div class="footer-bottom"><span>© <span data-year></span> श्रीहरि ज्योतिष परामर्श केन्द्र। सर्वाधिकार सुरक्षित।</span><span>www.astroshreehari.com</span></div>
        </div>
    </footer>
    <div class="float-actions">
        <a class="float-btn float-whatsapp" href="https://wa.me/9779844639228" target="_blank" rel="noopener" aria-label="WhatsApp">☏</a>
        <button class="float-btn back-top" aria-label="माथि जानुहोस्">↑</button>
    </div>
    <script src="assets/script.js"></script>
    <script>
        const kundaliForm = document.getElementById('kundaliForm');
        const kundaliResult = document.getElementById('kundaliResult');
        const kundaliCards = document.getElementById('kundaliCards');
        const kundaliStatus = document.getElementById('kundaliStatus');

        if (kundaliForm) {
            kundaliForm.addEventListener('submit', async (e) => {
                e.preventDefault();
                const btn = kundaliForm.querySelector('button[type="submit"]');
                btn.disabled = true;
                btn.textContent = 'गणना गर्दै...';
                kundaliStatus.style.display = 'none';

                const data = {
                    name: document.getElementById('kName').value,
                    phone: document.getElementById('kPhone').value,
                    birth_date: document.getElementById('kDate').value,
                    birth_time: document.getElementById('kTime').value,
                    birth_place: document.getElementById('kPlace').value,
                };

                try {
                    const res = await fetch('https://api.astroshreehari.com/api/kundali.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify(data)
                    });
                    const result = await res.json();
                    if (result.success) {
                        const k = result.data.kundali;
                        kundaliCards.innerHTML = `
                            <div class="card reveal" style="text-align:center"><div class="service-icon" style="margin:0 auto 16px">✦</div><h3>राशि</h3><p style="font-size:2rem;font-weight:800;color:var(--maroon)">${k.rashi}</p></div>
                            <div class="card reveal" style="text-align:center"><div class="service-icon" style="margin:0 auto 16px">⌁</div><h3>नक्षत्र</h3><p style="font-size:2rem;font-weight:800;color:var(--maroon)">${k.nakshatra}</p></div>
                            <div class="card reveal" style="text-align:center"><div class="service-icon" style="margin:0 auto 16px">◉</div><h3>लग्न</h3><p style="font-size:2rem;font-weight:800;color:var(--maroon)">${k.lagna}</p></div>
                        `;
                        kundaliResult.style.display = 'block';
                        kundaliResult.scrollIntoView({ behavior: 'smooth' });
                    } else {
                        throw new Error(result.message);
                    }
                } catch (err) {
                    kundaliStatus.textContent = '❌ गणना गर्न समस्या भयो। कृपया पुन: प्रयास गर्नुहोस्।';
                    kundaliStatus.style.display = 'block';
                } finally {
                    btn.disabled = false;
                    btn.textContent = 'कुण्डली हेर्नुहोस्';
                }
            });
        }
    </script>
</body>
</html>
```

---

### Phase 4: EPooja Booking

> **Goal:** Allow users to browse and book pooja services online. No payment integration needed — bookings create a record and the admin follows up via WhatsApp/phone.

### Task 4.1: Pooja Services API

**Files:**
- Create: `backend/api/pooja.php`
- Create: `backend/admin/pooja-orders.php`

- [ ] **Step 1: Create pooja services API**

```php
<?php
// backend/api/pooja.php

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/cors.php';
require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../middleware/validate.php';

$method = $_SERVER['REQUEST_METHOD'];
$db = Database::getConnection();

switch ($method) {
    case 'GET':
        // List active pooja services
        $stmt = $db->query("SELECT id, title_ne, title_en, description_ne, description_en, base_price, duration_minutes, materials_available FROM pooja_services WHERE is_active = 1 ORDER BY category");
        $services = $stmt->fetchAll();
        jsonSuccess($services);
        break;

    case 'POST':
        // Book a pooja
        $input = getJsonInput();
        $error = validateRequired($input, ['service_id', 'name', 'phone', 'preferred_date']);
        if ($error) jsonError($error);

        $stmt = $db->prepare("
            INSERT INTO pooja_bookings (service_id, name, phone, email, preferred_date, preferred_time, address, special_instructions, needs_materials, is_live_stream, status)
            VALUES (:service_id, :name, :phone, :email, :preferred_date, :preferred_time, :address, :instructions, :needs_materials, :is_live_stream, 'pending')
        ");
        $stmt->execute([
            ':service_id' => $input['service_id'],
            ':name' => sanitize($input['name']),
            ':phone' => sanitize($input['phone']),
            ':email' => sanitize($input['email'] ?? ''),
            ':preferred_date' => $input['preferred_date'],
            ':preferred_time' => $input['preferred_time'] ?? null,
            ':address' => sanitize($input['address'] ?? ''),
            ':instructions' => sanitize($input['special_instructions'] ?? ''),
            ':needs_materials' => !empty($input['needs_materials']) ? 1 : 0,
            ':is_live_stream' => !empty($input['is_live_stream']) ? 1 : 0,
        ]);

        $bookingId = $db->lastInsertId();
        jsonSuccess(['id' => $bookingId], 'पूजा बुकिङ सफल भयो। हामी चाँडै सम्पर्क गर्नेछौं।');
        break;

    default:
        jsonError('Method not allowed', 405);
}
```

- [ ] **Step 2: Create pooja frontend page (`pooja.html`)**

A page similar to `kundali.html` but listing pooja services from the API and allowing booking.

---

### Task 4.2: Manual Payment via Static QR Code

> **Goal:** Users scan a static QR (eSewa/Khalti/IME Pay/bank) and submit payment proof. Admin manually verifies and approves.

**Files:**
- Create: `backend/api/payments.php`
- Create: `backend/admin/payments.php`
- Create: `assets/payments/esewa-qr.png`
- Create: `assets/payments/khalti-qr.png`
- Create: `assets/payments/imepay-qr.png`
- Create: `assets/payments/bank-details.txt`

- [ ] **Step 1: Create payment submission API**

```php
<?php
// backend/api/payments.php

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/cors.php';
require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../middleware/validate.php';

$method = $_SERVER['REQUEST_METHOD'];
$db = Database::getConnection();

if ($method !== 'POST') {
    jsonError('Method not allowed', 405);
}

$input = getJsonInput();

// Validate
$error = validateRequired($input, ['booking_type', 'booking_id', 'user_name', 'user_phone', 'amount', 'method', 'transaction_ref']);
if ($error) {
    jsonError($error);
}

if (!in_array($input['method'], ['esewa', 'khalti', 'imepay', 'bank'])) {
    jsonError('अमान्य भुक्तानी विधि');
}

// Handle optional screenshot upload (base64 or file URL)
$screenshotPath = null;
if (!empty($input['screenshot'])) {
    $uploadDir = __DIR__ . '/../../uploads/payments/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }
    $filename = 'payment_' . time() . '_' . bin2hex(random_bytes(4)) . '.jpg';
    $data = base64_decode($input['screenshot']);
    if ($data !== false) {
        file_put_contents($uploadDir . $filename, $data);
        $screenshotPath = '/uploads/payments/' . $filename;
    }
}

$stmt = $db->prepare("
    INSERT INTO payments (booking_type, booking_id, user_name, user_phone, amount, method, transaction_ref, screenshot_path, status)
    VALUES (:booking_type, :booking_id, :user_name, :user_phone, :amount, :method, :transaction_ref, :screenshot, 'pending')
");

$stmt->execute([
    ':booking_type' => $input['booking_type'],
    ':booking_id' => (int)$input['booking_id'],
    ':user_name' => sanitize($input['user_name']),
    ':user_phone' => sanitize($input['user_phone']),
    ':amount' => (float)$input['amount'],
    ':method' => $input['method'],
    ':transaction_ref' => sanitize($input['transaction_ref']),
    ':screenshot' => $screenshotPath,
]);

// Update the associated booking status (optional: mark as payment_submitted)
// e.g., UPDATE pooja_bookings SET status = 'payment_submitted' WHERE id = :booking_id

jsonSuccess(['id' => $db->lastInsertId()], 'भुक्तानी विवरण प्राप्त भयो। प्रशासकले पुष्टि गरेपछि सूचित गरिनेछ।');
```

- [ ] **Step 2: Create payment QR display component**

Add to `pooja.html` (and any booking confirmation page):

```html
<div class="payment-section" id="paymentSection" style="display:none">
    <h3>भुक्तानी गर्नुहोस्</h3>
    <p>तलको QR कोड स्क्यान गरी भुक्तानी गर्नुहोस् र रसिद विवरण फाराम भर्नुहोस्।</p>

    <div class="payment-methods">
        <div class="payment-method">
            <h4>eSewa</h4>
            <img src="assets/payments/esewa-qr.png" alt="eSewa QR" class="qr-code">
        </div>
        <div class="payment-method">
            <h4>Khalti</h4>
            <img src="assets/payments/khalti-qr.png" alt="Khalti QR" class="qr-code">
        </div>
        <div class="payment-method">
            <h4>IME Pay</h4>
            <img src="assets/payments/imepay-qr.png" alt="IME Pay QR" class="qr-code">
        </div>
    </div>

    <form id="paymentForm">
        <div class="form-grid">
            <div class="field"><label>भुक्तानी विधि *</label>
                <select name="method" required>
                    <option value="">छनोट गर्नुहोस्</option>
                    <option value="esewa">eSewa</option>
                    <option value="khalti">Khalti</option>
                    <option value="imepay">IME Pay</option>
                    <option value="bank">बैंक Transfer</option>
                </select>
            </div>
            <div class="field"><label>रकम (रु) *</label><input name="amount" type="number" required readonly></div>
            <div class="field"><label>Transaction ID / Ref. No. *</label><input name="transaction_ref" required placeholder="eSewa/Khalti बाट प्राप्त Transaction ID"></div>
            <div class="field full"><label>स्क्रिनसट (वैकल्पिक)</label><input name="screenshot" type="file" accept="image/*"></div>
        </div>
        <input type="hidden" name="booking_type" value="pooja">
        <input type="hidden" name="booking_id" id="paymentBookingId">
        <input type="hidden" name="user_name" id="paymentUserName">
        <input type="hidden" name="user_phone" id="paymentUserPhone">
        <button class="btn btn-primary" type="submit">भुक्तानी विवरण पठाउनुहोस्</button>
    </form>
</div>
```

Add CSS for QR display:

```css
.payment-methods{display:grid;grid-template-columns:repeat(auto-fit,minmax(150px,1fr));gap:16px;margin:24px 0}
.payment-method{text-align:center;background:white;border:1px solid var(--line);border-radius:16px;padding:16px}
.payment-method h4{margin:0 0 12px;color:var(--maroon)}
.qr-code{width:140px;height:140px;object-fit:contain;display:block;margin:0 auto}
```

- [ ] **Step 3: Create admin payment verification page**

```php
<?php
// backend/admin/payments.php
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../config/database.php';

$db = Database::getConnection();

// Handle approval / rejection
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['payment_action'])) {
    $stmt = $db->prepare("UPDATE payments SET status = :status, admin_notes = :notes WHERE id = :id");
    $stmt->execute([
        ':status' => $_POST['payment_action'],
        ':notes' => sanitize($_POST['admin_notes'] ?? ''),
        ':id' => $_POST['id']
    ]);

    // If approved, update the related booking status
    if ($_POST['payment_action'] === 'approved') {
        $payStmt = $db->prepare("SELECT booking_type, booking_id FROM payments WHERE id = :id");
        $payStmt->execute([':id' => $_POST['id']]);
        $pay = $payStmt->fetch();
        if ($pay) {
            $table = $pay['booking_type'] === 'pooja' ? 'pooja_bookings' : 'appointments';
            $update = $db->prepare("UPDATE {$table} SET status = 'confirmed' WHERE id = :id");
            $update->execute([':id' => $pay['booking_id']]);
        }
    }

    echo '<div class="alert alert-success">भुक्तानी अपडेट गरियो</div>';
}

$statusFilter = $_GET['status'] ?? 'pending';
$stmt = $db->prepare("SELECT * FROM payments WHERE status = :status ORDER BY created_at DESC");
$stmt->execute([':status' => $statusFilter]);
$payments = $stmt->fetchAll();
?>

<h1>भुक्तानी प्रमाणिकरण</h1>

<div class="filter-tabs">
    <a href="?status=pending" class="<?= $statusFilter === 'pending' ? 'active' : '' ?>">पेन्डिङ</a>
    <a href="?status=approved" class="<?= $statusFilter === 'approved' ? 'active' : '' ?>">स्वीकृत</a>
    <a href="?status=rejected" class="<?= $statusFilter === 'rejected' ? 'active' : '' ?>">अस्वीकृत</a>
    <a href="?status=all" class="<?= $statusFilter === 'all' ? 'active' : '' ?>">सबै</a>
</div>

<table class="admin-table">
    <thead>
        <tr>
            <th>#</th>
            <th>नाम</th>
            <th>फोन</th>
            <th>रकम</th>
            <th>विधि</th>
            <th>Ref. ID</th>
            <th>स्क्रिनसट</th>
            <th>मिति</th>
            <th>कार्य</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($payments as $p): ?>
        <tr>
            <td><?= $p['id'] ?></td>
            <td><?= htmlspecialchars($p['user_name']) ?></td>
            <td><?= htmlspecialchars($p['user_phone']) ?></td>
            <td>रु <?= number_format($p['amount']) ?></td>
            <td><?= strtoupper($p['method']) ?></td>
            <td><?= htmlspecialchars($p['transaction_ref']) ?></td>
            <td>
                <?php if ($p['screenshot_path']): ?>
                    <a href="<?= htmlspecialchars($p['screenshot_path']) ?>" target="_blank">हेर्नुहोस्</a>
                <?php else: ?>
                    —
                <?php endif; ?>
            </td>
            <td><?= $p['created_at'] ?></td>
            <td>
                <?php if ($p['status'] === 'pending'): ?>
                <form method="POST" style="display:flex;gap:6px">
                    <input type="hidden" name="id" value="<?= $p['id'] ?>">
                    <input name="admin_notes" placeholder="नोट" style="width:100px;padding:4px 8px">
                    <button type="submit" name="payment_action" value="approved" class="btn-small" style="background:#155724;color:white">✔</button>
                    <button type="submit" name="payment_action" value="rejected" class="btn-small" style="background:#721c24;color:white">✘</button>
                </form>
                <?php else: ?>
                    <span class="badge badge-<?= $p['status'] ?>"><?= $p['status'] ?></span>
                    <?php if ($p['admin_notes']): ?>
                        <small style="display:block;color:#755f59"><?= htmlspecialchars($p['admin_notes']) ?></small>
                    <?php endif; ?>
                <?php endif; ?>
            </td>
        </tr>
        <?php endforeach; ?>
        <?php if (empty($payments)): ?>
        <tr><td colspan="9" style="text-align:center;padding:32px;color:#755f59">कुनै भुक्तानी फेला परेन</td></tr>
        <?php endif; ?>
    </tbody>
</table>
```

- [ ] **Step 4: Add payment nav link to admin header**

Add to admin nav links in `backend/includes/header.php`:

```php
<a href="payments.php" class="<?= $page === 'payments.php' ? 'active' : '' ?>">भुक्तानी</a>
```

---

### Task 4.3: Rewards & Awards System

> **Goal:** Admin can create and award rewards (free features, discounts, badges, service upgrades) to users. Users can see and claim their rewards.

**Files:**
- Create: `backend/api/rewards.php`
- Create: `backend/admin/rewards.php`

- [ ] **Step 1: Create rewards API**

```php
<?php
// backend/api/rewards.php

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/cors.php';
require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../middleware/validate.php';

$method = $_SERVER['REQUEST_METHOD'];
$db = Database::getConnection();

switch ($method) {
    case 'GET':
        // Get rewards for a user by phone
        $phone = $_GET['phone'] ?? '';
        if (!$phone) {
            jsonError('फोन नम्बर आवश्यक छ');
        }
        $stmt = $db->prepare("
            SELECT id, reward_type, title_ne, title_en, description_ne, description_en, is_redeemed, expires_at, created_at
            FROM rewards
            WHERE user_phone = :phone
            ORDER BY created_at DESC
        ");
        $stmt->execute([':phone' => $phone]);
        $rewards = $stmt->fetchAll();

        jsonSuccess([
            'rewards' => $rewards,
            'active_count' => count(array_filter($rewards, fn($r) => !$r['is_redeemed'])),
        ]);
        break;

    case 'POST':
        // Claim / redeem a reward
        $input = getJsonInput();
        $error = validateRequired($input, ['reward_id', 'user_phone']);
        if ($error) jsonError($error);

        $stmt = $db->prepare("
            UPDATE rewards
            SET is_redeemed = TRUE
            WHERE id = :id AND user_phone = :phone AND is_redeemed = FALSE
        ");
        $stmt->execute([
            ':id' => (int)$input['reward_id'],
            ':phone' => sanitize($input['user_phone']),
        ]);

        if ($stmt->rowCount() > 0) {
            jsonSuccess([], 'पुरस्कार प्रयोग गरियो');
        } else {
            jsonError('पुरस्कार फेला परेन वा पहिले नै प्रयोग भइसकेको छ');
        }
        break;

    default:
        jsonError('Method not allowed', 405);
}
```

- [ ] **Step 2: Create admin reward management page**

```php
<?php
// backend/admin/rewards.php
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../config/database.php';

$db = Database::getConnection();

// Create a new reward
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_reward'])) {
    $stmt = $db->prepare("
        INSERT INTO rewards (user_name, user_phone, reward_type, title_ne, title_en, description_ne, description_en, expires_at, awarded_by)
        VALUES (:user_name, :user_phone, :reward_type, :title_ne, :title_en, :description_ne, :description_en, :expires_at, :awarded_by)
    ");
    $stmt->execute([
        ':user_name' => sanitize($_POST['user_name']),
        ':user_phone' => sanitize($_POST['user_phone']),
        ':reward_type' => $_POST['reward_type'],
        ':title_ne' => sanitize($_POST['title_ne']),
        ':title_en' => sanitize($_POST['title_en'] ?? ''),
        ':description_ne' => sanitize($_POST['description_ne'] ?? ''),
        ':description_en' => sanitize($_POST['description_en'] ?? ''),
        ':expires_at' => $_POST['expires_at'] ?: null,
        ':awarded_by' => $_SESSION['admin_id'],
    ]);
    echo '<div class="alert alert-success">पुरस्कार सफलतापूर्वक सिर्जना गरियो</div>';
}

// Fetch existing rewards
$stmt = $db->query("SELECT r.*, a.display_name AS awarded_by_name FROM rewards r LEFT JOIN admin_users a ON r.awarded_by = a.id ORDER BY r.created_at DESC LIMIT 50");
$rewards = $stmt->fetchAll();
?>

<h1>पुरस्कार व्यवस्थापन</h1>

<div class="form-card" style="max-width:600px;margin-bottom:32px">
    <h3>नयाँ पुरस्कार दिनुहोस्</h3>
    <form method="POST">
        <div class="form-grid" style="grid-template-columns:1fr 1fr">
            <div class="field"><label>प्रयोगकर्ता नाम *</label><input name="user_name" required></div>
            <div class="field"><label>फोन नम्बर *</label><input name="user_phone" required></div>
            <div class="field"><label>पुरस्कार प्रकार *</label>
                <select name="reward_type" required>
                    <option value="feature">विशेष सुविधा (Feature)</option>
                    <option value="discount">छुट (Discount)</option>
                    <option value="badge">ब्याज / मानपदवी (Badge)</option>
                    <option value="service">निःशुल्क सेवा (Service)</option>
                    <option value="other">अन्य</option>
                </select>
            </div>
            <div class="field"><label>म्याद सकिने मिति</label><input name="expires_at" type="date"></div>
            <div class="field"><label>शीर्षक (नेपाली) *</label><input name="title_ne" required placeholder="e.g. निःशुल्क कुण्डली विश्लेषण"></div>
            <div class="field"><label>शीर्षक (अङ्ग्रेजी)</label><input name="title_en" placeholder="e.g. Free Kundali Analysis"></div>
            <div class="field full"><label>विवरण (नेपाली)</label><textarea name="description_ne" rows="2"></textarea></div>
            <div class="field full"><label>विवरण (अङ्ग्रेजी)</label><textarea name="description_en" rows="2"></textarea></div>
        </div>
        <button type="submit" name="create_reward" class="btn btn-primary">पुरस्कार सिर्जना गर्नुहोस्</button>
    </form>
</div>

<h3>हालको पुरस्कारहरू</h3>
<table class="admin-table">
    <thead>
        <tr>
            <th>#</th>
            <th>प्रयोगकर्ता</th>
            <th>फोन</th>
            <th>प्रकार</th>
            <th>शीर्षक</th>
            <th>प्रयोग भयो?</th>
            <th>प्रदान गर्ने</th>
            <th>मिति</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($rewards as $r): ?>
        <tr>
            <td><?= $r['id'] ?></td>
            <td><?= htmlspecialchars($r['user_name']) ?></td>
            <td><?= htmlspecialchars($r['user_phone']) ?></td>
            <td><?= $r['reward_type'] ?></td>
            <td><?= htmlspecialchars($r['title_ne']) ?></td>
            <td><span class="badge badge-<?= $r['is_redeemed'] ? 'completed' : 'pending' ?>"><?= $r['is_redeemed'] ? 'हो' : 'होइन' ?></span></td>
            <td><?= htmlspecialchars($r['awarded_by_name'] ?? '—') ?></td>
            <td><?= $r['created_at'] ?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
```

- [ ] **Step 3: Add rewards nav link to admin header**

```php
<a href="rewards.php" class="<?= $page === 'rewards.php' ? 'active' : '' ?>">पुरस्कार</a>
```

- [ ] **Step 4: Add reward display to user dashboard / profile page**

A user can check their rewards by providing their phone number. The frontend calls `GET /api/rewards.php?phone=97798...` and displays active rewards with a "Claim" button.

---

### Phase 5: Daily Panchang & Notifications

> **Goal:** Display daily panchang (tithi, nakshatra, sunrise/sunset, auspicious times) and send push notifications.

### Task 5.1: Panchang API

**Files:**
- Create: `backend/lib/Panchang.php`
- Create: `backend/api/panchang.php`

- [ ] **Step 1: Create panchang calculation library**

```php
<?php
// backend/lib/Panchang.php

class Panchang {
    public static function getForDate(string $date): array {
        $timestamp = strtotime($date);
        $dayOfYear = (int)date('z', $timestamp);

        // Simplified calculation — in production use Swiss Ephemeris or API
        $tithis = ['प्रतिपदा', 'द्वितीया', 'तृतीया', 'चतुर्थी', 'पञ्चमी', 'षष्ठी', 'सप्तमी', 'अष्टमी', 'नवमी', 'दशमी', 'एकादशी', 'द्वादशी', 'त्रयोदशी', 'चतुर्दशी', 'पूर्णिमा', 'प्रतिपदा', 'द्वितीया', 'तृतीया', 'चतुर्थी', 'पञ्चमी', 'षष्ठी', 'सप्तमी', 'अष्टमी', 'नवमी', 'दशमी', 'एकादशी', 'द्वादशी', 'त्रयोदशी', 'चतुर्दशी', 'अमावास्या'];
        $tithiIndex = ($dayOfYear * 2) % 30;

        $nakshatras = ['अश्विनी', 'भरणी', 'कृत्तिका', 'रोहिणी', 'मृगशिरा', 'आर्द्रा', 'पुनर्वसु', 'पुष्य', 'अश्लेषा', 'मघा', 'पूर्वाफाल्गुनी', 'उत्तराफाल्गुनी', 'हस्त', 'चित्रा', 'स्वाती', 'विशाखा', 'अनुराधा', 'ज्येष्ठा', 'मूल', 'पूर्वाषाढा', 'उत्तराषाढा', 'श्रवण', 'धनिष्ठा', 'शतभिषा', 'पूर्वभाद्रपद', 'उत्तरभाद्रपद', 'रेवती'];
        $nakshatraIndex = ($dayOfYear * 27 / 365) % 27;

        $sunrise = date_sunrise($timestamp, SUNFUNCS_RET_STRING, 27.7172, 85.3240, 90.5, 5.75);
        $sunset = date_sunset($timestamp, SUNFUNCS_RET_STRING, 27.7172, 85.3240, 90.5, 5.75);

        return [
            'date' => $date,
            'tithi' => $tithis[(int)$tithiIndex],
            'nakshatra' => $nakshatras[(int)$nakshatraIndex],
            'sunrise' => $sunrise ?: '06:00',
            'sunset' => $sunset ?: '18:00',
            'day_of_week' => date('l', $timestamp),
            'special_events' => self::getSpecialEvents($date),
        ];
    }

    private static function getSpecialEvents(string $date): array {
        $events = [];
        $month = date('m', strtotime($date));
        $day = date('d', strtotime($date));

        // Major Nepali festivals (simplified — use lunar calendar API in production)
        $festivals = [
            '01-01' => ['ne' => 'नयाँ वर्ष', 'en' => 'New Year'],
            '01-15' => ['ne' => 'माघे संक्रान्ति', 'en' => 'Maghe Sankranti'],
            '08-30' => ['ne' => 'गाई जात्रा', 'en' => 'Gai Jatra'],
            '09-15' => ['ne' => 'इन्द्र जात्रा', 'en' => 'Indra Jatra'],
            '10-01' => ['ne' => 'दशैं सुरु', 'en' => 'Dashain Begins'],
            '10-15' => ['ne' => 'धनतेरस', 'en' => 'Dhanteras'],
            '10-17' => ['ne' => 'दीपावली', 'en' => 'Deepawali'],
            '11-15' => ['ne' => 'छठ पर्व', 'en' => 'Chhath Parva'],
        ];

        $key = substr($date, 5);
        if (isset($festivals[$key])) {
            $events[] = $festivals[$key];
        }

        return $events;
    }
}
```

- [ ] **Step 2: Create panchang API endpoint**

```php
<?php
// backend/api/panchang.php

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/cors.php';
require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../lib/Panchang.php';

$db = Database::getConnection();
$date = $_GET['date'] ?? date('Y-m-d');

// Try database first
$stmt = $db->prepare("SELECT * FROM panchang WHERE date = :date LIMIT 1");
$stmt->execute([':date' => $date]);
$cached = $stmt->fetch();

if ($cached) {
    jsonSuccess([
        'panchang' => $cached,
        'source' => 'database',
    ]);
}

// Calculate on-the-fly
$panchang = Panchang::getForDate($date);

// Cache it
$stmt = $db->prepare("
    INSERT INTO panchang (date, tithi, nakshatra, sunrise, sunset, special_events)
    VALUES (:date, :tithi, :nakshatra, :sunrise, :sunset, :events)
    ON DUPLICATE KEY UPDATE tithi = VALUES(tithi), nakshatra = VALUES(nakshatra)
");
$stmt->execute([
    ':date' => $date,
    ':tithi' => $panchang['tithi'],
    ':nakshatra' => $panchang['nakshatra'],
    ':sunrise' => $panchang['sunrise'],
    ':sunset' => $panchang['sunset'],
    ':events' => json_encode($panchang['special_events']),
]);

jsonSuccess([
    'panchang' => $panchang,
    'source' => 'calculated',
]);
```

---

### Task 5.2: Panchang Frontend Page

**Files:**
- Create: `panchang.html`

- [ ] **Step 1: Create panchang page**

A static page at `panchang.html` with the nav link added. It fetches daily data from `backend/api/panchang.php` via JS and renders it in a clean card layout. Shows tithi, nakshatra, sunrise/sunset times, and any special events/festivals.

---

### Task 5.3: Push Notifications (PWA)

**Files:**
- Create: `sw.js`
- Create: `manifest.json`

- [ ] **Step 1: Create service worker**

```javascript
// sw.js
const CACHE_NAME = 'astroshreehari-v1';
const urlsToCache = [
    '/',
    '/index.html',
    '/about.html',
    '/services.html',
    '/appointment.html',
    '/contact.html',
    '/kundali.html',
    '/panchang.html',
    '/assets/styles.css',
    '/assets/script.js',
    '/assets/logo.svg',
    '/assets/favicon.svg',
];

self.addEventListener('install', event => {
    event.waitUntil(
        caches.open(CACHE_NAME).then(cache => cache.addAll(urlsToCache))
    );
});

self.addEventListener('fetch', event => {
    event.respondWith(
        caches.match(event.request).then(response => {
            return response || fetch(event.request).then(fetchResponse => {
                // Cache API responses
                if (event.request.url.includes('/backend/api/')) {
                    const clone = fetchResponse.clone();
                    caches.open(CACHE_NAME + '-api').then(cache => {
                        cache.put(event.request, clone);
                    });
                }
                return fetchResponse;
            });
        })
    );
});

// Push notification handler
self.addEventListener('push', event => {
    const data = event.data.json();
    const options = {
        body: data.body,
        icon: '/assets/favicon.svg',
        badge: '/assets/favicon.svg',
        vibrate: [200, 100, 200],
        data: { url: data.url || '/' },
    };
    event.waitUntil(
        self.registration.showNotification(data.title, options)
    );
});

self.addEventListener('notificationclick', event => {
    event.notification.close();
    event.waitUntil(clients.openWindow(event.notification.data.url));
});
```

- [ ] **Step 2: Create PWA manifest**

```json
{
    "name": "श्रीहरि ज्योतिष परामर्श केन्द्र",
    "short_name": "श्रीहरि ज्योतिष",
    "description": "ज्योतिष तथा कर्मकाण्डमा १८ वर्षभन्दा बढी अनुभव — अनलाइन परामर्श",
    "start_url": "/",
    "display": "standalone",
    "background_color": "#fffaf0",
    "theme_color": "#711d29",
    "icons": [
        { "src": "/assets/favicon.svg", "sizes": "any", "type": "image/svg+xml" },
        { "src": "/assets/logo.svg", "sizes": "any", "type": "image/svg+xml" }
    ]
}
```

- [ ] **Step 3: Register service worker in all pages**

Add before closing `</body>` tag in all HTML pages:

```html
<script>
if ('serviceWorker' in navigator) {
    navigator.serviceWorker.register('/sw.js');
}
</script>
```

---

### Phase 6: MultiLanguage (i18n)

> **Goal:** Support Nepali (default) and English with a language toggle.

### Task 6.1: Translation Files

**Files:**
- Create: `lang/ne.json`
- Create: `lang/en.json`

- [ ] **Step 1: Create Nepali translation file**

```json
{
    "site_name": "श्रीहरि ज्योतिष परामर्श केन्द्र",
    "nav_home": "गृहपृष्ठ",
    "nav_about": "हाम्रो बारेमा",
    "nav_services": "सेवाहरू",
    "nav_kundali": "कुण्डली",
    "nav_appointment": "परामर्श",
    "nav_contact": "सम्पर्क",
    "cta_book": "परामर्श बुक गर्नुहोस्",
    "hero_title": "परम्परागत ज्ञानबाट स्पष्ट मार्गदर्शन",
    "hero_subtitle": "ज्योतिष तथा कर्मकाण्ड विषयमा १८ वर्षभन्दा बढी अनुभवसहित व्यक्तिगत र गोपनीय परामर्श।",
    "whatsapp": "WhatsApp गर्नुहोस्",
    ...
}
```

- [ ] **Step 2: Create English translation file**

```json
{
    "site_name": "Shreehari Jyotish Paramarsha Kendra",
    "nav_home": "Home",
    "nav_about": "About Us",
    "nav_services": "Services",
    "nav_kundali": "Kundali",
    "nav_appointment": "Consultation",
    "nav_contact": "Contact",
    "cta_book": "Book Consultation",
    "hero_title": "Clear Guidance from Ancient Wisdom",
    "hero_subtitle": "Personalized and confidential astrological consultation with over 18 years of experience.",
    "whatsapp": "Chat on WhatsApp",
    ...
}
```

---

### Task 6.2: ClientSide i18n Engine

**Files:**
- Modify: `assets/script.js` (add i18n)
- Modify: All `.html` files (add data-i18n attributes)

- [ ] **Step 1: Add i18n logic to script.js**

```javascript
// i18n (Phase 6)
const i18n = {
    currentLang: localStorage.getItem('lang') || 'ne',
    translations: {},

    async init() {
        try {
            const res = await fetch(`/lang/${this.currentLang}.json`);
            this.translations = await res.json();
            this.apply();
        } catch (e) {
            console.warn('i18n: falling back to default text');
        }
    },

    t(key) {
        return this.translations[key] || key;
    },

    apply() {
        document.documentElement.lang = this.currentLang;
        document.querySelectorAll('[data-i18n]').forEach(el => {
            el.textContent = this.t(el.dataset.i18n);
        });
        document.querySelectorAll('[data-i18n-placeholder]').forEach(el => {
            el.placeholder = this.t(el.dataset.i18nPlaceholder);
        });
    },

    toggle() {
        this.currentLang = this.currentLang === 'ne' ? 'en' : 'ne';
        localStorage.setItem('lang', this.currentLang);
        this.init();
    }
};

// Add language toggle button to nav
const langToggle = document.createElement('button');
langToggle.className = 'lang-toggle';
langToggle.textContent = 'EN';
langToggle.addEventListener('click', () => i18n.toggle());
document.querySelector('.nav-actions')?.prepend(langToggle);

// Init i18n after page load
document.addEventListener('DOMContentLoaded', () => i18n.init());
```

- [ ] **Step 2: Add lang-toggle styles to styles.css**

```css
.lang-toggle{
  border:1px solid var(--line);background:transparent;color:var(--maroon);
  min-width:44px;height:44px;border-radius:14px;font-weight:800;font-size:.85rem;
  cursor:pointer;transition:.2s;
}
.lang-toggle:hover{background:var(--gold-light);border-color:var(--gold)}
```

---

### Phase 7: Admin Panel & Final Polish

> **Goal:** Complete admin panel, SEO optimization, and performance tuning.

### Task 7.1: Admin Panel Pages

**Files:**
- Create: `backend/admin/settings.php`
- Create: `assets/admin.css`
- Create: `assets/admin.js`

- [ ] **Step 1: Create admin CSS**

```css
/* assets/admin.css */
body{font-family:"Noto Sans Devanagari",system-ui,sans-serif;margin:0;background:#f5f0ec;color:#2f201e}
.login-page{display:grid;place-items:center;min-height:100vh;background:linear-gradient(135deg,#711d29,#4a121d)}
.login-box{background:white;padding:40px;border-radius:22px;width:min(400px,90vw);box-shadow:0 32px 70px rgba(0,0,0,.3)}
.login-box h1{color:#711d29;margin:0 0 24px;font-size:1.6rem}
.admin-nav{background:#4a121d;color:white;padding:14px 24px;display:flex;justify-content:space-between;align-items:center}
.admin-nav .brand{color:white;font-weight:700;text-decoration:none}
.admin-nav-links{display:flex;gap:16px}
.admin-nav-links a{color:#f4dfcf;text-decoration:none;padding:6px 0;border-bottom:2px solid transparent}
.admin-nav-links a.active,.admin-nav-links a:hover{border-color:#f4d782;color:white}
.admin-main{padding:24px;max-width:1200px;margin:auto}
.stats-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:16px;margin:24px 0}
.stat-card{background:white;border-radius:16px;padding:20px;box-shadow:0 4px 12px rgba(0,0,0,.06)}
.stat-card strong{font-size:2rem;color:#711d29;display:block}
.stat-card span{color:#755f59}
.admin-table{width:100%;border-collapse:collapse;background:white;border-radius:16px;overflow:hidden;box-shadow:0 4px 12px rgba(0,0,0,.06)}
.admin-table th,.admin-table td{padding:12px 16px;text-align:left;border-bottom:1px solid rgba(113,29,41,.1)}
.admin-table th{background:#fff6ef;color:#711d29;font-weight:700}
.filter-tabs{display:flex;gap:8px;margin-bottom:16px}
.filter-tabs a{padding:8px 16px;border-radius:8px;background:white;color:#755f59;text-decoration:none;font-weight:600}
.filter-tabs a.active{background:#711d29;color:white}
.badge{padding:4px 12px;border-radius:999px;font-size:.8rem;font-weight:700}
.badge-pending{background:#fff3cd;color:#856404}
.badge-confirmed{background:#d4edda;color:#155724}
.badge-completed{background:#cce5ff;color:#004085}
.badge-cancelled{background:#f8d7da;color:#721c24}
.btn-small{padding:6px 14px;border-radius:8px;border:1px solid rgba(113,29,41,.2);background:white;cursor:pointer}
.details-row td{background:#faf6f1;padding:16px}
.detail-grid{display:grid;grid-template-columns:1fr 1fr;gap:12px}
.alert{padding:12px 16px;border-radius:10px;margin-bottom:16px}
.alert-success{background:#d4edda;color:#155724}
.alert-error{background:#f8d7da;color:#721c24}
```

---

### Task 7.2: SEO Optimization

**Files:**
- Modify: `index.html` and all pages

- [ ] **Step 1: Add structured data (JSON-LD) to homepage**

```html
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "ProfessionalService",
    "name": "श्रीहरि ज्योतिष परामर्श केन्द्र",
    "alternateName": "Shreehari Jyotish Paramarsha Kendra",
    "url": "https://www.astroshreehari.com",
    "telephone": "+9779844639228",
    "email": "shreeharijyotishparamarsakendr@gmail.com",
    "areaServed": ["NP", "IN", "US", "GB", "AU", "MY", "JP", "KR"],
    "knowsAbout": ["Astrology", "Vedic Astrology", "Jyotish", "Kundali", "Pooja Rituals"],
    "founder": {
        "@type": "Person",
        "name": "Sitaram Timsina",
        "jobTitle": "Astrologer"
    }
}
</script>
```

- [ ] **Step 2: Add SEO meta tags to all pages**

Ensure every page has:
- Unique `<title>` and `<meta name="description">`
- Open Graph tags (already present)
- Canonical URL
- `hreflang` tags for multi-language support

```html
<link rel="canonical" href="https://www.astroshreehari.com/">
<link rel="alternate" href="https://www.astroshreehari.com/" hreflang="ne">
<link rel="alternate" href="https://www.astroshreehari.com/en/" hreflang="en">
<link rel="alternate" href="https://www.astroshreehari.com/" hreflang="x-default">
```

---

### Task 7.3: Performance Optimization

**Files:**
- Modify: `assets/styles.css` (add critical CSS)
- Modify: All pages (add preload hints)

- [ ] **Step 1: Add preconnect and preload hints**

In `<head>` of all pages:

```html
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link rel="preload" href="assets/styles.css" as="style">
<link rel="preload" href="assets/logo.svg" as="image">
```

- [ ] **Step 2: Inline critical CSS for above-the-fold content**

Extract hero section styles and inline them in `<head>` to eliminate render-blocking CSS (optional, for lighthouse 95+ score).

---

## Execution Order

| Phase | Tasks | Dependency | Est. Time |
|-------|-------|-----------|-----------|
| 1 | Backend Foundation + DB | None | 2-3 days |
| 2 | Appointment System | Phase 1 | 2-3 days |
| 3 | Kundali Generation | Phase 1 | 2-3 days |
| 4 | E-Pooja + Manual Payment QR + Rewards | Phase 1-2 | 3-4 days |
| 5 | Panchang & Notifications | Phase 1 | 2-3 days |
| 6 | Multi-Language (i18n) | None (can run parallel) | 1-2 days |
| 7 | Admin Panel + SEO Polish | Phase 1-5 | 2-3 days |

**Total estimate:** 13-18 days for full implementation.

---

## Deployment Notes

- **Frontend:** Push to GitHub repo → auto-deploys via GitHub Actions (already configured)
- **Backend:** Upload `backend/` folder to cPanel shared hosting → point `api.astroshreehari.com` subdomain
- **Database:** Run `schema.sql` via phpMyAdmin or MySQL CLI
- **Environment variables:** Set via hosting panel (DB_HOST, API keys, JWT_SECRET, etc.)
- **HTTPS:** Ensure SSL certificate installed on both frontend and backend domains

---

## API Integration Notes

For **production-quality astrology calculations** (beyond simplified examples above):

| Service | Type | Cost | Notes |
|---------|------|------|-------|
| [Swiss Ephemeris](https://www.astro.com/swisseph/) | Self-hosted C lib | Free | Needs PHP extension |
| [Prokerala Astrology API](https://www.prokerala.com/astrology/) | REST API | Paid (~$20/mo) | Easy PHP integration |
| [AstroSage API](https://www.astrosage.com/) | REST API | Paid | Good for Indian/Vedic |
| [Horoscope API](https://horoscope-api.com/) | REST API | Paid | Modern JSON API |

For **production panchang**, replace the simplified `Panchang.php` with one of:
- **Nepali Patro API** (for Nepal-specific data)
- **Prokerala Panchang API**
- **Swiss Ephemeris** with full Vedic calculations

---

## Design Guidelines (from the spec)

- **Mobile-first:** 70%+ users on mobile (current CSS already handles this well)
- **Colors:** Maroon (#711d29), Saffron (#e68118), Gold (#d6a83a), Cream (#fffaf0)
- **Typography:** Noto Sans Devanagari + Tiro Devanagari Sanskrit
- **UI feel:** Traditional yet modern & gentle; no flashy animations
- **Accessibility:** Easy for elderly users — clear fonts, large touch targets
- **Data privacy:** All user data encrypted, SSL everywhere, no data leaks
