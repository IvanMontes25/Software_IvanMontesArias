<?php
// includes/seguridad_membresia.php

function verificarMembresiaActiva(mysqli $conn, int $userId): void
{
    $sql = "
        SELECT estado_real
        FROM vw_cliente_estado
        WHERE user_id = ?
        LIMIT 1
    ";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $userId);
    $stmt->execute();
    $res = $stmt->get_result();
    $row = $res->fetch_assoc();
    $stmt->close();

    // Si no existe o está caducado → BLOQUEAR
    if (!$row || $row['estado_real'] !== 'activa') {
        header('Location: membresia_caducada.php');
        exit;
    }
}
