<?php
// cliente/index.php — Punto de entrada: Login
require_once __DIR__ . '/../core/bootstrap.php';
require_once __DIR__ . '/controllers/AuthController.php';

$controller = new AuthController($db);
$controller->login();
