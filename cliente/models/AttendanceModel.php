<?php
// cliente/models/AttendanceModel.php
// Modelo: Consultas de asistencia

class AttendanceModel
{
    private mysqli $db;

    public function __construct(mysqli $db)
    {
        $this->db = $db;
    }

    /**
     * Obtiene los días con asistencia (para el calendario).
     * Retorna [ 'YYYY-MM-DD' => 'HH:MM', ... ]
     */
    public function getPresentDays(int $userId, string $startDate, string $endDate): array
    {
        $presentDays = [];
        $sql = "SELECT curr_date, MIN(curr_time) AS first_time FROM attendance WHERE user_id = ? AND curr_date BETWEEN ? AND ? GROUP BY curr_date";

        if ($stmt = $this->db->prepare($sql)) {
            $stmt->bind_param('iss', $userId, $startDate, $endDate);
            $stmt->execute();
            $res = $stmt->get_result();
            while ($r = $res->fetch_assoc()) {
                $presentDays[$r['curr_date']] = date('H:i', strtotime((string) $r['first_time']));
            }
            $stmt->close();
        }

        return $presentDays;
    }

    /**
     * Obtiene la lista completa de asistencias para un rango de fechas.
     */
    public function getList(int $userId, string $startDate, string $endDate): array
    {
        $list = [];
        $sql = "SELECT curr_date, curr_time FROM attendance WHERE user_id = ? AND curr_date BETWEEN ? AND ? ORDER BY curr_date DESC, curr_time DESC";

        if ($stmt = $this->db->prepare($sql)) {
            $stmt->bind_param('iss', $userId, $startDate, $endDate);
            $stmt->execute();
            $list = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
            $stmt->close();
        }

        return $list;
    }

    /**
     * Cuenta el total de asistencias de un usuario.
     */
    public function countTotal(int $userId): int
    {
        $total = 0;
        $stmt = $this->db->prepare("SELECT COUNT(*) AS cnt FROM attendance WHERE user_id = ?");
        if ($stmt) {
            $stmt->bind_param("i", $userId);
            $stmt->execute();
            $total = (int) ($stmt->get_result()->fetch_assoc()['cnt'] ?? 0);
            $stmt->close();
        }
        return $total;
    }

    /**
     * Cuenta asistencias para un usuario (alias para perfil).
     */
    public function countByUser(int $userId): int
    {
        return $this->countTotal($userId);
    }
}
