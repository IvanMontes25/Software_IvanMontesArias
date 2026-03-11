<?php
require_once __DIR__ . '/BaseController.php';

class InboxController extends BaseController
{
    public function index(): void
    {
        // DB is available globally via bootstrap
        global $db;
        $this->render('inbox/index', [
            'page' => 'inbox',
            'db' => $db,
        ]);
    }
    public function detalle(): void
    {
        // DB is available globally via bootstrap
        global $db;
        $this->render('inbox/detalle', [
            'page' => 'inbox',
            'db' => $db,
        ]);
    }
}
