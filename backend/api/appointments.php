<?php

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/cors.php';
require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../middleware/validate.php';

$method = $_SERVER['REQUEST_METHOD'];
$db = Database::getConnection();

switch ($method) {
    case 'POST':
        $input = getJsonInput();

        $error = validateRequired($input, ['name', 'phone', 'service_type', 'message']);
        if ($error) {
            jsonError($error);
        }

        $stmt = $db->prepare("
            INSERT INTO appointments (name, phone, email, service_type, preferred_date, preferred_time, consultation_mode, birth_date, birth_time, birth_place, message, status)
            VALUES (:name, :phone, :email, :service_type, :preferred_date, :preferred_time, :consultation_mode, :birth_date, :birth_time, :birth_place, :message, 'pending')
        ");

        $stmt->execute([
            ':name' => sanitize($input['name']),
            ':phone' => sanitize($input['phone']),
            ':email' => sanitize($input['email'] ?? ''),
            ':service_type' => sanitize($input['service_type']),
            ':preferred_date' => $input['preferred_date'] ?? null,
            ':preferred_time' => $input['preferred_time'] ?? null,
            ':consultation_mode' => $input['consultation_mode'] ?? 'whatsapp',
            ':birth_date' => $input['birth_date'] ?? null,
            ':birth_time' => $input['birth_time'] ?? null,
            ':birth_place' => sanitize($input['birth_place'] ?? ''),
            ':message' => sanitize($input['message']),
        ]);

        $appointmentId = $db->lastInsertId();

        jsonSuccess(['id' => $appointmentId], 'तपाईंको अनुरोध सफलतापूर्वक प्राप्त भयो। हामी चाँडै सम्पर्क गर्नेछौं।');
        break;

    case 'GET':
        $date = $_GET['date'] ?? date('Y-m-d');
        $stmt = $db->prepare("
            SELECT preferred_time
            FROM appointments
            WHERE preferred_date = :date AND status != 'cancelled'
        ");
        $stmt->execute([':date' => $date]);
        $booked = $stmt->fetchAll(PDO::FETCH_COLUMN);

        $allSlots = ['09:00', '10:00', '11:00', '12:00', '14:00', '15:00', '16:00', '17:00', '18:00', '19:00'];
        $available = array_values(array_diff($allSlots, $booked));

        jsonSuccess(['date' => $date, 'available_slots' => $available, 'booked_slots' => $booked]);
        break;

    default:
        jsonError('Method not allowed', 405);
}
