<?php
require_once __DIR__ . '/../core/bootstrap.php';
require_once __DIR__ . '/../core/auth.php';

if (!$db instanceof mysqli) {
    die('Error de conexión a la base de datos.');
}

// ID del cliente al que se le envía el recordatorio
$user_id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($user_id <= 0) {
    echo "ERROR!! ID inválido.";
    exit;
}

// 1) Marcamos al cliente con reminder = 1 en members
if ($stmt = $db->prepare("UPDATE members SET reminder = 1 WHERE user_id = ?")) {
    $stmt->bind_param("i", $user_id);
    $okUpdate = $stmt->execute();
    $stmt->close();
} else {
    echo "ERROR!! No se pudo preparar el UPDATE.";
    exit;
}

// 2) Guardamos un registro en el historial de recordatorios (recordatorios_pago)
$okInsert = false;
$mensaje = "Tienes un pago pendiente en Gym Body Training. Por favor regulariza tu membresía.";
if ($stmt = $db->prepare("INSERT INTO recordatorios_pago (user_id, message, created_at) VALUES (?, ?, NOW())")) {
    $stmt->bind_param("is", $user_id, $mensaje);
    $okInsert = $stmt->execute();
    $stmt->close();
}

if ($okUpdate) {
    // aunque el INSERT falle, por lo menos se marcó el reminder
    echo "<script>alert('Notificación enviada al cliente seleccionado!');window.location.href='pagos.php';</script>";
    exit;
} else {
    echo "ERROR!! No se pudo actualizar el recordatorio.";
    exit;
}
