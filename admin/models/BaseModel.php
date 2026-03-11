<?php
/**
 * BaseModel — Modelo base MVC
 */
class BaseModel
{
    protected mysqli $db;

    public function __construct(mysqli $db)
    {
        $this->db = $db;
    }

    protected function fetchAll(string $sql, string $types = '', ...$params): array
    {
        $stmt = $this->db->prepare($sql);
        if (!$stmt) return [];
        if ($types !== '' && count($params) > 0) $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $res = $stmt->get_result();
        $rows = $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
        $stmt->close();
        return $rows;
    }

    protected function fetchOne(string $sql, string $types = '', ...$params): ?array
    {
        $stmt = $this->db->prepare($sql);
        if (!$stmt) return null;
        if ($types !== '' && count($params) > 0) $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $res = $stmt->get_result();
        $row = $res ? $res->fetch_assoc() : null;
        $stmt->close();
        return $row;
    }

    protected function fetchScalar(string $sql, string $types = '', ...$params)
    {
        $row = $this->fetchOne($sql, $types, ...$params);
        return $row ? reset($row) : null;
    }

    protected function execute(string $sql, string $types = '', ...$params): bool
    {
        $stmt = $this->db->prepare($sql);
        if (!$stmt) return false;
        if ($types !== '' && count($params) > 0) $stmt->bind_param($types, ...$params);
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }

    protected function insert(string $sql, string $types = '', ...$params): ?int
    {
        $stmt = $this->db->prepare($sql);
        if (!$stmt) return null;
        if ($types !== '' && count($params) > 0) $stmt->bind_param($types, ...$params);
        if ($stmt->execute()) {
            $id = $stmt->insert_id;
            $stmt->close();
            return $id;
        }
        $stmt->close();
        return null;
    }

    protected function query(string $sql): ?mysqli_result
    {
        return $this->db->query($sql) ?: null;
    }

    protected function error(): string { return $this->db->error; }
    protected function lastId(): int { return $this->db->insert_id; }
}
