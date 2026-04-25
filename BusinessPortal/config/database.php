<?php
/**
 * Database connection (PDO + MySQLi for legacy compat).
 *
 * Local dev:   uses inline defaults (XAMPP root/no-password).
 * Production:  loads credentials from `database.credentials.php`
 *              (gitignored), or falls back to env vars.
 */

declare(strict_types=1);

$_dbHost  = $_SERVER['HTTP_HOST'] ?? '';
$_isLocal = in_array($_dbHost, ['localhost', '127.0.0.1'], true)
         || str_starts_with($_dbHost, '192.168.')
         || PHP_SAPI === 'cli';

if ($_isLocal) {
    $dbConfig = [
        'host'     => 'localhost',
        'port'     => 3306,
        'dbname'   => 'jpilaw',
        'username' => 'root',
        'password' => '',
        'charset'  => 'utf8mb4',
    ];
} else {
    $credFile = __DIR__ . '/database.credentials.php';
    if (is_file($credFile)) {
        $dbConfig = require $credFile;
    } else {
        $dbConfig = [
            'host'     => getenv('DB_HOST')     ?: 'localhost',
            'port'     => (int)(getenv('DB_PORT') ?: 3306),
            'dbname'   => getenv('DB_NAME')     ?: '',
            'username' => getenv('DB_USER')     ?: '',
            'password' => getenv('DB_PASSWORD') ?: '',
            'charset'  => getenv('DB_CHARSET')  ?: 'utf8mb4',
        ];
    }
}

// ── PDO ──────────────────────────────────────────────────────────────────────
$dsn = sprintf(
    'mysql:host=%s;port=%d;dbname=%s;charset=%s',
    $dbConfig['host'],
    $dbConfig['port'],
    $dbConfig['dbname'],
    $dbConfig['charset']
);

try {
    $pdo = new PDO($dsn, $dbConfig['username'], $dbConfig['password'], [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    die('Database connection failed: ' . htmlspecialchars($e->getMessage()));
}

// ── MySQLi (legacy compatibility for existing inc/* files) ──────────────────
$conn = @new mysqli(
    $dbConfig['host'],
    $dbConfig['username'],
    $dbConfig['password'],
    $dbConfig['dbname'],
    $dbConfig['port']
);

if ($conn->connect_error) {
    http_response_code(500);
    die('MySQLi connection failed: ' . htmlspecialchars($conn->connect_error));
}
$conn->set_charset('utf8mb4');
