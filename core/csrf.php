<?php
// admin/core/csrf.php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Genera o devuelve el token CSRF actual
 */
function csrf_token(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Devuelve el input hidden listo para usar en formularios
 */
function csrf_field(): string {
    return '<input type="hidden" name="csrf_token" value="' 
        . htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8') 
        . '">';
}

/**
 * Verifica automáticamente el token desde $_POST
 */
function csrf_verify(): void {

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        exit('Método no permitido');
    }

    $token = $_POST['csrf_token'] ?? '';

    if (
        empty($_SESSION['csrf_token']) ||
        empty($token) ||
        !hash_equals($_SESSION['csrf_token'], $token)
    ) {
        http_response_code(403);
        exit('Token CSRF inválido');
    }
}
