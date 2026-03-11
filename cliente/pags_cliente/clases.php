<?php
// pags_cliente/clases.php — Punto de entrada: Clases disponibles
require_once dirname(__DIR__, 2) . '/core/bootstrap.php';
require_once dirname(__DIR__) . '/includes/auth.php';
require_once dirname(__DIR__) . '/controllers/ClaseController.php';

$controller = new ClaseController($db);

// Router simple por acción
$action = $_GET['action'] ?? $_POST['action'] ?? 'disponibles';

switch ($action) {
    case 'reservar':
        $controller->reservar();
        break;
    case 'cancelar':
        $controller->cancelar();
        break;
    case 'lista_espera':
        $controller->listaEspera();
        break;
    default:
        $controller->disponibles();
        break;
}
