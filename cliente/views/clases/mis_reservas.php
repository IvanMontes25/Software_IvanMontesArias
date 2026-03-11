<?php
// cliente/views/clases/mis_reservas.php
// Variables disponibles desde ClaseController::misReservas()
// $reservas, $user_id
$pageTitle = 'Mis Reservas';
?>
<?php include __DIR__ . '/../../theme/sb2/header.php'; ?>
<?php include __DIR__ . '/../../theme/sb2/sidebar.php'; ?>
<?php include __DIR__ . '/../../theme/sb2/topbar.php'; ?>

<style>
:root { --card-radius: 16px; }

.reserva-card {
    border: none;
    border-radius: var(--card-radius);
    box-shadow: 0 2px 12px rgba(0,0,0,0.07);
    overflow: hidden;
    transition: transform 0.2s;
}
.reserva-card:hover { transform: translateY(-2px); }
.tipo-badge {
    display: inline-block;
    padding: 3px 12px;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 700;
    color: #fff;
}
.estado-badge {
    display: inline-block;
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 0.78rem;
    font-weight: 700;
}
.estado-confirmada  { background: #d1fae5; color: #065f46; }
.estado-en_espera   { background: #fef3c7; color: #92400e; }
.estado-cancelada   { background: #fee2e2; color: #991b1b; }
.estado-completada  { background: #e0e7ff; color: #3730a3; }
.empty-state { padding: 60px 20px; text-align: center; }
.empty-state i { font-size: 3rem; color: #d1d5db; margin-bottom: 1rem; }
</style>

<div class="container-fluid py-3">

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h1 class="h3 mb-0 font-weight-bold">Mis Reservas</h1>
            <p class="text-muted small mb-0">Historial de todas tus reservas de clase</p>
        </div>
        <a href="clases.php" class="btn btn-primary btn-sm">
            <i class="fas fa-calendar-plus mr-1"></i> Reservar Clase
        </a>
    </div>

    <?php if (empty($reservas)): ?>
    <div class="card" style="border-radius: var(--card-radius);">
        <div class="empty-state">
            <i class="fas fa-bookmark d-block"></i>
            <h5 class="font-weight-bold">No tienes reservas aún</h5>
            <p class="text-muted">Explora las clases disponibles y reserva tu lugar.</p>
            <a href="clases.php" class="btn btn-primary rounded-pill">
                <i class="fas fa-search mr-1"></i> Ver clases disponibles
            </a>
        </div>
    </div>
    <?php else: ?>

    <!-- Resumen rápido -->
    <?php
        $activas    = array_filter($reservas, fn($r) => in_array($r['reserva_estado'], ['confirmada', 'en_espera']));
        $canceladas = array_filter($reservas, fn($r) => $r['reserva_estado'] === 'cancelada');
    ?>
    <div class="row mb-4">
        <div class="col-6 col-md-3 mb-3">
            <div class="card text-center p-3" style="border-radius:12px;">
                <div class="h4 font-weight-bold text-primary mb-0"><?= count($reservas) ?></div>
                <div class="small text-muted">Total</div>
            </div>
        </div>
        <div class="col-6 col-md-3 mb-3">
            <div class="card text-center p-3" style="border-radius:12px;">
                <div class="h4 font-weight-bold text-success mb-0"><?= count($activas) ?></div>
                <div class="small text-muted">Activas</div>
            </div>
        </div>
        <div class="col-6 col-md-3 mb-3">
            <div class="card text-center p-3" style="border-radius:12px;">
                <div class="h4 font-weight-bold text-danger mb-0"><?= count($canceladas) ?></div>
                <div class="small text-muted">Canceladas</div>
            </div>
        </div>
        <div class="col-6 col-md-3 mb-3">
            <div class="card text-center p-3" style="border-radius:12px;">
                <div class="h4 font-weight-bold text-info mb-0">
                    <?= count(array_filter($reservas, fn($r) => $r['reserva_estado'] === 'en_espera')) ?>
                </div>
                <div class="small text-muted">En espera</div>
            </div>
        </div>
    </div>

    <!-- Listado -->
    <div class="row">
        <?php foreach ($reservas as $r):
            $esFutura = strtotime($r['fecha'] . ' ' . $r['hora_inicio']) > time();
            $puedeCancel = $r['reserva_estado'] === 'confirmada' && $esFutura;
        ?>
        <div class="col-xl-4 col-lg-6 col-md-6 mb-4">
            <div class="card reserva-card h-100">

                <!-- Franja color -->
                <div style="height: 6px; background: <?= e($r['tipo_color']) ?>;"></div>

                <div class="card-body p-4 d-flex flex-column">

                    <!-- Tipo + estado -->
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <span class="tipo-badge" style="background: <?= e($r['tipo_color']) ?>">
                            <?= e($r['tipo_nombre']) ?>
                        </span>
                        <span class="estado-badge estado-<?= e($r['reserva_estado']) ?>">
                            <?php
                            $labels = [
                                'confirmada' => '✓ Confirmada',
                                'en_espera'  => '⏳ En espera',
                                'cancelada'  => '✕ Cancelada',
                                'completada' => '★ Completada',
                            ];
                            echo $labels[$r['reserva_estado']] ?? e($r['reserva_estado']);
                            ?>
                        </span>
                    </div>

                    <!-- Fecha y hora -->
                    <div class="mb-2">
                        <i class="fas fa-calendar text-primary mr-1"></i>
                        <strong><?= date('d/m/Y', strtotime($r['fecha'])) ?></strong>
                    </div>
                    <div class="mb-2">
                        <i class="fas fa-clock text-primary mr-1"></i>
                        <?= e($r['hora_inicio']) ?> – <?= e($r['hora_fin']) ?>
                    </div>
                    <div class="mb-3 text-muted small" style="flex:1;">
                        <i class="fas fa-user-tie mr-1"></i>
                        <?= e($r['entrenador_nombre']) ?>
                    </div>

                    <?php if ($r['reserva_estado'] === 'en_espera' && $r['posicion_espera']): ?>
                    <div class="alert alert-warning py-2 px-3 small mb-3 rounded-lg">
                        <i class="fas fa-hourglass-half mr-1"></i>
                        Posición en lista de espera: <strong>#<?= (int)$r['posicion_espera'] ?></strong>
                    </div>
                    <?php endif; ?>

                    <!-- Reservado el -->
                    <div class="text-muted small mb-3">
                        <i class="fas fa-bookmark mr-1"></i>
                        Reservado: <?= date('d/m/Y H:i', strtotime($r['fecha_reserva'])) ?>
                    </div>

                    <!-- Cancelar -->
                    <?php if ($puedeCancel): ?>
                    <button class="btn btn-outline-danger btn-sm w-100 rounded-pill btn-cancelar"
                            data-reserva="<?= (int)$r['reserva_id'] ?>"
                            data-nombre="<?= e($r['tipo_nombre']) ?>"
                            data-fecha="<?= date('d/m/Y', strtotime($r['fecha'])) ?>">
                        <i class="fas fa-times mr-1"></i> Cancelar reserva
                    </button>
                    <?php elseif (!$esFutura && $r['reserva_estado'] === 'confirmada'): ?>
                    <span class="btn btn-light btn-sm w-100 rounded-pill disabled small">
                        <i class="fas fa-check-circle text-success mr-1"></i> Clase completada
                    </span>
                    <?php endif; ?>

                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>

</div>

<?php include __DIR__ . '/../../theme/sb2/footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
const CSRF_TOKEN = '<?= csrf_token() ?>';

document.querySelectorAll('.btn-cancelar').forEach(btn => {
    btn.addEventListener('click', function() {
        const reservaId = this.dataset.reserva;
        const nombre    = this.dataset.nombre;
        const fecha     = this.dataset.fecha;
        const btnEl     = this;

        Swal.fire({
            title: '¿Cancelar reserva?',
            html: `<b>${nombre}</b> · ${fecha}<br><span class="text-danger small">No puedes cancelar con menos de 2 horas de anticipación.</span>`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sí, cancelar',
            cancelButtonText: 'No',
            confirmButtonColor: '#e74a3b'
        }).then(result => {
            if (!result.isConfirmed) return;

            btnEl.disabled = true;
            btnEl.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Cancelando…';

            const fd = new FormData();
            fd.append('action', 'cancelar');
            fd.append('reserva_id', reservaId);
            fd.append('csrf_token', CSRF_TOKEN);

            fetch('clases.php', { method: 'POST', body: fd })
                .then(r => r.json())
                .then(data => {
                    Swal.fire({
                        icon: data.ok ? 'success' : 'error',
                        title: data.ok ? 'Reserva cancelada' : 'No se pudo cancelar',
                        text: data.msg,
                        confirmButtonColor: '#4e73df'
                    }).then(() => { if (data.ok) location.reload(); });

                    if (!data.ok) {
                        btnEl.disabled = false;
                        btnEl.innerHTML = '<i class="fas fa-times mr-1"></i> Cancelar reserva';
                    }
                })
                .catch(() => {
                    Swal.fire({ icon: 'error', title: 'Error de conexión', text: 'Intenta de nuevo.' });
                    btnEl.disabled = false;
                    btnEl.innerHTML = '<i class="fas fa-times mr-1"></i> Cancelar reserva';
                });
        });
    });
});
</script>
