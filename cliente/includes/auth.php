<?php

// Detectar si es petición AJAX
$isAjax = (
    isset($_GET['ajax']) ||
    (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
     strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest')
);

// Función segura para salir
function auth_exit() {
    global $isAjax;

    if ($isAjax) {
        http_response_code(401);
        exit;
    }

    // Guardar destino actual (ej: checkin.php con parámetros QR)
    $next = urlencode($_SERVER['REQUEST_URI']);

    // Tu login real es /cliente/index.php
    header("Location: /GymBodyTrainingEST/cliente/index.php?next=$next");

    exit;
}

// =============================
// VALIDACIÓN DE SESIÓN
// =============================
if (!isset($_SESSION['user_id'])) {
    auth_exit();
}

$user_id = (int) $_SESSION['user_id'];

if ($user_id <= 0) {
    auth_exit();
}
if (!isset($_SESSION['user_id'])) {
    auth_exit();
}

if (($_SESSION['origen'] ?? '') !== 'members') {
    auth_exit();
}
require_once __DIR__ . '/cliente_data.php';

$clienteData = obtenerClienteData($db, $user_id);

if (!$clienteData) {
    auth_exit();
}
// Forzar cambio de contraseña si está marcado
$stmt = $db->prepare("
    SELECT must_change_password 
    FROM members 
    WHERE user_id = ?
    LIMIT 1
");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$stmt->bind_result($mustChange);
$stmt->fetch();
$stmt->close();

if ((int)$mustChange === 1) {
    $current = basename($_SERVER['PHP_SELF']);
    if ($current !== 'cambiar_password_obligatorio.php') {
        header("Location: /GymBodyTrainingEST/cliente/pags_cliente/cambiar_password_obligatorio.php");

        exit;
    }
}

// =============================
// VALIDACIÓN DE MEMBRESÍA
// =============================
if (!in_array($clienteData['estado'], ['activa', 'por_vencer'], true)) {

    $current = basename($_SERVER['PHP_SELF']);

    if ($current !== 'membresia_vencida.php') {

        if ($isAjax) {
            http_response_code(403);
            exit;
        }

        header("Location: membresia_vencida.php");
        exit;
    }
}


