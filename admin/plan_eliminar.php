<?php
require_once __DIR__ . '/../core/bootstrap.php';
require_once __DIR__ . '/../core/auth.php';
require_once __DIR__ . '/../core/roles.php';
require_modulo('administracion');

if (!$db instanceof mysqli) {
    http_response_code(500);
    die('No hay conexión a la base de datos');
}

/* ================= SOLO POST ================= */
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Método no permitido');
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

/* ================= VALIDAR ID ================= */
$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

if (!$id || $id <= 0) {
    header('Location: planes.php?e=' . urlencode('ID inválido'));
    exit;
}

/* ================= VERIFICAR EXISTENCIA ================= */
$stmt = $db->prepare("SELECT id FROM planes WHERE id = ? LIMIT 1");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$stmt->close();

if ($result->num_rows === 0) {
    header('Location: planes.php?e=' . urlencode('Plan no encontrado'));
    exit;
}

/* ================= DESACTIVAR ================= */
$stmt = $db->prepare("UPDATE planes SET estado = 0 WHERE id = ?");
$stmt->bind_param("i", $id);

if (!$stmt->execute()) {
    $stmt->close();
    header('Location: planes.php?e=' . urlencode('No se pudo desactivar'));
    exit;
}

$stmt->close();

// ── Auditoría ──
require_once __DIR__ . '/../core/audit.php';
registrar_auditoria($db, 'eliminar_plan', "Desactivó el plan ID $id", 'administracion');

header('Location: planes.php?success=1');
exit;
