<?php
require_once __DIR__ . '/BaseController.php';

class LogroController extends BaseController
{
    public function index(): void
    {
        require_once __DIR__ . '/../../core/roles.php';
        require_modulo('administracion');
        // DB is available globally via bootstrap
        global $db;
        $this->render('logros/index', [
            'page' => 'logros',
            'db' => $db,
        ]);
    }
    public function cliente(): void
    {
        // DB is available globally via bootstrap
        global $db;
        $this->render('logros/cliente', [
            'page' => 'logros',
            'db' => $db,
        ]);
    }
}
