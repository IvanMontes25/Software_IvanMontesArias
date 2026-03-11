<?php
$page = 'inbox';

require_once dirname(__DIR__) . '/core/bootstrap.php';

/* Helpers */
function e($s)
{
  return htmlspecialchars($s ?? '', ENT_QUOTES, 'UTF-8');
}

/* Marcar como leído (AJAX simple) */
if (isset($_POST['mark_read'])) {
  $id = (int) $_POST['mark_read'];
  $stmt = $db->prepare("UPDATE admin_inbox SET is_read = 1 WHERE id = ?");
  $stmt->bind_param('i', $id);
  $stmt->execute();
  exit('ok');
}

/* Búsqueda */
$q = isset($_GET['q']) ? trim($_GET['q']) : '';
$where = '1=1';
$params = [];
$types = '';

if ($q !== '') {
  $where .= " AND (titulo LIKE ? OR tipo LIKE ? OR origen LIKE ?)";
  $like = "%$q%";
  $params = [$like, $like, $like];
  $types = 'sss';
}

$sql = "
SELECT *
FROM admin_inbox
WHERE $where
ORDER BY is_read ASC, created_at DESC
";

$stmt = $db->prepare($sql);
if ($types)
  $stmt->bind_param($types, ...$params);
$stmt->execute();
$res = $stmt->get_result();
?>

<?php include 'theme/sb2/header.php'; ?>
<?php include 'theme/sb2/sidebar.php'; ?>
<?php include 'theme/sb2/topbar.php'; ?>

<div class="sb2-content d-flex flex-column min-vh-100">
  <div class="container-fluid flex-grow-1 py-3">

    <!-- PAGE HEADER -->
    <div class="page-header mb-4">
      <div class="page-header-inner">
        <h1 class="page-title">
          Bandeja de Entrada <i class="fas fa-inbox ml-2"></i>
        </h1>
        <p class="page-subtitle">
          Notificaciones del sistema y automatizaciones
        </p>
      </div>
    </div>

    <!-- CARD -->
    <div class="card inbox-card shadow-lg mb-4">

      <div class="card-header inbox-header d-flex justify-content-between align-items-center">
        <h6 class="m-0">
          <i class="fas fa-envelope-open-text mr-1"></i> Mensajes recibidos
        </h6>

        <!-- Buscador -->
        <div class="attendance-search">
          <i class="fas fa-search"></i>
          <input type="text" placeholder="Buscar mensaje…">
        </div>
      </div>

      <div class="card-body p-0">
        <div class="table-responsive pay-table-wrap">
          <table class="table table-hover table-sm mb-0 pay-table">

            <thead class="thead-light sticky-header">
              <tr>
                <th></th>
                <th>Tipo</th>
                <th>Título</th>
                <th>Origen</th>
                <th>Prioridad</th>
                <th>Fecha</th>
                <th>Acción</th>
              </tr>
            </thead>

            <tbody>

              <?php if ($res->num_rows === 0): ?>
                <tr>
                  <td colspan="7" class="text-center text-muted py-4">
                    Sin mensajes
                  </td>
                </tr>
              <?php endif; ?>

              <?php while ($row = $res->fetch_assoc()):

                /* Iconos por tipo */
                $icon = match ($row['tipo']) {
                  'pago' => 'fa-money-bill-wave text-success',
                  'alerta' => 'fa-exclamation-triangle text-warning',
                  default => 'fa-bell text-primary'
                };

                /* Prioridad */
                $prioClass = match ($row['prioridad']) {
                  'alta' => 'badge-danger',
                  'media' => 'badge-warning',
                  default => 'badge-secondary'
                };

                ?>

                <tr class="<?= $row['is_read'] ? '' : 'font-weight-bold' ?>">

                  <td>
                    <?php if (!$row['is_read']): ?>
                      <span class="badge badge-primary badge-pillish">Nuevo</span>
                    <?php endif; ?>
                  </td>

                  <td>
                    <i class="fas <?= $icon ?>"></i>
                    <?= ucfirst($row['tipo']) ?>
                  </td>

                  <td>
                    <a href="inbox_detalle.php?id=<?= $row['id'] ?>">
                      <?= e($row['titulo']) ?>
                    </a>
                  </td>

                  <td><?= e($row['origen']) ?></td>

                  <td>
                    <span class="badge <?= $prioClass ?> badge-pillish">
                      <?= ucfirst($row['prioridad']) ?>
                    </span>
                  </td>

                  <td><?= date('d/m/Y H:i', strtotime($row['created_at'])) ?></td>

                  <td>
                    <?php if (!$row['is_read']): ?>
                      <button class="btn btn-sm btn-outline-success mark-read" data-id="<?= $row['id'] ?>">
                        <i class="fas fa-check"></i>
                      </button>
                    <?php else: ?>
                      <i class="fas fa-check text-muted"></i>
                    <?php endif; ?>
                  </td>

                </tr>
              <?php endwhile; ?>

            </tbody>
          </table>
        </div>
      </div>

    </div>
  </div>
</div>


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

  .pay-table-wrap {
    max-height: 70vh;
    overflow: auto
  }

  .pay-table th,
  .pay-table td {
    text-align: center
  }

  .sticky-header {
    position: sticky;
    top: 0;
    z-index: 2
  }

  .badge-pillish {
    border-radius: 999px;
    font-weight: 500
  }

  .attendance-search {
    display: flex;
    align-items: center;
    gap: 10px;
    background: #fff;
    border-radius: 999px;
    padding: 7px 14px;
    box-shadow: 0 6px 18px rgba(0, 0, 0, .12)
  }

  .attendance-search input {
    border: none;
    outline: none;
    min-width: 220px
  }

  .attendance-search button {
    background: none;
    border: none;
    color: #adb5bd
  }

  /* ===============================
   CARD INBOX – JERARQUÍA VISUAL
=============================== */
  .inbox-card-header {
    background: #ffffff;
    color: #4e73df;
    border-bottom: 1px solid #e3e6f0;
  }

  .inbox-card-header h6,
  .inbox-card-header i {
    color: #4e73df;
  }

  .pay-card {
    background: #ffffff;
    margin-top: 1rem;
    /* 🔥 separación clara del header */
  }

  /* ===============================
   INBOX – ESTILO PRO
=============================== */

  .page-header {
    background: linear-gradient(135deg, #4e73df, #1cc88a);
    border-radius: 1rem;
    padding: 1.5rem 1rem;
    box-shadow: 0 10px 25px rgba(0, 0, 0, .18);
  }

  .page-header-inner {
    max-width: 1200px;
    margin: 0 auto;
    text-align: center;
    color: #fff;
  }

  .page-title {
    font-size: 1.45rem;
    font-weight: 700;
    margin-bottom: 6px;
  }

  .page-subtitle {
    font-size: .85rem;
    opacity: .9;
  }

  /* CARD */
  .inbox-card {
    border-radius: 1rem;
    border: none;
    background: #fff;
  }

  .inbox-header {
    background: linear-gradient(90deg, #4e73df, #1cc88a);
    color: #fff;
    border-radius: 1rem 1rem 0 0;
  }

  /* Tabla */
  .pay-table-wrap {
    max-height: 65vh;
    overflow: auto;
  }

  .pay-table thead th {
    font-weight: 600;
    font-size: .95rem;
  }

  .pay-table td {
    font-size: .95rem;
    padding: .7rem .5rem;
  }

  /* Buscador */
  .attendance-search {
    display: flex;
    align-items: center;
    gap: 10px;
    background: #fff;
    border-radius: 999px;
    padding: 6px 14px;
    box-shadow: 0 6px 16px rgba(0, 0, 0, .18);
  }

  .attendance-search input {
    border: none;
    outline: none;
    font-size: .85rem;
  }
</style>

<!-- JS -->
<script>
  document.querySelectorAll('.mark-read').forEach(btn => {
    btn.onclick = () => {
      fetch('inbox.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'mark_read=' + btn.dataset.id
      }).then(() => location.reload());
    }
  });
</script>

<?php include 'theme/sb2/footer.php'; ?>