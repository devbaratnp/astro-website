<?php

require_once __DIR__ . '/../lib/KundaliInquirySaver.php';
require_once __DIR__ . '/../includes/helpers.php';

function assertSameValue(mixed $expected, mixed $actual, string $message): void {
    if ($expected !== $actual) {
        throw new RuntimeException($message . "\nExpected: " . var_export($expected, true) . "\nActual: " . var_export($actual, true));
    }
}

final class ThrowingStatement {
    public function execute(array $params): void {
        throw new PDOException('Database write failed');
    }
}

final class ThrowingDatabase {
    public function prepare(string $query): ThrowingStatement {
        return new ThrowingStatement();
    }
}

$saved = KundaliInquirySaver::save(new ThrowingDatabase(), [
    'name' => 'Codex Kundali Test',
    'phone' => '9800000000',
    'birth_date' => '2026-07-25',
    'birth_time' => '05:41',
    'birth_place' => 'Jhapa',
]);

assertSameValue(false, $saved, 'A database-save failure must not break Kundali generation.');
echo "PASS\n";
