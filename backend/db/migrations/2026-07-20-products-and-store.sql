-- Products table for Pandit Ji's online store
CREATE TABLE IF NOT EXISTS products (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title_ne VARCHAR(200) NOT NULL,
    title_en VARCHAR(200),
    description_ne TEXT,
    description_en TEXT,
    price DECIMAL(10,2) NOT NULL,
    compare_price DECIMAL(10,2),
    images JSON,
    category VARCHAR(100),
    stock_status ENUM('in_stock', 'out_of_stock', 'pre_order') DEFAULT 'in_stock',
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_active (is_active),
    INDEX idx_category (category)
) ENGINE=InnoDB;

-- Add product_id to appointments
ALTER TABLE appointments
    ADD COLUMN product_id INT UNSIGNED DEFAULT NULL AFTER consultation_mode,
    ADD FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE SET NULL;
