<?php
require_once("includes/header.php");

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

// Création de la table `users` si elle n'existe pas
$sql = "CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    pass VARCHAR(255) NOT NULL
);";

$stmt = $pdo->prepare($sql);

$stmt->execute();


?>

<main>
    <h2>Créer un compte</h2>
    <form method="POST" action="">
        <label>Nom :</label><br>
        <input type="text" name="nom" value="<?= htmlspecialchars($nom ?? '') ?>" required><br><br>

        <label>Email :</label><br>
        <input type="email" name="email" value="<?= htmlspecialchars($email ?? '') ?>" required><br><br>

        <label>Mot de passe :</label><br>
        <input type="password" name="password" value="<?= htmlspecialchars($password ?? '') ?>" required><br><br>

        <input type="submit" name="register" value="S'inscrire" class="btn">
    </form>

    <?php
    if (isset($_POST['register'])) {
        $nom = $_POST['nom'];
        $email = $_POST['email'];
        $password = $_POST['password'];
        try {
            // Définition de la requête SQL pour ajouter un utilisateur
            $sql = "INSERT INTO users (
                username,
                email,
                pass
            ) VALUES (
                :username,
                :email,
                :pass
            )";

            // Préparation de la requête SQL
            $stmt = $pdo->prepare($sql);

            // Lien avec les paramètres
            $stmt->bindValue(':email', $email);
            $stmt->bindValue(':pass', $password);
            $stmt->bindvalue(':username', $nom);

            // Exécution de la requête SQL pour ajouter un utilisateur
            $stmt->execute();

            // Redirection vers la page d'accueil avec tous les utilisateurs
            //header("Location: index.php");
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
        echo "<p class='success'>Bienvenue $nom ! Ton compte a été créé (simulation).</p>";
    }
    ?>
</main>

<?php
require_once("includes/footer.php");
?>