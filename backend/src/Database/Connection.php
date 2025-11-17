<?php

declare(strict_types=1);

namespace App\Database;

use PDO;
use PDOException;

class Connection
{
    private static ?PDO $instance = null;

    public static function getInstance(): PDO
    {
        if (self::$instance === null) {
            $host = getenv('DB_HOST') ?: 'mysql';
            $port = getenv('DB_PORT') ?: '3306';
            $db   = getenv('DB_NAME') ?: 'scandiweb';
            $user = getenv('DB_USER') ?: 'scandi';
            $pass = getenv('DB_PASS') ?: 'scandi';
            $charset = 'utf8mb4';

            $dsn = "mysql:host={$host};port={$port};dbname={$db};charset={$charset}";

            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ];

            $caPath = __DIR__ . '/../../certs/aiven-ca.pem';
            if (file_exists($caPath)) {
                $options[PDO::MYSQL_ATTR_SSL_CA] = $caPath;
                if (defined('PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT')) {
                    $options[PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT] = false;
                }
            }

            try {
                self::$instance = new PDO($dsn, $user, $pass, $options);
            } catch (PDOException $e) {
                error_log('DB connection error: ' . $e->getMessage());

                http_response_code(500);
                header('Content-Type: application/json');
                echo json_encode(['error' => 'DB connection failed']);
                exit;
            }
        }

        return self::$instance;
    }
}
