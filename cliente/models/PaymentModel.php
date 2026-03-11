<?php
// cliente/models/PaymentModel.php
// Modelo: Consultas de pagos

class PaymentModel
{
    private mysqli $db;

    public function __construct(mysqli $db)
    {
        $this->db = $db;
    }

    /**
     * Historial de pagos por usuario (usa pmt_by_user del DAO).
     */
    public function getByUser(int $userId): array
    {
        $sql = "
            SELECT
                p.id,
                p.user_id,
                p.paid_date,
                p.amount,
                p.plan_id,
                pl.nombre AS plan_nombre,
                p.status,
                p.method,
                p.productos,
                p.created_at
            FROM payments p
            LEFT JOIN planes pl ON pl.id = p.plan_id
            WHERE p.user_id = ?
            ORDER BY p.paid_date DESC, p.id DESC
        ";

        $stmt = $this->db->prepare($sql);
        if (!$stmt) return [];

        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $res  = $stmt->get_result();
        $rows = $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
        $stmt->close();

        return $rows;
    }

    /**
     * Último pago de un usuario.
     */
    public function getLastPayment(int $userId): ?array
    {
        $stmt = $this->db->prepare("
            SELECT id, paid_date, amount, plan_id, status, method 
            FROM payments 
            WHERE user_id = ? 
            ORDER BY paid_date DESC, id DESC 
            LIMIT 1
        ");
        if (!$stmt) return null;

        $stmt->bind_param('i', $userId);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        return $row;
    }

    /**
     * Último pago asociado a una membresía específica.
     */
    public function getLastMembershipPayment(int $userId, int $planId): ?array
    {
        $stmt = $this->db->prepare("
            SELECT amount, paid_date 
            FROM payments
            WHERE user_id = ?
              AND plan_id = ?
              AND status = 'pagado'
            ORDER BY id DESC
            LIMIT 1
        ");
        if (!$stmt) return null;

        $stmt->bind_param("ii", $userId, $planId);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        return $row;
    }

    /**
     * Último monto pagado por un usuario (para perfil).
     */
    public function getLastAmount(int $userId): ?float
    {
        $stmt = $this->db->prepare("
            SELECT amount FROM payments 
            WHERE user_id = ? AND status = 'pagado' 
            ORDER BY id DESC LIMIT 1
        ");
        if (!$stmt) return null;

        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        return $row ? (float)$row['amount'] : null;
    }

    /**
     * Obtiene un recibo por ID + validación de usuario.
     */
    public function getReceipt(int $paymentId, int $userId): ?array
    {
        $sql = "SELECT 
                    p.id, 
                    p.paid_date, 
                    p.amount, 
                    p.method, 
                    p.status AS p_status,
                    p.plan_id,
                    p.productos,
                    m.fullname, 
                    m.ci, 
                    m.correo,
                    pl.nombre AS servicio, 
                    pl.duracion_dias
                FROM payments p
                JOIN members m ON m.user_id = p.user_id
                LEFT JOIN planes pl ON pl.id = p.plan_id
                WHERE p.id = ? AND p.user_id = ?";

        $stmt = $this->db->prepare($sql);
        if (!$stmt) return null;

        $stmt->bind_param('ii', $paymentId, $userId);
        $stmt->execute();
        $res = $stmt->get_result();
        $pay = $res ? $res->fetch_assoc() : null;
        $stmt->close();

        return $pay;
    }
}
