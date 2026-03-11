<?php
require_once __DIR__ . '/../core/bootstrap.php';
require_once __DIR__ . '/../core/auth.php';
require_once __DIR__ . '/../core/roles.php';
require_modulo('administracion');

if (!$db instanceof mysqli) {
    header('Content-Type: application/json');
    echo json_encode(['ok' => false, 'msg' => 'No hay conexión']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Content-Type: application/json');
    echo json_encode(['ok' => false, 'msg' => 'Método no permitido']);
    exit;
}

/* ========================
   CSRF
======================== */
$posted_token = $_POST['csrf_token'] ?? '';

if (
    empty($_SESSION['csrf_token']) ||
    empty($posted_token) ||
    !hash_equals($_SESSION['csrf_token'], $posted_token)
) {
    header('Content-Type: application/json');
    echo json_encode(['ok' => false, 'msg' => 'Token inválido']);
    exit;
}

$op = $_POST['op'] ?? '';

/* ========================
   DELETE STAFF
======================== */
if ($op === 'delete') {

    $id = (int) ($_POST['delete_id'] ?? 0);

    if ($id <= 0) {
        echo json_encode(['ok' => false, 'msg' => 'ID inválido']);
        exit;
    }

    $stmt = $db->prepare("DELETE FROM staffs WHERE user_id = ?");

    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        // ── Auditoría ──
        require_once __DIR__ . '/../core/audit.php';
        registrar_auditoria($db, 'eliminar_staff', "Eliminó al personal ID $id", 'administracion');

        echo json_encode(['ok' => true]);
    } else {
        echo json_encode(['ok' => false, 'msg' => 'No se pudo eliminar']);
    }

    $stmt->close();
    exit;
}

/* ========================
   UPDATE STAFF
======================== */
if ($op === 'update') {

    $id = (int) ($_POST['id'] ?? 0);

    if ($id <= 0) {
        $_SESSION['edit_error'] = 'ID inválido';
        header("Location: form_act_staff.php?id=$id");
        exit;
    }

    $stmt = $db->prepare("
        UPDATE staffs SET
            fullname = ?,
            username = ?,
            contact = ?,
            gender = ?
        WHERE user_id = ?
    ");

    $stmt->bind_param(
        "ssssi",
        $_POST['fullname'],
        $_POST['username'],
        $_POST['contact'],
        $_POST['gender'],
        $id
    );

    if ($stmt->execute()) {
        // ── Auditoría ──
        require_once __DIR__ . '/../core/audit.php';
        $fname = $_POST['fullname'] ?? '';
        registrar_auditoria($db, 'editar_staff', "Editó datos del personal $fname (ID $id)", 'administracion');

        header("Location: staffs.php?success=updated");
        exit;
    } else {
        header("Location: staffs.php?error=update");
        exit;
    }

    $stmt->close();
    header("Location: staffs.php?success=updated");

    exit;
}

echo json_encode(['ok' => false, 'msg' => 'Operación inválida']);
exit;
