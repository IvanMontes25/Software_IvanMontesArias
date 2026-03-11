<?php
// cliente/controllers/SoporteController.php
// Controlador: Centro de Soporte

class SoporteController
{
    private mysqli $db;

    public function __construct(mysqli $db)
    {
        $this->db = $db;
    }

    public function index(): void
    {
        $page = 'soporte';
        $pageTitle = 'Centro de Ayuda';

        // Renderizar vista
        include __DIR__ . '/../views/soporte/index.php';
    }
}
