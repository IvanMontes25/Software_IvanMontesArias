<?php
// cliente/controllers/InformeController.php
// Controlador: Informe del cliente

class InformeController
{
    private mysqli $db;

    public function __construct(mysqli $db)
    {
        $this->db = $db;
    }

    public function index(): void
    {
        require_once __DIR__ . '/../models/MemberModel.php';
        require_once __DIR__ . '/../models/PaymentModel.php';
        require_once dirname(__DIR__, 2) . '/includes/membership_helper.php';

        $memberModel  = new MemberModel($this->db);
        $paymentModel = new PaymentModel($this->db);

        // Helper de escape
        if (!function_exists('e')) {
            function e($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
        }

        // Configuración de Acceso
        $modo = 'cliente';
        $id = (int)$_SESSION['user_id'];

        if (isset($_GET['id']) && ctype_digit($_GET['id'])) {
            $modo = 'admin';
            $id = (int)$_GET['id'];
        }

        // Seguridad: Cliente no puede ver otros IDs
        if ($modo === 'cliente' && $id !== (int)$_SESSION['user_id']) {
            die('<div class="alert alert-danger m-4">Acceso denegado.</div>');
        }

        // A. Datos del Miembro
        $member = $memberModel->getForReport($id);

        if (!$member) {
            die('<div class="alert alert-warning m-4">Cliente no encontrado.</div>');
        }

        // B. Último Pago y Vigencia
        $ultimoPago = $paymentModel->getLastPayment($id);

        $m = membership_last($this->db, $id);
        $ultimoPagoMembresia = null;

        if ($m) {
            $ultimoPagoMembresia = $paymentModel->getLastMembershipPayment($id, $m['plan_id']);
        }

        $planNombre    = $m['plan_nombre'] ?? '—';
        $fechaFin      = membership_end_date($m);
        $diasRestantes = membership_days_left($m);
        $estadoTexto   = membership_status_text($m);
        $duracionDias  = $m['duracion_dias'] ?? 0;

        $estadoColor = match ($estadoTexto) {
            'Activa' => 'success',
            'Por vencer' => 'warning',
            'Vencida' => 'danger',
            default => 'secondary'
        };

        $estadoIcon = match ($estadoTexto) {
            'Activa' => 'fa-check-circle',
            'Por vencer' => 'fa-exclamation-circle',
            'Vencida' => 'fa-times-circle',
            default => 'fa-minus-circle'
        };

        // E. Formateo de variables
        $membresiaID = 'GBT-' . str_pad((string)$member['user_id'], 5, '0', STR_PAD_LEFT);
        $importe = ($ultimoPagoMembresia && $ultimoPagoMembresia['amount'] > 0)
            ? 'Bs ' . number_format($ultimoPagoMembresia['amount'], 2)
            : '—';

        $fechaPago = ($ultimoPagoMembresia && $ultimoPagoMembresia['paid_date'])
            ? date('d/m/Y', strtotime($ultimoPagoMembresia['paid_date']))
            : '—';

        $asistencias = (int)($member['attendance_count'] ?? 0);

        // Renderizar vista
        include __DIR__ . '/../views/informe/index.php';
    }
}
