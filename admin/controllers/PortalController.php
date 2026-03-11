<?php
require_once __DIR__ . '/BaseController.php';

class PortalController extends BaseController
{
    public function index(): void
    {
        require_once __DIR__ . '/../../core/roles.php';
        require_modulo('administracion');
        // DB is available globally via bootstrap
        global $db;
        $this->render('portal/index', [
            'page' => 'portal',
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
        require __DIR__ . '/../views/_actions/portal_actions.php';
    }
}
