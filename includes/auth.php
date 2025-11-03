<?php
// includes/auth.php
if (session_status() === PHP_SESSION_NONE) { session_start(); }

function require_login(){
  // redirige vers la page de login si pas connecté
  if (empty($_SESSION['user_id'])) {
    header('Location: login.php'); // fonctionne car le header est inclus depuis /public
    exit;
  }
}

function me(){
  return $_SESSION['user_id'] ?? null;
}

