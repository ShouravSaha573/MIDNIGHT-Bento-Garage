<?php
declare(strict_types=1);

header('Content-Type: text/plain; charset=utf-8');
require_once __DIR__ . '/config/database.php';

$localFile = __DIR__ . '/config/database.local.php';

echo "Midnight Bento Garage - Database Check\n";
echo "--------------------------------------\n";
echo 'database.local.php found: ' . (is_file($localFile) ? 'YES' : 'NO') . "\n";

$config = databaseConfig();
echo 'Using localhost: ' . (($config['host'] ?? '') === 'localhost' ? 'YES (wrong for InfinityFree)' : 'NO') . "\n";
echo 'Database name looks prefixed: ' . (str_starts_with((string)($config['database'] ?? ''), 'if0_') ? 'YES' : 'NO') . "\n";
echo 'Username looks prefixed: ' . (str_starts_with((string)($config['username'] ?? ''), 'if0_') ? 'YES' : 'NO') . "\n";

try {
    $pdo = connectDatabase();
    $pdo->query('SELECT 1');
    echo "Connection result: SUCCESS\n";

    $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
    echo 'Tables found: ' . count($tables) . "\n";
    echo 'Required tables present: ' . (
        in_array('users', $tables, true)
        && in_array('mechanics', $tables, true)
        && in_array('appointments', $tables, true)
        ? 'YES'
        : 'NO'
    ) . "\n";
} catch (PDOException $error) {
    echo "Connection result: FAILED\n";
    echo 'SQLSTATE: ' . $error->getCode() . "\n";

    $message = $error->getMessage();
    if (str_contains($message, 'Access denied')) {
        echo "Reason: Username or hosting-account password is incorrect.\n";
    } elseif (str_contains($message, 'Unknown database')) {
        echo "Reason: Full prefixed database name is incorrect.\n";
    } elseif (str_contains($message, 'getaddrinfo') || str_contains($message, 'Name or service not known')) {
        echo "Reason: MySQL hostname is incorrect.\n";
    } elseif (str_contains($message, 'Connection timed out') || str_contains($message, 'Connection refused')) {
        echo "Reason: MySQL hostname/server connection problem.\n";
    } else {
        echo "Reason: Database credentials or hosting database setup is incorrect.\n";
    }
}
