<?php
// cliente/models/MemberModel.php
// Modelo: Todas las consultas relacionadas con members

class MemberModel
{
    private mysqli $db;

    public function __construct(mysqli $db)
    {
        $this->db = $db;
    }

    /**
     * Busca un miembro por username para login.
     * Retorna [user_id, password, must_change_password] o null.
     */
    public function findByUsername(string $username): ?array
{
    $stmt = $this->db->prepare("
        SELECT user_id, password, must_change_password, fullname
        FROM members
        WHERE username = ?
        LIMIT 1
    ");
    if (!$stmt) return null;

    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows !== 1) {
        $stmt->close();
        return null;
    }

    $stmt->bind_result($userId, $passwordHash, $mustChangePassword, $fullname);
    $stmt->fetch();
    $stmt->close();

    return [
        'user_id'              => $userId,
        'password'             => $passwordHash,
        'must_change_password' => $mustChangePassword,
        'fullname'             => $fullname
    ];
}

    /**
     * Busca un miembro por username + CI (para forgot password).
     */
    public function findByUsernameAndCI(string $username, string $ci): ?int
    {
        $stmt = $this->db->prepare("
            SELECT user_id 
            FROM members 
            WHERE username = ? AND ci = ?
            LIMIT 1
        ");
        $stmt->bind_param("ss", $username, $ci);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows !== 1) {
            $stmt->close();
            return null;
        }

        $stmt->bind_result($userId);
        $stmt->fetch();
        $stmt->close();

        return $userId;
    }

    /**
     * Actualiza la contraseña de un miembro.
     */
    public function updatePassword(int $userId, string $newHash, int $mustChange = 0): bool
    {
        $stmt = $this->db->prepare("
            UPDATE members
            SET password = ?, must_change_password = ?
            WHERE user_id = ?
            LIMIT 1
        ");
        $stmt->bind_param("sii", $newHash, $mustChange, $userId);
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }

    /**
     * Verifica si el usuario debe cambiar contraseña.
     */
    public function mustChangePassword(int $userId): bool
    {
        $stmt = $this->db->prepare("
            SELECT must_change_password 
            FROM members 
            WHERE user_id = ?
            LIMIT 1
        ");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $stmt->bind_result($mustChange);
        $stmt->fetch();
        $stmt->close();

        return (int)$mustChange === 1;
    }

    /**
     * Obtiene datos básicos del miembro (para cliente_data).
     */
    public function getBasicData(int $userId): ?array
    {
        $stmt = $this->db->prepare("
            SELECT user_id, fullname, correo
            FROM members
            WHERE user_id = ?
            LIMIT 1
        ");
        if (!$stmt) return null;

        $stmt->bind_param('i', $userId);
        $stmt->execute();
        $cliente = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        return $cliente ?: null;
    }

    /**
     * Obtiene datos completos del miembro (para perfil).
     */
    public function getFullProfile(int $userId): array
    {
        $stmt = $this->db->prepare("
            SELECT fullname, username, gender, ci, contact, dor, correo 
            FROM members 
            WHERE user_id = ? 
            LIMIT 1
        ");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $member = $stmt->get_result()->fetch_assoc() ?: [];
        $stmt->close();
        return $member;
    }

    /**
     * Obtiene datos para el informe del miembro.
     */
    public function getForReport(int $userId): ?array
    {
        $stmt = $this->db->prepare("
            SELECT user_id, fullname, ci, contact, attendance_count, dor, username 
            FROM members 
            WHERE user_id = ? 
            LIMIT 1
        ");
        $stmt->bind_param('i', $userId);
        $stmt->execute();
        $member = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        return $member;
    }

    /**
     * Obtiene el estado del recordatorio del miembro.
     */
    public function getReminder(int $userId): string
    {
        $reminder = '0';
        $stmt = $this->db->prepare("SELECT reminder FROM members WHERE user_id = ? LIMIT 1");
        if (!$stmt) return $reminder;

        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $res = $stmt->get_result();
        if ($row = $res->fetch_assoc()) {
            $raw = strtolower(trim((string)$row['reminder']));
            if (in_array($raw, ['1','true','on','yes','si'])) $reminder = '1';
        }
        $stmt->close();
        return $reminder;
    }

    /**
     * Actualiza el estado del recordatorio.
     */
    public function updateReminder(int $userId, int $value): bool
    {
        $stmt = $this->db->prepare("UPDATE members SET reminder = ? WHERE user_id = ?");
        if (!$stmt) return false;

        $stmt->bind_param("ii", $value, $userId);
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }
}
