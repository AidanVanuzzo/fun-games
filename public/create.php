<?php
// ---------- CONFIGURATION ----------
const DATABASE_CONFIGURATION_FILE = __DIR__ . '/../src/config/database.ini';

// Lecture du fichier de configuration
$config = parse_ini_file(DATABASE_CONFIGURATION_FILE, true);
if (!$config) {
    throw new Exception("Erreur lors de la lecture du fichier : " . DATABASE_CONFIGURATION_FILE);
}

$host = $config['host'];
$port = $config['port'];
$database = $config['database'];
$username = $config['username'];
$password = $config['password'];

// ---------- CONNEXION PDO ----------
$pdo = new PDO("mysql:host=$host;port=$port;charset=utf8mb4", $username, $password, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
]);

// ---------- CRÉATION DE LA BASE ----------
$sql = "CREATE DATABASE IF NOT EXISTS `$database` CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;";
$stmt = $pdo->prepare($sql);
$stmt->execute();

// Sélection de la base
$sql = "USE `$database`;";
$stmt = $pdo->prepare($sql);
$stmt->execute();

// ---------- TABLE USERS ----------
$sql = "
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL UNIQUE,
    pass VARCHAR(255) NOT NULL,
    nom VARCHAR(100),
    telephone VARCHAR(30),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
";
$pdo->exec($sql);

// ---------- TABLE INSCRIPTION ----------
$sql = "
CREATE TABLE IF NOT EXISTS inscription (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    date_of VARCHAR(100) NOT NULL,
    time_of VARCHAR(100) NOT NULL,
    participant_number INT NOT NULL,
    group_name VARCHAR(25),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
";
$pdo->exec($sql);

// ---------- AJOUT CLÉ ÉTRANGÈRE ----------
try {
    $pdo->exec("
        ALTER TABLE inscription
        ADD CONSTRAINT fk_inscription_user
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    ");
} catch (PDOException $e) {
    // La clé existe déjà, on ignore
}
