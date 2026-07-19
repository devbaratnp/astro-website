<?php
$isCli = php_sapi_name() === 'cli';

if (!$isCli) {
    if (empty($_GET['key']) || $_GET['key'] !== 'astrotest2026') {
        http_response_code(403);
        die('Forbidden');
    }
    header('Content-Type: text/plain; charset=utf-8');
}

require_once __DIR__ . '/config/app.php';
require_once __DIR__ . '/lib/Mailer.php';

$to = $isCli ? ($argv[1] ?? 'mind59024@hmail.com') : ($_GET['to'] ?? 'mind59024@hmail.com');
$type = $isCli ? ($argv[2] ?? 'both') : ($_GET['type'] ?? 'both');

echo "Testing Mailer...\n";
echo "From: " . (defined('SMTP_FROM') ? SMTP_FROM : 'default') . "\n";
echo "SMTP Host: " . (defined('SMTP_HOST') && SMTP_HOST ? SMTP_HOST : '(using mail())') . "\n";
echo "To: {$to}\n";
echo "Type: {$type}\n\n";

$data = [
    'name' => 'Test User',
    'phone' => '9812345678',
    'email' => $to,
    'service_type' => 'kundali',
    'preferred_date' => date('Y-m-d'),
    'preferred_time' => '10:30',
    'consultation_mode' => 'video',
    'message' => 'नमस्कार गुरुज्यू, कृपया मेरो कुण्डली हेरिदिनुहोस्। यो परीक्षण सन्देश हो।',
    'meeting_url' => 'https://meet.jit.si/AstroShreeHari-test123',
    'birth_date' => '2050-05-15',
    'birth_time' => '08:30:00',
    'birth_place' => 'Kathmandu',
];

$mailer = new Mailer();

if ($type === 'admin' || $type === 'both') {
    echo "Sending admin notification... ";
    $ok = $mailer->send($to, '🔔 परीक्षण: नयाँ परामर्श अनुरोध', Mailer::adminNotification($data));
    echo $ok ? "SENT\n" : "FAILED\n";
}

if ($type === 'client' || $type === 'both') {
    echo "Sending client confirmation... ";
    $ok = $mailer->send($to, '🙏 परीक्षण: तपाईंको अनुरोध प्राप्त भयो', Mailer::clientConfirmation($data));
    echo $ok ? "SENT\n" : "FAILED\n";
}

echo "\nDone. Check {$to} inbox (and spam folder).\n";
