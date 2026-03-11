<?php
// cliente/controllers/NotificacionController.php
// Controlador: Centro de Novedades / Recordatorios

class NotificacionController
{
    private mysqli $db;

    public function __construct(mysqli $db)
    {
        $this->db = $db;
    }

    public function index(): void
    {
        require_once __DIR__ . '/../models/MemberModel.php';
        require_once __DIR__ . '/../models/AnnouncementModel.php';
        require_once __DIR__ . '/../models/ReminderModel.php';

        $memberModel       = new MemberModel($this->db);
        $announcementModel = new AnnouncementModel($this->db);
        $reminderModel     = new ReminderModel($this->db);

        $page   = 'notificaciones';
        $userId = (int)($_SESSION['user_id'] ?? 0);

        // Helper seguro
        function h($s) {
            return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8');
        }

        function pretty_date_short($s) {
            if (!$s) return '';
            return date('d M, Y', strtotime($s));
        }

        /* --- 1. PROCESAR FORMULARIO (Toggle Recordatorio) --- */
        $flash = null;
        $flash_type = 'success';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            if (!csrf_verify($_POST['csrf_token'] ?? '')) {
                die('Error de seguridad (CSRF).');
            }

            if (isset($_POST['reminder'])) {
                $set = ($_POST['reminder'] === '1') ? 1 : 0;
                if ($memberModel->updateReminder($userId, $set)) {
                    $flash = ($set === 1) ? "Recordatorios activados." : "Recordatorios desactivados.";
                    $flash_type = 'success';
                } else {
                    $flash = "Error al actualizar.";
                    $flash_type = 'danger';
                }
            }
        }

        /* --- 2. OBTENER PUBLICACIONES --- */
        $announcements = $announcementModel->getAll();

        /* --- 3. OBTENER ESTADO ACTUAL RECORDATORIO --- */
        $reminder = $memberModel->getReminder($userId);

        /* --- 4. OBTENER HISTORIAL DE ALERTAS --- */
        $paymentReminders = $reminderModel->getByUser($userId, 20);

        // Renderizar vista
        include __DIR__ . '/../views/notificaciones/index.php';
    }

    /**
     * Redirect desde notificaciones.php
     */
    public function redirect(): void
    {
        if (session_status() === PHP_SESSION_NONE) { session_start(); }
        if (!isset($_SESSION['user_id'])) {
            header('Location: ../index.php');
            exit;
        }
        header('Location: recordatorio_cliente.php');
        exit;
    }
}
