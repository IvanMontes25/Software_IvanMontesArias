<?php
require_once __DIR__ . '/BaseController.php';

class AuditoriaController extends BaseController
{
    public function index(): void
    {
        require_once __DIR__ . '/../../core/roles.php';
        require_modulo('administracion');
        // DB is available globally via bootstrap
        global $db;
        $this->render('auditoria/index', [
            'page' => 'auditoria',
            'db' => $db,
        ]);
    }
}
