<?php
// config.php - central configuration for database connection and shared settings

define('DB_HOST', 'localhost');
define('DB_NAME', 'biblioteca');
define('DB_USER', 'root');
define('DB_PASS', '');

date_default_timezone_set('America/Mexico_City');

function getPDO(): PDO
{
    static $pdo = null;

    if ($pdo === null) {
        $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4';
        try {
            $pdo = new PDO($dsn, DB_USER, DB_PASS, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]);
        } catch (PDOException $e) {
            die('Database connection failed: ' . htmlspecialchars($e->getMessage())) ;
        }
    }

    return $pdo;
}

function redirect(string $path): void
{
    header('Location: ' . $path);
    exit;
}
