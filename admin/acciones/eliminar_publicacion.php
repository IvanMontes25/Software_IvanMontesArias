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
  header('Location: ../admin_publicacion.php?error=unauthorized');
  exit;
}


// Mitigación CSRF para acción por GET: requerir referer admin o token válido
$tok = $_GET['_csrf'] ?? ($_GET['csrf'] ?? null);
$ref = $_SERVER['HTTP_REFERER'] ?? '';
$okRef = ($ref !== '' && preg_match('~/admin/~', $ref));
if (!$okRef && !csrf_verify($tok)) {
  header('Location: ../admin_publicacion.php?error=csrf');
  exit;
}
// Validar ID
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) {
  header('Location: ../admin_publicacion.php?error=invalid');
  exit;
}

// Eliminar anuncio
$stmt = $db->prepare("DELETE FROM announcements WHERE id = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
  header('Location: ../admin_publicacion.php?success=deleted');
  exit;
}

header('Location: ../admin_publicacion.php?error=delete');
exit;
