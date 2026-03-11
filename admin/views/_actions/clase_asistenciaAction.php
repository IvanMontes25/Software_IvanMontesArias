<?php
// admin/clase_asistencia_action.php — Guardar asistencia de una clase
// [MVC] bootstrap loaded by entry point
// [MVC] auth loaded by entry point
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
$asistencia = $_POST['asistencia'] ?? []; // array [reserva_id => 1]

if ($sesion_id <= 0) {
    $_SESSION['flash_error'] = 'Sesión no válida.';
    header('Location: mis_clases.php');
    exit;
}

// Obtener todas las reservas confirmadas de esta sesión
$st = $db->prepare("
    SELECT id FROM clases_reservas
    WHERE sesion_id = ? AND estado IN ('confirmada', 'asistio', 'no_asistio')
");
$st->bind_param("i", $sesion_id);
$st->execute();
$result = $st->get_result();

$todas_reservas = [];
while ($r = $result->fetch_assoc()) {
    $todas_reservas[] = (int) $r['id'];
}
$st->close();

$marcados = 0;
$no_marcados = 0;

foreach ($todas_reservas as $reserva_id) {
    if (isset($asistencia[$reserva_id])) {
        // Marcó asistencia
        $upd = $db->prepare("
            UPDATE clases_reservas
            SET estado = 'asistio', attended_at = NOW()
            WHERE id = ? AND sesion_id = ?
        ");
        $upd->bind_param("ii", $reserva_id, $sesion_id);
        $upd->execute();
        $upd->close();
        $marcados++;
    } else {
        // No marcó → no_asistio
        $upd = $db->prepare("
            UPDATE clases_reservas
            SET estado = 'no_asistio'
            WHERE id = ? AND sesion_id = ?
        ");
        $upd->bind_param("ii", $reserva_id, $sesion_id);
        $upd->execute();
        $upd->close();
        $no_marcados++;
    }
}

// Auditoría
require_once __DIR__ . '/../core/audit.php';
if (function_exists('registrar_auditoria')) {
    registrar_auditoria(
        $db,
        'asistencia_clase',
        "Registró asistencia sesión #$sesion_id: $marcados asistieron, $no_marcados no asistieron",
        'clases'
    );
}

$_SESSION['flash_ok'] = "✅ Asistencia guardada: $marcados asistieron, $no_marcados no asistieron.";
header("Location: clase_inscritos.php?id=$sesion_id");
exit;
