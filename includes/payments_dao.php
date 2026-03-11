<?php
// includes/payments_dao.php
// DAO de pagos (mysqli)
// Inserciones y consultas alineadas al esquema actual de `payments`
// NO usa `concept`

/* =========================
   INSERTAR PAGO (OPCIONAL)
   Nota: el flujo principal usa registrar_pago.php
========================= */
if (!function_exists('pmt_insert')) {
  function pmt_insert(mysqli $conn, array $data) {

    $sql = "
      INSERT INTO payments
        (user_id, paid_date, amount, plan_id, productos, status, method, created_at)
      VALUES
        (?, ?, ?, ?, ?, ?, ?, NOW())
    ";

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
      return ['ok' => false, 'error' => $conn->error];
    }

    $user_id   = (int)($data['user_id'] ?? 0);
    $paid_date = $data['paid_date'] ?? date('Y-m-d');
    $amount    = (float)($data['amount'] ?? 0);
    $plan_id   = !empty($data['plan_id']) ? (int)$data['plan_id'] : null;
    $productos = $data['productos'] ?? '[]';
    $status    = $data['status'] ?? 'pagado';
    $method    = $data['method'] ?? 'Efectivo';

    if ($user_id <= 0 || $amount <= 0) {
      return ['ok' => false, 'error' => 'Datos de pago inválidos'];
    }

    $stmt->bind_param(
      'isdisss',
      $user_id,
      $paid_date,
      $amount,
      $plan_id,
      $productos,
      $status,
      $method
    );

    $ok  = $stmt->execute();
    $err = $ok ? null : $stmt->error;
    $id  = $ok ? $stmt->insert_id : null;
    $stmt->close();

    return ['ok' => $ok, 'id' => $id, 'error' => $err];
  }
}

/* =========================
   PAGOS POR USUARIO (HISTORIAL)
========================= */
if (!function_exists('pmt_by_user')) {
  function pmt_by_user(mysqli $conn, int $user_id) {

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

    $stmt = $conn->prepare($sql);
    if (!$stmt) return [];

    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $res  = $stmt->get_result();
    $rows = $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
    $stmt->close();

    return $rows;
  }
}

/* =========================
   LISTADO GENERAL (ADMIN)
========================= */
if (!function_exists('pmt_list')) {
  function pmt_list(mysqli $conn, string $from, string $to, string $q = "") {

    $like = "%" . $q . "%";

    $sql = "
      SELECT
        p.*,
        m.fullname,
        m.ci
      FROM payments p
      JOIN members m ON m.user_id = p.user_id
      WHERE p.paid_date BETWEEN ? AND ?
        AND (? = '' OR m.fullname LIKE ? OR m.ci LIKE ?)
      ORDER BY p.paid_date DESC, p.id DESC
    ";

    $stmt = $conn->prepare($sql);
    if (!$stmt) return [];

    $stmt->bind_param("sssss", $from, $to, $q, $like, $like);
    $stmt->execute();
    $res  = $stmt->get_result();
    $rows = $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
    $stmt->close();

    return $rows;
  }
}
