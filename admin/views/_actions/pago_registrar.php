<?php
// [MVC] bootstrap loaded by entry point
// [MVC] auth loaded by entry point
require_once __DIR__ . '/../includes/membership_helper.php';
require_once __DIR__ . '/../core/n8n.php';

if (!$db instanceof mysqli) {
  http_response_code(500);
  die('No hay conexión a la base de datos');
}

/* ================= POST ONLY ================= */
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  http_response_code(405);
  die('Método no permitido');
}

/* ================= CSRF (compatible) =================
   Soporta:
   - POST csrf_token con SESSION csrf_token
   - POST csrf con SESSION csrf
*/
if (session_status() !== PHP_SESSION_ACTIVE) session_start();

$posted_token  = $_POST['csrf_token'] ?? '';
$session_token = $_SESSION['csrf_token'] ?? '';

if (empty($session_token) || empty($posted_token) || !hash_equals($session_token, $posted_token)) {
  http_response_code(403);
  die('Token CSRF inválido');
}


/* ================= ENTRADAS ================= */
$user_id       = (int)($_POST['user_id'] ?? 0);
$stmt = $db->prepare("SELECT user_id FROM members WHERE user_id = ? LIMIT 1");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$res = $stmt->get_result();
$stmt->close();

if ($res->num_rows === 0) {
  header('Location: clientes.php?e=' . urlencode('Usuario no existe'));
  exit;
}

$paid_date_in  = trim((string)($_POST['paid_date'] ?? ''));
$method        = trim((string)($_POST['method'] ?? 'Efectivo'));
$plan_id_raw = $_POST['plan_id'] ?? '';
$plan_id = ctype_digit($plan_id_raw) && (int)$plan_id_raw > 0
    ? (int)$plan_id_raw
    : null;

$productos_raw = trim((string)($_POST['productos'] ?? '[]'));

/* ================= VALIDACIONES ================= */
if ($user_id <= 0) {
  header('Location: clientes.php?e=' . urlencode('ID inválido'));
  exit;
}

/* Fecha: YYYY-MM-DD; si no viene, usa hoy */
$paid_date = $paid_date_in !== '' ? $paid_date_in : date('Y-m-d');
if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $paid_date)) {
  $paid_date = date('Y-m-d');
}

// ================= METODO DE PAGO (NORMALIZADO) =================
$method_raw = trim((string)($_POST['method'] ?? ''));

// Normaliza: quita espacios y compara en mayúsculas
$method_key = strtoupper(preg_replace('/\s+/', ' ', $method_raw));

// Mapeo tolerante (por si llega "qr", "Qr", "TRANSFERENCIA", etc.)
$map = [
  'EFECTIVO'      => 'Efectivo',
  'CASH'          => 'Efectivo',

  'QR'            => 'QR',
  'CODIGO QR'     => 'QR',
  'CÓDIGO QR'     => 'QR',

  'TRANSFERENCIA' => 'Transferencia',
  'BANCO'         => 'Transferencia',
  'TRANSF'        => 'Transferencia',
  'TRANSFERENCIA BANCARIA' => 'Transferencia',

  'TARJETA'       => 'Tarjeta',
  'CARD'          => 'Tarjeta',

  'OTRO'          => 'Otro',
];

// Aplica mapeo o fallback seguro
$method = $map[$method_key] ?? 'Efectivo';

/* ================= PRODUCTOS ================= */
$productos = [];
$total_productos = 0.0;

/* Evitar payload enorme */
if (strlen($productos_raw) > 20000) { // ~20KB, ajusta si quieres
  header('Location: pago_cliente.php?id='.$user_id.'&e='.urlencode('Productos demasiado grandes'));
  exit;
}

if ($productos_raw !== '' && $productos_raw !== '[]') {
  $decoded = json_decode($productos_raw, true);
  if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
    foreach ($decoded as $p) {
      $nombre = trim((string)($p['nombre'] ?? ''));
      $precio = (isset($p['precio']) && is_numeric($p['precio'])) ? (float)$p['precio'] : 0.0;

      // Normaliza: no negativos, no vacíos
      if ($nombre !== '' && $precio > 0) {
        // Redondeo a 2 decimales por seguridad financiera básica
        $precio = round($precio, 2);

        $productos[] = ['nombre' => $nombre, 'precio' => $precio];
        $total_productos += $precio;
      }
    }
  }
}

$productos_json = json_encode($productos, JSON_UNESCAPED_UNICODE);
if ($productos_json === false) {
  $productos_json = '[]';
  $total_productos = 0.0;
}

/* ================= PLAN ================= */
$precio_plan = 0.0;
$duracion_dias = 0;

if (!is_null($plan_id)) {


  $stmt = $db->prepare("
  SELECT precio_base, duracion_dias
  FROM planes
  WHERE id = ? AND estado = 1
  LIMIT 1
");


  if (!$stmt) {
    header('Location: pago_cliente.php?id='.$user_id.'&e='.urlencode('Error en plan'));
    exit;
  }

  $stmt->bind_param("i", $plan_id);
  $stmt->execute();
  $plan = $stmt->get_result()->fetch_assoc();
  $stmt->close();

  if (!$plan) {
    header('Location: pago_cliente.php?id='.$user_id.'&e='.urlencode('Plan inválido'));
    exit;
  }

  $precio_plan   = round((float)$plan['precio_base'], 2);
  $duracion_dias = (int)$plan['duracion_dias'];

  if ($precio_plan < 0) $precio_plan = 0.0;
  if ($duracion_dias < 0) $duracion_dias = 0;
}

/* ================= TOTAL FINAL ================= */
$amount = round($precio_plan + $total_productos, 2);

if ($amount <= 0) {
  header('Location: pago_cliente.php?id='.$user_id.'&e='.urlencode('Pago vacío'));
  exit;
}

try {

  $db->begin_transaction();

  /* ================= CALCULAR START_DATE =================
     Regla:
     - Por defecto: start_date = paid_date
     - Si hay plan y el usuario tiene membresía activa: start_date = fin actual (robusto)
  */
  $start_date = new DateTime($paid_date);

if (!is_null($plan_id)) {


    $ultima = membership_last($db, $user_id);

    if ($ultima && function_exists('membership_can_access') && membership_can_access($ultima)) {

      // end_date puede venir como DateTime o string; lo hacemos robusto
      $end = $ultima['end_date'] ?? ($ultima['fecha_fin'] ?? ($ultima['end_date_str'] ?? null));

      if ($end instanceof DateTime) {
        $end_dt = clone $end;
      } elseif (is_string($end) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $end)) {
        $end_dt = new DateTime($end);
      } else {
        $end_dt = null;
      }

      // Si tenemos end_dt, usamos la fecha mayor entre paid_date y end_dt
      if ($end_dt instanceof DateTime) {
        if ($end_dt > $start_date) {
          $start_date = $end_dt;
        }
      }
    }
  }

  $start_str = $start_date->format('Y-m-d');

  /* ================= INSERT PAYMENT ================= */
  $stmt = $db->prepare("
    INSERT INTO payments
      (user_id, paid_date, start_date, amount, plan_id, productos, status, method, created_at)
    VALUES
      (?, ?, ?, ?, ?, ?, 'pagado', ?, NOW())
  ");

  if (!$stmt) {
    throw new Exception('Error al preparar el pago');
  }
if ($plan_id === null) {
    $plan_id = null;
}

  $stmt->bind_param(
    'issdiss',
    $user_id,        // i
    $paid_date,      // s
    $start_str,      // s
    $amount,         // d
    $plan_id,        // i
    $productos_json, // s
    $method          // s
  );

  $stmt->execute();
  $payment_id = (int)$stmt->insert_id;
  $stmt->close();

  if ($payment_id <= 0) {
    throw new Exception('No se pudo registrar el pago');
  }

  /* ================= LIMPIAR RECORDATORIOS ================= */
  try {
    $stmt = $db->prepare("UPDATE members SET reminder = 0 WHERE user_id = ?");
    if ($stmt) {
      $stmt->bind_param("i", $user_id);
      $stmt->execute();
      $stmt->close();
    }
  } catch (Throwable $t) {}

  $db->commit();

  // ── Obtener datos del cliente ──
$stmtUser = $db->prepare("SELECT fullname, correo FROM members WHERE user_id = ?");
$stmtUser->bind_param("i", $user_id);
$stmtUser->execute();
$resUser = $stmtUser->get_result();
$rowUser = $resUser->fetch_assoc();
$stmtUser->close();

$fullname = $rowUser['fullname'] ?? 'Cliente';
$correo   = $rowUser['correo']   ?? '';

// ── Auditoría ──
require_once __DIR__ . '/../core/audit.php';
registrar_auditoria($db, 'registrar_pago', "Pago de Bs $amount para $fullname (ID $user_id) - Método: $method", 'pagos');



// === Disparar evento a n8n ===
try {
    $payload = [
        'evento'     => 'pago_confirmado',
        'payment_id' => $payment_id,
        'user_id'    => $user_id,
        'fullname'  => $fullname,
        'correo'     => $correo,
        'amount'     => $amount,
        'paid_date'  => $paid_date,
        'plan_id'    => $plan_id,
        'method'     => $method,
    ];
  
    

    $r = n8n_trigger('gym/pago', $payload);

if (!$r['ok']) {
    error_log('Error n8n: ' . json_encode($r));
}
} catch (Throwable $e) {
    error_log('Excepcion n8n: ' . $e->getMessage());
}



  header('Location: recibo_cliente.php?payment_id='.$payment_id.'&success=1');
  exit;

} catch (Throwable $e) {

  try { $db->rollback(); } catch (Throwable $t) {}

  header('Location: pago_cliente.php?id='.$user_id.'&e='.urlencode($e->getMessage()));
  exit;
}