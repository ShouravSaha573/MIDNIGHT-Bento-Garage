<?php
declare(strict_types=1);

date_default_timezone_set('Asia/Dhaka');

function databaseConfig(): array
{
    $config = [];
    $localFile = __DIR__ . '/database.local.php';

    if (is_file($localFile)) {
        $loaded = require $localFile;
        if (is_array($loaded)) {
            $config = $loaded;
        }
    }

    return [
        'host' => $config['host'] ?? (getenv('DB_HOST') ?: 'localhost'),
        'port' => $config['port'] ?? (getenv('DB_PORT') ?: '3306'),
        'database' => $config['database'] ?? (getenv('DB_NAME') ?: 'midnight_bento_garage'),
        'username' => $config['username'] ?? (getenv('DB_USER') ?: 'root'),
        'password' => $config['password'] ?? (getenv('DB_PASS') ?: ''),
    ];
}

function connectDatabase(): PDO
{
    $config = databaseConfig();

    $dsn = sprintf(
        'mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4',
        $config['host'],
        $config['port'],
        $config['database']
    );

    return new PDO($dsn, $config['username'], $config['password'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
}
