<?php

if (!defined('ASTRO_PUBLIC')) {
    define('ASTRO_PUBLIC', true);
}

define('SITE_URL', getenv('APP_URL') ?: 'https://www.astroshreehari.com');

function getSetting($key, $default = '') {
    static $cache = null;
    if ($cache === null) {
        try {
            $db = Database::getConnection();
            $stmt = $db->query("SELECT setting_key, setting_value FROM site_settings");
            $cache = [];
            while ($row = $stmt->fetch()) $cache[$row['setting_key']] = $row['setting_value'];
        } catch (Throwable $e) {
            $cache = [];
        }
    }
    return $cache[$key] ?? $default;
}

if (!defined('PHONE')) define('PHONE', getSetting('phone', '9779844639228'));
if (!defined('PHONE_DISPLAY')) define('PHONE_DISPLAY', getSetting('phone_display', '+977 9844639228'));
if (!defined('EMAIL')) define('EMAIL', getSetting('email', 'Astroshreeharee@gmail.com'));

$parsedUrl = parse_url(SITE_URL);
define('BASE_PATH', rtrim($parsedUrl['path'] ?? '', '/'));

function assetUrl(string $path): string {
    return BASE_PATH . $path;
}

require_once __DIR__ . '/../backend/includes/helpers.php';
require_once __DIR__ . '/../backend/config/database.php';

function getDbConnection(): ?PDO {
    try {
        return Database::getConnection();
    } catch (Throwable $e) {
        error_log('Database connection error in public frontend: ' . $e->getMessage());
        return null;
    }
}

function getServicesList(): array {
    try {
        $db = Database::getConnection();
        $stmt = $db->query("SELECT service_key, icon, title_ne, description_ne FROM services WHERE is_active = 1 ORDER BY sort_order ASC");
        $rows = $stmt->fetchAll();
        if (!empty($rows)) {
            return array_map(function($r) {
                return ['key' => $r['service_key'], 'icon' => $r['icon'], 'title' => $r['title_ne'], 'text' => $r['description_ne']];
            }, $rows);
        }
    } catch (Throwable $e) {
        error_log('getServicesList DB error: ' . $e->getMessage());
    }
    return [
        ['key' => 'kundali', 'icon' => 'ChartPolar', 'title' => 'जन्मकुण्डली विश्लेषण', 'text' => 'व्यक्तित्व, करियर, स्वास्थ्य र जीवनका महत्वपूर्ण पक्षहरूको शास्त्रीय विश्लेषण।'],
        ['key' => 'marriage', 'icon' => 'Heart', 'title' => 'विवाह तथा गुण मिलान', 'text' => 'वैवाहिक अनुकूलता, गुण मिलान र दाम्पत्य जीवनका लागि स्पष्ट मार्गदर्शन।'],
        ['key' => 'vastu', 'icon' => 'Compass', 'title' => 'वास्तु परामर्श', 'text' => 'घर, कार्यालय र व्यवसायिक स्थानमा सकारात्मक ऊर्जा र समृद्धिका उपाय।'],
        ['key' => 'grahadasha', 'icon' => 'Planet', 'title' => 'ग्रह शान्ति', 'text' => 'नवग्रह शान्ति, दोष निवारण तथा शास्त्रसम्मत वैदिक उपाय।'],
        ['key' => 'pooja', 'icon' => 'Campfire', 'title' => 'वैदिक कर्मकाण्ड', 'text' => 'पूजा, होम, व्रत, संस्कार र जीवनका सम्पूर्ण वैदिक कर्मकाण्ड सेवा।'],
        ['key' => 'general', 'icon' => 'CalendarDots', 'title' => 'शुभ मुहूर्त', 'text' => 'विवाह, गृहप्रवेश, व्यवसाय, यात्रा र अन्य कार्यका लागि शुभ समय निर्धारण।'],
    ];
}
