<?php
require_once __DIR__ . '/BaseController.php';

class ReporteController extends BaseController
{
    public function index(): void
    {
        require_once __DIR__ . '/../../core/roles.php';
        require_modulo('reportes');
        // DB is available globally via bootstrap
        global $db;
        $this->render('reportes/index', [
            'page' => 'chart',
            'db' => $db,
        ]);
    }
    public function cliente(): void
    {
        require_once __DIR__ . '/../../core/roles.php';
        require_modulo('reportes');
        // DB is available globally via bootstrap
        global $db;
        $this->render('reportes/cliente', [
            'page' => 'member-repo',
            'db' => $db,
        ]);
    }
    public function verCliente(): void
    {
        require_once __DIR__ . '/../../core/roles.php';
        require_modulo('reportes');
        // DB is available globally via bootstrap
        global $db;
        $this->render('reportes/ver_cliente', [
            'page' => 'member-repo',
            'db' => $db,
        ]);
    }
    public function automatizacion(): void
    {
        require_once __DIR__ . '/../../core/roles.php';
        require_modulo('reportes');
        // DB is available globally via bootstrap
        global $db;
        $this->render('reportes/automatizacion', [
            'page' => 'auto-repo',
            'db' => $db,
        ]);
    }
}
