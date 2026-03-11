<?php
// cliente/views/clases/disponibles.php
// Variables disponibles desde ClaseController::disponibles()
// $clases, $tipos, $entrenadores, $reservas_activas, $sesiones_reservadas, $membresia_activa, $user_id
$pageTitle = 'Clases Disponibles';
?>
<?php include __DIR__ . '/../../theme/sb2/header.php'; ?>
<?php include __DIR__ . '/../../theme/sb2/sidebar.php'; ?>
<?php include __DIR__ . '/../../theme/sb2/topbar.php'; ?>

<style>
:root { --card-radius: 16px; }

.clase-card {
    border: none;
    border-radius: var(--card-radius);
    box-shadow: 0 2px 12px rgba(0,0,0,0.07);
    transition: transform 0.2s, box-shadow 0.2s;
    overflow: hidden;
}
.clase-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 24px rgba(0,0,0,0.12);
}
.clase-card .tipo-badge {
    display: inline-block;
    padding: 4px 14px;
    border-radius: 20px;
    font-size: 0.78rem;
    font-weight: 700;
    color: #fff;
    letter-spacing: 0.5px;
}
.clase-card .cupo-bar {
    height: 6px;
    border-radius: 10px;
    background: #e9ecef;
    overflow: hidden;
}
.clase-card .cupo-bar-fill {
    height: 100%;
    border-radius: 10px;
    transition: width 0.4s;
}
.filter-chip {
    display: inline-block;
    padding: 6px 16px;
    border-radius: 20px;
    border: 2px solid #e2e8f0;
    font-weight: 600;
    font-size: 0.82rem;
    cursor: pointer;
    color: #475569;
    text-decoration: none;
    transition: all 0.2s;
}
.filter-chip:hover, .filter-chip.active {
    background: #4e73df;
    border-color: #4e73df;
    color: #fff;
    text-decoration: none;
}
.stat-pill {
    background: #f8f9fc;
    border-radius: 12px;
    padding: 12px 20px;
    text-align: center;
}
.empty-state {
    padding: 60px 20px;
    text-align: center;
}
.empty-state i { font-size: 3rem; color: #d1d5db; margin-bottom: 1rem; }
</style>

<div class="container-fluid py-3">

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h1 class="h3 mb-0 font-weight-bold">Clases Disponibles</h1>
            <p class="text-muted small mb-0">Reserva tu lugar en las sesiones grupales</p>
        </div>
        <a href="mis_reservas.php" class="btn btn-outline-primary btn-sm">
            <i class="fas fa-bookmark mr-1"></i> Mis Reservas
        </a>
    </div>

    <?php if (!$membresia_activa): ?>
    <div class="alert alert-warning rounded-lg shadow-sm mb-4">
        <i class="fas fa-exclamation-triangle mr-2"></i>
        <strong>Tu membresía no está activa.</strong>
        Renuévala para poder reservar clases.
        <a href="membresia_pagos.php" class="btn btn-warning btn-sm ml-2">Renovar</a>
    </div>
    <?php endif; ?>

    <!-- Stats rápidas -->
    <div class="row mb-4">
        <div class="col-6 col-md-3 mb-3">
            <div class="stat-pill">
                <div class="h4 font-weight-bold text-primary mb-0"><?= count($clases) ?></div>
                <div class="small text-muted">Clases disponibles</div>
            </div>
        </div>
        <div class="col-6 col-md-3 mb-3">
            <div class="stat-pill">
                <div class="h4 font-weight-bold text-success mb-0"><?= $reservas_activas ?></div>
                <div class="small text-muted">Mis reservas activas</div>
            </div>
        </div>
        <div class="col-6 col-md-3 mb-3">
            <div class="stat-pill">
                <div class="h4 font-weight-bold text-info mb-0"><?= count($tipos) ?></div>
                <div class="small text-muted">Tipos de clase</div>
            </div>
        </div>
        <div class="col-6 col-md-3 mb-3">
            <div class="stat-pill">
                <div class="h4 font-weight-bold text-warning mb-0"><?= count($entrenadores) ?></div>
                <div class="small text-muted">Entrenadores</div>
            </div>
        </div>
    </div>

    <!-- Filtros -->
    <div class="card mb-4" style="border-radius: var(--card-radius);">
        <div class="card-body py-3">
            <form method="GET" class="d-flex flex-wrap gap-2 align-items-center" id="formFiltros">

                <!-- Filtro por tipo -->
                <div class="d-flex flex-wrap gap-2 align-items-center mr-3">
                    <span class="small font-weight-bold text-muted mr-1">Tipo:</span>
                    <a href="?<?= http_build_query(array_merge($_GET, ['tipo' => ''])) ?>"
                       class="filter-chip <?= empty($_GET['tipo']) ? 'active' : '' ?>">Todos</a>
                    <?php foreach ($tipos as $t): ?>
                    <a href="?<?= http_build_query(array_merge($_GET, ['tipo' => $t['id']])) ?>"
                       class="filter-chip <?= (isset($_GET['tipo']) && $_GET['tipo'] == $t['id']) ? 'active' : '' ?>"
                       style="<?= (!empty($_GET['tipo']) && $_GET['tipo'] == $t['id']) ? 'background:' . e($t['color']) . ';border-color:' . e($t['color']) : '' ?>">
                        <?= e($t['nombre']) ?>
                    </a>
                    <?php endforeach; ?>
                </div>

                <?php if (!empty($entrenadores)): ?>
                <!-- Filtro por entrenador -->
                <div class="d-flex flex-wrap gap-2 align-items-center">
                    <span class="small font-weight-bold text-muted mr-1">Entrenador:</span>
                    <select name="entrenador" class="form-control form-control-sm"
                            style="border-radius:20px; width:auto;"
                            onchange="this.form.submit()">
                        <option value="">Todos</option>
                        <?php foreach ($entrenadores as $ent): ?>
                        <option value="<?= $ent['user_id'] ?>"
                            <?= (isset($_GET['entrenador']) && $_GET['entrenador'] == $ent['user_id']) ? 'selected' : '' ?>>
                            <?= e($ent['fullname']) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <?php endif; ?>

            </form>
        </div>
    </div>

    <!-- Grid de clases -->
    <?php if (empty($clases)): ?>
    <div class="card" style="border-radius: var(--card-radius);">
        <div class="empty-state">
            <i class="fas fa-calendar-times d-block"></i>
            <h5 class="font-weight-bold">No hay clases disponibles</h5>
            <p class="text-muted">No hay sesiones próximas con cupo disponible. ¡Vuelve pronto!</p>
        </div>
    </div>
    <?php else: ?>
    <div class="row">
        <?php foreach ($clases as $c):
            $yaReservada = in_array((int)$c['id'], $sesiones_reservadas);
            $pct = (int)$c['porcentaje_ocupacion'];
            $barColor = $pct >= 80 ? '#e74a3b' : ($pct >= 50 ? '#f6c23e' : '#1cc88a');
        ?>
        <div class="col-xl-4 col-lg-6 col-md-6 mb-4">
            <div class="card clase-card h-100">

                <!-- Franja color superior -->
                <div style="height: 6px; background: <?= e($c['tipo_color']) ?>;"></div>

                <div class="card-body d-flex flex-column p-4">

                    <!-- Tipo + fecha -->
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <span class="tipo-badge" style="background: <?= e($c['tipo_color']) ?>">
                            <?= e($c['tipo_nombre']) ?>
                        </span>
                        <span class="small text-muted font-weight-bold">
                            <?= date('d/m/Y', strtotime($c['fecha'])) ?>
                        </span>
                    </div>

                    <!-- Horario -->
                    <div class="mb-2">
                        <i class="fas fa-clock text-primary mr-1"></i>
                        <strong><?= e($c['hora_inicio']) ?> – <?= e($c['hora_fin']) ?></strong>
                    </div>

                    <!-- Entrenador -->
                    <div class="mb-3 text-muted small">
                        <i class="fas fa-user-tie mr-1"></i>
                        <?= e($c['entrenador_nombre']) ?>
                    </div>

                    <!-- Descripción -->
                    <?php if (!empty($c['descripcion'])): ?>
                    <p class="small text-muted mb-3" style="flex: 1;"><?= e($c['descripcion']) ?></p>
                    <?php else: ?>
                    <div style="flex: 1;"></div>
                    <?php endif; ?>

                    <!-- Cupo -->
                    <div class="mb-3">
                        <div class="d-flex justify-content-between small mb-1">
                            <span class="text-muted">Cupo disponible</span>
                            <strong><?= (int)$c['cupo_disponible'] ?> / <?= (int)$c['cupo_maximo'] ?></strong>
                        </div>
                        <div class="cupo-bar">
                            <div class="cupo-bar-fill" style="width:<?= $pct ?>%; background:<?= $barColor ?>;"></div>
                        </div>
                    </div>

                    <!-- Botón acción -->
                    <?php if ($yaReservada): ?>
                        <button class="btn btn-success btn-sm w-100 rounded-pill" disabled>
                            <i class="fas fa-check mr-1"></i> Ya reservada
                        </button>
                    <?php elseif (!$membresia_activa): ?>
                        <a href="membresia_pagos.php" class="btn btn-warning btn-sm w-100 rounded-pill">
                            <i class="fas fa-lock mr-1"></i> Renovar membresía
                        </a>
                    <?php else: ?>
                        <button class="btn btn-primary btn-sm w-100 rounded-pill btn-reservar"
                                data-sesion="<?= (int)$c['id'] ?>"
                                data-nombre="<?= e($c['tipo_nombre']) ?>"
                                data-fecha="<?= date('d/m/Y', strtotime($c['fecha'])) ?>"
                                data-hora="<?= e($c['hora_inicio']) ?> - <?= e($c['hora_fin']) ?>">
                            <i class="fas fa-calendar-plus mr-1"></i> Reservar
                        </button>
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

document.querySelectorAll('.btn-reservar').forEach(btn => {
    btn.addEventListener('click', function() {
        const sesionId = this.dataset.sesion;
        const nombre   = this.dataset.nombre;
        const fecha    = this.dataset.fecha;
        const hora     = this.dataset.hora;
        const btnEl    = this;

        Swal.fire({
            title: '¿Confirmar reserva?',
            html: `<b>${nombre}</b><br><span class="text-muted">${fecha} · ${hora}</span>`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Sí, reservar',
            cancelButtonText: 'Cancelar',
            confirmButtonColor: '#4e73df'
        }).then(result => {
            if (!result.isConfirmed) return;

            btnEl.disabled = true;
            btnEl.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Reservando…';

            const fd = new FormData();
            fd.append('action', 'reservar');
            fd.append('sesion_id', sesionId);
            fd.append('csrf_token', CSRF_TOKEN);

            fetch('clases.php', { method: 'POST', body: fd })
                .then(r => r.json())
                .then(data => {
                    if (data.ok) {
                        Swal.fire({
                            icon: 'success',
                            title: '¡Reserva confirmada!',
                            text: data.msg,
                            confirmButtonColor: '#1cc88a'
                        }).then(() => location.reload());
                    } else if (data.lista_espera) {
                        Swal.fire({
                            title: 'Clase llena',
                            text: data.msg,
                            icon: 'info',
                            showCancelButton: true,
                            confirmButtonText: 'Entrar en lista de espera',
                            cancelButtonText: 'No, gracias',
                            confirmButtonColor: '#f6c23e'
                        }).then(r2 => {
                            if (!r2.isConfirmed) {
                                btnEl.disabled = false;
                                btnEl.innerHTML = '<i class="fas fa-calendar-plus mr-1"></i> Reservar';
                                return;
                            }
                            const fd2 = new FormData();
                            fd2.append('action', 'lista_espera');
                            fd2.append('sesion_id', sesionId);
                            fd2.append('csrf_token', CSRF_TOKEN);
                            fetch('clases.php', { method: 'POST', body: fd2 })
                                .then(r => r.json())
                                .then(d2 => {
                                    Swal.fire({
                                        icon: d2.ok ? 'success' : 'error',
                                        title: d2.ok ? '¡En lista de espera!' : 'Error',
                                        text: d2.msg,
                                        confirmButtonColor: '#4e73df'
                                    }).then(() => { if (d2.ok) location.reload(); });
                                });
                        });
                    } else {
                        Swal.fire({ icon: 'error', title: 'No se pudo reservar', text: data.msg });
                        btnEl.disabled = false;
                        btnEl.innerHTML = '<i class="fas fa-calendar-plus mr-1"></i> Reservar';
                    }
                })
                .catch(() => {
                    Swal.fire({ icon: 'error', title: 'Error de conexión', text: 'Intenta de nuevo.' });
                    btnEl.disabled = false;
                    btnEl.innerHTML = '<i class="fas fa-calendar-plus mr-1"></i> Reservar';
                });
        });
    });
});
</script>
