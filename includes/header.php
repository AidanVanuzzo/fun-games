<?php
// includes/header.php

require_once __DIR__ . '/../src/config/translations.php';
require_once __DIR__ . '/auth.php';

// Langue par défaut
$language = 'fr';

// Si un cookie existe, on l'utilise
if (isset($_COOKIE['language']) && array_key_exists($_COOKIE['language'], $translations)) {
    $language = $_COOKIE['language'];
}

// Si l'utilisateur change de langue via l'URL (ex: ?lang=en)
if (isset($_GET['lang']) && array_key_exists($_GET['lang'], $translations)) {
    $language = $_GET['lang'];
    // Le cookie dure 30 jours
    setcookie('language', $language, time() + (30 * 24 * 60 * 60), "/");
}
?>
<!DOCTYPE html>
<html lang="<?= $language ?>">
<head>
    <meta charset="UTF-8">
    <title><?= $translations[$language]['title'] ?></title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>
    <header class="header">
        <div class="header-left">
            <a href="index.php"><img src="../assets/LOGO.png" alt="Logo Salle de Jeux" class="logo"></a>
            <a href="index.php"><h1><?= $translations[$language]['title'] ?></h1></a>
        </div>

        <nav class="nav-links">
            <?php if (me()): ?>
                <a href="reservations_list.php"><?= $translations[$language]['bookings'] ?></a>
                <a href="account.php"><?= $translations[$language]['account_title'] ?></a>

            <?php if (($_SESSION['role'] ?? 'user') === 'admin'): ?>
                <a href="admin.php">Admin</a>
            <?php endif; ?>

                <a href="logout.php"><?= $translations[$language]['logout'] ?></a>
            <?php else: ?>
                <a href="login.php"><?= $translations[$language]['login'] ?></a>
                <a href="register.php"><?= $translations[$language]['register'] ?></a>
            <?php endif; ?>

            <div class="lang-switch">
                <a href="?lang=fr" class="<?= $language == 'fr' ? 'active' : '' ?>">🇫🇷</a>
                <a href="?lang=en" class="<?= $language == 'en' ? 'active' : '' ?>">🇬🇧</a>
            </div>
        </nav>
    </header>
