<?php

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/cors.php';
require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../lib/Astrology.php';
require_once __DIR__ . '/../lib/KundaliInquirySaver.php';

$method = $_SERVER['REQUEST_METHOD'];

if ($method !== 'POST') {
    jsonError('Method not allowed', 405);
}

$input = getJsonInput();

$requiredFields = ['name', 'phone', 'birth_date', 'birth_time', 'birth_place'];
foreach ($requiredFields as $field) {
    if (!isset($input[$field]) || !is_string($input[$field]) || trim($input[$field]) === '') {
        jsonError("कृपया आवश्यक विवरण भरिपुर्याउनुहोस्।", 422);
    }
}

$birthDateStr = trim($input['birth_date']);
$birthTimeStr = trim($input['birth_time']);

$d = DateTime::createFromFormat('Y-m-d', $birthDateStr);
$dateErrs = DateTime::getLastErrors();
if (!$d || ($dateErrs && ($dateErrs['warning_count'] > 0 || $dateErrs['error_count'] > 0)) || $d->format('Y-m-d') !== $birthDateStr) {
    jsonError('जन्म मिति मान्य छैन। कृपया विवरण जाँच गर्नुहोस्।', 422);
}

$t = DateTime::createFromFormat('H:i', $birthTimeStr) ?: DateTime::createFromFormat('H:i:s', $birthTimeStr);
$timeErrs = DateTime::getLastErrors();
if (!$t || ($timeErrs && ($timeErrs['warning_count'] > 0 || $timeErrs['error_count'] > 0)) || ($t->format('H:i') !== $birthTimeStr && $t->format('H:i:s') !== $birthTimeStr)) {
    jsonError('जन्म समय मान्य छैन। कृपया विवरण जाँच गर्नुहोस्।', 422);
}

try {
    $astrology = new Astrology(
        $birthDateStr,
        $birthTimeStr,
        trim($input['birth_place'])
    );

    $details = $astrology->getBasicDetails();
} catch (Throwable $error) {
    error_log('Kundali calculation failed: ' . $error->getMessage());
    jsonError('जन्म मिति वा समय मान्य छैन। कृपया विवरण जाँच गर्नुहोस्।', 422);
}

try {
    $db = Database::getConnection();
    KundaliInquirySaver::save($db, $input);
} catch (Throwable $error) {
    error_log('Kundali inquiry database failure: ' . $error->getMessage());
}

jsonSuccess([
    'kundali' => $details,
    'message' => 'तपाईंको आधारभूत कुण्डली विवरण तयार छ।',
]);
