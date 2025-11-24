<?php

session_start();

$userId = $_SESSION['user_id'] ?? null;

if (!$userId) {
    // Redirige vers la page de connexion si l'utilisateur n'est pas authentifié
    header('Location: login.php');
    exit();
}

$sql = "SELECT email, nom FROM users WHERE id = :user_id";

// On prépare la requête SQL
$stmt = $this->database->getPdo()->prepare($sql);

// On lie le paramètre
$stmt->bindValue(':user_id', $userId);

// On exécute la requête SQL
$stmt->execute();

// On récupère le résultat comme tableau associatif
$email = $stmt->fetch();

require_once __DIR__ . '/../src/utils/autoloader.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

const MAIL_CONFIGURATION_FILE = __DIR__ . '/../src/config/mail.ini';

$config = parse_ini_file(MAIL_CONFIGURATION_FILE, true);

if (!$config) {
    throw new Exception("Erreur lors de la lecture du fichier de configuration : " .
        MAIL_CONFIGURATION_FILE);
}

$host = $config['host'];
$port = filter_var($config['port'], FILTER_VALIDATE_INT);
$authentication = filter_var($config['authentication'], FILTER_VALIDATE_BOOLEAN);
$username = $config['username'];
$password = $config['password'];
$from_email = $config['from_email'];
$from_name = $config['from_name'];

$mail = new PHPMailer(true);

require_once __DIR__ . '/../includes/header.php';

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

// Connexion PDO
$pdo = new PDO("mysql:host=$host;port=$port;charset=utf8mb4", $username, $password, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
]);

// Création de la base
$sql = "CREATE DATABASE IF NOT EXISTS `$database` CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;";
$stmt = $pdo->prepare($sql);
$stmt->execute();

$sql = "USE `$database`;";
$stmt = $pdo->prepare($sql);
$stmt->execute();

// Création de la table 'inscription'
$sql = "CREATE TABLE IF NOT EXISTS inscription (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    activity VARCHAR(100) NOT NULL,
    date_of VARCHAR(100) NOT NULL,
    time_of VARCHAR(100) NOT NULL,
    participant_number INT NOT NULL,
    group_name VARCHAR(25)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
$stmt = $pdo->prepare($sql);
$stmt->execute();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $date = $_POST["date"];
    $time = $_POST["time"];
    $number = $_POST["number"];
    $name = $_POST["name"];

    echo "<p>" . ($translations[$language]['form_received'] ?? 'Les données du formulaire ont été reçues :') . "</p>";
    echo "<ul>";
    echo "<li>" . ($translations[$language]['form_date'] ?? 'Date') . " : $date</li>";
    echo "<li>" . ($translations[$language]['form_time'] ?? 'Heure de début') . " : $time</li>";
    echo "<li>" . ($translations[$language]['form_number'] ?? 'Nombre de participants') . " : $number</li>";
    echo "<li>" . ($translations[$language]['form_group'] ?? 'Nom du groupe') . " : $name</li>";
    echo "</ul>";

    if (empty($date)) {
        $errors[] = $translations[$language]['error_date'] ?? "La date doit être présente";
    }

    if (empty($time)) {
        $errors[] = $translations[$language]['error_time'] ?? "L'heure de début doit être présente";
    }

    if (empty($number) || $number < 4 || $number > 8) {
        $errors[] = $translations[$language]['error_number'] ?? "Le nombre de participants doit être entre 4 et 8";
    }

    if (!empty($name) && strlen($name) < 2 || !empty($name) && strlen($name) > 25) {
        $errors[] = $translations[$language]['error_group'] ?? "Le nom du groupe doit être entre 2 et 25 caractères";
    }

    if (empty($errors)) {
        try {
            $sql = "INSERT INTO inscription (user_id, activity, date_of, time_of, participant_number, group_name)
                    VALUES (:user_id, 'LG', :date_of, :time_of, :participant_number, :group_name)";
            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(':user_id', $userId);
            $stmt->bindValue(':date_of', $date);
            $stmt->bindValue(':time_of', $time);
            $stmt->bindValue(':participant_number', $number);
            $stmt->bindValue(':group_name', $name);
            $stmt->execute();

            $mail->isSMTP();
            $mail->Host = $host;
            $mail->Port = $port;
            $mail->SMTPAuth = $authentication;
            $mail->Username = $username;
            $mail->Password = $password;
            $mail->CharSet = "UTF-8";
            $mail->Encoding = "base64";

            // Expéditeur et destinataire
            $mail->setFrom($from_email, $from_name);
            $mail->addAddress($email['email'], $email['nom']);

            // Contenu du mail
            $mail->isHTML(true);
            $mail->Subject = 'Inscription LASER GAME';
            $mail->Body    = "<b>Vous vous êtes bel et bien inscrits à l'activité laser game</b>";
            $mail->AltBody = "Vous vous êtes bel et bien inscrits à l'activité laser game";

            $mail->send();
        } catch (PDOException $e) {
            // if ($e->getCode() === "23000") {
            //     $errors[] = $translations[$language]['error_email'] ?? "L'adresse e-mail est déjà utilisée.";
            // } else {
            //     $errors[] = ($translations[$language]['error_db'] ?? "Erreur lors de l'interaction avec la base de données : ") . $e->getMessage();
            // }
        } catch (Exception $e) {
            $errors[] = ($translations[$language]['error_unexpected'] ?? "Erreur inattendue : ") . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="color-scheme" content="light dark">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@picocss/pico@2/css/pico.min.css">
    <title><?= $translations[$language]['form_title_lg'] ?? "S'inscrire pour l'activité LASER GAME" ?></title>
</head>

<body>
    <main class="container">
        <h1><?= $translations[$language]['form_create'] ?? "Créer un nouveau rendez-vous" ?></h1>

        <?php if ($_SERVER["REQUEST_METHOD"] === "POST") { ?>
            <?php if (empty($errors)) { ?>
                <p style="color: green;"><?= $translations[$language]['form_success'] ?? 'Le formulaire a été soumis avec succès !' ?></p>
            <?php } else { ?>
                <p style="color: red;"><?= $translations[$language]['form_error'] ?? 'Le formulaire contient des erreurs :' ?></p>
                <ul>
                    <?php foreach ($errors as $error) { ?>
                        <li><?= htmlspecialchars($error) ?></li>
                    <?php } ?>
                </ul>
            <?php } ?>
        <?php } ?>

        <form action="Inscription.php" method="POST">
            <label for="date"><?= $translations[$language]['form_label_date'] ?? 'Date du fun' ?></label>
            <input type="date" id="date" name="date" value="<?= htmlspecialchars($date ?? '') ?>" required min="2025-09-30">

            <label for="time"><?= $translations[$language]['form_label_time'] ?? 'Heure de début du fun' ?></label>
            <select id="time" name="time" required>
                <option value="10h" <?= (isset($time) && $time == "10h") ? "selected" : "" ?>>10h</option>
                <option value="11h" <?= (isset($time) && $time == "11h") ? "selected" : "" ?>>11h</option>
                <option value="12h" <?= (isset($time) && $time == "12h") ? "selected" : "" ?>>12h</option>
                <option value="13h" <?= (isset($time) && $time == "13h") ? "selected" : "" ?>>13h</option>
                <option value="14h" <?= (isset($time) && $time == "14h") ? "selected" : "" ?>>14h</option>
                <option value="15h" <?= (isset($time) && $time == "15h") ? "selected" : "" ?>>15h</option>
                <option value="16h" <?= (isset($time) && $time == "16h") ? "selected" : "" ?>>16h</option>
                <option value="17h" <?= (isset($time) && $time == "17h") ? "selected" : "" ?>>17h</option>
                <option value="18h" <?= (isset($time) && $time == "18h") ? "selected" : "" ?>>18h</option>
                <option value="19h" <?= (isset($time) && $time == "19h") ? "selected" : "" ?>>19h</option>
            </select>

            <label for="number"><?= $translations[$language]['form_label_number'] ?? 'Nombre de participants au fun (min : 4, max : 8)' ?></label>
            <input type="number" id="number" name="number" value="<?= htmlspecialchars($number ?? '') ?>" required min="4" max="8" />

            <label for="name"><?= $translations[$language]['form_label_group'] ?? 'Nom du groupe' ?></label>
            <input type="text" id="name" name="name" value="<?= htmlspecialchars($name ?? '') ?>" minlength="2" maxlength="25" />

            <button type="submit"><?= $translations[$language]['form_submit'] ?? 'Créer' ?></button>
        </form>
    </main>
</body>

</html>