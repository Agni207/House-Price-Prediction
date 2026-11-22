<?php
/**
 * includes/db.php
 * PDO database connection for HousePrice AI
 */
define('DB_HOST', '127.0.0.1');
define('DB_NAME', 'house_price_db');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHAR', 'utf8mb4');

function getDB(): PDO {
    static $pdo = null;
    if ($pdo === null) {
        $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHAR;
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];
        try {
            $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            $msg = 'Database connection failed. Start MySQL in XAMPP and import database/house_price_db.sql.';
            if (!empty($_SERVER['HTTP_ACCEPT']) && str_contains($_SERVER['HTTP_ACCEPT'], 'application/json')) {
                die(json_encode(['success' => false, 'error' => $msg]));
            }
            die($msg);
        }
    }
    return $pdo;
}
