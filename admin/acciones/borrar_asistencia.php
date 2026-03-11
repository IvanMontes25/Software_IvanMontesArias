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
// Solo administradores
if (($_SESSION['rol'] ?? '') !== 'admin') {
  http_response_code(403);
  echo json_encode(['ok' => false, 'msg' => 'Solo administradores']);
  exit;
}

header('Content-Type: application/json; charset=utf-8');


// Mitigación CSRF para acciones por GET (mejor usar POST + token)
// Permitimos solo si viene desde el módulo admin (referer) O si llega token válido.
$tok = $_GET['_csrf'] ?? ($_GET['csrf'] ?? null);
$ref = $_SERVER['HTTP_REFERER'] ?? '';
$okRef = ($ref !== '' && preg_match('~/admin/~', $ref));
if (!$okRef && !csrf_verify($tok)) {
  http_response_code(403);
  echo json_encode(['ok' => false, 'msg' => 'CSRF/Origen inválido']);
  exit;
}
// Validar ID
$user_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$user_id) {
  http_response_code(400);
  echo json_encode(['ok' => false, 'msg' => 'ID inválido']);
  exit;
}

// 1) Eliminar asistencias
$stmt = $db->prepare("DELETE FROM attendance WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();

// 2) Recalcular contador (en este caso queda en 0)
$stmt = $db->prepare("UPDATE members SET attendance_count = 0 WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();

echo json_encode(['ok' => true]);
exit;
