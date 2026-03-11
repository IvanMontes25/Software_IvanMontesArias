<?php
// pags_cliente/recordatorio_cliente.php — Punto de entrada
require_once dirname(__DIR__, 2) . '/core/bootstrap.php';
require_once dirname(__DIR__) . '/includes/auth.php';
require_once dirname(__DIR__) . '/controllers/NotificacionController.php';

$controller = new NotificacionController($db);
$controller->index();
