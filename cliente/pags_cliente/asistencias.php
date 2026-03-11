<?php
// pags_cliente/asistencias.php — Punto de entrada
require_once dirname(__DIR__, 2) . '/core/bootstrap.php';
require_once dirname(__DIR__) . '/includes/auth.php';
require_once dirname(__DIR__) . '/controllers/AsistenciaController.php';

$controller = new AsistenciaController($db);
$controller->index($clienteData);
