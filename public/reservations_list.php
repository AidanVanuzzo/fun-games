<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_login();
require_once __DIR__ . '/../includes/header.php';

$stmt = $pdo->prepare("
  SELECT id, date_of, time_of, participant_number, group_name
  FROM inscription
  WHERE user_id = ?
  ORDER BY date_of DESC, time_of DESC
");
$stmt->execute([$_SESSION['user_id']]);
$reservations = $stmt->fetchAll();
?>
<main>
  <h2>Mes réservations</h2>

  <?php if (empty($reservations)): ?>
    <p>Aucune réservation pour le moment.</p>
  <?php else: ?>
    <?php foreach ($reservations as $r): ?>
      <div style="border:1px solid #333;border-radius:10px;padding:12px;margin-bottom:12px">
        <p><strong><?= htmlspecialchars($r['group_name'] ?: 'Sans nom') ?></strong></p>
        <p><?= htmlspecialchars($r['date_of']) ?> à <?= htmlspecialchars($r['time_of']) ?> — 
           <?= (int)$r['participant_number'] ?> joueurs</p>

        <a class="btn" href="reservations_show.php?id=<?= (int)$r['id'] ?>">Détails</a>
        <a class="btn" href="reservations_edit.php?id=<?= (int)$r['id'] ?>">Modifier</a>
      </div>
    <?php endforeach; ?>
  <?php endif; ?>
</main>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
