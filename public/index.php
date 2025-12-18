<?php

session_start();

$userId = $_SESSION['user_id'] ?? null;

require_once __DIR__ . '/../includes/header.php';
?>

<main>
    <h2><?= $translations[$language]['welcome_title'] ?? 'Bienvenue à LSBOWL!' ?></h2>
    <p>
        <?= $translations[$language]['welcome_message'] ?? 'Réservez vos parties de LASER GAME et de BOWLING facilement. Pour ceci, veuillez créer un compte ou vous connecter.' ?>
    </p>

    <div class="buttons">
        <?php if (!$userId) { ?>
        <a href="login.php" class="btn"><?= $translations[$language]['login'] ?></a>
        <a href="register.php" class="btn"><?= $translations[$language]['register'] ?></a>
        <?php } ?>
        <a href="Inscription.php" class="btn"><?= $translations[$language]['inscription'] ?></a>
    </div>
</main>

<?php
require_once __DIR__ . '/../includes/footer.php';
?>
