<?php
// admin/clase_agendar_action.php — Procesa la creación de una sesión
require_once __DIR__ . '/../core/bootstrap.php';
require_once __DIR__ . '/../core/auth.php';
require_once __DIR__ . '/../core/roles.php';
require_modulo('clases');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: clase_agendar.php');
    exit;
}

// CSRF
$token = $_POST['csrf_token'] ?? '';
if (function_exists('csrf_verify') && !csrf_verify($token)) {
    $_SESSION['flash_error'] = 'Token de seguridad inválido.';
    header('Location: clase_agendar.php');
    exit;
}

$rol = $_SESSION['rol'] ?? '';
$user_id = (int) ($_SESSION['user_id'] ?? 0); // staffs.user_id

// Si es admin y eligió otro entrenador
if ($rol === 'admin') {
    if (empty($_POST['entrenador_override'])) {
        $_SESSION['flash_error'] = 'Debes seleccionar un entrenador.';
        header('Location: clase_agendar.php');
        exit;
    }
    $entrenador_id = (int) $_POST['entrenador_override'];
} else {
    $entrenador_id = $user_id; // el entrenador agenda para sí mismo
}

$tipo_id = (int) ($_POST['tipo_clase_id'] ?? 0);
$fecha = trim($_POST['fecha'] ?? '');
$hora_inicio = trim($_POST['hora_inicio'] ?? '');
$hora_fin = trim($_POST['hora_fin'] ?? '');
$cupo = (int) ($_POST['cupo_maximo'] ?? 15);
$descripcion = trim($_POST['descripcion'] ?? '');

// ── Validaciones ──
$errors = [];
if ($tipo_id <= 0)
    $errors[] = 'Selecciona un tipo de clase.';
if ($entrenador_id <= 0)
    $errors[] = 'Entrenador no válido.';
if (!$fecha)
    $errors[] = 'La fecha es requerida.';
if (!$hora_inicio)
    $errors[] = 'La hora de inicio es requerida.';
if (!$hora_fin)
    $errors[] = 'La hora de fin es requerida.';
if ($hora_fin <= $hora_inicio)
    $errors[] = 'La hora de fin debe ser posterior al inicio.';
if ($cupo < 1 || $cupo > 100)
    $errors[] = 'El cupo debe ser entre 1 y 100.';
if ($fecha < date('Y-m-d'))
    $errors[] = 'No puedes agendar en una fecha pasada.';

if (!empty($errors)) {
    $_SESSION['flash_error'] = implode(' | ', $errors);
    header('Location: clase_agendar.php');
    exit;
}

// ── Verificar traslape de horarios ──
$sql = "SELECT id FROM clases_sesiones
        WHERE entrenador_id = ?
          AND fecha = ?
          AND estado = 'activa'
          AND (
            (hora_inicio < ? AND hora_fin > ?)
            OR (hora_inicio < ? AND hora_fin > ?)
            OR (hora_inicio >= ? AND hora_fin <= ?)
          )
        LIMIT 1";

$st = $db->prepare($sql);
$st->bind_param(
    "isssssss",
    $entrenador_id,
    $fecha,
    $hora_fin,
    $hora_inicio,
    $hora_fin,
    $hora_inicio,
    $hora_inicio,
    $hora_fin
);
$st->execute();
$st->store_result();

if ($st->num_rows > 0) {
    $_SESSION['flash_error'] = 'Ya existe una clase agendada en ese horario. Elige otro horario.';
    $st->close();
    header('Location: clase_agendar.php');
    exit;
}
$st->close();

// ── Insertar sesión ──
$ins = $db->prepare("
    INSERT INTO clases_sesiones
        (tipo_clase_id, entrenador_id, fecha, hora_inicio, hora_fin,
         cupo_maximo, cupo_disponible, descripcion)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?)
");
$ins->bind_param(
    "iisssiss",
    $tipo_id,
    $entrenador_id,
    $fecha,
    $hora_inicio,
    $hora_fin,
    $cupo,
    $cupo,
    $descripcion
);

if ($ins->execute()) {
    $new_id = $ins->insert_id;
    $ins->close();

    // Auditoría
    require_once __DIR__ . '/../core/audit.php';
    if (function_exists('registrar_auditoria')) {
        registrar_auditoria(
            $db,
            'agendar_clase',
            "Agendó clase (sesión #$new_id) para $fecha $hora_inicio-$hora_fin, cupo $cupo",
            'clases'
        );
    }

    $_SESSION['flash_ok'] = '✅ Clase agendada para ' . date('d/m/Y', strtotime($fecha)) . ' de ' . $hora_inicio . ' a ' . $hora_fin;
    header('Location: mis_clases.php');
} else {
    $ins->close();
    $_SESSION['flash_error'] = 'Error al guardar la clase: ' . $db->error;
    header('Location: clase_agendar.php');
}
exit;
