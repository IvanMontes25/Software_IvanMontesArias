<?php
require_once __DIR__ . '/../core/bootstrap.php';
require_once __DIR__ . '/../core/auth.php';
require_once __DIR__ . '/../includes/membership_helper.php';

if (!$db instanceof mysqli) {
  die('No hay conexión a la base de datos');
}

$pageTitle = $pageTitle ?? 'Panel';
require_once __DIR__ . '/../includes/payments_dao.php';

/* Helpers */
function e($s): string
{
  return htmlspecialchars((string) ($s ?? ''), ENT_QUOTES, 'UTF-8');
}

function i(int $n): int
{
  return $n;
}
function qs(array $extra = []): string
{
  $base = [];
  if (!empty($_GET['q']))
    $base['q'] = $_GET['q'];

  $merged = array_merge($base, $extra);
  return $merged ? ('?' . http_build_query($merged)) : '';
}

/* Parámetros */
$q = isset($_GET['q']) ? trim($_GET['q']) : '';

$perPage = 20;
$page = isset($_GET['page']) && ctype_digit($_GET['page']) ? max(1, (int) $_GET['page']) : 1;
$offset = ($page - 1) * $perPage;

/* Filtro */
$where = '1=1';
$types = '';
$params = [];
if ($q !== '') {
  $like = "%{$q}%";
  $where .= ' AND (m.fullname LIKE ? OR m.ci LIKE ? OR pl.nombre LIKE ?';

  $types .= 'sss';
  $params = [$like, $like, $like];
  if (ctype_digit($q)) {
    $where .= ' OR m.user_id=?';
    $types .= 'i';
    $params[] = (int) $q;
  }
  $where .= ')';
}

/* Conteo */
$total = 0;
$stmt = $db->prepare("
  SELECT COUNT(DISTINCT m.user_id) c
  FROM members m
  LEFT JOIN (
    SELECT p1.user_id, p1.plan_id


    FROM payments p1
    INNER JOIN (
      SELECT user_id, MAX(id) AS max_id
      FROM payments
      WHERE status='pagado'
      GROUP BY user_id
    ) x ON x.max_id = p1.id
  ) lp ON lp.user_id = m.user_id
  LEFT JOIN planes pl ON pl.id = lp.plan_id

  WHERE $where
");

if ($types)
  $stmt->bind_param($types, ...$params);
$stmt->execute();
$row = $stmt->get_result()->fetch_assoc();
$stmt->close();
$total = (int) ($row['c'] ?? 0);

/* Listado principal: cliente + último pago */
$members = [];

$sql = "
SELECT
  m.user_id,
  m.fullname,
  m.ci,
  pl.nombre AS servicio,
  lp.amount,
  lp.paid_date,

  DATE_ADD(
    COALESCE(lp.start_date, lp.paid_date),
    INTERVAL pl.duracion_dias DAY
  ) AS fecha_fin,

  GREATEST(
    DATEDIFF(
      DATE_ADD(
        COALESCE(lp.start_date, lp.paid_date),
        INTERVAL pl.duracion_dias DAY
      ),
      CURDATE()
    ),
    0
  ) AS dias_restantes

FROM members m

LEFT JOIN (
   SELECT p1.*
   FROM payments p1
   INNER JOIN (
      SELECT user_id, MAX(id) AS max_id
      FROM payments
      WHERE status = 'pagado'
      GROUP BY user_id
   ) x ON x.max_id = p1.id
) lp ON lp.user_id = m.user_id

LEFT JOIN planes pl ON pl.id = lp.plan_id

WHERE $where
ORDER BY m.fullname ASC
LIMIT ? OFFSET ?
";


$typesList = $types . 'ii';
$paramsList = [...$params, $perPage, $offset];

$stmt = $db->prepare($sql);
$stmt->bind_param($typesList, ...$paramsList);
$stmt->execute();
$res = $stmt->get_result();
while ($r = $res->fetch_assoc())
  $members[] = $r;
$stmt->close();

/* Historial */
$hist_rows = [];


$totalPages = max(1, (int) ceil($total / $perPage));

function pager(int $page, int $total): string
{
  if ($total <= 1)
    return '';
  $prev = max(1, $page - 1);
  $next = min($total, $page + 1);

  $h = '<nav aria-label="Paginación"><ul class="pagination justify-content-center mb-0">';
  $h .= '<li class="page-item ' . ($page <= 1 ? 'disabled' : '') . '"><a class="page-link" href="pagos.php' . qs(['page' => $prev]) . '">&laquo;</a></li>';

  $start = max(1, $page - 2);
  $end = min($total, $page + 2);

  if ($start > 1) {
    $h .= '<li class="page-item"><a class="page-link" href="pagos.php' . qs(['page' => 1]) . '">1</a></li>';
    if ($start > 2)
      $h .= '<li class="page-item disabled"><span class="page-link">…</span></li>';
  }

  for ($p = $start; $p <= $end; $p++) {
    $h .= '<li class="page-item ' . ($p === $page ? 'active' : '') . '"><a class="page-link" href="pagos.php' . qs(['page' => $p]) . '">' . $p . '</a></li>';
  }

  if ($end < $total) {
    if ($end < $total - 1)
      $h .= '<li class="page-item disabled"><span class="page-link">…</span></li>';
    $h .= '<li class="page-item"><a class="page-link" href="pagos.php' . qs(['page' => $total]) . '">' . $total . '</a></li>';
  }

  $h .= '<li class="page-item ' . ($page >= $total ? 'disabled' : '') . '"><a class="page-link" href="pagos.php' . qs(['page' => $next]) . '">&raquo;</a></li>';
  $h .= '</ul></nav>';

  return $h;
}

?>

<?php include __DIR__ . '/theme/sb2/header.php'; ?>
<?php include __DIR__ . '/theme/sb2/sidebar.php'; ?>
<?php include __DIR__ . '/theme/sb2/topbar.php'; ?>

<div class="sb2-content d-flex flex-column min-vh-100">
  <div class="container-fluid flex-grow-1 py-3">

    <!-- ===== PAGE HEADER ===== -->
    <div class="page-header mb-4">
      <div class="page-header-inner">
        <h1 class="page-title">
          Pagos y Membresías <i class="fas fa-money-bill-wave ml-2"></i>
        </h1>
        <p class="page-subtitle">
          Gestión de pagos, estados de membresía e historial de clientes
        </p>
      </div>
    </div>

    <!-- ESTILOS ORIGINALES (sin cambios estéticos) -->
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

      .pay-table thead th {
        font-size: 1rem;
        font-weight: 600
      }

      .pay-table tbody td {
        font-size: 1.03rem;
        padding: .75rem .5rem
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

      .pay-table th,
      .pay-table td {
        text-align: center
      }

      .pay-table td.text-right {
        text-align: right !important
      }

      .actions .btn {
        font-size: .78rem;
        padding: .25rem .5rem;
        border-radius: 999px
      }

      .actions.historial .btn {
        font-size: .75rem;
        padding: .22rem .45rem
      }
    </style>

    <div class="sb2-content d-flex flex-column min-vh-100">
      <div class="container-fluid flex-grow-1">

        <div class="card shadow mb-4 pay-card">
          <div class="card-header py-3 d-flex align-items-center justify-content-between flex-wrap">

            <h6 class="m-0">
              <i class="fas fa-table mr-1"></i> Lista de clientes
            </h6>

            <form method="get" class="m-0 mt-2 mt-md-0">

              <div class="attendance-search">
                <i class="fas fa-search"></i>

                <input type="text" name="q" value="<?= e($q); ?>" placeholder="Buscar por nombre, CI o membresía…"
                  autocomplete="off">

                <?php if ($q !== ''): ?>
                  <button type="button" title="Limpiar" onclick="window.location='pagos.php'">

                    <i class="fas fa-times"></i>
                  </button>
                <?php endif; ?>
              </div>

            </form>

          </div>


          <div class="px-3 pt-3 pb-2 d-flex justify-content-end">

          </div>

          <div class="card-body">
            <div class="table-responsive pay-table-wrap">
              <table class="table table-hover table-sm mb-0 pay-table">
                <thead class="thead-light sticky-header">
                  <tr>
                    <th>#</th>
                    <th>Nombre</th>
                    <th>CI</th>
                    <th>Servicio</th>
                    <th class="text-right">Monto</th>

                    <th>Último pago</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                    <th>Historial</th>
                  </tr>
                </thead>
                <tbody>

                  <?php if (!$members): ?>
                    <tr>
                      <td colspan="10" class="text-center text-muted">Sin registros</td>
                    </tr>
                  <?php else:
                    $n = $offset + 1;
                    foreach ($members as $m): ?>
                      <tr>
                        <td><?= $n++; ?></td>
                        <td><?= e($m['fullname']); ?></td>
                        <td><?= e($m['ci']); ?></td>
                        <td><?= e($m['servicio'] ?? 'Sin membresía'); ?></td>

                        <td class="text-right"><?= $m['amount'] !== null ? number_format($m['amount'], 2) : '—'; ?></td>

                        <td><?= $m['paid_date'] ?? '—'; ?></td>

                        <!-- ESTADO DE MEMBRESÍA -->
                        <td>
                          <?php
                          $membership = membership_last($db, (int) $m['user_id']);
                          $estadoHtml = membership_badge($membership);
                          $dias = membership_days_left($membership);


                          $title = '';

                          if ($membership) {
                            $title = $dias >= 0
                              ? "Quedan {$dias} día(s)"
                              : "Venció hace " . abs($dias) . " día(s)";
                          } else {
                            $title = "Sin membresía registrada";
                          }

                          echo '<span 
        data-toggle="tooltip"
        data-placement="top"
        title="' . e($title) . '"
      >' . $estadoHtml . '</span>';
                          ?>
                        </td>

                        <!-- ACCIONES -->
                        <td class="actions">
                          <a href="#" class="btn btn-primary btn-pagar" data-id="<?= $m['user_id']; ?>">
                            Pagar
                          </a>

                          <a class="btn btn-outline-info" href="perfil_cliente.php?user_id=<?= $m['user_id']; ?>">Perfil</a>
                        </td>

                        <!-- HISTORIAL -->
                        <td class="actions historial">
                          <a class="btn btn-outline-primary"
                            href="historial_cliente.php?user_id=<?= (int) $m['user_id']; ?>">Ver historial</a>

                        </td>
                      </tr>

                    <?php endforeach; endif; ?>

                </tbody>
              </table>
            </div>
            <?= pager($page, $totalPages); ?>
          </div>
        </div>

      </div>
    </div>

    <script>

      document.addEventListener('DOMContentLoaded', function () {
        $('[data-toggle="tooltip"]').tooltip();
      });



      document.addEventListener('DOMContentLoaded', function () {

        document.querySelectorAll('.btn-pagar').forEach(btn => {
          btn.addEventListener('click', function (e) {
            e.preventDefault();

            Swal.fire({
              title: 'Registrar pago',
              html: `
          <div style="
            width:90px;
            height:90px;
            margin:0 auto 12px;
            border-radius:50%;
            background:linear-gradient(135deg,#1cc88a,#17a673);
            display:flex;
            align-items:center;
            justify-content:center;
            box-shadow:0 10px 25px rgba(28,200,138,.45);
            animation: pop .35s ease-out;
          ">
            <i class="fas fa-money-bill-wave" style="font-size:38px;color:#fff"></i>
          </div>
          <p style="font-size:15px;margin:0">
            ¿Deseas registrar un <strong>pago</strong> para este cliente?
          </p>
        `,
              showCancelButton: true,
              confirmButtonColor: '#1cc88a',
              cancelButtonColor: '#858796',
              confirmButtonText: 'Sí, continuar',
              cancelButtonText: 'Cancelar'
            }).then(result => {
              if (result.isConfirmed) {
                window.location.href = 'pago_cliente.php?id=' + btn.dataset.id;
              }
            });

          });
        });

      });
    </script>

    <style>
      @keyframes pop {
        0% {
          transform: scale(.85);
          opacity: 0
        }

        100% {
          transform: scale(1);
          opacity: 1
        }
      }

      /* ===============================
   BUSCADOR PREMIUM – PAGOS
=============================== */
      .attendance-search {
        display: flex;
        align-items: center;
        gap: 10px;

        background: #ffffff;
        border-radius: 999px;
        padding: 7px 14px;

        box-shadow: 0 6px 18px rgba(0, 0, 0, .12);
        transition: box-shadow .2s ease;
      }

      .attendance-search:focus-within {
        box-shadow: 0 0 0 3px rgba(78, 115, 223, .25);
      }

      .attendance-search i {
        color: #6c757d;
        font-size: 14px;
      }

      .attendance-search input {
        border: none;
        outline: none;
        background: transparent;
        color: #343a40;
        min-width: 260px;
        font-size: .9rem;
      }

      .attendance-search input::placeholder {
        color: #9aa0a6;
      }

      .attendance-search button {
        background: transparent;
        border: none;
        color: #adb5bd;
        cursor: pointer;
        padding: 0;
        display: flex;
        align-items: center;
      }

      .attendance-search button:hover {
        color: #e74a3b;
      }

      /* ===============================
   PAGE HEADER (ESTÁNDAR SISTEMA)
=============================== */
      .page-header {
        background: linear-gradient(135deg, #4e73df, #1cc88a);
        border-radius: 1rem;
        padding: 1.3rem 1rem;
        box-shadow: 0 8px 20px rgba(0, 0, 0, .16);
      }

      .page-header-inner {
        max-width: 1200px;
        /* 🔥 MISMO ANCHO QUE TABLAS */
        margin: 0 auto;
        text-align: center;
        color: #fff;
      }

      .page-title {
        font-size: 1.35rem;
        font-weight: 700;
        margin-bottom: 4px;
      }

      .page-title i {
        opacity: .95;
      }

      .page-subtitle {
        font-size: .85rem;
        opacity: .9;
        margin-bottom: 0;
      }
    </style>

    <script>
      (function () {
        const input = document.querySelector('.attendance-search input[name="q"]');
        if (!input) return;

        let timer = null;

        input.addEventListener('keyup', function () {
          clearTimeout(timer);

          timer = setTimeout(() => {
            const value = input.value.trim();

            const url = new URL(window.location.href);
            if (value) {
              url.searchParams.set('q', value);
            } else {
              url.searchParams.delete('q');
            }

            url.searchParams.delete('page'); // reset página
            window.location.href = url.toString();
          }, 350); // debounce elegante
        });
      })();
    </script>

    <?php include __DIR__ . '/theme/sb2/footer.php'; ?>