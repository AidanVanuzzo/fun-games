<?php
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/db.php';

// Initialisation
$errors = [];
$success = false;

// Soumission du formulaire
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    // Validation basique
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Adresse e-mail invalide.";
    }
    if (strlen($password) < 4) {
        $errors[] = "Le mot de passe doit contenir au moins 4 caractères.";
    }

    // Si aucune erreur
    if (empty($errors)) {
        try {
            // Vérifie si l'email existe déjà
            $check = $pdo->prepare("SELECT id FROM users WHERE email = ?");
            $check->execute([$email]);
            if ($check->fetch()) {
                $errors[] = "Cet e-mail est déjà utilisé.";
            } else {
                // Hachage du mot de passe
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

                // Insertion dans la base
                $stmt = $pdo->prepare("
                    INSERT INTO users (email, pass, created_at)
                    VALUES (:email, :pass, NOW())
                ");
                $stmt->bindValue(':email', $email);
                $stmt->bindValue(':pass', password_hash($password, PASSWORD_DEFAULT));
                $stmt->execute();

                $success = true;
            }
        } catch (PDOException $e) {
            $errors[] = "Erreur: " . $e->getMessage();
        }
    }
}
?>

<main>
    <h2>Créer un compte</h2>

    <?php if ($success): ?>
        <p class="success">✅ Compte créé avec succès ! Vous pouvez maintenant <a href="login.php">vous connecter</a>.</p>
    <?php else: ?>
        <?php if (!empty($errors)): ?>
            <div class="error">
                <?php foreach ($errors as $err): ?>
                    <p>❌ <?= htmlspecialchars($err) ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <label for="email">Email :</label>
            <input type="email" name="email" required value="<?= htmlspecialchars($email ?? '') ?>">

            <label for="password">Mot de passe :</label>
            <input type="password" name="password" required>

            <input type="submit" value="S'inscrire" class="btn">
        </form>
    <?php endif; ?>
</main>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
