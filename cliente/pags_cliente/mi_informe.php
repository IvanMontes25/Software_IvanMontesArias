<?php
// pags_cliente/mi_informe.php — Punto de entrada
require_once dirname(__DIR__, 2) . '/core/bootstrap.php';
require_once dirname(__DIR__, 2) . '/includes/membership_helper.php';
require_once dirname(__DIR__) . '/includes/auth.php';
require_once dirname(__DIR__) . '/controllers/InformeController.php';

$controller = new InformeController($db);
$controller->index();
