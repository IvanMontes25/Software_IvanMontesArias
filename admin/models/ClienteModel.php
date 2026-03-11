<?php
require_once __DIR__ . '/BaseModel.php';

class ClienteModel extends BaseModel
{
    public function listarConMembresia(): array {
        $sql = "SELECT m.user_id, m.fullname, m.username, m.gender, m.contact, m.dor, m.ci,
                pl.nombre AS plan_nombre, p.start_date, p.paid_date, pl.duracion_dias
                FROM members m
                LEFT JOIN payments p ON p.id = (SELECT id FROM payments WHERE user_id = m.user_id AND status = 'pagado' AND plan_id IS NOT NULL ORDER BY id DESC LIMIT 1)
                LEFT JOIN planes pl ON pl.id = p.plan_id ORDER BY m.fullname";
        $res = $this->db->query($sql); $rows = [];
        while ($r = $res->fetch_assoc()) $rows[] = $r;
        return $rows;
    }
    public function getById(int $id): ?array {
        return $this->fetchOne("SELECT * FROM members WHERE user_id = ? LIMIT 1", 'i', $id);
    }
    public function getForEdit(int $id): ?array {
        return $this->fetchOne("SELECT user_id, fullname, username, gender, contact, ci, correo, dor FROM members WHERE user_id = ? LIMIT 1", 'i', $id);
    }
    public function checkDuplicates(array $fields, ?int $excludeId = null): array {
        $errors = [];
        foreach ($fields as $col => $info) {
            $sql = "SELECT user_id, fullname FROM members WHERE {$col} = ?";
            $types = 's'; $params = [$info['value']];
            if ($excludeId) { $sql .= " AND user_id != ?"; $types .= 'i'; $params[] = $excludeId; }
            $row = $this->fetchOne($sql . " LIMIT 1", $types, ...$params);
            if ($row) $errors[] = $info['label'] . ' "' . $info['value'] . '" ya pertenece a: ' . ($row['fullname'] ?? 'otro cliente');
        }
        return $errors;
    }
    public function crear(array $d): ?int {
        $hash = password_hash($d['password'], PASSWORD_DEFAULT);
        return $this->insert("INSERT INTO members (fullname, username, password, dor, gender, ci, contact, correo) VALUES (?,?,?,?,?,?,?,?)",
            'ssssssss', $d['fullname'], $d['username'], $hash, $d['dor'], $d['gender'], $d['ci'], $d['contact'], $d['correo']);
    }
    public function actualizar(int $id, array $d): bool {
        return $this->execute("UPDATE members SET fullname=?, username=?, gender=?, ci=?, contact=?, correo=? WHERE user_id=?",
            'ssssssi', $d['fullname'], $d['username'], $d['gender'], $d['ci'], $d['contact'], $d['correo'], $id);
    }
    public function eliminar(int $id): bool {
        return $this->execute("DELETE FROM members WHERE user_id = ?", 'i', $id);
    }
    public function exists(int $id): bool {
        return $this->fetchOne("SELECT user_id FROM members WHERE user_id = ? LIMIT 1", 'i', $id) !== null;
    }
    public function getNombre(int $id): string {
        return $this->fetchScalar("SELECT fullname FROM members WHERE user_id = ? LIMIT 1", 'i', $id) ?? 'Cliente';
    }
    public function checkFieldDuplicate(string $field, string $value): ?array {
        $allowed = ['username', 'ci', 'correo', 'contact'];
        if (!in_array($field, $allowed, true) || $value === '') return null;
        return $this->fetchOne("SELECT user_id, fullname FROM members WHERE {$field} = ? LIMIT 1", 's', $value);
    }
}
