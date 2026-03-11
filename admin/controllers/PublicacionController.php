<?php
require_once __DIR__ . '/BaseController.php';

class PublicacionController extends BaseController
{
    public function index(): void
    {
        require_once __DIR__ . '/../../core/roles.php';
        require_modulo('administracion');
        // DB is available globally via bootstrap
        global $db;
        $this->render('publicaciones/index', [
            'page' => 'announcement',
            'db' => $db,
        ]);
    }
    public function admin(): void
    {
        require_once __DIR__ . '/../../core/roles.php';
        require_modulo('administracion');
        // DB is available globally via bootstrap
        global $db;
        $this->render('publicaciones/admin', [
            'page' => 'announcement',
            'db' => $db,
        ]);
    }
    public function post(): void
    {
        require_once __DIR__ . '/../../core/roles.php';
        require_modulo('administracion');
        // Action handler - delegates to original logic
        global $db;
        // The original action logic is preserved in the view layer
        // for backward compatibility during MVC transition
        require __DIR__ . '/../views/_actions/publicacion_post.php';
    }
}
