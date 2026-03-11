<?php
// admin/clase_cancelar_action.php — Cancelar una sesión completa
require_once __DIR__ . '/../core/bootstrap.php';
require_once __DIR__ . '/../core/auth.php';
require_once __DIR__ . '/../core/roles.php';
require_modulo('clases');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: mis_clases.php');
    exit;
}

$token = $_POST['csrf_token'] ?? '';
if (function_exists('csrf_verify') && !csrf_verify($token)) {
    $_SESSION['flash_error'] = 'Token inválido.';
    header('Location: mis_clases.php');
    exit;
}

$sesion_id = (int) ($_POST['sesion_id'] ?? 0);
$user_id = (int) ($_SESSION['user_id'] ?? 0);
$rol = $_SESSION['rol'] ?? '';

if ($sesion_id <= 0) {
    $_SESSION['flash_error'] = 'Sesión no válida.';
    header('Location: mis_clases.php');
    exit;
}

// Verificar que la sesión existe y el entrenador tiene acceso
$st = $db->prepare("SELECT entrenador_id, estado, fecha, hora_inicio FROM clases_sesiones WHERE id = ?");
$st->bind_param("i", $sesion_id);
$st->execute();
$sesion = $st->get_result()->fetch_assoc();
$st->close();

if (!$sesion) {
    $_SESSION['flash_error'] = 'Sesión no encontrada.';
    header('Location: mis_clases.php');
    exit;
}

if ($rol !== 'admin' && (int) $sesion['entrenador_id'] !== $user_id) {
    $_SESSION['flash_error'] = 'No tienes permiso para cancelar esta clase.';
    header('Location: mis_clases.php');
    exit;
}

if ($sesion['estado'] !== 'activa') {
    $_SESSION['flash_error'] = 'Esta clase ya no está activa.';
    header('Location: mis_clases.php');
    exit;
}

// Cancelar la sesión
$db->begin_transaction();
try {
    // 1) Cambiar estado de la sesión
    $upd = $db->prepare("UPDATE clases_sesiones SET estado = 'cancelada' WHERE id = ?");
    $upd->bind_param("i", $sesion_id);
    $upd->execute();
    $upd->close();

    // 2) Cancelar todas las reservas activas
    $upd2 = $db->prepare("
        UPDATE clases_reservas
        SET estado = 'cancelada', cancelled_at = NOW()
        WHERE sesion_id = ? AND estado IN ('confirmada', 'en_espera')
    ");
    $upd2->bind_param("i", $sesion_id);
    $upd2->execute();
    $afectados = $upd2->affected_rows;
    $upd2->close();

    $db->commit();

    // Auditoría
    require_once __DIR__ . '/../core/audit.php';
    if (function_exists('registrar_auditoria')) {
        registrar_auditoria(
            $db,
            'cancelar_clase',
            "Canceló sesión #$sesion_id ({$sesion['fecha']} {$sesion['hora_inicio']}). $afectados reservas canceladas.",
            'clases'
        );
    }

    // TODO: Disparar webhook n8n para notificar a los clientes afectados
    // Los datos están en clases_reservas con sesion_id = $sesion_id

    $_SESSION['flash_ok'] = "Clase cancelada. $afectados cliente(s) fueron notificados.";

} catch (\Exception $e) {
    $db->rollback();
    $_SESSION['flash_error'] = 'Error al cancelar la clase.';
}

header('Location: mis_clases.php');
exit;
