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
    INSERT INTO panchang (date, tithi, nakshatra, moon_rashi, yoga, karana, sunrise, sunset, rahu_kaal, auspicious_times, special_events_ne, special_events_en)
    VALUES (:date, :tithi, :nakshatra, :moon_rashi, :yoga, :karana, :sunrise, :sunset, :rahu_kaal, :auspicious_times, :events_ne, :events_en)
    ON DUPLICATE KEY UPDATE
        tithi = VALUES(tithi),
        nakshatra = VALUES(nakshatra),
        moon_rashi = VALUES(moon_rashi),
        yoga = VALUES(yoga),
        karana = VALUES(karana),
        sunrise = VALUES(sunrise),
        sunset = VALUES(sunset),
        rahu_kaal = VALUES(rahu_kaal),
        auspicious_times = VALUES(auspicious_times),
        special_events_ne = VALUES(special_events_ne),
        special_events_en = VALUES(special_events_en)
");
$stmt->execute([
    ':date' => $date,
    ':tithi' => $panchang['tithi'],
    ':nakshatra' => $panchang['nakshatra'],
    ':moon_rashi' => $panchang['moon_rashi'],
    ':yoga' => $panchang['yoga'] ?? null,
    ':karana' => $panchang['karana'] ?? null,
    ':sunrise' => $panchang['sunrise'],
    ':sunset' => $panchang['sunset'],
    ':rahu_kaal' => $panchang['rahu_kaal'] ?? null,
    ':auspicious_times' => isset($panchang['auspicious_times']) ? json_encode($panchang['auspicious_times']) : null,
    ':events_ne' => json_encode($panchang['special_events'] ?? []),
    ':events_en' => json_encode($panchang['special_events_en'] ?? []),
]);

jsonSuccess([
    'panchang' => $panchang,
    'source' => 'calculated',
]);
