<?php
$secret = 'ds2hmb9tiejy68up50vq1nxkozrcw7fg';

$sig = $_SERVER['HTTP_X_HUB_SIGNATURE_256'] ?? '';
$payload = file_get_contents('php://input');

[$algo, $hash] = explode('=', $sig, 2) + [null, null];

if ($hash !== hash_hmac('sha256', $payload, $secret)) {
    http_response_code(401);
    die(json_encode(['ok' => false, 'error' => 'bad signature']));
}

$home = posix_getpwuid(posix_geteuid())['dir'] ?? '/home/ektamultp';
$log = shell_exec("export HOME=$home && cd $home/repositories/astro-website && bash deploy.sh 2>&1");

echo json_encode(['ok' => true, 'output' => $log]);
