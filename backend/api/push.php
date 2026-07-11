<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/cors.php';
require_once __DIR__ . '/../includes/helpers.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST') jsonError('Method not allowed',405);
$input=getJsonInput(); $subscription=$input['subscription']??[];
if (empty($subscription['endpoint'])||empty($subscription['keys']['p256dh'])||empty($subscription['keys']['auth'])) jsonError('Invalid subscription');
$db=Database::getConnection();
$stmt=$db->prepare('INSERT INTO push_subscriptions(endpoint,p256dh,auth,language) VALUES(:endpoint,:p256dh,:auth,:language) ON DUPLICATE KEY UPDATE p256dh=VALUES(p256dh),auth=VALUES(auth),language=VALUES(language)');
$stmt->execute([':endpoint'=>$subscription['endpoint'],':p256dh'=>$subscription['keys']['p256dh'],':auth'=>$subscription['keys']['auth'],':language'=>in_array($input['language']??'ne',['ne','en'],true)?$input['language']:'ne']);
jsonSuccess(null,'Notifications enabled');
