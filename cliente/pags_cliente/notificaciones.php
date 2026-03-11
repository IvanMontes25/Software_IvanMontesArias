<?php
// pags_cliente/notificaciones.php
require_once dirname(__DIR__, 2) . '/core/bootstrap.php';
require_once dirname(__DIR__) . '/includes/auth.php';

// Redirect a recordatorio_cliente
header('Location: recordatorio_cliente.php');
exit;
