<?php

require_once __DIR__ . '/../includes/error-handler.php';

/**
 * Optional private credentials file for shared hosting. This file must not be
 * committed; see database.credentials.example.php.
 *
 * @var array{host?: string, name?: string, user?: string, pass?: string} $databaseCredentials
 */
$credentialsFile = __DIR__ . '/database.credentials.php';
$databaseCredentials = is_file($credentialsFile) ? require $credentialsFile : [];

class Database {
    private static ?PDO $instance = null;

    public static function getConnection(): PDO {
        if (self::$instance === null) {
            global $databaseCredentials;

            $host = getenv('DB_HOST') ?: ($databaseCredentials['host'] ?? 'localhost');
            $dbname = getenv('DB_NAME') ?: ($databaseCredentials['name'] ?? 'ektamultp_astro_hari');
            $username = getenv('DB_USER') ?: ($databaseCredentials['user'] ?? '');
            $password = getenv('DB_PASS') ?: ($databaseCredentials['pass'] ?? '');
            $charset = 'utf8mb4';

            if ($username === '' || $password === '') {
                throw new RuntimeException('Database credentials are not configured.');
            }

            self::$instance = new PDO(
                "mysql:host={$host};dbname={$dbname};charset={$charset}",
                $username,
                $password,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ]
            );
        }
        return self::$instance;
    }
}
