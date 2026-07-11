<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/cors.php';
require_once __DIR__ . '/../includes/helpers.php';

if (session_status() === PHP_SESSION_NONE) session_start();
if (empty($_SESSION['admin_id'])) jsonError('Unauthenticated', 401);

$db = Database::getConnection();
$resource = $_GET['resource'] ?? 'dashboard';
$input = in_array($_SERVER['REQUEST_METHOD'], ['POST','PUT','PATCH','DELETE'], true) ? getJsonInput() : [];

$definitions = [
    'services' => ['table'=>'pooja_services','fields'=>['title_ne','title_en','description_ne','description_en','category','base_price','duration_minutes','materials_available','is_active'],'required'=>['title_ne','title_en','category']],
    'articles' => ['table'=>'articles','fields'=>['title_ne','title_en','slug','content_ne','content_en','excerpt_ne','excerpt_en','cover_image','is_published'],'required'=>['title_ne','slug','content_ne']],
    'rewards' => ['table'=>'rewards','fields'=>['user_name','user_phone','reward_type','title_ne','title_en','description_ne','description_en','is_redeemed','expires_at'],'required'=>['user_name','user_phone','reward_type','title_ne']],
    'panchang' => ['table'=>'panchang','fields'=>['date','tithi','nakshatra','sunrise','sunset','rahu_kaal','auspicious_times','special_events_ne','special_events_en'],'required'=>['date']],
];

if (in_array($_SERVER['REQUEST_METHOD'], ['POST','PUT'], true)) {
    if (!isset($definitions[$resource])) jsonError('Unsupported resource', 400);
    $definition = $definitions[$resource];
    foreach ($definition['required'] as $field) if (!isset($input[$field]) || trim((string)$input[$field]) === '') jsonError("$field is required");
    if ($resource === 'services' && !in_array($input['category'], ['shanti','graha','sanskar','festival','other'], true)) jsonError('Invalid category');
    if ($resource === 'rewards' && !in_array($input['reward_type'], ['feature','discount','badge','service','other'], true)) jsonError('Invalid reward type');
    $values = [];
    foreach ($definition['fields'] as $field) if (array_key_exists($field, $input)) $values[$field] = $input[$field] === '' ? null : $input[$field];
    if ($resource === 'rewards' && $_SERVER['REQUEST_METHOD'] === 'POST') $values['awarded_by'] = $_SESSION['admin_id'];
    if ($resource === 'articles' && !empty($values['is_published'])) $values['published_at'] = date('Y-m-d H:i:s');
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $columns = array_keys($values); $params = array_map(fn($f) => ":$f", $columns);
        $sql = 'INSERT INTO ' . $definition['table'] . ' (`' . implode('`,`', $columns) . '`) VALUES (' . implode(',', $params) . ')';
    } else {
        if (empty($input['id'])) jsonError('id is required');
        $assignments = array_map(fn($f) => "`$f`=:$f", array_keys($values));
        $sql = 'UPDATE ' . $definition['table'] . ' SET ' . implode(',', $assignments) . ' WHERE id=:id';
        $values['id'] = (int)$input['id'];
    }
    $db->prepare($sql)->execute(array_combine(array_map(fn($k)=>":$k",array_keys($values)),array_values($values)));
    jsonSuccess(['id' => $_SERVER['REQUEST_METHOD']==='POST' ? $db->lastInsertId() : $input['id']], 'Saved');
}

if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    if (!isset($definitions[$resource]) || empty($input['id'])) jsonError('Invalid request');
    $db->prepare('DELETE FROM ' . $definitions[$resource]['table'] . ' WHERE id=:id')->execute([':id'=>(int)$input['id']]);
    jsonSuccess(null, 'Deleted');
}

if ($_SERVER['REQUEST_METHOD'] === 'PATCH') {
    if ($resource === 'settings') {
        if (strlen((string)($input['password'] ?? '')) < 8) jsonError('Password must be at least 8 characters');
        $db->prepare('UPDATE admin_users SET password_hash=:hash WHERE id=:id')->execute([':hash'=>password_hash($input['password'], PASSWORD_DEFAULT), ':id'=>$_SESSION['admin_id']]);
        jsonSuccess(null, 'Password updated');
    }
    $allowed = [
        'appointments' => ['table' => 'appointments', 'fields' => ['status', 'admin_notes']],
        'pooja' => ['table' => 'pooja_bookings', 'fields' => ['status']],
        'payments' => ['table' => 'payments', 'fields' => ['status', 'admin_notes']],
        'messages' => ['table' => 'contact_messages', 'fields' => ['is_read']],
        'rewards' => ['table' => 'rewards', 'fields' => ['is_redeemed']],
    ];
    if (!isset($allowed[$resource]) || empty($input['id'])) jsonError('Invalid request');
    $updates = []; $params = [':id' => (int)$input['id']];
    foreach ($allowed[$resource]['fields'] as $field) {
        if (array_key_exists($field, $input)) { $updates[] = "$field = :$field"; $params[":$field"] = $input[$field]; }
    }
    if (!$updates) jsonError('Nothing to update');
    $sql = 'UPDATE ' . $allowed[$resource]['table'] . ' SET ' . implode(', ', $updates) . ' WHERE id = :id';
    $db->prepare($sql)->execute($params);
    jsonSuccess(null, 'Updated');
}

if ($_SERVER['REQUEST_METHOD'] !== 'GET') jsonError('Method not allowed', 405);

if ($resource === 'settings') jsonSuccess(['display_name'=>$_SESSION['admin_name'],'role'=>$_SESSION['admin_role']]);

if ($resource === 'dashboard') {
    $counts = [];
    foreach ([
        'pending_appointments' => "SELECT COUNT(*) FROM appointments WHERE status='pending'",
        'pending_pooja' => "SELECT COUNT(*) FROM pooja_bookings WHERE status='pending'",
        'pending_payments' => "SELECT COUNT(*) FROM payments WHERE status='pending'",
        'unread_messages' => "SELECT COUNT(*) FROM contact_messages WHERE is_read=0",
        'active_services' => "SELECT COUNT(*) FROM pooja_services WHERE is_active=1",
        'total_articles' => 'SELECT COUNT(*) FROM articles',
    ] as $key => $sql) $counts[$key] = (int)$db->query($sql)->fetchColumn();
    jsonSuccess($counts);
}

$queries = [
    'appointments' => 'SELECT * FROM appointments ORDER BY created_at DESC LIMIT 100',
    'pooja' => 'SELECT b.*, s.title_ne AS service_name FROM pooja_bookings b LEFT JOIN pooja_services s ON s.id=b.service_id ORDER BY b.created_at DESC LIMIT 100',
    'payments' => 'SELECT * FROM payments ORDER BY created_at DESC LIMIT 100',
    'messages' => 'SELECT * FROM contact_messages ORDER BY created_at DESC LIMIT 100',
    'services' => 'SELECT * FROM pooja_services ORDER BY created_at DESC',
    'articles' => 'SELECT id,title_ne,title_en,slug,is_published,published_at,created_at FROM articles ORDER BY created_at DESC',
    'rewards' => 'SELECT * FROM rewards ORDER BY created_at DESC LIMIT 100',
    'panchang' => 'SELECT * FROM panchang ORDER BY date DESC LIMIT 100',
];
if (!isset($queries[$resource])) jsonError('Unknown resource', 404);
jsonSuccess($db->query($queries[$resource])->fetchAll());
