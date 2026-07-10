<?php

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/cors.php';
require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../middleware/validate.php';

$method = $_SERVER['REQUEST_METHOD'];
$db = Database::getConnection();

switch ($method) {
    case 'GET':
        $phone = $_GET['phone'] ?? '';
        if (!$phone) {
            jsonError('फोन नम्बर आवश्यक छ');
        }
        $stmt = $db->prepare("
            SELECT id, reward_type, title_ne, title_en, description_ne, description_en, is_redeemed, expires_at, created_at
            FROM rewards
            WHERE user_phone = :phone
            ORDER BY created_at DESC
        ");
        $stmt->execute([':phone' => $phone]);
        $rewards = $stmt->fetchAll();

        jsonSuccess([
            'rewards' => $rewards,
            'active_count' => count(array_filter($rewards, fn($r) => !$r['is_redeemed'])),
        ]);
        break;

    case 'POST':
        $input = getJsonInput();
        $error = validateRequired($input, ['reward_id', 'user_phone']);
        if ($error) jsonError($error);

        $stmt = $db->prepare("
            UPDATE rewards
            SET is_redeemed = TRUE
            WHERE id = :id AND user_phone = :phone AND is_redeemed = FALSE
        ");
        $stmt->execute([
            ':id' => (int)$input['reward_id'],
            ':phone' => sanitize($input['user_phone']),
        ]);

        if ($stmt->rowCount() > 0) {
            jsonSuccess([], 'पुरस्कार प्रयोग गरियो');
        } else {
            jsonError('पुरस्कार फेला परेन वा पहिले नै प्रयोग भइसकेको छ');
        }
        break;

    default:
        jsonError('Method not allowed', 405);
}
