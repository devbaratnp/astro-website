<?php

define('SITE_NAME_NE', 'श्रीहरि ज्योतिष परामर्श केन्द्र');
define('SITE_NAME_EN', 'Shreehari Jyotish Paramarsha Kendra');
define('BASE_URL', getenv('APP_URL') ?: 'https://www.astroshreehari.com');
define('API_URL', getenv('API_URL') ?: 'https://api.astroshreehari.com');
define('ADMIN_EMAIL', 'shreeharijyotishparamarsakendr@gmail.com');
define('WHATSAPP_NUMBER', '9779844639228');
define('TIMEZONE', 'Asia/Kathmandu');
define('JWT_SECRET', getenv('JWT_SECRET') ?: 'change-this-to-a-random-secret');

date_default_timezone_set(TIMEZONE);
