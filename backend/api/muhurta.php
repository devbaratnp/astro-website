<?php
require_once __DIR__ . '/../config/cors.php';
require_once __DIR__ . '/../includes/helpers.php';

$nakshatras = ['अश्विनी', 'भरणी', 'कृत्तिका', 'रोहिणी', 'मृगशिरा', 'आर्द्रा', 'पुनर्वसु', 'पुष्य', 'अश्लेषा', 'मघा', 'पूर्वाफाल्गुनी', 'उत्तराफाल्गुनी', 'हस्त', 'चित्रा', 'स्वाती', 'विशाखा', 'अनुराधा', 'ज्येष्ठा', 'मूल', 'पूर्वाषाढा', 'उत्तराषाढा', 'श्रवण', 'धनिष्ठा', 'शतभिषा', 'पूर्वभाद्रपद', 'उत्तरभाद्रपद', 'रेवती'];

$muhurtaTypes = [
    'विवाह' => ['good' => [3,5,7,10,11,13,14,15,16,17,21,22,24,25,26], 'avoid' => [1,4,6,8,9,18,19,20,23,27]],
    'गृहप्रवेश' => ['good' => [2,3,5,7,10,11,13,14,15,16,21,22,24,25,26], 'avoid' => [1,4,6,8,9,17,18,19,20,23,27]],
    'व्रतबन्ध' => ['good' => [2,3,5,7,10,11,13,14,15,16,21,22,24,25,26], 'avoid' => [1,4,6,8,9,12,17,18,19,20,23,27]],
    'व्यवसाय' => ['good' => [2,3,5,7,10,11,13,14,15,16,21,22,24,25,26], 'avoid' => [1,4,6,8,9,12,17,18,19,20,23,27]],
    'यात्रा' => ['good' => [2,3,5,7,10,11,13,14,15,16,21,22,24,25,26], 'avoid' => [1,4,6,8,9,12,17,18,19,20,23,27]],
];

$type = $_GET['type'] ?? 'विवाह';
$date = $_GET['date'] ?? date('Y-m-d');

if (!isset($muhurtaTypes[$type])) jsonError('Invalid muhurta type', 400);

$timestamp = strtotime($date);
$dayOfYear = (int)date('z', $timestamp);
$nakshatraIndex = ((int)($dayOfYear * 27 / 365)) % 27;
$nakshatra = $nakshatras[$nakshatraIndex];
$config = $muhurtaTypes[$type];

$isGood = in_array($nakshatraIndex + 1, $config['good']);
$isAvoid = in_array($nakshatraIndex + 1, $config['avoid']);

$dayNames = ['आइतबार', 'सोमबार', 'मङ्गलबार', 'बुधबार', 'बिहीबार', 'शुक्रबार', 'शनिबार'];
$dayOfWeek = (int)date('w', $timestamp);
$goodDays = [0 => false, 1 => true, 2 => false, 3 => true, 4 => true, 5 => true, 6 => false];
$isGoodDay = $goodDays[$dayOfWeek];

if ($isGood && $isGoodDay) {
    $verdict = 'शुभ';
    $description = 'यस दिनको नक्षत्र र वार दुवै शुभ छन्। ' . $type . ' का लागि उत्तम मुहूर्त।';
} elseif ($isGood) {
    $verdict = 'मध्यम';
    $description = 'नक्षत्र शुभ भए पनि वार शुभ छैन। आवश्यक भए मात्र गर्नुहोस्।';
} elseif ($isGoodDay) {
    $verdict = 'मध्यम';
    $description = 'वार शुभ भए पनि नक्षत्र शुभ छैन। वैकल्पिक मिति रोज्नु उचित हुन्छ।';
} else {
    $verdict = 'अशुभ';
    $description = 'यस दिन ' . $type . ' का लागि मुहूर्त शुभ छैन। अर्को शुभ मिति हेर्नुहोस्।';
}

jsonSuccess([
    'date' => $date,
    'type' => $type,
    'nakshatra' => $nakshatra,
    'day' => $dayNames[$dayOfWeek],
    'is_nakshatra_good' => $isGood,
    'is_day_good' => $isGoodDay,
    'verdict' => $verdict,
    'description' => $description,
    'good_nakshatras' => array_map(fn($i) => $nakshatras[$i - 1], $config['good']),
    'disclaimer' => 'यो सामान्य मुहूर्त जानकारी हो; व्यक्तिगत कुण्डली अनुसार विस्तृत परामर्शका लागि गुरुज्यूसँग सम्पर्क गर्नुहोस्।',
]);
