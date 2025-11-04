<?php
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/db.php';

$errors = [];
$success = false;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $nom = trim($_POST['nom'] ?? '');
    $telephone = trim($_POST['telephone'] ?? '');

    // Email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = $translations[$language]['register_invalid_email'] ?? "Adresse e-mail invalide.";
    }
    // Password
    if (strlen($password) < 4) {
        $errors[] = $translations[$language]['register_short_password'] ?? "Le mot de passe doit contenir au moins 4 caractères.";
    }
    // Nom (2–100)
    if ($nom === '' || mb_strlen($nom) < 2 || mb_strlen($nom) > 100) {
        $errors[] = $translations[$language]['register_invalid_name'] ?? "Le nom doit faire entre 2 et 100 caractères.";
    }
    // Téléphone (facultatif) — chiffres, espaces, +, -, .
    if ($telephone !== '' && !preg_match('/^[0-9 +\-\.()]{6,30}$/', $telephone)) {
        $errors[] = $translations[$language]['register_invalid_phone'] ?? "Le numéro de téléphone n'est pas valide.";
    }

    if (empty($errors)) {
        try {
            // Email unique ?
            $check = $pdo->prepare("SELECT id FROM users WHERE email = ?");
            $check->execute([$email]);
            if ($check->fetch()) {
                $errors[] = $translations[$language]['register_email_used'] ?? "Cet e-mail est déjà utilisé.";
            } else {
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

                $stmt = $pdo->prepare("
                    INSERT INTO users (email, pass, nom, telephone, created_at)
                    VALUES (:email, :pass, :nom, :telephone, NOW())
                ");
                $stmt->execute([
                    ':email' => $email,
                    ':pass' => $hashedPassword,
                    ':nom' => $nom,
                    ':telephone' => ($telephone !== '' ? $telephone : null),
                ]);

                $success = true;
            }
        } catch (PDOException $e) {
            $errors[] = ($translations[$language]['register_error'] ?? "Erreur : ") . $e->getMessage();
        }
    }
}
?>


<main>
    <h2><?= $translations[$language]['register_title'] ?? 'Créer un compte' ?></h2>

    <?php if ($success): ?>
        <p class="success">
            ✅ <?= $translations[$language]['register_success'] ?? 'Compte créé avec succès ! Vous pouvez maintenant' ?>
            <a href="login.php"><?= $translations[$language]['register_login_link'] ?? 'vous connecter' ?></a>.
        </p>
    <?php else: ?>
        <?php if (!empty($errors)): ?>
            <div class="error">
                <?php foreach ($errors as $err): ?>
                    <p>❌ <?= htmlspecialchars($err) ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

       <form method="POST" action="">
    <label for="nom">Nom complet :</label>
    <input type="text" name="nom" id="nom" required minlength="2" maxlength="100"
           value="<?= htmlspecialchars($nom ?? '') ?>">

    <label for="telephone">Téléphone (facultatif) :</label>
    <input type="text" name="telephone" id="telephone" maxlength="30"
           placeholder="+41 79 123 45 67"
           value="<?= htmlspecialchars($telephone ?? '') ?>">

    <label for="email">Email :</label>
    <input type="email" name="email" id="email" required value="<?= htmlspecialchars($email ?? '') ?>">

    <label for="password">Mot de passe :</label>
    <input type="password" name="password" id="password" required minlength="4">

    <input type="submit" value="S'inscrire" class="btn">
</form>

    <?php endif; ?>
</main>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
