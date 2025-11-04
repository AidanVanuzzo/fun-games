<?php
// includes/auth.php

// 1) Paramètres du cookie de session : expire à la fermeture du navigateur
//    + sécurisation basique (HttpOnly, SameSite, Secure si HTTPS).
if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params([
        'lifetime' => 0,                 // <-- expire à la fermeture du navigateur
        'path'     => '/',
        'domain'   => '',                // laisse vide = domaine actuel
        'secure'   => !empty($_SERVER['HTTPS']), // true si HTTPS
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_start();
}

// Helpers d’auth
function require_login(){
  if (empty($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
  }
}

function me(){
  return $_SESSION['user_id'] ?? null;
}
