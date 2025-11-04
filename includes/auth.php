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

/** Renvoie l'id de l'utilisateur connecté ou null */
function me() {
  return $_SESSION['user_id'] ?? null;
}

/** Exige une authentification (cours : vérifier $_SESSION['user_id']) */
function require_login() {
  if (empty($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
  }
}

/** Exige un rôle précis (cours : contrôler l’autorisation via $_SESSION['role']) */
function require_role(string $role) {
  require_login();
  if (($_SESSION['role'] ?? 'user') !== $role) {
    http_response_code(403);
    // Si tu as une page 403 dédiée : header('Location: 403.php'); exit;
    echo "Accès refusé (403).";
    exit;
  }
}