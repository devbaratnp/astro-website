<?php

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/cors.php';
require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../middleware/validate.php';
require_once __DIR__ . '/../lib/GoogleCalendar.php';
require_once __DIR__ . '/../lib/Mailer.php';

$method = $_SERVER['REQUEST_METHOD'];
$db = Database::getConnection();
$gcal = new GoogleCalendar();

switch ($method) {
    case 'POST':
        $input = getJsonInput();

        $error = validateRequired($input, ['name', 'phone', 'service_type', 'message']);
        if ($error) {
            jsonError($error);
        }
        if (!in_array($input['service_type'], ['kundali','marriage','grahadasha','vastu','pooja','general'], true)) jsonError('Invalid service type');
        if (!empty($input['consultation_mode']) && !in_array($input['consultation_mode'], ['phone','whatsapp','video','inperson'], true)) jsonError('Invalid consultation mode');

        $meetingUrl = null;
        $jitsiUrl = null;

        if ($input['consultation_mode'] === 'video') {
            $jitsiUrl = 'https://meet.jit.si/AstroShreeHari-' . bin2hex(random_bytes(8));
            $meetingUrl = $jitsiUrl;
        }

        $date = $input['preferred_date'] ?? date('Y-m-d');
        $startTime = $input['preferred_time'] ?? '10:00';
        $duration = GCAL_SLOT_DURATION;
        $startDt = new DateTime("{$date} {$startTime}", new DateTimeZone(TIMEZONE));
        $endDt = (clone $startDt)->modify("+{$duration} minutes");

        $description = "नाम: {$input['name']}\nफोन: {$input['phone']}\nइमेल: " . ($input['email'] ?? '') . "\nसेवा: {$input['service_type']}\nमाध्यम: {$input['consultation_mode']}\nप्रश्न: {$input['message']}";
        if ($jitsiUrl) {
            $description .= "\nभिडियो लिङ्क: {$jitsiUrl}";
        }

        $attendees = [['email' => ADMIN_EMAIL]];
        if (!empty($input['email'])) {
            $attendees[] = ['email' => $input['email']];
        }

        if ($gcal->isConfigured()) {
            $gcal->createEvent([
                'title' => "परामर्श: {$input['name']} - {$input['service_type']}",
                'description' => $description,
                'date' => $date,
                'start_time' => $startTime,
                'end_time' => $endDt->format('H:i'),
                'attendees' => $attendees,
            ]);
        }

        $stmt = $db->prepare("
            INSERT INTO appointments (name, phone, email, service_type, preferred_date, preferred_time, consultation_mode, meeting_url, birth_date, birth_time, birth_place, nwaran_name, father_name, mother_name, birth_order, birth_gender, message, status)
            VALUES (:name, :phone, :email, :service_type, :preferred_date, :preferred_time, :consultation_mode, :meeting_url, :birth_date, :birth_time, :birth_place, :nwaran_name, :father_name, :mother_name, :birth_order, :birth_gender, :message, 'pending')
        ");

        $stmt->execute([
            ':name' => sanitize($input['name']),
            ':phone' => sanitize($input['phone']),
            ':email' => sanitize($input['email'] ?? ''),
            ':service_type' => sanitize($input['service_type']),
            ':preferred_date' => $input['preferred_date'] ?? null,
            ':preferred_time' => $input['preferred_time'] ?? null,
            ':consultation_mode' => $input['consultation_mode'] ?? 'whatsapp',
            ':meeting_url' => $meetingUrl,
            ':birth_date' => $input['birth_date'] ?? null,
            ':birth_time' => $input['birth_time'] ?? null,
            ':birth_place' => sanitize($input['birth_place'] ?? ''),
            ':nwaran_name' => sanitize($input['nwaran_name'] ?? ''),
            ':father_name' => sanitize($input['father_name'] ?? ''),
            ':mother_name' => sanitize($input['mother_name'] ?? ''),
            ':birth_order' => sanitize($input['birth_order'] ?? ''),
            ':birth_gender' => sanitize($input['birth_gender'] ?? ''),
            ':message' => sanitize($input['message']),
        ]);

        $appointmentId = $db->lastInsertId();

        try {
            $mailer = new Mailer();
            $emailData = [
                'name' => $input['name'],
                'phone' => $input['phone'],
                'email' => $input['email'] ?? '',
                'service_type' => $input['service_type'],
                'preferred_date' => $input['preferred_date'] ?? '',
                'preferred_time' => $input['preferred_time'] ?? '',
                'consultation_mode' => $input['consultation_mode'] ?? 'whatsapp',
                'message' => $input['message'],
                'meeting_url' => $meetingUrl ?? '',
                'birth_date' => $input['birth_date'] ?? '',
                'birth_time' => $input['birth_time'] ?? '',
                'birth_place' => $input['birth_place'] ?? '',
            ];

            $adminSubject = '🔔 नयाँ परामर्श अनुरोध: ' . $input['name'] . ' - ' . $input['service_type'];
            $mailer->send(ADMIN_EMAIL, $adminSubject, Mailer::adminNotification($emailData));

            if (!empty($input['email'])) {
                $clientSubject = '🙏 तपाईंको परामर्श अनुरोध प्राप्त भयो - श्रीहरि ज्योतिष';
                $mailer->send($input['email'], $clientSubject, Mailer::clientConfirmation($emailData));
            }
        } catch (\Throwable $e) {
            error_log("Mailer error: " . $e->getMessage());
        }

        jsonSuccess(['id' => $appointmentId, 'meeting_url' => $meetingUrl], 'तपाईंको अनुरोध सफलतापूर्वक प्राप्त भयो। हामी चाँडै सम्पर्क गर्नेछौं।');
        break;

    case 'GET':
        $date = $_GET['date'] ?? date('Y-m-d');

        if ($gcal->isConfigured()) {
            $available = $gcal->getAvailableSlots($date);

            $stmt = $db->prepare("
                SELECT preferred_time
                FROM appointments
                WHERE preferred_date = :date AND status != 'cancelled'
            ");
            $stmt->execute([':date' => $date]);
            $bookedInDb = $stmt->fetchAll(PDO::FETCH_COLUMN);

            $available = array_values(array_diff($available, $bookedInDb));

            jsonSuccess([
                'date' => $date,
                'available_slots' => $available,
                'booked_slots' => $bookedInDb,
                'source' => 'google_calendar',
            ]);
        }

        $stmt = $db->prepare("
            SELECT preferred_time
            FROM appointments
            WHERE preferred_date = :date AND status != 'cancelled'
        ");
        $stmt->execute([':date' => $date]);
        $booked = $stmt->fetchAll(PDO::FETCH_COLUMN);

        $allSlots = ['09:00', '10:00', '11:00', '12:00', '14:00', '15:00', '16:00', '17:00', '18:00', '19:00'];
        $available = array_values(array_diff($allSlots, $booked));

        jsonSuccess(['date' => $date, 'available_slots' => $available, 'booked_slots' => $booked, 'source' => 'database']);
        break;

    default:
        jsonError('Method not allowed', 405);
}