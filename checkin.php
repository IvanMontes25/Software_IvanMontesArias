<?php

if (session_status() === PHP_SESSION_NONE) {
  session_start();
}
// Cargar config para tener BASE_URL disponible
require_once __DIR__ . '/config/config.php';

// Si la sesión expiró por inactividad, destruirla y redirigir al login CLIENTE
if (
  isset($_SESSION['LAST_ACTIVITY']) &&
  (time() - $_SESSION['LAST_ACTIVITY'] > 1800)
) {
  session_unset();
  session_destroy();
  // Reconstruir la URL actual para pasarla como ?next=
  $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
  $host = $_SERVER['HTTP_HOST'];
  $next = urlencode($scheme . '://' . $host . $_SERVER['REQUEST_URI']);
  header('Location: ' . rtrim(BASE_URL, '/') . '/cliente/index.php?next=' . $next);
  exit;
}

// Ahora sí cargar bootstrap (ya no disparará el redirect a admin)
require_once __DIR__ . '/core/bootstrap.php';

// -----------------------------------------------------
// 1) Asegura sesión: si no hay, manda a login cliente y vuelve
// -----------------------------------------------------
if (!isset($_SESSION['user_id'])) {
  // Preserva la URL completa con sus parámetros firmados (?gym=...&t=...&sig=...)
  $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
  $host = $_SERVER['HTTP_HOST'];
  $next = urlencode($scheme . '://' . $host . $_SERVER['REQUEST_URI']);

  header('Location: ' . rtrim(BASE_URL, '/') . '/cliente/index.php?next=' . $next);
  exit;

}

// -----------------------------------------------------
// 2) Valida parámetros firmados del QR
// -----------------------------------------------------
$gym = isset($_GET['gym']) ? (int) $_GET['gym'] : 0;
$t = isset($_GET['t']) ? (int) $_GET['t'] : 0;  // slot de 60s (intdiv(time(),60) desde el emisor)
$sig = isset($_GET['sig']) ? trim($_GET['sig']) : '';

if ($gym <= 0 || $t <= 0 || $sig === '') {
  header('Location: ' . rtrim(BASE_URL, '/') . '/cliente/pags_cliente/asistencias.php?error=param');
  exit;
}

// Permitir tolerancia de ±1 minuto por desfases de reloj
$ok = false;
for ($dt = -1; $dt <= 1; $dt++) {
  $calc = hash_hmac('sha256', $gym . ':' . ($t + $dt), GYM_SECRET);
  if (hash_equals($calc, $sig)) {
    $ok = true;
    break;
  }
}
if (!$ok) {
  header('Location: ' . rtrim(BASE_URL, '/') . '/cliente/pags_cliente/asistencias.php?error=firma');
  exit;
}

// -----------------------------------------------------
// 3) Registrar asistencia (si no existe) del usuario logueado
// -----------------------------------------------------
$uid = (int) $_SESSION['user_id'];
$today = date('Y-m-d');
$now = date('h:i A');

// Verificar si ya registró asistencia hoy
$exists = false;
if ($stmt = $db->prepare('SELECT 1 FROM attendance WHERE user_id = ? AND curr_date = ? LIMIT 1')) {
  $stmt->bind_param('is', $uid, $today);
  $stmt->execute();
  $stmt->store_result();
  $exists = $stmt->num_rows > 0;
  $stmt->close();
}

if (!$exists) {
  if ($stmt = $db->prepare('INSERT INTO attendance (user_id, curr_date, curr_time, present) VALUES (?, ?, ?, 1)')) {
    $stmt->bind_param('iss', $uid, $today, $now);
    $stmt->execute();
    $stmt->close();

    // Sube contador en members
    $db->query('UPDATE members SET attendance_count = attendance_count + 1 WHERE user_id = ' . $uid);
  }
}

// -----------------------------------------------------
// 4) Respuesta / redirección
// -----------------------------------------------------
$query = $exists ? 'ya=1' : 'ok=1';
header('Location: ' . rtrim(BASE_URL, '/') . '/cliente/pags_cliente/asistencias.php?' . $query);
exit;