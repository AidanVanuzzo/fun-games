<?php
const DATABASE_CONFIGURATION_FILE = __DIR__ . '/database.ini';

// Documentation : https://www.php.net/manual/fr/function.parse-ini-file.php
$config = parse_ini_file(DATABASE_CONFIGURATION_FILE, true);

if (!$config) {
    throw new Exception("Erreur lors de la lecture du fichier de configuration : " . DATABASE_CONFIGURATION_FILE);
}

$host = $config['host'];
$port = $config['port'];
$database = $config['database'];
$username = $config['username'];
$password = $config['password'];

// Documentation :
//   - https://www.php.net/manual/fr/pdo.connections.php
//   - https://www.php.net/manual/fr/ref.pdo-mysql.connection.php
$pdo = new PDO("mysql:host=$host;port=$port;charset=utf8mb4", $username, $password);

// Création de la base de données si elle n'existe pas
$sql = "CREATE DATABASE IF NOT EXISTS `$database` CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;";
$stmt = $pdo->prepare($sql);
$stmt->execute();

// Sélection de la base de données
$sql = "USE `$database`;";
$stmt = $pdo->prepare($sql);
$stmt->execute();

// Création de la table 'inscription' si elle n'existe pas

$sql = "CREATE TABLE IF NOT EXISTS inscription (
    id INT AUTO_INCREMENT PRIMARY KEY,
    date_of VARCHAR(100) NOT NULL,
    time_of VARCHAR(100) NOT NULL,
    participant_number INT NOT NULL,
    group_name VARCHAR(25)
);";

$stmt = $pdo->prepare($sql);

$stmt->execute();

//gère la soumission du formulaire
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Récupération des données du formulaire
    $date = $_POST["date"];
    $time = $_POST["time"];
    $number = $_POST["number"];
    $name = $_POST["name"];

    echo "<p>Les données du formulaire ont été reçues :</p>";
    echo "<ul>";
    echo "<li>date : $date</li>";
    echo "<li>Heure de début : $time</li>";
    echo "<li>Nombre de participants : $number</li>";
    echo "<li>Nom du groupe : $name</li>";
    echo "</ul>";

    if (empty($date)) {
        $errors[] = "La date doit être présente";
    }

    if (empty($time)) {
        $errors[] = "L'heure de début doit être présente";
    }

    if (empty($number) || $number < 4 || $number > 8) {
        $errors[] = "Le nombre de participants doit être entre 4 et 8";
    }

    if (!empty($name) && strlen($name) < 2 || !empty($name) && strlen($name) > 25) {
        $errors[] = "Le nom doit groupe doit être entre 2 et 25 caractères";
    }

    //si pas d'erreurs, insertion dans la base de données
    if (empty($errors)) {
        try {
        //Définition de la requête SQL pour ajouter un rendez-vous
        $sql = "INSERT INTO inscription (
            date_of,
            time_of,
            participant_number,
            group_name
        ) VALUES (
            :date_of,
            :time_of,
            :participant_number,
            :group_name
        )";

        //Préparation de la requête SQL
        $stmt = $pdo->prepare($sql);

        //Lien avec les paramètres
        $stmt->bindValue(':date_of', $date);
        $stmt->bindValue(':time_of', $time);
        $stmt->bindValue(':participant_number', $number);
        $stmt->bindValue(':group_name', $name);

        // Exécution de la requête SQL pour ajouter un utilisateur
        $stmt->execute();

        // Redirection vers la page d'utilisateur avec tous les rendez-vous
        //header("Location: ---");
        //exit();
    } catch (PDOException $e) {
            // Liste des codes d'erreurs : https://en.wikipedia.org/wiki/SQLSTATE
            if ($e->getCode() === "23000") {
                // Erreur de contrainte d'unicité (par exemple, email déjà utilisé)
                $errors[] = "L'adresse e-mail est déjà utilisée.";
            } else {
                $errors[] = "Erreur lors de l'interaction avec la base de données : " . $e->getMessage();
            }
        } catch (Exception $e) {
            $errors[] = "Erreur inattendue : " . $e->getMessage();
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

    <title>S'inscrire pour l'activité</title>
</head>

<body>
    <main class="container">
        <h1>Créer un nouveau rendez-vous</h1>

        <?php if ($_SERVER["REQUEST_METHOD"] === "POST") { ?>
            <?php if (empty($errors)) { ?>
                <p style="color: green;">Le formulaire a été soumis avec succès !</p>
            <?php } else { ?>
                <p style="color: red;">Le formulaire contient des erreurs :</p>
                <ul>
                    <?php foreach ($errors as $error) { ?>
                        <li><?php echo $error; ?></li>
                    <?php } ?>
                </ul>
            <?php } ?>
        <?php } ?>

        <form action="Inscription.php" method="POST">
            <label for="date">Date du fun</label>
            <input type="date" id="date" name="date" value="<?= htmlspecialchars($date ?? '') ?>" required min="2025-09-30">

            <label for="time">Heure de début du fun</label>
            <select id="time" name="time" required>
                <option value="10h" <?php if (isset($time) && $time == "10h") echo "selected"; ?>>10h</option>
                <option value="11h" <?php if (isset($time) && $time == "11h") echo "selected"; ?>>11h</option>
                <option value="12h" <?php if (isset($time) && $time == "12h") echo "selected"; ?>>12h</option>
                <option value="13h" <?php if (isset($time) && $time == "13h") echo "selected"; ?>>13h</option>
                <option value="14h" <?php if (isset($time) && $time == "14h") echo "selected"; ?>>14h</option>
                <option value="15h" <?php if (isset($time) && $time == "15h") echo "selected"; ?>>15h</option>
                <option value="16h" <?php if (isset($time) && $time == "16h") echo "selected"; ?>>16h</option>
                <option value="17h" <?php if (isset($time) && $time == "17h") echo "selected"; ?>>17h</option>
                <option value="18h" <?php if (isset($time) && $time == "18h") echo "selected"; ?>>18h</option>
                <option value="19h" <?php if (isset($time) && $time == "19h") echo "selected"; ?>>19h</option>
            </select>

            <label for="number">Nombre de participants au fun (min : 4, max : 8)</label>
            <input type="number" id="number" name="number" value="<?= htmlspecialchars($number ?? '') ?>" required min="4" max="8" />

            <label for="name">Nom du groupe</label>
            <input type="text" id="name" name="name" value="<?= htmlspecialchars($name ?? '') ?>" minlength="2" maxlength="25" />

            <button type="submit">Créer</button>
        </form>
    </main>
</body>

</html>