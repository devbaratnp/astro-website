<?php

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/cors.php';
require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../middleware/validate.php';

$method = $_SERVER['REQUEST_METHOD'];
$db = Database::getConnection();

switch ($method) {
    case 'GET':
        $stmt = $db->query("SELECT id, title_ne, title_en, description_ne, description_en, base_price, duration_minutes, materials_available FROM pooja_services WHERE is_active = 1 ORDER BY category");
        $services = $stmt->fetchAll();
        jsonSuccess($services);
        break;

    case 'POST':
        $input = getJsonInput();
        $error = validateRequired($input, ['service_id', 'name', 'phone', 'preferred_date']);
        if ($error) jsonError($error);

        $stmt = $db->prepare("
            INSERT INTO pooja_bookings (service_id, name, phone, email, preferred_date, preferred_time, address, special_instructions, needs_materials, is_live_stream, status)
            VALUES (:service_id, :name, :phone, :email, :preferred_date, :preferred_time, :address, :instructions, :needs_materials, :is_live_stream, 'pending')
        ");
        $stmt->execute([
            ':service_id' => $input['service_id'],
            ':name' => sanitize($input['name']),
            ':phone' => sanitize($input['phone']),
            ':email' => sanitize($input['email'] ?? ''),
            ':preferred_date' => $input['preferred_date'],
            ':preferred_time' => $input['preferred_time'] ?? null,
            ':address' => sanitize($input['address'] ?? ''),
            ':instructions' => sanitize($input['special_instructions'] ?? ''),
            ':needs_materials' => !empty($input['needs_materials']) ? 1 : 0,
            ':is_live_stream' => !empty($input['is_live_stream']) ? 1 : 0,
        ]);

        $bookingId = $db->lastInsertId();
        jsonSuccess(['id' => $bookingId], 'पूजा बुकिङ सफल भयो। हामी चाँडै सम्पर्क गर्नेछौं।');
        break;

    default:
        jsonError('Method not allowed', 405);
}
