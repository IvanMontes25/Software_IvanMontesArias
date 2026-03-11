<?php
// cliente/models/AchievementModel.php
// Modelo: Consultas de logros

class AchievementModel
{
    private mysqli $db;

    public function __construct(mysqli $db)
    {
        $this->db = $db;
    }

    /**
     * Obtiene todos los logros activos ordenados por meta.
     */
    public function getAll(): array
    {
        $achievements = [];
        $stmt = $this->db->prepare("
            SELECT nombre, meta_asistencias, descuento_porcentaje, icono_fa 
            FROM logros 
            WHERE activo = 1 
            ORDER BY meta_asistencias ASC
        ");
        if (!$stmt)
            return [];

        $stmt->execute();
        $res = $stmt->get_result();
        while ($row = $res->fetch_assoc()) {
            $achievements[] = [
                'goal' => (int) $row['meta_asistencias'],
                'label' => $row['nombre'],
                'icon' => $row['icono_fa'] ?: 'fa-medal',
                'discount' => (float) $row['descuento_porcentaje'],
            ];
        }
        $stmt->close();

        return $achievements;
    }
}
