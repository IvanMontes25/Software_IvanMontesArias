<?php
// cliente/forgot_password.php — Punto de entrada: Recuperar contraseña (API JSON)
require_once __DIR__ . '/../core/bootstrap.php';
require_once __DIR__ . '/controllers/AuthController.php';

$controller = new AuthController($db);
$controller->forgotPassword();
