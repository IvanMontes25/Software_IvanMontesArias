<?php
// core/auth.php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {

    $next = urlencode($_SERVER['REQUEST_URI']);
    header("Location: " . BASE_URL . "/index.php?next=$next");
    exit;
}

// 2) Es staff/admin? (NO cliente)
//    Si el origen es 'members' (cliente), no debe estar aqui.
if (($_SESSION['origen'] ?? '') !== 'staffs') {
    header("Location: " . BASE_URL . "/index.php");
    exit;
}
function require_cliente() {
    if (
        empty($_SESSION['user_id']) ||
        ($_SESSION['origen'] ?? '') !== 'members'
    ) {
        header("Location: index.php");
        exit;
    }
}
function require_staff() {
    if (
        empty($_SESSION['user_id']) ||
        ($_SESSION['origen'] ?? '') !== 'staffs'
    ) {
        header("Location: " . BASE_URL . "index.php");
        exit;
    }
}