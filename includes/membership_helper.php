<?php

/* =====================================================
   OBTENER ÚLTIMA MEMBRESÍA DESDE PAYMENTS
===================================================== */

function membership_last(mysqli $conn, int $userId): ?array
{
    $sql = "
        SELECT 
            p.id,
            p.start_date,
            p.paid_date,
            p.plan_id,
            pl.nombre AS plan_nombre,
            pl.duracion_dias
        FROM payments p
        INNER JOIN planes pl ON pl.id = p.plan_id
        WHERE p.user_id = ?
          AND p.status = 'pagado'
        ORDER BY p.id DESC
        LIMIT 1
    ";

    $stmt = $conn->prepare($sql);
    if (!$stmt) return null;

    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $res = $stmt->get_result();
    $row = $res->fetch_assoc();
    $stmt->close();

    if (!$row) return null;

    $duracion = (int)$row['duracion_dias'];
    if ($duracion <= 0) return null;

    $startRaw = $row['start_date'] ?: $row['paid_date'];

    try {
        $startDate = new DateTime($startRaw);
    } catch (Exception $e) {
        return null;
    }

    $endDate = clone $startDate;
  $endDate->modify("+{$duracion} days");



    $today = new DateTime('today');
    $daysLeft = (int)$today->diff($endDate)->format('%r%a');

    return [
        'start_date'  => $startDate,
        'end_date'    => $endDate,
        'days_left'   => $daysLeft,
        'plan_id'     => (int)$row['plan_id'],
        'plan_nombre' => $row['plan_nombre'],
        'duracion_dias'  => $duracion
    ];
}

function membership_status(?array $m, int $expiringDays = 3): string
{
    if (!$m) return 'sin_membresia';

    if ($m['days_left'] < 0) return 'vencida';

    if ($m['days_left'] <= $expiringDays) return 'por_vencer';

    return 'activa';
}


/* =========================
   FUNCIONES PÚBLICAS
========================= */
function membership_is_active(?array $m): bool
{
    return membership_status($m) === 'activa';
}

function membership_is_expired(?array $m): bool
{
    return membership_status($m) === 'vencida';
}

function membership_is_expiring_soon(?array $m, int $days = 3): bool
{
    return membership_status($m, $days) === 'por_vencer';
}

function membership_can_access(?array $m): bool
{
    $status = membership_status($m);
    return in_array($status, ['activa', 'por_vencer'], true);
}
function membership_badge(?array $m): string
{
    return match (membership_status($m)) {

        'activa' =>
            '<span class="badge badge-success">Activa</span>',

        'por_vencer' =>
            '<span class="badge badge-warning">Por vencer</span>',

        'vencida' =>
            '<span class="badge badge-danger">Vencida</span>',

        default =>
            '<span class="badge badge-secondary">Sin membresía</span>',
    };
}
function membership_status_text(?array $m): string
{
    return match (membership_status($m)) {

        'activa'       => 'Activa',
        'por_vencer'   => 'Por vencer',
        'vencida'      => 'Vencida',
        default        => 'Sin membresía',
    };
}
function membership_days_left(?array $m): int
{
    
    return $m['days_left'] ?? 0;
}

function membership_end_date(?array $m): ?string
{
    if (!$m || !isset($m['end_date'])) {
        return null;
    }

    return $m['end_date']->format('Y-m-d');
}
