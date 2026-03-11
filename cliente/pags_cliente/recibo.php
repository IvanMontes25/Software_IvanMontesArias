<?php
// pags_cliente/recibo.php — Punto de entrada
require_once dirname(__DIR__, 2) . '/core/bootstrap.php';
require_once dirname(__DIR__) . '/includes/auth.php';
require_once dirname(__DIR__) . '/controllers/ReciboController.php';

$controller = new ReciboController($db);
$controller->index();
