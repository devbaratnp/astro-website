<?php

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/cors.php';
require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../lib/Panchang.php';

$db = Database::getConnection();
$date = $_GET['date'] ?? date('Y-m-d');

$stmt = $db->prepare("SELECT * FROM panchang WHERE date = :date LIMIT 1");
$stmt->execute([':date' => $date]);
$cached = $stmt->fetch();

if ($cached) {
    jsonSuccess([
        'panchang' => $cached,
        'source' => 'database',
    ]);
}

$panchang = Panchang::getForDate($date);

$stmt = $db->prepare("
    INSERT INTO panchang (date, tithi, nakshatra, sunrise, sunset, special_events_ne)
    VALUES (:date, :tithi, :nakshatra, :sunrise, :sunset, :events)
    ON DUPLICATE KEY UPDATE
        tithi = VALUES(tithi),
        nakshatra = VALUES(nakshatra),
        sunrise = VALUES(sunrise),
        sunset = VALUES(sunset),
        special_events_ne = VALUES(special_events_ne)
");
$stmt->execute([
    ':date' => $date,
    ':tithi' => $panchang['tithi'],
    ':nakshatra' => $panchang['nakshatra'],
    ':sunrise' => $panchang['sunrise'],
    ':sunset' => $panchang['sunset'],
    ':events' => json_encode($panchang['special_events']),
]);

jsonSuccess([
    'panchang' => $panchang,
    'source' => 'calculated',
]);
