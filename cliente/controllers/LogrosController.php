<?php
// cliente/controllers/LogrosController.php
// Controlador: Logros del cliente

class LogrosController
{
    private mysqli $db;

    public function __construct(mysqli $db)
    {
        $this->db = $db;
    }

    public function index(array $clienteData): void
    {
        require_once __DIR__ . '/../models/AchievementModel.php';
        require_once __DIR__ . '/../models/AttendanceModel.php';

        $achievementModel = new AchievementModel($this->db);
        $attendanceModel  = new AttendanceModel($this->db);

        $page = 'logros';

        if (!$clienteData) {
            header('Location: ../cerrar_session.php');
            exit;
        }

        // Control de acceso por membresía
        if (!in_array($clienteData['estado'], ['activa', 'por_vencer'], true)) {
            header('Location: membresia_vencida.php');
            exit;
        }

        // Variables para alerta de membresía por vencer
        $statusKey = $clienteData['estado'];
        $daysLeft  = $clienteData['dias_restantes'] ?? 0;

        $user_id = (int)($_SESSION['user_id'] ?? 0);

        // 1. Total de asistencias
        $total = $attendanceModel->countTotal($user_id);

        // 2. Obtener Logros
        $achievements = $achievementModel->getAll();

        // 3. Calcular Progreso Actual
        $next = null;
        foreach ($achievements as $a) {
            if ($total < $a['goal']) { $next = $a; break; }
        }

        $progress = 100;
        $prevGoal = 0;
        $nextGoalLabel = "¡Eres una leyenda!";
        $remaining = 0;

        if ($next) {
            foreach ($achievements as $a) {
                if ($a['goal'] < $next['goal']) $prevGoal = $a['goal'];
            }

            $range = max(1, $next['goal'] - $prevGoal);
            $currentVal = $total - $prevGoal;
            $progress = max(0, min(100, (int)round(($currentVal / $range) * 100)));

            $nextGoalLabel = $next['label'];
            $remaining = $next['goal'] - $total;
        }

        // Helper
        function h($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }

        // Renderizar vista
        include __DIR__ . '/../views/logros/index.php';
    }
}
