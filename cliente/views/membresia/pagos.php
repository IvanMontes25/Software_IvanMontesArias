<?php
// Vista - Variables disponibles desde el controlador
?>
<?php include __DIR__ . '/../../theme/sb2/header.php'; ?>
<?php include __DIR__ . '/../../theme/sb2/sidebar.php'; ?>
<?php include __DIR__ . '/../../theme/sb2/topbar.php'; ?>

<style>
    :root {
        --card-radius: 20px;
    }
    
    .modern-card {
        border: 0;
        border-radius: var(--card-radius);
        box-shadow: 0 5px 20px rgba(0,0,0,0.05);
        background: #fff;
        overflow: hidden;
    }

    /* Tarjeta de Membresía Digital */
    .membership-card {
        background: <?= $cardColor ?>;
        color: white;
        border-radius: var(--card-radius);
        padding: 2rem;
        position: relative;
        overflow: hidden;
        box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        min-height: 220px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }
    
    .membership-card::before {
        content: '';
        position: absolute;
        top: -50px; right: -50px;
        width: 200px; height: 200px;
        background: rgba(255,255,255,0.1);
        border-radius: 50%;
    }
    .membership-card::after {
        content: '';
        position: absolute;
        bottom: -30px; left: -30px;
        width: 140px; height: 140px;
        background: rgba(255,255,255,0.1);
        border-radius: 50%;
    }

    .chip-img {
        width: 50px;
        height: 35px;
        background: linear-gradient(135deg, #e0e0e0 0%, #b0b0b0 100%);
        border-radius: 6px;
        margin-bottom: 20px;
        position: relative;
        border: 1px solid rgba(0,0,0,0.1);
    }
    
    /* Tabla Moderna */
    .table-modern thead th {
        border-top: none;
        border-bottom: 1px solid #e3e6f0;
        text-transform: uppercase;
        font-size: 0.75rem;
        font-weight: 700;
        color: #b7b9cc;
        padding: 1rem;
    }
    .table-modern tbody td {
        padding: 1rem;
        vertical-align: middle;
        font-size: 0.9rem;
        color: #5a5c69;
    }
    .table-modern tr:hover td {
        background-color: #f8f9fc;
    }

    .icon-circle-lg {
        width: 50px; height: 50px;
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.5rem;
    }
</style>

<div class="container-fluid sb2-content">

    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800 font-weight-bold">Historial de Pagos</h1>
            <p class="mb-0 text-muted">Administra tus recibos y estado de cuenta.</p>
        </div>
        <div class="d-none d-sm-block">
            <span class="badge badge-light shadow-sm p-2 text-primary">
                <i class="fas fa-calendar-alt mr-1"></i> Hoy: <?= date('d/m/Y') ?>
            </span>
        </div>
    </div>

    <div class="row">
        
        <div class="col-lg-4 mb-4">
            <div class="membership-card">
                <div class="d-flex justify-content-between align-items-start z-index-1">
                    <div class="chip-img"></div>
                    <div class="text-right">
                        <h5 class="font-weight-bold mb-0 text-uppercase" style="letter-spacing: 2px;">Membresía</h5>
                        <small style="opacity:0.8">Gym Body Training</small>
                    </div>
                </div>

                <div class="z-index-1 mt-3">
                    <h4 class="font-weight-bold mb-1" style="text-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                        <?= h($clienteData['nombre']) ?>
                    </h4>
                    <p class="mb-0" style="opacity: 0.9; letter-spacing: 1px;">
                        Plan: <?= h($planReal) ?>

                    </p>
                </div>

                <div class="d-flex justify-content-between align-items-end z-index-1 mt-3">
                    <div>
                        <small class="text-uppercase" style="font-size: 0.65rem; opacity: 0.7;">Vencimiento</small>
                        <div class="font-weight-bold "><?= h($fechaFinReal) ?>
</div>
                    </div>
                    <div class="text-right">
                         <div class="badge badge-light text-dark px-3 py-1 font-weight-bold shadow-sm">
                            <i class="fas text-success <?= $statusIcon ?> mr-1"></i> <?= strtoupper($estadoReal) ?>

                         </div>
                    </div>
                </div>
            </div>
            
            <div class="row mt-4">
                <div class="col-6">
                    <div class="card modern-card p-3 text-center">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Días Restantes</div>
                        <div class="h3 mb-0 font-weight-bold text-gray-800"><?= $diasRestantes ?></div>
                    </div>
                </div>
                <div class="col-6">
                    <div class="card modern-card p-3 text-center">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Pagos Totales</div>
                        <div class="h3 mb-0 font-weight-bold text-gray-800"><?= $numPagos ?></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            
            <div class="row mb-4">
                <div class="col-md-6 mb-4 mb-md-0">
                    <div class="card modern-card h-100 py-2">
                        <div class="card-body d-flex align-items-center">
                            <div class="icon-circle-lg bg-light text-success mr-3">
                                <i class="fas fa-dollar-sign"></i>
                            </div>
                            <div>
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Total Invertido</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800"><?= fmt_money($totalPagado) ?></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card modern-card h-100 py-2">
                        <div class="card-body d-flex align-items-center">
                            <div class="icon-circle-lg bg-light text-info mr-3">
                                <i class="fas fa-receipt"></i>
                            </div>
                            <div>
                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Última Transacción</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $ultimoPagoFecha ?></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card modern-card mb-4">
                <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-list-alt mr-2"></i>Detalle de Transacciones
                    </h6>
                    </div>
                
                <div class="table-responsive">
                    <?php if (empty($rows)): ?>
                        <div class="text-center py-5">
                            <div class="mb-3 text-gray-300">
                                <i class="fas fa-file-invoice-dollar fa-4x"></i>
                            </div>
                            <p class="text-muted">Aún no tienes pagos registrados.</p>
                        </div>
                    <?php else: ?>
                        <table class="table table-modern width-100" id="dataTable">
                            <thead>
                                <tr>
                                    <th>Fecha</th>
                                    <th>Concepto / Detalle</th>
                                    <th>Monto</th>
                                    <th class="text-center">Estado</th>
                                    <th class="text-right">Recibo</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($rows as $r): 
                                    // Mapeo seguro de datos (fallback si faltan columnas)
                                    $id    = $r['id'] ?? 0;
                                    $fecha = isset($r['paid_date']) ? fmt_date_short($r['paid_date']) : '—';
                                    $monto = isset($r['amount']) ? fmt_money($r['amount']) : '—';
                                    
                                    // Intento de adivinar concepto o mostrar ID
                                   if (!empty($r['plan_nombre']) && empty($r['productos'])) {
    $concepto = $r['plan_nombre'];
}
elseif (empty($r['plan_nombre']) && !empty($r['productos'])) {
    $concepto = 'Compra de Productos';
}
elseif (!empty($r['plan_nombre']) && !empty($r['productos'])) {
    $concepto = $r['plan_nombre'] . ' + Productos';
}
else {
    $concepto = 'Pago';
}

                                    
                                    // Estado (si existe en BD, sino 'Pagado')
                                    $estado = isset($r['status']) ? ucfirst($r['status']) : 'Pagado';
                                    $badgeClass = ($estado == 'Pagado' || $estado == 'Paid') ? 'badge-success' : 'badge-secondary';
                                ?>
                                <tr>
                                    <td>
                                        <div class="font-weight-bold text-dark"><?= $fecha ?></div>
                                        <small class="text-muted">ID: #<?= $id ?></small>
                                    </td>
                                    <td>
                                        <span class="d-block text-dark"><?= h($concepto) ?></span>
                                        <?php if(isset($r['method'])): ?>
                                            <small class="text-muted"><i class="fas fa-credit-card mr-1"></i> <?= h($r['method']) ?></small>
                                        <?php endif; ?>
                                    </td>
                                    <td class="font-weight-bold text-dark">
                                        <?= $monto ?>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge badge-pill <?= $badgeClass ?> px-3">
                                            <?= h($estado) ?>
                                        </span>
                                    </td>
                                    <td class="text-right">
                                        <?php if($id): ?>
                                            <a href="recibo.php?id=<?= $id ?>" class="btn btn-sm btn-outline-primary rounded-circle" title="Ver Recibo">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>
            </div>

        </div>
    </div>

</div>

<?php include __DIR__ . '/../../theme/sb2/footer.php'; ?>

<?php if ($statusKey === 'por_vencer'): ?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
Swal.fire({
    icon: 'warning',
    title: '⚠️ Membresía por vencer',
    html: 'Tu membresía vence en <strong><?= (int)$daysLeft ?> día<?= ((int)$daysLeft !== 1) ? "s" : "" ?></strong>.<br>Renueva tu plan para seguir entrenando sin interrupciones.',
    confirmButtonText: 'Entendido',
    confirmButtonColor: '#f6c23e',
    showCloseButton: true
});
</script>
<?php endif; ?>