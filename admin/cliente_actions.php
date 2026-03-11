<?php
require_once __DIR__ . '/../core/bootstrap.php';
require_once __DIR__ . '/../core/auth.php';

if (!$db instanceof mysqli) {
    die('No hay conexión a la base de datos');
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die('Método no permitido');
}

/* 
   CSRF UNIFICADO
 */
$posted_token = $_POST['csrf_token'] ?? ($_POST['_csrf'] ?? ($_POST['csrf'] ?? ''));

if (
    empty($_SESSION['csrf_token']) ||
    empty($posted_token) ||
    !hash_equals($_SESSION['csrf_token'], $posted_token)
) {
    http_response_code(403);
    exit('Token CSRF inválido');
}


$op = $_POST['op'] ?? '';

/* 
   A) CREAR CLIENTE
 */
if ($op === 'create') {

    $fullname = trim($_POST['fullname'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $password_plain = $_POST['password'] ?? '';
    $dor = trim($_POST['dor'] ?? '');
    $gender = trim($_POST['gender'] ?? '');
    $ci = trim($_POST['ci'] ?? '');
    $correo = trim($_POST['correo'] ?? '');
    $contact = trim($_POST['contact'] ?? '');

    // VALIDACIONES
    if (strlen($fullname) < 3) {
        $_SESSION['client_error'] = 'El nombre debe tener al menos 3 caracteres.';
        header('Location: cliente_entry.php');
        exit;
    }

    if (strlen($username) < 3 || preg_match('/\s/', $username)) {
        $_SESSION['client_error'] = 'Usuario inválido.';
        header('Location: cliente_entry.php');
        exit;
    }

    if (strlen($password_plain) < 6) {
        $_SESSION['client_error'] = 'La contraseña debe tener mínimo 6 caracteres.';
        header('Location: cliente_entry.php');
        exit;
    }

    if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['client_error'] = 'Correo inválido.';
        header('Location: cliente_entry.php');
        exit;
    }

    // VERIFICAR DUPLICADOS (campo por campo para mensaje específico)
    $dup_errors = [];

    $dup_fields = [
        'username' => ['value' => $username, 'label' => 'Usuario'],
        'ci' => ['value' => $ci, 'label' => 'CI'],
        'correo' => ['value' => $correo, 'label' => 'Correo electrónico'],
    ];

    // También verificar teléfono si viene con valor
    if ($contact !== '') {
        $dup_fields['contact'] = ['value' => $contact, 'label' => 'Teléfono'];
    }

    foreach ($dup_fields as $col => $info) {
        $check = $db->prepare("SELECT user_id, fullname FROM members WHERE {$col} = ? LIMIT 1");
        $check->bind_param("s", $info['value']);
        $check->execute();
        $res = $check->get_result();
        $row_dup = $res->fetch_assoc();
        $check->close();

        if ($row_dup) {
            $dup_errors[] = $info['label'] . ' "' . $info['value'] . '" ya pertenece a: ' . ($row_dup['fullname'] ?? 'otro cliente');
        }
    }

    if (!empty($dup_errors)) {
        $_SESSION['client_error'] = implode(' | ', $dup_errors);
        header('Location: cliente_entry.php');
        exit;
    }

    // ENCRIPTAR PASSWORD
    $password_hash = password_hash($password_plain, PASSWORD_DEFAULT);

    // INSERTAR
    $stmt = $db->prepare("
        INSERT INTO members (fullname, username, password, dor, gender, ci, contact, correo)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)
    ");

    $stmt->bind_param("ssssssss", $fullname, $username, $password_hash, $dor, $gender, $ci, $contact, $correo);


    if ($stmt->execute()) {

        $last_id = $db->insert_id;

        // ── Auditoría ──
        require_once __DIR__ . '/../core/audit.php';
        registrar_auditoria($db, 'crear_cliente', "Inscribió al cliente $fullname (ID $last_id)", 'clientes');

        $_SESSION['client_success'] = 'Cliente registrado correctamente.';
        $stmt->close();

        header("Location: pago_cliente.php?id=" . $last_id);

        exit;
    }


    $stmt->close();
    $_SESSION['client_error'] = 'Error al registrar cliente.';
    header('Location: cliente_entry.php');
    exit;
}


/* ======================================
   B) EDITAR CLIENTE  (antes: edit_cliente_req.php)
====================================== */
if ($op === 'update') {

    $id = (int) ($_POST['id'] ?? 0);
    $fullname = trim($_POST['fullname'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $gender = trim($_POST['gender'] ?? '');
    $ci = trim($_POST['ci'] ?? '');
    $contact = trim($_POST['contact'] ?? '');
    $correo = trim($_POST['correo'] ?? '');

    if ($id <= 0) {
        $_SESSION['edit_error'] = 'ID inválido.';
        header("Location: edit_clienteform.php?id=$id");
        exit;
    }

    // Verificar que exista el cliente
    $exists = $db->prepare("SELECT user_id FROM members WHERE user_id = ? LIMIT 1");
    $exists->bind_param("i", $id);
    $exists->execute();
    $res = $exists->get_result();
    $exists->close();

    if (!$res || $res->num_rows === 0) {
        $_SESSION['edit_error'] = 'Cliente no encontrado.';
        header("Location: clientes.php");
        exit;
    }

    // Validaciones
    if (strlen($fullname) < 3) {
        $_SESSION['edit_error'] = 'El nombre debe tener al menos 3 caracteres.';
        header("Location: edit_clienteform.php?id=$id");
        exit;
    }

    if (strlen($username) < 3 || preg_match('/\s/', $username)) {
        $_SESSION['edit_error'] = 'El usuario debe tener mínimo 3 caracteres y no contener espacios.';
        header("Location: edit_clienteform.php?id=$id");
        exit;
    }

    if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['edit_error'] = 'Correo electrónico no válido.';
        header("Location: edit_clienteform.php?id=$id");
        exit;
    }

    if (!preg_match('/^[0-9]{7,10}$/', $contact)) {
        $_SESSION['edit_error'] = 'El teléfono debe tener entre 7 y 10 dígitos.';
        header("Location: edit_clienteform.php?id=$id");
        exit;
    }

    // Validar username único (excepto el mismo usuario)
    $check = $db->prepare("SELECT user_id FROM members WHERE username = ? AND user_id != ?");
    $check->bind_param("si", $username, $id);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        $_SESSION['edit_error'] = 'El nombre de usuario ya está en uso.';
        $check->close();
        header("Location: edit_clienteform.php?id=$id");
        exit;
    }
    $check->close();

    // UPDATE
    $sql = "
      UPDATE members SET
        fullname = ?,
        username = ?,
        gender   = ?,
        ci       = ?,
        contact  = ?,
        correo   = ?
      WHERE user_id = ?
    ";

    $stmt = $db->prepare($sql);
    $stmt->bind_param("ssssssi", $fullname, $username, $gender, $ci, $contact, $correo, $id);

    if ($stmt->execute()) {
        // ── Auditoría ──
        require_once __DIR__ . '/../core/audit.php';
        registrar_auditoria($db, 'editar_cliente', "Editó datos del cliente $fullname (ID $id)", 'clientes');

        $_SESSION['edit_success'] = 'Los datos del cliente se actualizaron correctamente.';
    } else {
        $_SESSION['edit_error'] = 'No se pudo actualizar el cliente.';
    }

    $stmt->close();
    header("Location: edit_clienteform.php?id=$id");
    exit;
}


/* ======================================
   C) ELIMINAR CLIENTE (antes: POST en clientes.php)
   Devuelve JSON
====================================== */
if ($op === 'delete') {

    header('Content-Type: application/json; charset=utf-8');

    $id = (int) ($_POST['delete_id'] ?? 0);
    if ($id <= 0) {
        echo json_encode(['ok' => false, 'msg' => 'ID inválido']);
        exit;
    }

    $stmt = $db->prepare("DELETE FROM members WHERE user_id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        // ── Auditoría ──
        require_once __DIR__ . '/../core/audit.php';
        registrar_auditoria($db, 'eliminar_cliente', "Eliminó al cliente ID $id", 'clientes');

        echo json_encode(['ok' => true]);
    } else {
        echo json_encode(['ok' => false, 'msg' => 'Error al eliminar']);
    }

    $stmt->close();
    exit;
}
