CREATE TABLE IF NOT EXISTS site_settings (
    setting_key VARCHAR(100) PRIMARY KEY,
    setting_value TEXT,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

INSERT IGNORE INTO site_settings (setting_key, setting_value) VALUES
('site_name', 'Shreehari Jyotish'),
('site_tagline_ne', 'शास्त्रीय ज्योतिष, वैदिक कर्मकाण्ड तथा आध्यात्मिक मार्गदर्शन'),
('site_tagline_en', 'Classical Astrology, Vedic Rituals & Spiritual Guidance'),
('phone', '9779844639228'),
('phone_display', '+977 9844639228'),
('email', 'Astroshreeharee@gmail.com'),
('address', 'काठमाडौं, नेपाल'),
('youtube_url', ''),
('facebook_url', ''),
('whatsapp_number', '9779844639228'),
('logo_url', ''),
('favicon_url', '/assets/favicon.svg'),
('business_hours', 'बिहान ६:०० — साँझ ७:००'),
('consultation_hours', 'पूर्व निर्धारित समय अनुसार');
