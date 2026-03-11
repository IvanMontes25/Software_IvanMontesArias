<?php
// pags_cliente/perfil.php — Punto de entrada
require_once dirname(__DIR__, 2) . '/core/bootstrap.php';
require_once dirname(__DIR__) . '/includes/auth.php';
require_once dirname(__DIR__) . '/controllers/PerfilController.php';

$controller = new PerfilController($db);
$controller->index();
