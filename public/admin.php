<?php
// public/admin.php
require_once __DIR__ . '/../includes/auth.php';
require_role('admin'); // → refuse l’accès aux non-admins (403/redirect selon ton helper)
require_once __DIR__ . '/../includes/header.php';
?>
<main>
  <h2>Administration</h2>
  <p>Contenu réservé aux admins.</p>
</main>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
