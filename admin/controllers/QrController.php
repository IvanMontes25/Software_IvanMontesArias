<?php
require_once __DIR__ . '/BaseController.php';

class QrController extends BaseController
{
    public function recepcion(): void
    {
        // DB is available globally via bootstrap
        global $db;
        $this->render('qr/recepcion', [
            'page' => 'qr_admin',
            'db' => $db,
        ]);
    }
}
