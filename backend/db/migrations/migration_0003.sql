CREATE TABLE IF NOT EXISTS services (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    service_key VARCHAR(50) NOT NULL UNIQUE,
    icon VARCHAR(50) DEFAULT '',
    title_ne VARCHAR(200) NOT NULL,
    title_en VARCHAR(200) DEFAULT '',
    description_ne TEXT,
    description_en TEXT,
    sort_order INT UNSIGNED DEFAULT 0,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_active_sort (is_active, sort_order)
) ENGINE=InnoDB;

INSERT IGNORE INTO services (service_key, icon, title_ne, title_en, description_ne, description_en, sort_order) VALUES
('kundali', 'ChartPolar', 'जन्मकुण्डली विश्लेषण', 'Birth Chart Analysis', 'व्यक्तित्व, करियर, स्वास्थ्य र जीवनका महत्वपूर्ण पक्षहरूको शास्त्रीय विश्लेषण।', 'Classical analysis of personality, career, health, and important life aspects.', 1),
('marriage', 'Heart', 'विवाह तथा गुण मिलान', 'Marriage & Compatibility', 'वैवाहिक अनुकूलता, गुण मिलान र दाम्पत्य जीवनका लागि स्पष्ट मार्गदर्शन।', 'Marital compatibility, guna matching, and clear guidance for married life.', 2),
('vastu', 'Compass', 'वास्तु परामर्श', 'Vastu Consultation', 'घर, कार्यालय र व्यवसायिक स्थानमा सकारात्मक ऊर्जा र समृद्धिका उपाय।', 'Remedies for positive energy and prosperity in home, office, and business spaces.', 3),
('grahadasha', 'Planet', 'ग्रह शान्ति', 'Planetary Peace', 'नवग्रह शान्ति, दोष निवारण तथा शास्त्रसम्मत वैदिक उपाय।', 'Navagraha peace, dosha remedies, and scripture-based Vedic solutions.', 4),
('pooja', 'Campfire', 'वैदिक कर्मकाण्ड', 'Vedic Rituals', 'पूजा, होम, व्रत, संस्कार र जीवनका सम्पूर्ण वैदिक कर्मकाण्ड सेवा।', 'Pooja, homa, fasting, sanskaras, and all Vedic ritual services for life.', 5),
('general', 'CalendarDots', 'शुभ मुहूर्त', 'Auspicious Timing', 'विवाह, गृहप्रवेश, व्यवसाय, यात्रा र अन्य कार्यका लागि शुभ समय निर्धारण।', 'Determination of auspicious times for marriage, housewarming, business, travel, and more.', 6);
