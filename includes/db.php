<?php
// includes/db.php
if (!isset($pdo)) {
    $config = parse_ini_file(__DIR__ . '/../src/config/database.ini', true);
    if (!$config) { throw new Exception("Impossible de lire database.ini"); }

    $host = $config['host'];
    $port = $config['port'];
    $database = $config['database'];
    $username = $config['username'];
    $password = $config['password'];

    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$database;charset=utf8mb4", $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);
}
