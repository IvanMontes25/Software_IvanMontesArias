<?php
require_once __DIR__ . '/BaseController.php';

class AsistenciaController extends BaseController
{
    public function index(): void
    {
        require_once __DIR__ . '/../../includes/membership_helper.php';
        // DB is available globally via bootstrap
        global $db;
        $this->render('asistencias/index', [
            'page' => 'attendance',
            'db' => $db,
        ]);
    }
    public function cliente(): void
    {
        // DB is available globally via bootstrap
        global $db;
        $this->render('asistencias/cliente', [
            'page' => 'attendance',
            'db' => $db,
        ]);
    }
}
