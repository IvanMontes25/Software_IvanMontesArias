?>

<div class="container-fluid d-flex flex-column min-vh-100">
<?php

/* ================= MODO DE ACCESO ================= */
$modo = 'admin';

if (isset($_SESSION['rol']) && $_SESSION['rol'] === 'cliente') {
  $modo = 'cliente';
  $id = (int)$_SESSION['user_id'];
} else {
  if (!isset($_GET['user_id']) || !ctype_digit($_GET['user_id'])) {
    echo '<div class="alert alert-danger">ID inválido.</div>';
    exit;
}
$id = (int)$_GET['user_id'];
}

if ($modo === 'cliente' && $id !== (int)$_SESSION['user_id']) {
  echo '<div class="alert alert-danger">Acceso no autorizado.</div>';
  exit;
}

/* ================= CLIENTE ================= */
$stmt = $db->prepare("
  SELECT user_id, fullname, ci, attendance_count, dor
  FROM members
  WHERE user_id = ?
  LIMIT 1
");
$stmt->bind_param('i', $id);
$stmt->execute();
$row = $stmt->get_result()->fetch_assoc();
$stmt->close();

if ($row):

$membresiaId  = 'GGP-SS-' . $row['user_id'];
$ci           = e($row['ci']);
$asistencias  = (int)$row['attendance_count'] . ' día(s)';
$miembroDesde = e($row['dor']);

/* ================= MEMBRESÍA (SOLO HELPER) ================= */
$m = membership_last($db, $id);

$estadoKey = membership_status($m);
$estatus   = membership_status_text($m);

switch ($estadoKey) {
  case 'activa':
    $estatusClass = 'success';
    break;
  case 'por_vencer':
    $estatusClass = 'warning';
    break;
  case 'vencida':
    $estatusClass = 'danger';
    break;
  default:
    $estatusClass = 'secondary';
}

$fechaFin = membership_end_date($m);
$dias     = membership_days_left($m);


/* ================= ÚLTIMO PAGO ================= */
$stmtPago = $db->prepare("
  SELECT amount, paid_date
  FROM payments
  WHERE user_id = ?
    AND status = 'pagado'
  ORDER BY id DESC
  LIMIT 1
");
$stmtPago->bind_param("i", $id);
$stmtPago->execute();
$pago = $stmtPago->get_result()->fetch_assoc();
$stmtPago->close();

$importe = 'Bs. 0.00';
$paid_date = null;

if ($pago) {
  $importe = 'Bs. ' . number_format((float)$pago['amount'], 2, '.', ',');
  $paid_date = $pago['paid_date'];
}

?>

<!-- ================= ENCABEZADO ================= -->
<div class="d-sm-flex align-items-center justify-content-between mb-3">
  <h1 class="h4 mb-0 text-gray-800">Informe de Cliente <i class="fas fa-file"></i></h1>
  <button class="btn btn-danger" onclick="window.print()">
    <i class="fas fa-print"></i> Imprimir
  </button>
</div>

<!-- ================= CARD ================= -->
<div class="card shadow mb-4 print-container">
  <div class="card-header d-flex justify-content-between align-items-center">
    <div>
      <h6 class="font-weight-bold text-primary mb-0">Golds Gym Premium</h6>
      <small class="text-muted d-block">Av. Apumalla 422, La Paz - Bolivia</small>
      <small class="text-muted d-block">Tel: 76231235 · Correo: gymboddygolds@gmail.com</small>
    </div>
    <span class="badge badge-<?php echo e($estatusClass); ?> px-3 py-2">
      Estado: <?php echo e($estatus); ?>
    </span>
  </div>

  <div class="card-body">
    <div class="row">
      <div class="col-lg-4 mb-3">
        <div class="card border-left-primary h-100">
          <div class="card-body py-3">
            <h6 class="text-primary mb-3"><i class="fas fa-user"></i> Datos del cliente</h6>
            <ul class="list-unstyled mb-0">
              <li><strong>Nombre:</strong> <?php echo e($row['fullname']); ?></li>
              <li><strong>CI:</strong> <?php echo $ci; ?></li>
              <li><strong>Miembro desde:</strong> <?php echo $miembroDesde; ?></li>
            </ul>
          </div>
        </div>
      </div>

      <div class="col-lg-8 mb-3">
        <table class="table table-sm table-bordered text-center mb-3">
          <thead class="thead-light">
            <tr>
              <th>ID</th>
              <th>Estado</th>
              <th>Plan (hasta)</th>
              <th>Último Pago</th>
              <th>Asistencias</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td><?php echo $membresiaId; ?></td>
              <td><?php echo e($estatus); ?></td>
              <td><?php echo $fechaFin ? e($fechaFin) : '—'; ?></td>
              <td><?php echo $importe; ?></td>
              <td><?php echo $asistencias; ?></td>
            </tr>
          </tbody>
        </table>

        <div class="alert alert-info mb-0">
          <strong>Último pago realizado:</strong>
          <?php echo $importe; ?>
          <?php if ($paid_date): ?>
            <span class="text-muted">(<?php echo e($paid_date); ?>)</span>
          <?php endif; ?>
        </div>
      </div>
    </div>

    <hr>

    <p class="mb-2"><strong>Estimado/a <?php echo e($row['fullname']); ?>,</strong></p>
    <p>
      Su membresía se encuentra
<strong class="text-<?php echo e($estatusClass); ?>">
  <?php echo e($estatus); ?>
</strong>
<?php if ($m && $dias >= 0): ?>
  (Restan <?php echo $dias; ?> día(s))
<?php elseif ($m && $dias < 0): ?>
  (Venció hace <?php echo abs($dias); ?> día(s))
<?php endif; ?>.

      ¡Gracias por elegir nuestros servicios en <em>Gym Body Training</em>!
    </p>

  </div>
</div>

<?php else: ?>
  <div class="alert alert-warning">No se encontró el cliente.</div>
<?php endif; ?>
</div>

