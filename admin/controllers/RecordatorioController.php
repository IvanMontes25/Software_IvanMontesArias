<?php
require_once __DIR__ . '/BaseController.php';

class RecordatorioController extends BaseController
{
    public function enviar(): void
    {
        // Action handler - delegates to original logic
        global $db;
        // The original action logic is preserved in the view layer
        // for backward compatibility during MVC transition
        require __DIR__ . '/../views/_actions/recordatorio_enviar.php';
    }
}
