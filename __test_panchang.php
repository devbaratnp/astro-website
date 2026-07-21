<?php
$_GET['date'] = '2026-07-21';
try {
    require "backend/api/panchang.php";
} catch (Throwable $e) {
    echo "Error: " . $e->getMessage() . "\n" . $e->getTraceAsString() . "\n";
}
