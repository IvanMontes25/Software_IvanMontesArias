<?php
// pags_cliente/soporte.php — Punto de entrada
require_once dirname(__DIR__, 2) . '/core/bootstrap.php';
require_once dirname(__DIR__) . '/includes/auth.php';
require_once dirname(__DIR__) . '/controllers/SoporteController.php';

$controller = new SoporteController($db);
$controller->index();
