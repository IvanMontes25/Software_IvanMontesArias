<?php
// cliente/models/ReminderModel.php
// Modelo: Consultas de recordatorios de pago

class ReminderModel
{
    private mysqli $db;

    public function __construct(mysqli $db)
    {
        $this->db = $db;
    }

    /**
     * Obtiene el historial de alertas de pago de un usuario.
     */
    public function getByUser(int $userId, int $limit = 20): array
    {
        $reminders = [];
        $stmt = $this->db->prepare("
            SELECT id, message, created_at 
            FROM recordatorios_pago 
            WHERE user_id = ? 
            ORDER BY created_at DESC, id DESC 
            LIMIT ?
        ");
        if (!$stmt) return [];

        $stmt->bind_param("ii", $userId, $limit);
        $stmt->execute();
        $res = $stmt->get_result();
        while ($row = $res->fetch_assoc()) {
            $reminders[] = $row;
        }
        $stmt->close();

        return $reminders;
    }

    /**
     * Obtiene la última alerta por tipo.
     */
    public function getLastByType(int $userId, string $tipo): ?array
    {
        $stmt = $this->db->prepare("
            SELECT message, created_at, sent_channel
            FROM recordatorios_pago
            WHERE user_id = ?
            AND tipo_alerta = ?
            ORDER BY created_at DESC
            LIMIT 1
        ");
        if (!$stmt) return null;

        $stmt->bind_param("is", $userId, $tipo);
        $stmt->execute();
        $res = $stmt->get_result();
        $row = $res->fetch_assoc();
        $stmt->close();

        return $row ?: null;
    }

    /**
     * Obtiene la última alerta de membresía vencida.
     */
    public function getLastExpiredAlert(int $userId): ?array
    {
        return $this->getLastByType($userId, 'vencida');
    }
}
