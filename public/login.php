<?php
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/db.php';

if (session_status() === PHP_SESSION_NONE) session_start();

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Adresse e-mail invalide.";
    }

    if (empty($errors)) {
        try {
            // ⬇️ on lit bien la colonne `pass`
            $stmt = $pdo->prepare("SELECT id, pass FROM users WHERE email = ? LIMIT 1");
            $stmt->execute([$email]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($row && !empty($row['pass']) && password_verify($password, $row['pass'])) {
                $_SESSION['user_id'] = (int)$row['id'];
                header('Location: account.php'); // ou reservations_list.php
                exit;
            } else {
                $errors[] = "Identifiants incorrects.";
            }
        } catch (PDOException $e) {
            $errors[] = "Erreur: " . $e->getMessage();
        }
    }
}
?>
<main>
  <h2>Connexion</h2>
  <?php if (!empty($errors)): ?>
    <div class="error"><?php foreach ($errors as $err): ?><p>❌ <?= htmlspecialchars($err) ?></p><?php endforeach; ?></div>
  <?php endif; ?>
  <form method="POST" action="">
    <label>Email :</label>
    <input type="email" name="email" required value="<?= htmlspecialchars($email ?? '') ?>">
    <label>Mot de passe :</label>
    <input type="password" name="password" required>
    <input type="submit" value="Se connecter" class="btn">
  </form>
</main>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
