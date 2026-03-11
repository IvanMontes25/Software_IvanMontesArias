<?php
// pags_cliente/membresia_vencida.php — Punto de entrada
require_once dirname(__DIR__, 2) . '/core/bootstrap.php';
require_once dirname(__DIR__) . '/includes/auth.php';
require_once dirname(__DIR__) . '/controllers/MembresiaController.php';

$controller = new MembresiaController($db);
$controller->vencida($clienteData);
