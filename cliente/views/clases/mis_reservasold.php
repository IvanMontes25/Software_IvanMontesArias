<?php
// pags_cliente/mis_reservas.php — Punto de entrada: Mis Reservas
require_once dirname(__DIR__, 2) . '/core/bootstrap.php';
require_once dirname(__DIR__, 2) . '/core/auth.php';
require_once dirname(__DIR__) . '/controllers/ClaseController.php';

$controller = new ClaseController($db);
$controller->misReservas();
