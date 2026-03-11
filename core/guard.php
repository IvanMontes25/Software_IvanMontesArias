<?php
// core/guard.php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Obliga a que el usuario esté logueado
 */
function require_login(): void {
    if (empty($_SESSION['user_id'])) {
        header('Location: ../index.php');
        exit;
    }
}

/**
 * Obliga a que el usuario tenga un rol permitido
 */
function require_role(array $roles): void {

    $rol = $_SESSION['rol'] ?? null;

    if (!$rol || !in_array($rol, $roles, true)) {
        http_response_code(403);
        exit('No autorizado.');
    }
}
