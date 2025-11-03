<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_login();
require_once __DIR__ . '/../includes/header.php';

$id = (int)($_GET['id'] ?? 0);

$stmt = $pdo->prepare("SELECT * FROM inscription WHERE id = ? AND user_id = ?");
$stmt->execute([$id, $_SESSION['user_id']]);
$r = $stmt->fetch();
?>
<main>
  <?php if (!$r): ?>
    <p class="error">Réservation introuvable.</p>
  <?php else: ?>
    <h2>Détail de la réservation</h2>
    <p><strong>Groupe :</strong> <?= htmlspecialchars($r['group_name']) ?></p>
    <p><strong>Date :</strong> <?= htmlspecialchars($r['date_of']) ?></p>
    <p><strong>Heure :</strong> <?= htmlspecialchars($r['time_of']) ?></p>
    <p><strong>Nombre de joueurs :</strong> <?= (int)$r['participant_number'] ?></p>

    <a class="btn" href="reservations_edit.php?id=<?= (int)$r['id'] ?>">Modifier</a>
    <a class="btn" href="reservations_list.php">Retour</a>
  <?php endif; ?>
</main>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
