<?php
// includes/db.php
const DATABASE_CONFIGURATION_FILE = __DIR__ . '/../src/config/database.ini';
$config = parse_ini_file(DATABASE_CONFIGURATION_FILE);

if (!$config) { die("Impossible de lire database.ini"); }

$host = $config['host'];
$port = $config['port'] ?? 3306;
$db   = $config['database'];
$user = $config['username'];
$pass = $config['password'];

$dsn = "mysql:host=$host;port=$port;dbname=$db;charset=utf8mb4";
try {
  $pdo = new PDO($dsn, $user, $pass, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
  ]);
} catch (PDOException $e) {
  die("Erreur PDO: " . $e->getMessage());
}

