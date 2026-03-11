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
if (!$db instanceof mysqli) {
    http_response_code(500);
    die('No hay conexión a la base de datos');
}

/* ================= SOLO POST ================= */
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Método no permitido');
}


$tok = $_POST['_csrf'] ?? ($_POST['csrf'] ?? ($_SERVER['HTTP_X_CSRF_TOKEN'] ?? null));
if (!csrf_verify($tok)) {
    http_response_code(403);
    exit('CSRF');
}
/* ================= SOLO ADMIN ================= */
if (($_SESSION['rol'] ?? '') !== 'admin') {
    header('Location: ../clientes.php?error=unauthorized');
    exit;
}

/* ================= CSRF ================= */
$posted_token = $_POST['csrf_token'] ?? '';

if (
    empty($_SESSION['csrf_token']) ||
    empty($posted_token) ||
    !hash_equals($_SESSION['csrf_token'], $posted_token)
) {
    http_response_code(403);
    exit('Token CSRF inválido');
}

/* ================= VALIDAR USUARIO ================= */
$userId = filter_input(INPUT_POST, 'user_id', FILTER_VALIDATE_INT);

if (!$userId) {
    $_SESSION['pwd_reset_error'] = 'ID de usuario inválido.';
    header('Location: ../clientes.php');
    exit;
}

/* ================= OBTENER CLIENTE ================= */
$stmt = $db->prepare("
  SELECT fullname, username
  FROM members
  WHERE user_id = ?
  LIMIT 1
");

$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

if (!$user) {
    $_SESSION['pwd_reset_error'] = 'Cliente no encontrado.';
    header('Location: ../clientes.php');
    exit;
}

/* ======================================================
   GENERAR NUEVA CONTRASEÑA SEGURA
====================================================== */

// Contraseña fuerte aleatoria
$newPlain = substr(str_shuffle(
    'ABCDEFGHJKLMNPQRSTUVWXYZabcdefghjkmnpqrstuvwxyz23456789!@#$%'
), 0, 10);


/* ======================================================
   GENERAR HASH MODERNO (SIEMPRE BCRYPT)
====================================================== */
$newHash = password_hash($newPlain, PASSWORD_BCRYPT);

/* ======================================================
   ACTUALIZAR PASSWORD
====================================================== */
$mustChange = 1;
$now = date('Y-m-d H:i:s');

$upd = $db->prepare("
    UPDATE members 
    SET password = ?, 
        must_change_password = ?, 
        password_reset_at = ?
    WHERE user_id = ?
");
$upd->bind_param("sisi", $newHash, $mustChange, $now, $userId);


if (!$upd->execute()) {
    $_SESSION['pwd_reset_error'] = 'No se pudo actualizar la contraseña.';
    $upd->close();
    header('Location: ../clientes.php');
    exit;
}

$upd->close();

/* ======================================================
   CONFIRMAR ÉXITO
====================================================== */
$_SESSION['pwd_reset_success'] = $newPlain;

header('Location: ../edit_clienteform.php?id=' . $userId);
exit;
