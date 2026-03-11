<?php
// pags_cliente/cambiar_password_obligatorio.php — Punto de entrada
require_once dirname(__DIR__, 2) . '/core/bootstrap.php';
require_once dirname(__DIR__) . '/includes/auth.php';
require_once dirname(__DIR__) . '/controllers/AuthController.php';

$controller = new AuthController($db);
$controller->cambiarPasswordObligatorio();
