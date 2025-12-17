<?php
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/db.php';

if (session_status() === PHP_SESSION_NONE) session_start();

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = $translations[$language]['login_invalid_email'] ?? "Adresse e-mail invalide.";
    }

    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("SELECT id, pass, role FROM users WHERE email = ? LIMIT 1");
            $stmt->execute([$email]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($row && !empty($row['pass']) && password_verify($password, $row['pass'])) {
                session_regenerate_id(true);           // bonne pratique
                $_SESSION['user_id'] = (int)$row['id'];
                $_SESSION['role']    = $row['role'];   // <<--- essentiel pour l’autorisation
                header('Location: index.php');
                exit;

           } else {
                $errors[] = $translations[$language]['login_wrong_credentials'] ?? "Identifiants incorrects.";
            }
        } catch (PDOException $e) {
            $errors[] = ($translations[$language]['login_error'] ?? "Erreur : ") . $e->getMessage();
        }
    }
}
?>
<main>
  <h2><?= $translations[$language]['login_title'] ?? 'Connexion' ?></h2>
  <?php if (!empty($errors)): ?>
    <div class="error">
      <?php foreach ($errors as $err): ?>
        <p>❌ <?= htmlspecialchars($err) ?></p>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
  <form method="POST" action="">
    <label><?= $translations[$language]['login_email'] ?? 'Email :' ?></label>
    <input type="email" name="email" required value="<?= htmlspecialchars($email ?? '') ?>">

    <label><?= $translations[$language]['login_password'] ?? 'Mot de passe :' ?></label>
    <input type="password" name="password" required>

    <input type="submit" value="<?= $translations[$language]['login_button'] ?? 'Se connecter' ?>" class="btn">
  </form>
</main>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
