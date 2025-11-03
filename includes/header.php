<!DOCTYPE html>

<?php require_once __DIR__ . '/auth.php'; ?>

<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>LS BOWL</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <header>
  
    <div class="header-left">
        <img src="assets/LOGO.png" alt="Logo Salle de Jeux" class="logo">

        <h1>SALLE DE JEUX</h1>
    </div>

    <?php if (me()): ?>
  <nav>
    <a href="reservations_list.php">Mes inscriptions</a>
    <a href="account.php">Mon compte</a>
    <a href="logout.php">Se déconnecter</a>
  </nav>
<?php else: ?>
  <nav>
    <a href="login.php">Se connecter</a>
    <a href="register.php">Créer un compte</a>
  </nav>
<?php endif; ?>


    </header>
