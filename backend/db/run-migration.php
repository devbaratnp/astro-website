<?php
$db = new PDO('mysql:host=localhost;dbname=astroshreehari;charset=utf8mb4', 'root', '');
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$sql = file_get_contents(__DIR__ . '/migrations/2026-07-20-products-and-store.sql');
$statements = explode(';', $sql);
foreach ($statements as $stmt) {
    $stmt = trim($stmt);
    if (!empty($stmt)) {
        $db->exec($stmt);
        echo "Executed: " . substr($stmt, 0, 60) . "...\n";
    }
}
echo "Migration completed successfully.\n";
