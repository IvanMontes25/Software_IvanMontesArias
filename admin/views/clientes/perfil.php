
<div class="container-fluid">

    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-user-tag text-gray-400 mr-2"></i>Expediente de Cliente
        </h1>
        <div class="mt-2 mt-sm-0">
            <a href="clientes.php" class="btn btn-sm btn-secondary shadow-sm">
                <i class="fas fa-chevron-left fa-sm text-white-50"></i> Regresar
            </a>
            <button onclick="window.print();" class="btn btn-sm btn-primary shadow-sm">
                <i class="fas fa-print fa-sm text-white-50"></i> Imprimir
            </button>
        </div>
    </div>

    <div class="row">

        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4 border-bottom-<?= $conf['class']; ?>">
                <div class="card-body pt-5">
                    <div class="text-center">
                        <div class="mb-4">
                            <i class="fas fa-user-circle fa-6x text-<?= $conf['class']; ?> shadow-sm rounded-circle"></i>
                        </div>
                        <h4 class="font-weight-bold text-gray-900 mb-0"><?= e($cli['fullname']); ?></h4>
                        <p class="text-muted mb-3">@<?= e($cli['username']); ?></p>
                        
                        <div class="badge badge-<?= $conf['class']; ?> px-4 py-2 mb-4" style="font-size: 0.9rem;">
                            <i class="fas <?= $conf['icon']; ?> mr-1"></i> <?= $estadoText; ?>
                        </div>
                    </div>

                    <div class="border-top pt-3">
                        <div class="row no-gutters align-items-center mb-2">
                            <div class="col-auto mr-3"><i class="fas fa-id-card text-gray-400"></i></div>
                            <div class="col">
                                <small class="text-xs font-weight-bold text-uppercase text-muted">Carnet de Identidad</small>
                                <div class="h6 mb-0 font-weight-bold text-gray-800"><?= e(val($cli,'ci')); ?></div>
                            </div>
                        </div>
                        <div class="row no-gutters align-items-center mb-2">
                            <div class="col-auto mr-3"><i class="fas fa-th-large text-gray-400"></i></div>
                            <div class="col">
                                <small class="text-xs font-weight-bold text-uppercase text-muted">Plan Actual</small>
                                <div class="h6 mb-0 font-weight-bold text-primary"><?= e($planNombre); ?></div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="card-footer bg-gray-100 py-3">
                    <div class="row no-gutters align-items-center">
                        <?php if ($m): ?>
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-<?= $conf['class']; ?> text-uppercase mb-1">
                                    Vigencia del: <?= $fechaInicio; ?> al <?= e($fechaFin); ?>
                                </div>
                                <div class="h6 mb-0 font-weight-bold text-gray-800">
                                    <?= ($dias >= 0) ? "Restan $dias días" : "Vencido hace " . abs($dias) . " días"; ?>
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-calendar fa-2x text-gray-300"></i>
                            </div>
                        <?php else: ?>
                            <div class="col text-center py-2">
                                <span class="text-muted font-italic small">Sin historial de suscripción</span>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-8 col-lg-7">

    <div class="row justify-content-start">

        <div class="col-6 col-md mb-4">
            <a href="pago_cliente.php?id=<?= $user_id; ?>" class="card border-left-primary shadow h-100 py-2 text-decoration-none bg-hover-light">
                <div class="card-body text-center">
                    <i class="fas fa-cash-register fa-2x text-primary mb-2"></i>
                    <div class="text-xs font-weight-bold text-primary text-uppercase">Nuevo Pago</div>
                </div>
            </a>
        </div>

        <div class="col-6 col-md mb-4">
            <a href="pagos_cliente.php?user_id=<?= $user_id; ?>" class="card border-left-info shadow h-100 py-2 text-decoration-none">
                <div class="card-body text-center">
                    <i class="fas fa-history fa-2x text-info mb-2"></i>
                    <div class="text-xs font-weight-bold text-info text-uppercase">Historial</div>
                </div>
            </a>
        </div>

        <div class="col-6 col-md mb-4">
            <a href="asistencia_cliente.php?user_id=<?= $user_id; ?>" class="card border-left-warning shadow h-100 py-2 text-decoration-none">
                <div class="card-body text-center">
                    <i class="fas fa-user-check fa-2x text-warning mb-2"></i>
                    <div class="text-xs font-weight-bold text-warning text-uppercase">Asistencias</div>
                </div>
            </a>
        </div>

        <div class="col-6 col-md mb-4">
            <a href="recibos_cliente.php?user_id=<?= $user_id; ?>" class="card border-left-dark shadow h-100 py-2 text-decoration-none">
                <div class="card-body text-center">
                    <i class="fas fa-file-pdf fa-2x text-dark mb-2"></i>
                    <div class="text-xs font-weight-bold text-dark text-uppercase">Recibos</div>
                </div>
            </a>
        </div>

        <div class="col-6 col-md mb-4">
            <a href="ver_reporte_cliente.php?user_id=<?= $user_id; ?>" class="card border-left-success shadow h-100 py-2 text-decoration-none">
                <div class="card-body text-center">
                    <i class="fas fa-file-alt fa-2x text-success mb-2"></i>
                    <div class="text-xs font-weight-bold text-success text-uppercase">Informe</div>
                </div>
            </a>
        </div>

    </div>


            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Información de Contacto</h6>
                    <a href="#" class="btn btn-sm btn-circle btn-light text-primary"><i class="fas fa-pen"></i></a>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-sm-4 text-muted font-weight-bold">Género:</div>
                        <div class="col-sm-8 text-gray-900"><?= e(val($cli,'gender')); ?></div>
                    </div>
                    <div class="row mb-3 border-top pt-2">
                        <div class="col-sm-4 text-muted font-weight-bold">Teléfono / Celular:</div>
                        <div class="col-sm-8 text-gray-900">
                            <a href="tel:<?= e($cli['contact']); ?>" class="text-decoration-none">
                                <i class="fas fa-phone fa-sm mr-1"></i> <?= e(val($cli,'contact')); ?>
                            </a>
                        </div>
                    </div>
                    <div class="row mb-3 border-top pt-2">
                        <div class="col-sm-4 text-muted font-weight-bold">Correo Electrónico:</div>
                        <div class="col-sm-8 text-gray-900">
                             <i class="far fa-envelope fa-sm mr-1"></i> <?= e(val($cli,'correo')); ?>
                        </div>
                    </div>
                    <div class="row mb-0 border-top pt-2">
                        <div class="col-sm-4 text-muted font-weight-bold">Miembro desde:</div>
                        <div class="col-sm-8 text-gray-900"><?= e(val($cli,'dor')); ?></div>
                    </div>
                </div>
            </div>

            <div class="card bg-light shadow mb-4">
                <div class="card-body py-3">
                    <div class="row no-gutters align-items-center">
                        <div class="col-auto mr-3">
                            <i class="fas fa-info-circle fa-2x text-gray-400"></i>
                        </div>
                        <div class="col">
                            <div class="small text-gray-600">
                                <strong>Nota:</strong> Los estados de membresía se actualizan automáticamente según la fecha actual. Para renovaciones, use el botón <strong>Nuevo Pago</strong>.
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<style>
    .bg-hover-light:hover { background-color: #f8f9fc; transition: 0.3s; }
    .card { border-radius: 0.75rem; }
    .badge { border-radius: 50px; }
</style>
