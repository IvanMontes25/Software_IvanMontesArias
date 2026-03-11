<?php
// cliente/includes/cliente_data.php

require_once __DIR__ . '/../../includes/membership_helper.php';

/**
 * Obtiene todos los datos consolidados del cliente
 * incluyendo estado de membresía calculado desde helper.
 */
function obtenerClienteData(mysqli $db, int $userId): ?array
{
    // =========================
    // Validación básica
    // =========================
    if ($userId <= 0) {
        return null;
    }

    if (!$db instanceof mysqli) {
        return null;
    }

    // =========================
    // 1. DATOS DEL CLIENTE
    // =========================
    $sqlUser = "
        SELECT user_id, fullname, correo
        FROM members
        WHERE user_id = ?
        LIMIT 1
    ";

    $stmt = $db->prepare($sqlUser);
    if (!$stmt) {
        return null;
    }

    $stmt->bind_param('i', $userId);
    $stmt->execute();
    $cliente = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$cliente) {
        return null;
    }

    // =========================
    // 2. MEMBRESÍA (desde helper)
    // =========================
    $m = membership_last($db, $userId);

    $estado        = membership_status($m);
    $fechaFin      = membership_end_date($m);
    $diasRestantes = membership_days_left($m);
    $puedeIngresar = membership_can_access($m);
    $badge         = function_exists('membership_badge') 
                        ? membership_badge($m) 
                        : null;

    // Manejo seguro de fecha_inicio
    $fechaInicio = null;
    if (
        $m &&
        isset($m['start_date']) &&
        $m['start_date'] instanceof DateTime
    ) {
        $fechaInicio = $m['start_date']->format('Y-m-d');
    }

    // =========================
    // 3. DATA FINAL CENTRALIZADA
    // =========================
    return [
        // Cliente
        'id'              => (int) $cliente['user_id'],
        'nombre'          => $cliente['fullname'],
        'email'           => $cliente['correo'],

        // Membresía
        'estado'          => $estado ?? 'sin_membresia',
        'plan'            => $m['plan_nombre'] ?? null,
        'fecha_inicio'    => $fechaInicio,
        'fecha_fin'       => $fechaFin,
        'dias_restantes'  => $diasRestantes,
        'puede_ingresar'  => $puedeIngresar,
        'badge'           => $badge,
    ];
}
