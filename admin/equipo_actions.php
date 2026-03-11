<?php
require_once __DIR__ . '/../core/bootstrap.php';
require_once __DIR__ . '/../core/auth.php';
require_once __DIR__ . '/../core/roles.php';
require_modulo('equipos');

if (!$db instanceof mysqli) {
    $_SESSION['equipo_error'] = 'No hay conexión a la base de datos.';
    header("Location: equipos.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: equipos.php");
    exit;
}

/* =========================
   CSRF
========================= */
$posted_token = $_POST['csrf_token'] ?? '';

if (
    empty($_SESSION['csrf_token']) ||
    empty($posted_token) ||
    !hash_equals($_SESSION['csrf_token'], $posted_token)
) {
    $_SESSION['equipo_error'] = 'Token inválido.';
    header("Location: equipos.php");
    exit;
}

$op = $_POST['op'] ?? '';

/* =========================
   CREAR EQUIPO
========================= */
if ($op === 'create') {

    $stmt = $db->prepare("
        INSERT INTO equipment (name, description, quantity, vendor, contact, date, amount)
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ");

    $stmt->bind_param(
        "ssisssd",
        $_POST['name'],
        $_POST['description'],
        $_POST['quantity'],
        $_POST['vendor'],
        $_POST['contact'],
        $_POST['date'],
        $_POST['amount']
    );

    if ($stmt->execute()) {
        // ── Auditoría ──
        require_once __DIR__ . '/../core/audit.php';
        registrar_auditoria($db, 'crear_equipo', "Registró equipo: " . ($_POST['name'] ?? ''), 'equipos');

        $_SESSION['equipo_success'] = 'Equipo registrado correctamente.';
    } else {
        $_SESSION['equipo_error'] = 'No se pudo registrar el equipo.';
    }

    $stmt->close();
    header("Location: equipos.php");
    exit;
}

/* =========================
   ACTUALIZAR EQUIPO
========================= */
if ($op === 'update') {

    $id = (int) ($_POST['id'] ?? 0);

    if ($id <= 0) {
        $_SESSION['equipo_error'] = 'ID inválido.';
        header("Location: equipos.php");
        exit;
    }

    $stmt = $db->prepare("
        UPDATE equipment SET
            name = ?,
            description = ?,
            quantity = ?,
            vendor = ?,
            contact = ?,
            date = ?,
            amount = ?
        WHERE id = ?
    ");

    $stmt->bind_param(
        "ssisssdi",
        $_POST['name'],
        $_POST['description'],
        $_POST['quantity'],
        $_POST['vendor'],
        $_POST['contact'],
        $_POST['date'],
        $_POST['amount'],
        $id
    );

    if ($stmt->execute()) {
        $_SESSION['equipo_success'] = 'Equipo actualizado correctamente.';
    } else {
        $_SESSION['equipo_error'] = 'No se pudo actualizar el equipo.';
    }

    $stmt->close();
    header("Location: equipos.php");
    exit;
}

/* =========================
   ELIMINAR EQUIPO
========================= */
if ($op === 'delete') {

    $id = (int) ($_POST['delete_id'] ?? 0);

    if ($id <= 0) {
        echo json_encode(['ok' => false, 'msg' => 'ID inválido']);
        exit;
    }

    $stmt = $db->prepare("DELETE FROM equipment WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo json_encode(['ok' => true]);
    } else {
        echo json_encode(['ok' => false, 'msg' => 'No se pudo eliminar']);
    }

    $stmt->close();
    exit;
}
