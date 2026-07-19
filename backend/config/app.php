<?php

define('SITE_NAME_NE', 'श्रीहरि ज्योतिष परामर्श केन्द्र');
define('SITE_NAME_EN', 'Shreehari Jyotish Paramarsha Kendra');
define('BASE_URL', getenv('APP_URL') ?: 'https://www.astroshreehari.com');
define('API_URL', getenv('API_URL') ?: 'https://api.astroshreehari.com');
define('ADMIN_EMAIL', 'astroshreehari3m@gmail.com');
define('WHATSAPP_NUMBER', '9779844639228');
define('TIMEZONE', 'Asia/Kathmandu');
define('JWT_SECRET', getenv('JWT_SECRET') ?: 'change-this-to-a-random-secret');

// Google Calendar Integration
define('GCAL_CALENDAR_ID', getenv('GCAL_CALENDAR_ID') ?: 'astroshreehari3m@gmail.com');
define('GCAL_CREDENTIALS_PATH', getenv('GCAL_CREDENTIALS_PATH') ?: __DIR__ . '/gcal-service-account.json');
define('GCAL_SLOT_DURATION', (int)getenv('GCAL_SLOT_DURATION') ?: 30);
define('GCAL_BUFFER_MINUTES', (int)getenv('GCAL_BUFFER_MINUTES') ?: 15);
define('GCAL_WORKING_HOURS_START', (int)getenv('GCAL_WORKING_HOURS_START') ?: 9);
define('GCAL_WORKING_HOURS_END', (int)getenv('GCAL_WORKING_HOURS_END') ?: 20);

// SMTP Mail Configuration (credentials loaded from separate file, never committed)
$smtpCredentials = [];
$smtpCredFile = __DIR__ . '/smtp.credentials.php';
if (is_file($smtpCredFile)) {
    $smtpCredentials = require $smtpCredFile;
}
define('SMTP_HOST', getenv('SMTP_HOST') ?: ($smtpCredentials['host'] ?? ''));
define('SMTP_PORT', (int)(getenv('SMTP_PORT') ?: ($smtpCredentials['port'] ?? 587)));
define('SMTP_USER', getenv('SMTP_USER') ?: ($smtpCredentials['user'] ?? ''));
define('SMTP_PASS', getenv('SMTP_PASS') ?: ($smtpCredentials['pass'] ?? ''));
define('SMTP_FROM', getenv('SMTP_FROM') ?: ($smtpCredentials['from'] ?? 'noreply@astroshreehari.com'));

date_default_timezone_set(TIMEZONE);
