<?php
require_once __DIR__ . '/../core/bootstrap.php';
require_once __DIR__ . '/../core/auth.php';
if (!$db instanceof mysqli) {
  die('No hay conexión a la base de datos');
}


require_once __DIR__ . '/../config/config.php'; // BASE_URL, GYM_ID, GYM_SECRET
date_default_timezone_set('America/La_Paz');

$pageTitle = 'Recibos del Cliente';

/* Helpers */
function e($s)
{
  return htmlspecialchars((string) $s, ENT_QUOTES, 'UTF-8');
}

/* ID cliente */
if (!isset($_GET['user_id']) || !ctype_digit($_GET['user_id'])) {
  exit('<div class="alert alert-danger">ID de cliente inválido</div>');
}
$user_id = (int) $_GET['user_id'];
/* Validación por rol */
if (isset($_SESSION['rol']) && $_SESSION['rol'] === 'cliente') {
  if ($user_id !== (int) $_SESSION['user_id']) {
    exit('Acceso no autorizado');
  }
}
/* Datos cliente */
$stmt = $db->prepare("
  SELECT user_id, fullname, ci
  FROM members
  WHERE user_id = ?
  LIMIT 1
");
$stmt->bind_param('i', $user_id);
$stmt->execute();
$cliente = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$cliente)
  exit('Cliente no encontrado');

/* =========================
   RECIBOS (payments) — JOIN REAL
========================= */
$stmt = $db->prepare("
  SELECT
  p.id AS payment_id,
  p.paid_date,
  p.amount,
  p.method,
  p.status,
  p.productos,
  pl.nombre AS plan_nombre,
  pl.duracion_dias
FROM payments p
LEFT JOIN planes pl ON pl.id = p.plan_id
WHERE p.user_id = ?
ORDER BY p.paid_date DESC, p.id DESC

");
$stmt->bind_param('i', $user_id);
$stmt->execute();
$recibos = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

/* KPIs */
$totalRecibos = count($recibos);
$montoTotal = array_sum(array_column($recibos, 'amount'));
?>

<?php include __DIR__ . '/theme/sb2/header.php'; ?>
<?php include __DIR__ . '/theme/sb2/sidebar.php'; ?>
<?php include __DIR__ . '/theme/sb2/topbar.php'; ?>

<div class="sb2-content">
  <div class="container-fluid">

    <!-- HEADER -->
    <div class="d-flex justify-content-between align-items-center mb-4">
      <div>
        <h4 class="font-weight-bold text-primary mb-0">
          Recibos de <?= e($cliente['fullname']); ?>
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
        <div class="card shadow-sm border-left-primary">
          <div class="card-body text-center">
            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
              Recibos Emitidos
            </div>
            <div class="h4 font-weight-bold"><?= $totalRecibos ?></div>
          </div>
        </div>
      </div>

      <div class="col-md-4">
        <div class="card shadow-sm border-left-success">
          <div class="card-body text-center">
            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
              Total Cancelado
            </div>
            <div class="h4 font-weight-bold">
              Bs <?= number_format($montoTotal, 2) ?>
            </div>
          </div>
        </div>
      </div>

      <div class="col-md-4">
        <div class="card shadow-sm border-left-info">
          <div class="card-body text-center">
            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
              Último Recibo
            </div>
            <div class="h5 font-weight-bold">
              <?= $recibos[0]['paid_date'] ?? '—' ?>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- TABLA -->
    <div class="card shadow mb-4">
      <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Historial de Recibos</h6>
      </div>

      <div class="card-body">
        <?php if (!$recibos): ?>
          <div class="alert alert-light text-center">
            Este cliente no tiene recibos registrados.
          </div>
        <?php else: ?>
          <div class="table-responsive">
            <table class="table table-hover table-sm">
              <thead class="thead-light">
                <tr>
                  <th>N°</th>
                  <th>Fecha</th>
                  <th>Método</th>
                  <th class="text-right">Monto</th>
                  <th>Estado</th>
                  <th>Detalle</th>
                  <th>Acción</th>
                </tr>
              </thead>

              <tbody>
                <?php foreach ($recibos as $r): ?>
                  <tr>
                    <td>GBT_<?= $r['payment_id'] ?></td>
                    <td><?= e($r['paid_date']) ?></td>
                    <td>
                      <?php
                      $method = $r['method'] ?? '';

                      switch ($method) {
                        case 'Efectivo':
                          $icon = 'fa-money-bill-wave';
                          $class = 'text-success';
                          break;

                        case 'QR':
                          $icon = 'fa-qrcode';
                          $class = 'text-primary';
                          break;

                        case 'Transferencia':
                          $icon = 'fa-university';
                          $class = 'text-info';
                          break;

                        default:
                          $icon = 'fa-question-circle';
                          $class = 'text-muted';
                          break;
                      }
                      ?>
                      <span class="<?= $class ?>">
                        <i class="fas <?= $icon ?>"></i>
                        <?= e($method) ?>
                      </span>
                    </td>
                    <td class="text-right">
                      Bs <?= number_format($r['amount'], 2) ?>
                    </td>

                    <td>
                      <?php
                      $status = strtolower($r['status'] ?? '');
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

                    <td>
                      <?php
                      $detalle = [];

                      if (!empty($r['plan_nombre'])) {
                        $detalle[] = 'Plan ' . $r['plan_nombre'] . ' (' . $r['duracion_dias'] . ' días)';
                      }

                      if (!empty($r['productos'])) {
                        $prods = json_decode($r['productos'], true);
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
                      <a href="recibo_cliente.php?payment_id=<?= $r['payment_id'] ?>"
                        class="btn btn-sm btn-outline-primary">
                        <i class="fas fa-eye"></i> Ver
                      </a>
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

<?php include __DIR__ . '/theme/sb2/footer.php'; ?>