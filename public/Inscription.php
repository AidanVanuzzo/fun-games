<?php

session_start();

$userId = $_SESSION['user_id'] ?? null;

if (!$userId) {
    // Redirige vers la page de connexion si l'utilisateur n'est pas authentifié
    header('Location: login.php');
    exit();
}

require_once __DIR__ . '/../includes/header.php';

?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="color-scheme" content="light dark">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@picocss/pico@2/css/pico.min.css">
    <title><?= $translations[$language]['inscription_title'] ?? "Choix de l'activité" ?></title>
</head>

<body>
    <div class="buttons">
        <a href="InscriptionBowling.php" class="btn"><?= $translations[$language]['BOWLING'] ?></a>
        <a href="InscriptionLG.php" class="btn"><?= $translations[$language]['LASER GAME'] ?></a>
    </div>
</body>