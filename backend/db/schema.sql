-- backend/db/schema.sql

CREATE DATABASE IF NOT EXISTS astroshreehari CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE astroshreehari;

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
    meeting_url VARCHAR(500),
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

CREATE TABLE admin_users (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    display_name VARCHAR(100),
    role ENUM('admin', 'editor') DEFAULT 'editor',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

INSERT INTO admin_users (username, password_hash, display_name, role)
VALUES ('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrator', 'admin');

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
