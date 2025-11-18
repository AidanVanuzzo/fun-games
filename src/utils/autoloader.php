<?php
// src/utils/autoloader.php

spl_autoload_register(function (string $class) {
    // On ne gère que les classes du namespace PHPMailer\PHPMailer
    $prefix = 'PHPMailer\\PHPMailer\\';
    $baseDir = __DIR__ . '/../classes/PHPMailer/PHPMailer/';

    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        // Si la classe ne commence pas par ce préfixe, on ignore
        return;
    }

    // Nom de la classe sans le namespace
    $relativeClass = substr($class, $len);

    // Remplace les "\" par "/" et ajoute ".php"
    $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';

    if (file_exists($file)) {
        require $file;
    }
});
