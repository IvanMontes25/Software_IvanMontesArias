<?php
// core/audit.php — Registra acciones en la tabla audit_log
//
// Uso:
//   registrar_auditoria($db, 'crear_cliente', 'Inscribió al cliente Juan Pérez (ID 45)', 'clientes');
//   registrar_auditoria($db, 'registrar_pago', 'Pago de Bs 150 para María López', 'pagos');

function registrar_auditoria(mysqli $db, string $accion, string $descripcion, string $modulo): bool {

    // Datos del usuario logueado
    $user_id  = $_SESSION['user_id']  ?? 0;
    $username = $_SESSION['username'] ?? 'desconocido';
    $fullname = $_SESSION['fullname'] ?? $username;
    $rol      = $_SESSION['rol']      ?? 'desconocido';
    $ip       = $_SERVER['REMOTE_ADDR'] ?? '';

    $stmt = $db->prepare("
        INSERT INTO audit_log (user_id, username, fullname, rol, accion, descripcion, modulo, ip)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)
    ");

    if (!$stmt) return false;

    $stmt->bind_param("isssssss",
        $user_id,
        $username,
        $fullname,
        $rol,
        $accion,
        $descripcion,
        $modulo,
        $ip
    );

    $ok = $stmt->execute();
    $stmt->close();

    return $ok;
}
