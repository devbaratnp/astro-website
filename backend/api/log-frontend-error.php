<?php

require_once __DIR__ . '/../includes/error-handler.php';

$input = json_decode(file_get_contents('php://input'), true);
if (!$input || empty($input['message'])) {
    http_response_code(400);
    exit;
}

$logDir = dirname(__DIR__, 2) . '/logs';
if (!is_dir($logDir)) {
    @mkdir($logDir, 0755, true);
}

$log = sprintf(
    "[%s] Frontend Error: %s\nURL: %s\nUA: %s\nStack: %s\nComponent: %s\n---\n",
    date('Y-m-d H:i:s'),
    $input['message'],
    $input['url'] ?? 'unknown',
    $input['userAgent'] ?? 'unknown',
    $input['stack'] ?? 'none',
    $input['componentStack'] ?? 'none'
);

file_put_contents($logDir . '/frontend-errors.log', $log, FILE_APPEND);

http_response_code(200);
