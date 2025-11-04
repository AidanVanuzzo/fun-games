<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_login();
require_once __DIR__ . '/../includes/header.php';

$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

$updated = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $nom = $_POST['nom'] ?? $user['nom'];
  $tel = $_POST['telephone'] ?? $user['telephone'];
  $pass = $_POST['password'] ?? '';

  if ($pass !== '') {
    $hash = password_hash($pass, PASSWORD_DEFAULT);
    $upd = $pdo->prepare("UPDATE users SET nom=?, telephone=?, pass=? WHERE id=?");
    $upd->execute([$nom, $tel, $hash, $_SESSION['user_id']]);
  } else {
    $upd = $pdo->prepare("UPDATE users SET nom=?, telephone=? WHERE id=?");
    $upd->execute([$nom, $tel, $_SESSION['user_id']]);
  }
  $updated = true;
  $stmt->execute([$_SESSION['user_id']]);
  $user = $stmt->fetch();
}
?>
<main>
  <h2><?= $translations[$language]['account_title'] ?? 'Mon compte' ?></h2>
  <?php if ($updated): ?>
    <p class="success"><?= $translations[$language]['account_updated'] ?? 'Profil mis à jour ✅' ?></p>
  <?php endif; ?>

  <form method="POST">
    <label><?= $translations[$language]['account_name'] ?? 'Nom' ?></label>
    <input type="text" name="nom" value="<?= htmlspecialchars($user['nom'] ?? '') ?>">

    <label><?= $translations[$language]['account_phone'] ?? 'Téléphone' ?></label>
    <input type="text" name="telephone" value="<?= htmlspecialchars($user['telephone'] ?? '') ?>">

    <label><?= $translations[$language]['account_password'] ?? 'Nouveau mot de passe (laisser vide pour ne pas changer)' ?></label>
    <input type="password" name="password">

    <button class="btn" type="submit"><?= $translations[$language]['account_save'] ?? 'Enregistrer' ?></button>
  </form>
</main>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
