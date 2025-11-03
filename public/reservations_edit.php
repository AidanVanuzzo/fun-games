<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_login();
require_once __DIR__ . '/../includes/header.php';

$id = (int)($_GET['id'] ?? 0);
$stmt = $pdo->prepare("SELECT * FROM inscription WHERE id = ? AND user_id = ?");
$stmt->execute([$id, $_SESSION['user_id']]);
$r = $stmt->fetch();

if (!$r) {
  echo "<main><p class='error'>Réservation introuvable.</p></main>";
  require_once __DIR__ . '/../includes/footer.php';
  exit;
}

$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $date = $_POST['date_of'] ?? $r['date_of'];
  $time = $_POST['time_of'] ?? $r['time_of'];
  $nb = (int)($_POST['participant_number'] ?? $r['participant_number']);
  $group = $_POST['group_name'] ?? $r['group_name'];

  $upd = $pdo->prepare("UPDATE inscription
                        SET date_of=?, time_of=?, participant_number=?, group_name=?
                        WHERE id=? AND user_id=?");
  $upd->execute([$date, $time, $nb, $group, $id, $_SESSION['user_id']]);
  $success = true;

  $stmt->execute([$id, $_SESSION['user_id']]);
  $r = $stmt->fetch();
}
?>
<main>
  <h2>Modifier la réservation</h2>
  <?php if ($success): ?><p class="success">Réservation modifiée ✅</p><?php endif; ?>

  <form method="POST">
    <label>Date</label>
    <input type="date" name="date_of" value="<?= htmlspecialchars($r['date_of']) ?>" required>

    <label>Heure</label>
    <input type="time" name="time_of" value="<?= htmlspecialchars($r['time_of']) ?>" required>

    <label>Nombre de joueurs</label>
    <input type="number" name="participant_number" value="<?= (int)$r['participant_number'] ?>" min="1" required>

    <label>Nom du groupe</label>
    <input type="text" name="group_name" value="<?= htmlspecialchars($r['group_name']) ?>">

    <button class="btn" type="submit">Enregistrer</button>
    <a class="btn" href="reservations_show.php?id=<?= (int)$r['id'] ?>">Annuler</a>
  </form>
</main>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
