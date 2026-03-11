<?php
// cliente/controllers/PerfilController.php
// Controlador: Perfil del cliente

class PerfilController
{
    private mysqli $db;

    public function __construct(mysqli $db)
    {
        $this->db = $db;
    }

    public function index(): void
    {
        require_once __DIR__ . '/../models/MemberModel.php';
        require_once __DIR__ . '/../models/AttendanceModel.php';
        require_once __DIR__ . '/../models/PaymentModel.php';
        require_once dirname(__DIR__, 2) . '/includes/membership_helper.php';

        $memberModel     = new MemberModel($this->db);
        $attendanceModel = new AttendanceModel($this->db);
        $paymentModel    = new PaymentModel($this->db);

        $page    = 'perfil';
        $user_id = (int)$_SESSION['user_id'];

        // A. Datos del miembro
        $member = $memberModel->getFullProfile($user_id);

        // Variables básicas
        $fullname     = $member['fullname'] ?? 'Usuario';
        $initial      = strtoupper(substr($fullname, 0, 1));
        $membershipId = 'GBT-SS-' . $user_id;

        // B. Membresía y Estado
        $m = membership_last($this->db, $user_id);
        $statusKey = membership_status($m);

        $daysLeft  = $m ? membership_days_left($m) : 0;
        $planesTxt = $m ? $daysLeft . ' día(s)' : '—';
        $planName  = $m['plan_nombre'] ?? 'Sin Plan';

        $estadoUI    = membership_status_text($m);
        $estadoBadge = match ($statusKey) {
            'activa'     => 'success',
            'por_vencer' => 'warning',
            'vencida'    => 'danger',
            default      => 'secondary'
        };

        // Cálculo de barra de progreso
        $totalDaysBase = 30;
        $percent = 0;
        $progressColor = 'bg-success';

        if ($m && ($statusKey === 'activa' || $statusKey === 'por_vencer')) {
            $percent = min(100, max(0, ($daysLeft / $totalDaysBase) * 100));
            if ($daysLeft <= 5) $progressColor = 'bg-danger';
            elseif ($daysLeft <= 10) $progressColor = 'bg-warning';
        }

        // C. Precio Último Pago
        $lastAmount = $paymentModel->getLastAmount($user_id);
        $precioBs   = $lastAmount !== null ? number_format($lastAmount, 2, '.', '') : '—';

        // D. Asistencias
        $asistencias = (string)$attendanceModel->countByUser($user_id);

        // Helper de escape
        function e($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }

        // Renderizar vista
        include __DIR__ . '/../views/perfil/index.php';
    }
}
