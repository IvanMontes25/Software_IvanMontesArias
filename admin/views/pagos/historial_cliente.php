
<div class="sb2-content">
<div class="container-fluid">

<!-- HEADER -->
<div class="d-flex justify-content-between align-items-center mb-4">
  <div>
    <h4 class="font-weight-bold text-primary mb-0">
      Pagos de <?= e($cliente['fullname']); ?>
    </h4>
    <small class="text-muted">CI: <?= e($cliente['ci']); ?></small>
  </div>
  <a href="perfil_cliente.php?user_id=<?= $user_id ?>" class="btn btn-outline-secondary">
    <i class="fas fa-arrow-left"></i> Volver al perfil
  </a>
</div>

<!-- KPIs -->
<div class="row mb-4">
  <div class="col-md-4">
    <div class="card shadow-sm border-left-success">
      <div class="card-body text-center">
        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
          Total Pagado
        </div>
        <div class="h4 font-weight-bold">
          Bs <?= number_format($montoTotal, 2); ?>
        </div>
      </div>
    </div>
  </div>

  <div class="col-md-4">
    <div class="card shadow-sm border-left-primary">
      <div class="card-body text-center">
        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
          Cantidad de Pagos
        </div>
        <div class="h4 font-weight-bold">
          <?= $totalPagos ?>
        </div>
      </div>
    </div>
  </div>

  <div class="col-md-4">
    <div class="card shadow-sm border-left-info">
      <div class="card-body text-center">
        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
          Último Pago
        </div>
        <div class="h5 font-weight-bold">
          <?= e($ultimoPago) ?>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- TABLA HISTORIAL -->
<div class="card shadow mb-4">
  <div class="card-header py-3">
    <h6 class="m-0 font-weight-bold text-primary">Historial de Pagos</h6>
  </div>

  <div class="card-body">
    <?php if (!$pagos): ?>
      <div class="alert alert-light text-center">
        Este cliente no tiene pagos registrados.
      </div>
    <?php else: ?>
      <div class="table-responsive">
        <table class="table table-hover table-sm">
          <thead class="thead-light">
            <tr>
              <th>Fecha</th>
              <th>Método</th>
              <th>Plan</th>
              <th class="text-right">Monto</th>
              <th>Concepto</th>
              <th>Estado</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($pagos as $p): ?>
              <tr>
                <td><?= e($p['paid_date']); ?></td>
                <td><?= e($p['method']); ?></td>
                <td>
<?php
if (!empty($p['plan_nombre'])) {
    echo e($p['plan_nombre']);
} elseif (!empty($p['productos'])) {
    echo 'Productos';
} else {
    echo '—';
}
?>
</td>

                <td class="text-right">
                  Bs <?= number_format($p['amount'], 2); ?>
                </td>
                <td>
                  <?php
$detalle = [];

if (!empty($p['plan_nombre'])) {
    $detalle[] = 'Membresía ' . $p['plan_nombre'];
}

if (!empty($p['productos'])) {
    $prods = json_decode($p['productos'], true);
    if (is_array($prods)) {
        foreach ($prods as $prod) {
            if (!empty($prod['nombre'])) {
                $detalle[] = $prod['nombre'];
            }
        }
    }
}

echo $detalle
    ? e(implode(' | ', $detalle))
    : 'Pago general';
?>
                </td>
               <td>
<?php
$status = $p['status'] ?? '';

switch ($status) {
  case 'pagado':
    $class = 'success';
    break;
  case 'pendiente':
    $class = 'warning';
    break;
  case 'anulado':
    $class = 'danger';
    break;
  default:
    $class = 'secondary';
}
?>
<span class="badge badge-<?= $class; ?>">
  <?= e(ucfirst($status)); ?>
</span>
</td>

              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php endif; ?>
  </div>
</div>

</div>
</div>

