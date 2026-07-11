<?php

date_default_timezone_set('Asia/Kathmandu');

define('ERROR_LOG_DIR', __DIR__ . '/../../logs');
if (!is_dir(ERROR_LOG_DIR)) {
    @mkdir(ERROR_LOG_DIR, 0755, true);
}

set_error_handler(function (int $severity, string $message, string $file, int $line): bool {
    if (!(error_reporting() & $severity)) return false;
    $log = sprintf("[%s] %s: %s in %s:%d\n", date('Y-m-d H:i:s'), severityName($severity), $message, $file, $line);
    file_put_contents(ERROR_LOG_DIR . '/php-errors.log', $log, FILE_APPEND);
    if (isApiRequest()) {
        jsonResponse(['success' => false, 'message' => 'Internal server error'], 500);
    }
    return true;
});

set_exception_handler(function (Throwable $e): void {
    $log = sprintf("[%s] Uncaught %s: %s in %s:%d\n%s\n",
        date('Y-m-d H:i:s'), get_class($e), $e->getMessage(), $e->getFile(), $e->getLine(), $e->getTraceAsString());
    file_put_contents(ERROR_LOG_DIR . '/php-exceptions.log', $log, FILE_APPEND);
    if (isApiRequest()) {
        jsonResponse(['success' => false, 'message' => 'Internal server error'], 500);
    }
});

function severityName(int $severity): string {
    return match ($severity) {
        E_ERROR => 'Fatal Error',
        E_WARNING => 'Warning',
        E_PARSE => 'Parse Error',
        E_NOTICE => 'Notice',
        E_USER_ERROR => 'User Error',
        E_USER_WARNING => 'User Warning',
        E_USER_NOTICE => 'User Notice',
        E_DEPRECATED => 'Deprecated',
        default => "Unknown ($severity)",
    };
}

function isApiRequest(): bool {
    static $api = null;
    if ($api === null) {
        $path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
        $api = str_contains($path, '/api/') || (($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') === 'XMLHttpRequest');
    }
    return $api;
}
