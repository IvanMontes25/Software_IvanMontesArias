<?php
require_once __DIR__ . '/../../core/bootstrap.php';
require_once __DIR__ . '/../../core/auth.php';


// === CSRF helpers (fallback para endpoints) ===
if (!function_exists('csrf_token')) {
  function csrf_token(): string
  {
    if (session_status() === PHP_SESSION_NONE) {
      session_start();
    }
    if (empty($_SESSION['_csrf'])) {
      $_SESSION['_csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['_csrf'];
  }
}
if (!function_exists('csrf_verify')) {
  function csrf_verify(?string $token): bool
  {
    return is_string($token) && isset($_SESSION['_csrf']) && hash_equals($_SESSION['_csrf'], $token);
  }
}
// Solo roles con permiso de asistencias
require_once __DIR__ . '/../../core/roles.php';
if (!puede('asistencias')) {
  http_response_code(403);
  echo json_encode(['ok' => false, 'msg' => 'No autorizado']);
  exit;
}



// Validar ID
$user_id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if ($user_id <= 0) {
  header('Location: ../asistencias.php');
  exit;
}

// Fecha y hora actual
$curr_date = date('Y-m-d');
$curr_time = date('H:i:s');

// =====================================================
// EVITAR DOBLE ASISTENCIA EL MISMO DÍA
// =====================================================
$sqlCheck = "
  SELECT 1
  FROM attendance
  WHERE user_id = ?
    AND curr_date = ?
  LIMIT 1
";
$stmtCheck = $db->prepare($sqlCheck);
$stmtCheck->bind_param("is", $user_id, $curr_date);
$stmtCheck->execute();
$resCheck = $stmtCheck->get_result();

if ($resCheck->num_rows > 0) {
  // Ya registró hoy → volver sin error
  header('Location: ../asistencias.php?already=1');
  exit;
}

// =====================================================
// REGISTRAR ASISTENCIA
// =====================================================
$sqlInsert = "
  INSERT INTO attendance (user_id, curr_date, curr_time, present)
  VALUES (?, ?, ?, 1)
";
$stmtInsert = $db->prepare($sqlInsert);
$stmtInsert->bind_param("iss", $user_id, $curr_date, $curr_time);

if ($stmtInsert->execute()) {

  // ── Auditoría ──
  require_once __DIR__ . '/../../core/audit.php';
  registrar_auditoria($db, 'registrar_asistencia', "Registró asistencia del cliente ID $user_id", 'asistencias');

  // Incrementar contador
  $sqlUpd = "
      UPDATE members
      SET attendance_count = attendance_count + 1
      WHERE user_id = ?
    ";
  $stmtUpd = $db->prepare($sqlUpd);
  $stmtUpd->bind_param("i", $user_id);
  $stmtUpd->execute();

  header('Location: ../asistencias.php?success=1');
  exit;

}

// Si algo falla, volver sin mensaje
header('Location: ../asistencias.php?error=1');
exit;

