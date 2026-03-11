<?php
// pags_cliente/logros.php — Punto de entrada
require_once dirname(__DIR__, 2) . '/core/bootstrap.php';
require_once dirname(__DIR__) . '/includes/auth.php';
require_once dirname(__DIR__) . '/controllers/LogrosController.php';

$controller = new LogrosController($db);
$controller->index($clienteData);
