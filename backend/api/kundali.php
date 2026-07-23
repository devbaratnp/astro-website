<?php

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/cors.php';
require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../lib/Astrology.php';
require_once __DIR__ . '/../lib/Panchang.php';
require_once __DIR__ . '/../lib/KundaliInquirySaver.php';

$method = $_SERVER['REQUEST_METHOD'];

if ($method !== 'POST') {
    jsonError('Method not allowed', 405);
}

$input = getJsonInput();

$requiredFields = [
    'name' => 'नाम',
    'phone' => 'फोन',
    'birth_date' => 'जन्म मिति',
    'birth_time' => 'जन्म समय',
    'birth_place' => 'जन्म स्थान',
];
$missing = [];
foreach ($requiredFields as $field => $label) {
    if (!isset($input[$field]) || !is_string($input[$field]) || trim($input[$field]) === '') {
        $missing[] = $label;
    }
}
if (count($missing) > 0) {
    jsonError('कृपया ' . implode(', ', $missing) . ' भर्नुहोस्।', 422);
}

$birthTimeStr = trim($input['birth_time']);

$t = DateTime::createFromFormat('H:i', $birthTimeStr) ?: DateTime::createFromFormat('H:i:s', $birthTimeStr);
$timeErrs = DateTime::getLastErrors();
if (!$t || ($timeErrs && ($timeErrs['warning_count'] > 0 || $timeErrs['error_count'] > 0)) || ($t->format('H:i') !== $birthTimeStr && $t->format('H:i:s') !== $birthTimeStr)) {
    jsonError('जन्म समय (घण्टा:मिनेट) मान्य छैन। उदाहरण: १२:३०', 422);
}

$birthBs = null;
$birthDateStr = null;
if (!empty($input['birth_date'])) {
    $parts = explode('-', $input['birth_date']);
    if (count($parts) === 3) {
        $bsYear = (int)$parts[0];
        $bsMonth = (int)$parts[1];
        $bsDay = (int)$parts[2];
        if ($bsYear >= 1900 && $bsYear <= 2100 && $bsMonth >= 1 && $bsMonth <= 12 && $bsDay >= 1 && $bsDay <= 32) {
            $ad = Panchang::bsToAd($bsYear, $bsMonth, $bsDay);
            $birthDateStr = sprintf('%04d-%02d-%02d', $ad['y'], $ad['m'], $ad['d']);
            $birthBs = $input['birth_date'];
        }
    }
}
if (!$birthDateStr) {
    jsonError('जन्म मिति (बि.सं.) मान्य छैन। वर्ष, महिना र गते ठीकसँग चयन गर्नुहोस्।', 422);
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
    jsonError('गणना गर्दा समस्या भयो। कृपया मिति, समय र स्थान जाँच गरी पुनः प्रयास गर्नुहोस्।', 422);
}

try {
    $db = Database::getConnection();
    KundaliInquirySaver::save($db, $input);
} catch (Throwable $error) {
    error_log('Kundali inquiry database failure: ' . $error->getMessage());
}

jsonSuccess([
    'kundali' => $details,
    'message' => 'तपाईंको पूर्ण कुण्डली विवरण तयार छ।',
]);