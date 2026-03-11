<?php
require_once __DIR__ . '/BaseController.php';

class ClaseController extends BaseController
{
    public function misClases(): void
    {
        require_once __DIR__ . '/../../core/roles.php';
        require_modulo('clases');
        // DB is available globally via bootstrap
        global $db;
        $this->render('clases/mis_clases', [
            'page' => 'mis-clases',
            'db' => $db,
        ]);
    }
    public function agendar(): void
    {
        require_once __DIR__ . '/../../core/roles.php';
        require_modulo('clases');
        // DB is available globally via bootstrap
        global $db;
        $this->render('clases/agendar', [
            'page' => 'clase-agendar',
            'db' => $db,
        ]);
    }
    public function tipos(): void
    {
        require_once __DIR__ . '/../../core/roles.php';
        require_modulo('administracion');
        // DB is available globally via bootstrap
        global $db;
        $this->render('clases/tipos', [
            'page' => 'clase-tipos',
            'db' => $db,
        ]);
    }
    public function inscritos(): void
    {
        require_once __DIR__ . '/../../core/roles.php';
        require_modulo('clases');
        // DB is available globally via bootstrap
        global $db;
        $this->render('clases/inscritos', [
            'page' => 'clase-inscritos',
            'db' => $db,
        ]);
    }
    public function agendarAction(): void
    {
        require_once __DIR__ . '/../../core/roles.php';
        require_modulo('clases');
        // Action handler - delegates to original logic
        global $db;
        // The original action logic is preserved in the view layer
        // for backward compatibility during MVC transition
        require __DIR__ . '/../views/_actions/clase_agendarAction.php';
    }
    public function asistenciaAction(): void
    {
        require_once __DIR__ . '/../../core/roles.php';
        require_modulo('clases');
        // Action handler - delegates to original logic
        global $db;
        // The original action logic is preserved in the view layer
        // for backward compatibility during MVC transition
        require __DIR__ . '/../views/_actions/clase_asistenciaAction.php';
    }
    public function cancelarAction(): void
    {
        require_once __DIR__ . '/../../core/roles.php';
        require_modulo('clases');
        // Action handler - delegates to original logic
        global $db;
        // The original action logic is preserved in the view layer
        // for backward compatibility during MVC transition
        require __DIR__ . '/../views/_actions/clase_cancelarAction.php';
    }
}
