<?php
require_once __DIR__ . '/../core/bootstrap.php';
require_once __DIR__ . '/../core/auth.php';
require_once __DIR__ . '/../includes/membership_helper.php';
require_once __DIR__ . '/../includes/payments_dao.php';

if (!$db instanceof mysqli) {
  die('No hay conexión a la base de datos');
}

function e($s): string
{
  return htmlspecialchars((string) ($s ?? ''), ENT_QUOTES, 'UTF-8');
}

$user_id = (isset($_GET['user_id']) && ctype_digit($_GET['user_id'])) ? (int) $_GET['user_id'] : 0;
if ($user_id <= 0) {
  die("<div class='alert alert-danger text-center'>ID de cliente no válido.</div>");
}

/* Cliente */
$stmt = $db->prepare("
  SELECT m.user_id, m.fullname, m.ci
  FROM members m
  WHERE m.user_id = ?
");
$stmt->bind_param('i', $user_id);
$stmt->execute();
$member = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$member) {
  die("<div class='alert alert-warning text-center'>Cliente no encontrado.</div>");
}

/* Historial */
$rows = pmt_by_user($db, $user_id);

$pageTitle = 'Historial del Cliente';

include __DIR__ . '/theme/sb2/header.php';
include __DIR__ . '/theme/sb2/sidebar.php';
include __DIR__ . '/theme/sb2/topbar.php';
?>

<style>
  .pay-card {
    border: 0;
    border-radius: 1rem;
    overflow: hidden
  }

  .pay-card .card-header {
    background: linear-gradient(90deg, #4e73df, #1cc88a);
    color: #fff
  }

  .pay-table thead th {
    font-size: 1rem;
    font-weight: 600
  }

  .pay-table tbody td {
    font-size: 1.03rem;
    padding: .75rem .5rem
  }

  .pay-table th,
  .pay-table td {
    text-align: center
  }

  .pay-table td.text-right {
    text-align: right !important
  }

  .badge-pillish {
    border-radius: 999px;
    font-weight: 500
  }
</style>

<div class="sb2-content d-flex flex-column min-vh-100">
  <div class="container-fluid flex-grow-1">

    <div class="card shadow mb-4 pay-card">
      <div class="card-header py-3 d-flex align-items-center justify-content-between flex-wrap">
        <div>
          <div style="font-weight:700;font-size:1.05rem;">
            Historial — <?= e($member['fullname']); ?>
          </div>
          <div style="opacity:.9;font-size:.9rem;">
            CI: <?= e($member['ci']); ?> • ID: <?= (int) $member['user_id']; ?>
          </div>
        </div>

        <div class="mt-2 mt-md-0">
          <a class="btn btn-light btn-sm" href="pagos.php">
            <i class="fas fa-arrow-left mr-1"></i> Volver a Pagos
          </a>
          <a class="btn btn-outline-light btn-sm" href="perfil_cliente.php?user_id=<?= (int) $member['user_id']; ?>">
            <i class="fas fa-user mr-1"></i> Perfil
          </a>
        </div>
      </div>

      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-sm pay-table mb-0">
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
              <?php if (!$rows): ?>
                <tr>
                  <td colspan="6" class="text-center text-muted">Sin pagos registrados</td>
                </tr>
              <?php else:
                foreach ($rows as $p): ?>
                  <tr>
                    <td><?= e($p['paid_date']); ?></td>
                    <td><?= e($p['method']); ?></td>
                    <td><?= e($p['plan_nombre'] ?? 'Sin plan'); ?></td>
                    <td class="text-right"><?= number_format((float) $p['amount'], 2); ?></td>
                    <?php
                    $concepto = 'Pago';
                    $tienePlan = !empty($p['plan_nombre']);

                    $tieneProductos = false;
                    if (!empty($p['productos'])) {
                      $prods = json_decode($p['productos'], true);
                      $tieneProductos = is_array($prods) && count($prods) > 0;
                    }

                    if ($tienePlan && $tieneProductos) {
                      $concepto = 'Membresía: ' . $p['plan_nombre'] . ' + Productos';
                    } elseif ($tienePlan) {
                      $concepto = 'Membresía: ' . $p['plan_nombre'];
                    } elseif ($tieneProductos) {
                      $concepto = 'Productos';
                    }
                    ?>
                    <td><?= e($concepto); ?></td>
                    <td><?= e($p['status']); ?></td>
                  </tr>
                <?php endforeach; endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>

  </div>
</div>

<?php include __DIR__ . '/theme/sb2/footer.php'; ?>