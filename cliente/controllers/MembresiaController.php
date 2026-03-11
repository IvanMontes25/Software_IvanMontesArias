<?php
// cliente/controllers/MembresiaController.php
// Controlador: Membresía - Pagos y Vencida

class MembresiaController
{
    private mysqli $db;

    public function __construct(mysqli $db)
    {
        $this->db = $db;
    }

    /**
     * Página de historial de pagos y membresía.
     */
    public function pagos(array $clienteData): void
    {
        require_once __DIR__ . '/../models/PaymentModel.php';
        require_once dirname(__DIR__, 2) . '/includes/membership_helper.php';

        $paymentModel = new PaymentModel($this->db);

        $page = 'pagos';

        if (!$clienteData) {
            header('Location: ../cerrar_session.php');
            exit;
        }

        // Control de acceso
        $m = membership_last($this->db, (int)$clienteData['id']);

        if (!membership_can_access($m)) {
            header('Location: membresia_vencida.php');
            exit;
        }

        // Datos desde helper
        $estadoReal      = membership_status_text($m);
        $diasRestantes   = membership_days_left($m);
        $fechaFinReal    = $m['end_date']->format('d/m/Y');
        $planReal        = $m['plan_nombre'];
        $cardStatus      = mb_strtolower($estadoReal);

        // Variables para alerta de membresía por vencer
        $statusKey = membership_status($m);
        $daysLeft  = $diasRestantes;

        // Historial de pagos
        $rows = $paymentModel->getByUser((int)$clienteData['id']);

        // Helpers de formato
        function h($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }

        function fmt_date_short($s) {
            if (!$s) return '—';
            try { return (new DateTime($s))->format('d/m/Y'); }
            catch (Exception $e) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
        }

        function fmt_money($n) {
            if ($n === null || $n === '') return '—';
            return 'Bs ' . number_format((float)$n, 2, '.', ',');
        }

        // Estadísticas
        $totalPagado = 0;
        $numPagos = count($rows);
        $ultimoPagoFecha = '—';

        if (!empty($rows)) {
            $ultimoPagoFecha = isset($rows[0]['paid_date']) ? fmt_date_short($rows[0]['paid_date']) : '—';
            foreach ($rows as $r) {
                if (isset($r['amount']) && is_numeric($r['amount'])) {
                    $totalPagado += (float)$r['amount'];
                }
            }
        }

        // Datos para la tarjeta visual
        $cardColor  = 'linear-gradient(135deg, #4e73df 0%, #224abe 100%)';
        $statusIcon = 'fa-check-circle';

        if (in_array($cardStatus, ['vencida', 'vencido', 'sin_membresia'])) {
            $cardColor = 'linear-gradient(135deg, #e74a3b 0%, #be2617 100%)';
            $statusIcon = 'fa-times-circle';
        } elseif (in_array($cardStatus, ['por_vencer', 'pendiente'])) {
            $cardColor = 'linear-gradient(135deg, #f6c23e 0%, #dda20a 100%)';
            $statusIcon = 'fa-exclamation-circle';
        }

        // Renderizar vista
        include __DIR__ . '/../views/membresia/pagos.php';
    }

    /**
     * Página de membresía vencida.
     */
    public function vencida(array $clienteData): void
    {
        require_once __DIR__ . '/../models/ReminderModel.php';
        require_once dirname(__DIR__, 2) . '/includes/membership_helper.php';

        $reminderModel = new ReminderModel($this->db);

        $page = 'pagos';

        if (!$clienteData) {
            header('Location: ../cerrar_session.php');
            exit;
        }

        $m = membership_last($this->db, (int)$clienteData['id']);

        $estadoReal    = membership_status($m);
        $estadoTexto   = membership_status_text($m);
        $diasRestantes = membership_days_left($m);

        $vence_el  = $m ? $m['end_date']->format('Y-m-d') : null;
        $paid_date = $m ? $m['start_date']->format('Y-m-d') : null;

        $fullname = $clienteData['nombre'] ?? '';

        // Mensaje dinámico según estado real
        $mensaje_dinamico = null;
        $clase_alerta = 'info';

        if ($estadoReal === 'vencida' && $vence_el) {
            $dias = abs((int)$diasRestantes);
            $mensaje_dinamico = "Tu membresía <strong>VENCIÓ el "
                . date('d/m/Y', strtotime($vence_el))
                . "</strong> (hace {$dias} día" . ($dias === 1 ? '' : 's') . ").";
            $clase_alerta = 'danger';
        } elseif ($estadoReal === 'activa' && in_array($diasRestantes, [7,3,1])) {
            $mensaje_dinamico = "Tu membresía <strong>VENCERÁ dentro de "
                . $diasRestantes . " día"
                . ($diasRestantes === 1 ? '' : 's')
                . "</strong>.";
            $clase_alerta = 'warning';
        } elseif ($estadoReal === 'activa' && $vence_el) {
            $mensaje_dinamico = "Tu membresía está activa hasta el "
                . date('d/m/Y', strtotime($vence_el)) . ".";
            $clase_alerta = 'success';
        }

        // Obtener alerta según estado
        $alerta_n8n = null;

        if ($estadoReal === 'vencida') {
            $alerta_n8n = $reminderModel->getLastExpiredAlert((int)$clienteData['id']);
        } elseif (in_array($diasRestantes, [7,3,1])) {
            $tipo = 'pre_' . abs((int)$diasRestantes);
            $alerta_n8n = $reminderModel->getLastByType((int)$clienteData['id'], $tipo);
        }

        // Texto de días (UX)
        $dias_texto = '';
        if ($estadoReal === 'vencida' && $vence_el) {
            $dias_texto = 'hace ' . abs((int)$diasRestantes) .
                          ' día' . (abs($diasRestantes) === 1 ? '' : 's');
        }

        // Helper
        function h($s) {
            return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8');
        }

        // Renderizar vista
        include __DIR__ . '/../views/membresia/vencida.php';
    }
}
