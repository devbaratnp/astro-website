<?php

function validateRequired(array $data, array $fields): ?string {
    foreach ($fields as $field) {
        if (!isset($data[$field]) || trim((string)$data[$field]) === '') {
            return "{$field} is required";
        }
    }
    return null;
}

function validatePhone(string $phone): bool {
    return (bool)preg_match('/^\+?[0-9]{7,15}$/', $phone);
}

function validateEmail(string $email): bool {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}
