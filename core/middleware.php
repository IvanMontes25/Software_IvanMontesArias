<?php

function require_login() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    if (empty($_SESSION['user_id'])) {
        header("Location: /index.php");
        exit;
    }
}

function require_admin() {
    require_login();

    if ($_SESSION['role'] !== 'admin') {
        http_response_code(403);
        exit('Acceso no autorizado');
    }
}
