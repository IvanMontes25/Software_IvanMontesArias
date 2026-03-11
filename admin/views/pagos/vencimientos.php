?>

<style>
.page-header{background:linear-gradient(135deg,#4e73df,#1cc88a);border-radius:1rem;padding:1.3rem 1rem;box-shadow:0 8px 20px rgba(0,0,0,.16)}
.page-header-inner{max-width:900px;margin:0 auto;text-align:center;color:#fff}
.page-title{font-size:1.35rem;font-weight:700;margin-bottom:4px}
.page-subtitle{font-size:.85rem;opacity:.9;margin-bottom:0}
.btn-white{background-color:#fff;color:#5a5c69}.btn-white:hover{background-color:#f8f9fc;color:#2e59d9}
.bg-warning-light{background-color:rgba(246,194,62,.15)}.bg-danger-light{background-color:rgba(231,74,59,.15)}
.icon-circle{width:35px;height:35px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:.9rem}
.bg-light-danger-hover:hover{background-color:rgba(231,74,59,.04)!important}
.custom-table td{vertical-align:middle!important;border-top:1px solid #f1f3f6;padding:1rem .75rem}
.progress{background-color:#eaecf4;border-radius:10px}.progress-bar{border-radius:10px}
.rounded-pill{border-radius:50px!important}.font-weight-medium{font-weight:500}
.hover-lift{transition:transform .2s ease,box-shadow .2s ease}
.hover-lift:hover{transform:translateY(-2px);box-shadow:0 .25rem .5rem rgba(0,0,0,.15)!important}
.empty-state{padding:2rem 0}.opacity-50{opacity:.5}
</style>

<div class="container-fluid">

    <div class="page-header mb-4">
        <div class="page-header-inner">
            <h1 class="page-title">
                Reporte de Cobranza <i class="fas fa-file-invoice-dollar ml-2"></i>
            </h1>
            <p class="page-subtitle">
                Gestiona las membresias vencidas y proximas a expirar
            </p>
        </div>
    </div>

    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between mb-4">
        <div class="mt-0 d-flex align-items-center flex-wrap">
            <div class="mr-4 d-none d-sm-block">
                <span class="text-muted small font-weight-bold mr-2 text-uppercase">Ver proximos:</span>
                <div class="btn-group shadow-sm" role="group">
                    <?php foreach($opcionesDias as $d): ?>
                        <a href="?dias=<?= $d ?>" class="btn btn-sm <?= $diasFilter === $d ? 'btn-primary' : 'btn-white border' ?>">
                            <?= $d ?> dias
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="d-flex shadow-sm rounded-pill overflow-hidden border">
                <div class="bg-warning text-dark px-3 py-2 text-sm font-weight-bold">
                    <i class="fas fa-clock mr-1"></i> <?= count($porVencer); ?>
                </div>
                <div class="bg-danger text-white px-3 py-2 text-sm font-weight-bold">
                    <i class="fas fa-exclamation-circle mr-1"></i> <?= count($vencidos); ?>
                </div>
            </div>
        </div>
    </div>

    <div class="d-block d-sm-none mb-4">
        <select class="form-control shadow-sm" onchange="window.location.href='?dias='+this.value">
            <?php foreach($opcionesDias as $d): ?>
                <option value="<?= $d ?>" <?= $diasFilter === $d ? 'selected' : '' ?>>Ver proximos <?= $d ?> dias</option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="row">
        <div class="col-xl-12 mb-4">
            <div class="card shadow-sm border-0 border-left-warning rounded-lg">
                <div class="card-header py-3 bg-white border-0 d-flex align-items-center">
                    <div class="icon-circle bg-warning-light text-warning mr-3">
                        <i class="fas fa-hourglass-half"></i>
                    </div>
                    <h6 class="m-0 font-weight-bold text-gray-800">Proximos a vencer <span class="text-muted font-weight-normal small">(en <?= $diasFilter ?> dias)</span></h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0 align-middle custom-table">
                            <thead class="bg-light text-muted small text-uppercase">
                                <tr>
                                    <th class="pl-4 border-0">Cliente</th>
                                    <th class="border-0">Contacto</th>
                                    <th class="border-0">Vencimiento</th>
                                    <th class="border-0">Dias</th>
                                    <th class="text-center border-0">Accion</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($porVencer)): ?>
                                    <tr>
                                        <td colspan="5" class="text-center py-5">
                                            <div class="empty-state">
                                                <i class="fas fa-check-circle text-success opacity-50 mb-3" style="font-size: 3rem;"></i>
                                                <h5 class="text-gray-800 font-weight-bold">Todo despejado!</h5>
                                                <p class="text-muted mb-0">No hay membresias por expirar en los proximos <?= $diasFilter ?> dias.</p>
                                            </div>
                                        </td>
                                    </tr>
                                <?php else: foreach ($porVencer as $row): 
                                    $c = $row['cliente']; $m = $row['m']; $dias = $row['dias'];
                                    $barColor = ($dias <= 3) ? 'bg-danger' : 'bg-warning';
                                    $porcentaje = min(100, max(0, $dias) / $diasFilter * 100);
                                ?>
                                    <tr>
                                        <td class="pl-4">
                                            <div class="d-flex align-items-center">
                                                <div class="avatar bg-light text-primary font-weight-bold rounded-circle mr-3 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                                    <?= strtoupper(substr($c['fullname'], 0, 1)); ?>
                                                </div>
                                                <div>
                                                    <div class="font-weight-bold text-gray-800"><?= e($c['fullname']); ?></div>
                                                    <div class="small text-muted">CI: <?= e($c['ci']); ?></div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <a href="https://wa.me/<?= preg_replace('/\D/', '', $c['contact']); ?>" target="_blank" class="btn btn-sm btn-light text-success border shadow-sm rounded-pill px-3">
                                                <i class="fab fa-whatsapp mr-1"></i> <?= e($c['contact']); ?>
                                            </a>
                                        </td>
                                        <td><span class="text-dark font-weight-medium"><?= date('d/m/Y', strtotime(membership_end_date($m))); ?></span></td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="progress progress-sm mr-2" style="width: 60px;">
                                                    <div class="progress-bar <?= $barColor ?>" style="width: <?= $porcentaje ?>%"></div>
                                                </div>
                                                <span class="small font-weight-bold text-gray-700"><?= $dias; ?>d</span>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <a href="pago_cliente.php?id=<?= (int)$c['user_id']; ?>" class="btn btn-primary btn-sm px-3 shadow-sm rounded-pill hover-lift">
                                                Renovar
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-12">
            <div class="card shadow-sm border-0 border-left-danger rounded-lg mb-4">
                <div class="card-header py-3 bg-white border-0 d-flex align-items-center">
                    <div class="icon-circle bg-danger-light text-danger mr-3">
                        <i class="fas fa-ban"></i>
                    </div>
                    <h6 class="m-0 font-weight-bold text-gray-800">Membresias Vencidas</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0 custom-table">
                            <thead class="bg-light text-muted small text-uppercase">
                                <tr>
                                    <th class="pl-4 border-0">Cliente</th>
                                    <th class="border-0">Contacto</th>
                                    <th class="border-0">Vencio el</th>
                                    <th class="border-0">Estado</th>
                                    <th class="text-center border-0">Accion</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($vencidos)): ?>
                                    <tr>
                                        <td colspan="5" class="text-center py-5">
                                            <div class="empty-state">
                                                <i class="fas fa-glass-cheers text-success opacity-50 mb-3" style="font-size: 3rem;"></i>
                                                <h5 class="text-gray-800 font-weight-bold">Excelente noticia!</h5>
                                                <p class="text-muted mb-0">No hay ningun socio con membresia vencida en este momento.</p>
                                            </div>
                                        </td>
                                    </tr>
                                <?php else: foreach ($vencidos as $row): 
                                    $c = $row['cliente']; $m = $row['m'];
                                ?>
                                    <tr class="bg-light-danger-hover">
                                        <td class="pl-4">
                                            <div class="font-weight-bold text-danger"><?= e($c['fullname']); ?></div>
                                            <div class="small text-muted">@<?= e($c['username']); ?></div>
                                        </td>
                                        <td>
                                            <a href="https://wa.me/<?= preg_replace('/\D/', '', $c['contact']); ?>" target="_blank" class="btn btn-sm btn-white text-muted border shadow-sm rounded-pill px-3">
                                                <i class="fas fa-phone-alt mr-1"></i> <?= e($c['contact']); ?>
                                            </a>
                                        </td>
                                        <td>
                                            <span class="text-danger font-weight-bold">
                                                <i class="fas fa-calendar-times mr-1"></i> <?= date('d/m/Y', strtotime(membership_end_date($m))); ?>
                                            </span>
                                        </td>
                                        <td><?= membership_badge($m); ?></td>
                                        <td class="text-center">
                                            <a href="pago_cliente.php?id=<?= (int)$c['user_id']; ?>" class="btn btn-danger btn-sm px-3 shadow-sm rounded-pill hover-lift">
                                                <i class="fas fa-hand-holding-usd mr-1"></i> Cobrar
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

