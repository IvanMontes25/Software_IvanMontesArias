<?php
require_once __DIR__ . '/BaseController.php';

class StaffController extends BaseController
{
    public function index(): void
    {
        require_once __DIR__ . '/../../core/roles.php';
        require_modulo('administracion');
        // DB is available globally via bootstrap
        global $db;
        $this->render('staffs/index', [
            'page' => 'staff-management',
            'db' => $db,
        ]);
    }
    public function registro(): void
    {
        require_once __DIR__ . '/../../core/roles.php';
        require_modulo('administracion');
        // DB is available globally via bootstrap
        global $db;
        $this->render('staffs/registro', [
            'page' => 'staff-management',
            'db' => $db,
        ]);
    }
    public function editForm(): void
    {
        require_once __DIR__ . '/../../core/roles.php';
        require_modulo('administracion');
        // DB is available globally via bootstrap
        global $db;
        $this->render('staffs/edit_form', [
            'page' => 'staff-management',
            'db' => $db,
        ]);
    }
    public function actions(): void
    {
        require_once __DIR__ . '/../../core/roles.php';
        require_modulo('administracion');
        // Action handler - delegates to original logic
        global $db;
        // The original action logic is preserved in the view layer
        // for backward compatibility during MVC transition
        require __DIR__ . '/../views/_actions/staff_actions.php';
    }
    public function agregado(): void
    {
        require_once __DIR__ . '/../../core/roles.php';
        require_modulo('administracion');
        // Action handler - delegates to original logic
        global $db;
        // The original action logic is preserved in the view layer
        // for backward compatibility during MVC transition
        require __DIR__ . '/../views/_actions/staff_agregado.php';
    }
}
