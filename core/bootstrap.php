<?php
// core/bootstrap.php

// ==============================
// 1. Definir ROOT_PATH
// ==============================
require_once __DIR__ . '/../config/config.php';


if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', dirname(__DIR__));
}

// ==============================
// 2. Iniciar sesión segura
// ==============================
require_once __DIR__ . '/session.php';

// ==============================
// 3. Regeneración periódica
// ==============================
if (!isset($_SESSION['created'])) {
    session_regenerate_id(true);
    $_SESSION['created'] = time();
} elseif (time() - $_SESSION['created'] > 1800) {
    session_regenerate_id(true);
    $_SESSION['created'] = time();
}

// ==============================
// 4. Timeout automático
// ==============================
if (isset($_SESSION['LAST_ACTIVITY']) && 
    (time() - $_SESSION['LAST_ACTIVITY'] > 1800)) {

    session_unset();
    session_destroy();
    header("Location: " . BASE_URL . "/index.php");

    exit;
}

$_SESSION['LAST_ACTIVITY'] = time();

// ==============================
// 5. Zona horaria
// ==============================
date_default_timezone_set('America/La_Paz');

// ==============================
// 6. Conexión DB
// ==============================
require_once __DIR__ . '/db.php';

// ==============================
// 7. Funciones CSRF
// ==============================
function csrf_token() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrf_verify($token) {
    if ($token === null || $token === '' || !isset($_SESSION['csrf_token'])) {
        return false;
    }
    return hash_equals($_SESSION['csrf_token'], $token);
}
function csrf_field() {
    return '<input type="hidden" name="csrf_token" value="' . csrf_token() . '">';
}