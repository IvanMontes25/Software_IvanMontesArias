<?php
// cliente/controllers/AsistenciaController.php
// Controlador: Asistencias del cliente

class AsistenciaController
{
    private mysqli $db;

    public function __construct(mysqli $db)
    {
        $this->db = $db;
    }

    public function index(array $clienteData): void
    {
        require_once __DIR__ . '/../models/AttendanceModel.php';
        require_once dirname(__DIR__, 2) . '/includes/membership_helper.php';

        $attendanceModel = new AttendanceModel($this->db);

        $page = 'asistencias';

        if (!$clienteData) {
            header('Location: ../cerrar_session.php');
            exit;
        }

        // Control de acceso por membresía
        $m = membership_last($this->db, (int)$clienteData['id']);

        if (!membership_can_access($m)) {
            header('Location: membresia_vencida.php');
            exit;
        }

        // Variables para alerta de membresía por vencer
        $statusKey = membership_status($m);
        $daysLeft  = $m ? membership_days_left($m) : 0;

        /* ================== LÓGICA DE FECHAS ================== */
        $userId = (int)$clienteData['id'];

        $year  = isset($_GET['y']) ? max(2000, min(2100, (int)$_GET['y'])) : (int)date('Y');
        $month = isset($_GET['m']) ? max(1, min(12, (int)$_GET['m']))      : (int)date('n');

        // Navegación
        $prevY = $year; $prevM = $month - 1;
        if ($prevM < 1) { $prevM = 12; $prevY--; }

        $nextY = $year; $nextM = $month + 1;
        if ($nextM > 12) { $nextM = 1;  $nextY++; }

        // Datos del mes actual
        $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);
        $startDate   = sprintf('%04d-%02d-01', $year, $month);
        $endDate     = sprintf('%04d-%02d-%02d', $year, $month, $daysInMonth);
        $firstDow    = (int)date('N', strtotime($startDate));
        $todayStr    = date('Y-m-d');

        // Nombres de meses en español
        $mesesEs = [
            1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril',
            5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto',
            9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'
        ];
        $monthName = $mesesEs[$month] . ' ' . $year;

        /* ================== CONSULTAS VÍA MODELO ================== */

        // 1. Días asistidos (calendario)
        $presentDays = $attendanceModel->getPresentDays($userId, $startDate, $endDate);

        // 2. Lista completa (historial)
        $list = $attendanceModel->getList($userId, $startDate, $endDate);

        // 3. Datos para el Gráfico
        $perDay = array_fill(1, $daysInMonth, 0);
        $totalMonth = 0;
        foreach ($list as $row) {
            $d = (int)date('j', strtotime($row['curr_date']));
            if ($d >= 1 && $d <= $daysInMonth) {
                $perDay[$d]++;
                $totalMonth++;
            }
        }

        // Helper escape
        function h($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }

        // Renderizar vista
        include __DIR__ . '/../views/asistencias/index.php';
    }
}
