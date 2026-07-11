<?php

function jsonResponse(mixed $data, int $status = 200): void {
    http_response_code($status);
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

function jsonError(string $message, int $status = 400): void {
    jsonResponse(['success' => false, 'message' => $message], $status);
}

function jsonSuccess(mixed $data, string $message = 'OK'): void {
    jsonResponse(['success' => true, 'message' => $message, 'data' => $data]);
}

function getJsonInput(): array {
    $input = file_get_contents('php://input');
    return json_decode($input, true) ?: [];
}

function sanitize(string $value): string {
    return htmlspecialchars(strip_tags(trim($value)), ENT_QUOTES, 'UTF-8');
}

function generateCsrfToken(): string {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    if (empty($_SESSION['_csrf_token'])) {
        $_SESSION['_csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['_csrf_token'];
}

function validateCsrf(): void {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') return;
    $token = $_POST['_csrf'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    if (!hash_equals($_SESSION['_csrf_token'] ?? '', $token)) {
        http_response_code(419);
        if (str_contains($_SERVER['REQUEST_URI'] ?? '', '/api/')) {
            jsonError('CSRF token mismatch', 419);
        }
        die('CSRF token mismatch — please refresh and try again.');
    }
}

function csrfField(): string {
    return '<input type="hidden" name="_csrf" value="' . generateCsrfToken() . '">';
}

function csrfQuery(): string {
    return '_csrf=' . generateCsrfToken();
}

function validateCsrfGet(): void {
    $token = $_GET['_csrf'] ?? '';
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    if (!hash_equals($_SESSION['_csrf_token'] ?? '', $token)) {
        http_response_code(419);
        die('CSRF token mismatch.');
    }
}
