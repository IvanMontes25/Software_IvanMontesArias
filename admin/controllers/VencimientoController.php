<?php
require_once __DIR__ . '/BaseController.php';

class VencimientoController extends BaseController
{
    public function index(): void
    {
        require_once __DIR__ . '/../../includes/membership_helper.php';
        // DB is available globally via bootstrap
        global $db;
        $this->render('pagos/vencimientos', [
            'page' => 'vencimientos',
            'db' => $db,
        ]);
    }
}
