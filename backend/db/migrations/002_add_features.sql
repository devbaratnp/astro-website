CREATE TABLE IF NOT EXISTS testimonials (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    title VARCHAR(200) DEFAULT '',
    content TEXT NOT NULL,
    rating TINYINT UNSIGNED DEFAULT 5,
    photo VARCHAR(300) DEFAULT '',
    location VARCHAR(200) DEFAULT '',
    sort_order INT UNSIGNED DEFAULT 0,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_active (is_active, sort_order)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS events (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    type ENUM('event', 'tour') NOT NULL DEFAULT 'event',
    title_ne VARCHAR(200) NOT NULL,
    title_en VARCHAR(200) DEFAULT '',
    description_ne TEXT,
    description_en TEXT,
    date_from DATE NOT NULL,
    date_to DATE DEFAULT NULL,
    time_from TIME DEFAULT NULL,
    location VARCHAR(300) DEFAULT '',
    cover_image VARCHAR(300) DEFAULT '',
    registration_url VARCHAR(500) DEFAULT '',
    contact_person VARCHAR(100) DEFAULT '',
    contact_phone VARCHAR(20) DEFAULT '',
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_type_date (type, is_active, date_from)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS gallery_items (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    type ENUM('image', 'video', 'audio') NOT NULL DEFAULT 'image',
    title_ne VARCHAR(200) NOT NULL,
    title_en VARCHAR(200) DEFAULT '',
    url VARCHAR(500) DEFAULT '',
    thumbnail VARCHAR(300) DEFAULT '',
    embed_url VARCHAR(500) DEFAULT '',
    source VARCHAR(100) DEFAULT '',
    sort_order INT UNSIGNED DEFAULT 0,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_type_active (type, is_active, sort_order)
) ENGINE=InnoDB;
