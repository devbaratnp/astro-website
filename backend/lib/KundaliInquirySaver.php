<?php

final class KundaliInquirySaver {
    /**
     * The Kundali calculation is still useful if the optional inquiry record
     * cannot be stored. Database failures are logged for administrators but
     * must not turn a successful calculation into a visitor-facing 500.
     */
    public static function save(?object $db, array $input): bool {
        if (!$db) {
            return false;
        }
        try {
            $stmt = $db->prepare(
                "INSERT INTO appointments (name, phone, service_type, birth_date, birth_time, birth_place, message, status)
                 VALUES (:name, :phone, 'kundali', :birth_date, :birth_time, :birth_place, :message, 'pending')"
            );
            $stmt->execute([
                ':name' => sanitize($input['name'] ?? ''),
                ':phone' => sanitize($input['phone'] ?? ''),
                ':birth_date' => $input['birth_date'] ?? null,
                ':birth_time' => $input['birth_time'] ?? null,
                ':birth_place' => sanitize($input['birth_place'] ?? ''),
                ':message' => 'स्वचालित कुण्डली हेरेपछि परामर्श अनुरोध',
            ]);
            return true;
        } catch (Throwable $error) {
            error_log('Kundali inquiry save failed: ' . $error->getMessage());
            return false;
        }
    }
}
