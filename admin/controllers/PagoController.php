<?php
require_once __DIR__ . '/BaseController.php';

class PagoController extends BaseController
{
    public function index(): void
    {
        // DB is available globally via bootstrap
        global $db;
        $this->render('pagos/index', [
            'page' => 'payment',
            'db' => $db,
        ]);
    }
    public function cliente(): void
    {
        // DB is available globally via bootstrap
        global $db;
        $this->render('pagos/cliente', [
            'page' => 'payment',
            'db' => $db,
        ]);
    }
    public function historialCliente(): void
    {
        // DB is available globally via bootstrap
        global $db;
        $this->render('pagos/historial_cliente', [
            'page' => 'payment',
            'db' => $db,
        ]);
    }
    public function historial(): void
    {
        // DB is available globally via bootstrap
        global $db;
        $this->render('pagos/historial', [
            'page' => 'payment',
            'db' => $db,
        ]);
    }
    public function recibo(): void
    {
        // DB is available globally via bootstrap
        global $db;
        $this->render('recibos/recibo', [
            'page' => 'payment',
            'db' => $db,
        ]);
    }
    public function recibosCliente(): void
    {
        // DB is available globally via bootstrap
        global $db;
        $this->render('recibos/lista', [
            'page' => 'payment',
            'db' => $db,
        ]);
    }
    public function registrar(): void
    {
        // Action handler - delegates to original logic
        global $db;
        // The original action logic is preserved in the view layer
        // for backward compatibility during MVC transition
        require __DIR__ . '/../views/_actions/pago_registrar.php';
    }
}
