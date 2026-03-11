$tok = $_POST['_csrf'] ?? ($_POST['csrf'] ?? ($_SERVER['HTTP_X_CSRF_TOKEN'] ?? null));
if (!csrf_verify($tok)) {
http_response_code(403);
exit('CSRF');
}
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
  header('Location: ../vencimientos.php?error=unauthorized');
  exit;
}

// Validar cliente
$userIdCliente = filter_input(INPUT_POST, 'user_id', FILTER_VALIDATE_INT);
if (!$userIdCliente) {
  header('Location: ../vencimientos.php?e=cliente_invalido');
  exit;
}

$mensaje = "🔔 Recordatorio de pago: tu membresía está vencida o por vencer. Por favor, acércate a recepción para regularizar tu pago.";

// Registrar recordatorio
$stmt = $db->prepare("
  INSERT INTO recordatorios_pago (user_id, message, created_at)
  VALUES (?, ?, NOW())
");
$stmt->bind_param("is", $userIdCliente, $mensaje);
$stmt->execute();

header('Location: ../vencimientos.php?ok=recordatorio_enviado');
exit;
