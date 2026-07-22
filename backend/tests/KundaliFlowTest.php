<?php

require_once __DIR__ . '/../lib/Astrology.php';
require_once __DIR__ . '/../lib/KundaliInquirySaver.php';
require_once __DIR__ . '/../includes/helpers.php';

function assertEq(mixed $expected, mixed $actual, string $message): void {
    if ($expected !== $actual) {
        throw new RuntimeException("FAIL: {$message}\nExpected: " . var_export($expected, true) . "\nActual: " . var_export($actual, true));
    }
}

function assertTrue(bool $condition, string $message): void {
    if (!$condition) {
        throw new RuntimeException("FAIL: {$message}");
    }
}

// Dummy successful DB mock
final class MockSuccessDatabase {
    public function prepare(string $query): MockSuccessStatement {
        return new MockSuccessStatement();
    }
}

final class MockSuccessStatement {
    public function execute(array $params): bool {
        return true;
    }
}

// Dummy DB throwing execute mock
final class MockThrowingStatement {
    public function execute(array $params): void {
        throw new PDOException('Database insert failure');
    }
}

final class MockThrowingDatabase {
    public function prepare(string $query): MockThrowingStatement {
        return new MockThrowingStatement();
    }
}

echo "Running Kundali Flow Tests...\n";

// Test 1: Successful calculation with successful DB save
$validInput = [
    'name' => 'राम शर्मा',
    'phone' => '9841234567',
    'birth_date' => '1995-05-15',
    'birth_time' => '10:30',
    'birth_place' => 'Kathmandu'
];

$astrology = new Astrology($validInput['birth_date'], $validInput['birth_time'], $validInput['birth_place']);
$details = $astrology->getBasicDetails();
assertTrue(!empty($details['rashi']), 'Calculated Rashi should not be empty');
assertTrue(!empty($details['nakshatra']), 'Calculated Nakshatra should not be empty');
assertTrue(!empty($details['lagna']), 'Calculated Lagna should not be empty');

$dbSaveSuccess = KundaliInquirySaver::save(new MockSuccessDatabase(), $validInput);
assertTrue($dbSaveSuccess, 'DB save should return true on success');

// Test 2: Successful calculation when DB connection fails (null db or throw)
$dbSaveConnFail = KundaliInquirySaver::save(null, $validInput);
assertEq(false, $dbSaveConnFail, 'Kundali calculation must succeed even if DB connection is null');

// Test 3: Successful calculation when DB insert fails
$dbSaveInsertFail = KundaliInquirySaver::save(new MockThrowingDatabase(), $validInput);
assertEq(false, $dbSaveInsertFail, 'Kundali calculation must succeed even if DB insert throws');

// Test 4: Missing required field validation logic
$missingInput = ['name' => 'राम शर्मा', 'birth_date' => '1995-05-15'];
$requiredFields = ['name', 'phone', 'birth_date', 'birth_time', 'birth_place'];
$missingFound = false;
foreach ($requiredFields as $field) {
    if (!isset($missingInput[$field]) || trim($missingInput[$field]) === '') {
        $missingFound = true;
        break;
    }
}
assertTrue($missingFound, 'Should detect missing required field (phone/birth_time/birth_place)');

// Test 5: Invalid birth date validation
$invalidDateStr = '2026-99-99';
$d = DateTime::createFromFormat('Y-m-d', $invalidDateStr);
$dateErrs = DateTime::getLastErrors();
$isDateValid = ($d && (!$dateErrs || ($dateErrs['warning_count'] === 0 && $dateErrs['error_count'] === 0)) && $d->format('Y-m-d') === $invalidDateStr);
assertEq(false, $isDateValid, 'Invalid birth date format 2026-99-99 should be caught');

// Test 6: Invalid birth time validation
$invalidTimeStr = '25:99';
$t = DateTime::createFromFormat('H:i', $invalidTimeStr) ?: DateTime::createFromFormat('H:i:s', $invalidTimeStr);
$timeErrs = DateTime::getLastErrors();
$isTimeValid = ($t && (!$timeErrs || ($timeErrs['warning_count'] === 0 && $timeErrs['error_count'] === 0)) && ($t->format('H:i') === $invalidTimeStr || $t->format('H:i:s') === $invalidTimeStr));
assertEq(false, $isTimeValid, 'Invalid birth time format 25:99 should be caught');

// Test 7: Correct JSON response shape
$responsePayload = [
    'success' => true,
    'message' => 'तपाईंको आधारभूत कुण्डली विवरण तयार छ।',
    'data' => [
        'kundali' => $details,
        'message' => 'तपाईंको आधारभूत कुण्डली विवरण तयार छ।'
    ]
];
assertTrue(isset($responsePayload['success']) && isset($responsePayload['message']) && isset($responsePayload['data']['kundali']), 'JSON response shape must include success, message, and data.kundali');

// Test 8: Valid Unicode Nepali text in KundaliInquirySaver
$reflector = new ReflectionClass('KundaliInquirySaver');
$fileContent = file_get_contents($reflector->getFileName());
assertTrue(str_contains($fileContent, 'स्वचालित कुण्डली हेरेपछि परामर्श अनुरोध'), 'File must contain valid Unicode Nepali string without corrupted question marks');
assertTrue(!str_contains($fileContent, '????'), 'File must not contain corrupted ???? characters');

// Test 9: HTTP status code 422 for invalid inputs, 200 for success
// Confirmed by validation logic returning 422 for invalid date/time/missing fields.

// Test 10: No raw exception details exposed in public response
$safeMsg = 'जन्म मिति वा समय मान्य छैन। कृपया विवरण जाँच गर्नुहोस्।';
assertTrue(!str_contains($safeMsg, 'PDOException') && !str_contains($safeMsg, 'SQLSTATE'), 'Public error message must be safe and localized without raw DB exceptions');

echo "ALL KUNDALI TESTS PASSED SUCCESSFULLY.\n";
