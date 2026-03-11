<?php
require_once __DIR__ . '/BaseController.php';

class EquipoController extends BaseController
{
    public function index(): void
    {
        require_once __DIR__ . '/../../core/roles.php';
        require_modulo('equipos');
        // DB is available globally via bootstrap
        global $db;
        $this->render('equipos/index', [
            'page' => 'list-equip',
            'db' => $db,
        ]);
    }
    public function registro(): void
    {
        require_once __DIR__ . '/../../core/roles.php';
        require_modulo('equipos');
        // DB is available globally via bootstrap
        global $db;
        $this->render('equipos/registro', [
            'page' => 'add-equip',
            'db' => $db,
        ]);
    }
    public function editForm(): void
    {
        require_once __DIR__ . '/../../core/roles.php';
        require_modulo('equipos');
        // DB is available globally via bootstrap
        global $db;
        $this->render('equipos/edit_form', [
            'page' => 'list-equip',
            'db' => $db,
        ]);
    }
    public function actions(): void
    {
        require_once __DIR__ . '/../../core/roles.php';
        require_modulo('equipos');
        // Action handler - delegates to original logic
        global $db;
        // The original action logic is preserved in the view layer
        // for backward compatibility during MVC transition
        require __DIR__ . '/../views/_actions/equipo_actions.php';
    }
}
