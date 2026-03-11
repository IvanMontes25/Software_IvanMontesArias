<?php
require_once __DIR__ . '/BaseModel.php';

class DashboardModel extends BaseModel
{
    public function ingresosMes(): float {
        $q = $this->db->query("SELECT COALESCE(SUM(amount),0) AS total FROM payments WHERE status='pagado' AND paid_date >= DATE_FORMAT(CURDATE(),'%Y-%m-01')");
        return (float)($q->fetch_assoc()['total'] ?? 0);
    }
    public function ingresosMesAnterior(): float {
        $q = $this->db->query("SELECT COALESCE(SUM(amount),0) AS total FROM payments WHERE status='pagado' AND paid_date >= DATE_FORMAT(DATE_SUB(CURDATE(), INTERVAL 1 MONTH),'%Y-%m-01') AND paid_date < DATE_FORMAT(CURDATE(),'%Y-%m-01')");
        return (float)($q->fetch_assoc()['total'] ?? 0);
    }
    public function totalClientes(): int {
        return (int)$this->db->query("SELECT COUNT(*) AS t FROM members")->fetch_assoc()['t'];
    }
    public function nuevosMes(): int {
        return (int)$this->db->query("SELECT COUNT(*) AS t FROM members WHERE dor >= DATE_FORMAT(CURDATE(),'%Y-%m-01')")->fetch_assoc()['t'];
    }
    public function membresiasActivas(): int {
        return (int)$this->db->query("SELECT COUNT(*) AS t FROM members m INNER JOIN payments p ON p.id=(SELECT p2.id FROM payments p2 WHERE p2.user_id=m.user_id AND p2.status='pagado' AND p2.plan_id IS NOT NULL ORDER BY p2.id DESC LIMIT 1) INNER JOIN planes pl ON pl.id=p.plan_id WHERE DATE_ADD(COALESCE(p.start_date,p.paid_date),INTERVAL pl.duracion_dias DAY)>=CURDATE()")->fetch_assoc()['t'];
    }
    public function porVencer(): int {
        return (int)$this->db->query("SELECT COUNT(*) AS t FROM members m INNER JOIN payments p ON p.id=(SELECT p2.id FROM payments p2 WHERE p2.user_id=m.user_id AND p2.status='pagado' AND p2.plan_id IS NOT NULL ORDER BY p2.id DESC LIMIT 1) INNER JOIN planes pl ON pl.id=p.plan_id WHERE DATEDIFF(DATE_ADD(COALESCE(p.start_date,p.paid_date),INTERVAL pl.duracion_dias DAY),CURDATE()) BETWEEN 0 AND 7")->fetch_assoc()['t'];
    }
    public function pagosHoy(): int {
        return (int)$this->db->query("SELECT COUNT(*) AS t FROM payments WHERE status='pagado' AND paid_date=CURDATE()")->fetch_assoc()['t'];
    }
    public function ingresosHoy(): float {
        return (float)$this->db->query("SELECT COALESCE(SUM(amount),0) AS t FROM payments WHERE status='pagado' AND paid_date=CURDATE()")->fetch_assoc()['t'];
    }
    public function ingresosMensuales(): array {
        $rows = []; $r = $this->db->query("SELECT DATE_FORMAT(paid_date,'%Y-%m') AS mes, SUM(amount) AS total FROM payments WHERE status='pagado' AND paid_date>=DATE_SUB(CURDATE(),INTERVAL 12 MONTH) GROUP BY mes ORDER BY mes");
        while ($row = $r->fetch_assoc()) $rows[] = $row; return $rows;
    }
    public function ingresosDiarios(): array {
        $rows = []; $r = $this->db->query("SELECT paid_date AS dia, SUM(amount) AS total FROM payments WHERE status='pagado' AND paid_date>=DATE_SUB(CURDATE(),INTERVAL 30 DAY) GROUP BY paid_date ORDER BY paid_date");
        while ($row = $r->fetch_assoc()) $rows[] = $row; return $rows;
    }
    public function planesVendidos(): array {
        $rows = []; $r = $this->db->query("SELECT pl.nombre, COUNT(*) AS ventas FROM payments p INNER JOIN planes pl ON pl.id=p.plan_id WHERE p.status='pagado' AND p.plan_id IS NOT NULL GROUP BY p.plan_id ORDER BY ventas DESC");
        while ($row = $r->fetch_assoc()) $rows[] = $row; return $rows;
    }
    public function metodosPago(): array {
        $rows = []; $r = $this->db->query("SELECT method, COUNT(*) AS cantidad FROM payments WHERE status='pagado' GROUP BY method ORDER BY cantidad DESC");
        while ($row = $r->fetch_assoc()) $rows[] = $row; return $rows;
    }
    public function pagosDiaSemana(): array {
        $rows = []; $r = $this->db->query("SELECT DAYOFWEEK(paid_date) AS dow, COUNT(*) AS pagos, SUM(amount) AS monto FROM payments WHERE status='pagado' GROUP BY dow ORDER BY dow");
        while ($row = $r->fetch_assoc()) $rows[] = $row; return $rows;
    }
    public function segmentos(): array {
        $rows = []; $r = $this->db->query("SELECT segmento, COUNT(*) AS cantidad FROM (SELECT m.user_id, CASE WHEN COUNT(p.id)>=4 AND DATEDIFF(CURDATE(),MAX(p.paid_date))<=35 THEN 'VIP' WHEN COUNT(p.id) BETWEEN 2 AND 3 AND DATEDIFF(CURDATE(),MAX(p.paid_date))<=40 THEN 'Regular' WHEN COUNT(p.id)=1 AND DATEDIFF(CURDATE(),MAX(p.paid_date))<=35 THEN 'Nuevo' WHEN DATEDIFF(CURDATE(),MAX(p.paid_date)) BETWEEN 36 AND 90 THEN 'Dormido' WHEN MAX(p.paid_date) IS NULL THEN 'Sin pagos' ELSE 'Perdido' END AS segmento FROM members m LEFT JOIN payments p ON p.user_id=m.user_id AND p.status='pagado' GROUP BY m.user_id) sub GROUP BY segmento ORDER BY FIELD(segmento,'VIP','Regular','Nuevo','Dormido','Perdido','Sin pagos')");
        while ($row = $r->fetch_assoc()) $rows[] = $row; return $rows;
    }
    public function clientesRiesgo(): array {
        $rows = []; $r = $this->db->query("SELECT m.fullname, m.contact, m.correo, COUNT(p.id) AS total_pagos, MAX(p.paid_date) AS ultimo_pago, DATEDIFF(CURDATE(),MAX(p.paid_date)) AS dias_sin_pago, CASE WHEN COUNT(p.id)=1 AND DATEDIFF(CURDATE(),MAX(p.paid_date))>25 THEN 90 WHEN COUNT(p.id)=1 AND DATEDIFF(CURDATE(),MAX(p.paid_date))>20 THEN 70 WHEN COUNT(p.id)>=2 AND DATEDIFF(CURDATE(),MAX(p.paid_date))>35 THEN 80 WHEN COUNT(p.id)>=2 AND DATEDIFF(CURDATE(),MAX(p.paid_date))>25 THEN 50 ELSE 10 END AS riesgo FROM members m LEFT JOIN payments p ON p.user_id=m.user_id AND p.status='pagado' GROUP BY m.user_id HAVING riesgo>=50 ORDER BY riesgo DESC LIMIT 10");
        while ($row = $r->fetch_assoc()) $rows[] = $row; return $rows;
    }
    public function ultimosPagos(int $limit = 8): array {
        $rows = []; $r = $this->db->query("SELECT m.fullname, p.amount, p.method, p.paid_date, COALESCE(pl.nombre,'Productos') AS plan_nombre FROM payments p INNER JOIN members m ON m.user_id=p.user_id LEFT JOIN planes pl ON pl.id=p.plan_id WHERE p.status='pagado' ORDER BY p.id DESC LIMIT $limit");
        while ($row = $r->fetch_assoc()) $rows[] = $row; return $rows;
    }
    public function nuevosPorMes(): array {
        $rows = []; $r = $this->db->query("SELECT DATE_FORMAT(dor,'%Y-%m') AS mes, COUNT(*) AS total FROM members WHERE dor>=DATE_SUB(CURDATE(),INTERVAL 6 MONTH) GROUP BY mes ORDER BY mes");
        while ($row = $r->fetch_assoc()) $rows[] = $row; return $rows;
    }
    public function tasaRetencion(): int {
        $actual = (int)$this->db->query("SELECT COUNT(DISTINCT user_id) AS t FROM payments WHERE status='pagado' AND plan_id IS NOT NULL AND paid_date>=DATE_FORMAT(CURDATE(),'%Y-%m-01')")->fetch_assoc()['t'];
        $anterior = (int)$this->db->query("SELECT COUNT(DISTINCT user_id) AS t FROM payments WHERE status='pagado' AND plan_id IS NOT NULL AND paid_date>=DATE_FORMAT(DATE_SUB(CURDATE(),INTERVAL 1 MONTH),'%Y-%m-01') AND paid_date<DATE_FORMAT(CURDATE(),'%Y-%m-01')")->fetch_assoc()['t'];
        return $anterior > 0 ? (int)round(($actual / $anterior) * 100) : 0;
    }
}
