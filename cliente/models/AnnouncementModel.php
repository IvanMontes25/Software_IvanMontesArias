<?php
// cliente/models/AnnouncementModel.php
// Modelo: Consultas de publicaciones/anuncios

class AnnouncementModel
{
    private mysqli $db;

    public function __construct(mysqli $db)
    {
        $this->db = $db;
    }

    /**
     * Obtiene todas las publicaciones ordenadas por fecha desc.
     */
    public function getAll(): array
    {
        $announcements = [];
        $stmt = $this->db->prepare("
            SELECT id, message, date, images_json 
            FROM announcements 
            ORDER BY date DESC, id DESC
        ");
        if (!$stmt)
            return [];

        $stmt->execute();
        $res = $stmt->get_result();
        while ($row = $res->fetch_assoc()) {
            $announcements[] = [
                'id' => (int) $row['id'],
                'message' => (string) ($row['message'] ?? ''),
                'date' => (string) ($row['date'] ?? ''),
                'images' => (string) ($row['images_json'] ?? ''),
            ];
        }
        $stmt->close();

        return $announcements;
    }
}
