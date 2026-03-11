<?php
require_once __DIR__ . '/../core/bootstrap.php';
require_once __DIR__ . '/../core/auth.php';
require_once __DIR__ . '/../includes/membership_helper.php';


$todays_date = date('Y-m-d');

/* =========================
   POST – borrar asistencia de hoy
========================= */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_attendance'])) {

  header('Content-Type: application/json; charset=utf-8');

  $userId = filter_input(INPUT_POST, 'delete_attendance', FILTER_VALIDATE_INT);
  if (!$userId || $userId <= 0) {
    echo json_encode(['ok' => false]);
    exit;
  }

  $stmt = $db->prepare("DELETE FROM attendance WHERE user_id = ? AND curr_date = ?");
  $stmt->bind_param("is", $userId, $todays_date);
  $stmt->execute();

  // ── Auditoría ──
  require_once __DIR__ . '/../core/audit.php';
  registrar_auditoria($db, 'borrar_asistencia', "Eliminó asistencia de hoy del cliente ID $userId", 'asistencias');

  $stmt2 = $db->prepare("
    UPDATE members
    SET attendance_count = (
      SELECT COUNT(*) FROM attendance WHERE user_id = ?
    )
    WHERE user_id = ?
  ");
  $stmt2->bind_param("ii", $userId, $userId);
  $stmt2->execute();

  echo json_encode(['ok' => true]);
  exit;
}

/* =========================
   QUERY BASE (SIN PLANES)
========================= */
function build_query($todays_date, $q, $filter, $withLimit = false, $page = 1, $per_page = 15)
{

  $sql = "FROM members m
          LEFT JOIN attendance a
            ON a.user_id = m.user_id
           AND a.curr_date = ? ";

  $types = "s";
  $params = [$todays_date];

  if ($q !== '') {
    $sql .= "WHERE (m.fullname LIKE ? OR m.username LIKE ?) ";
    $types .= "ss";
    $like = "%{$q}%";
    array_push($params, $like, $like);
  }

  if ($filter === 'yes') {
    $sql .= ($q ? "AND " : "WHERE ") . "a.curr_date IS NOT NULL ";
  } elseif ($filter === 'no') {
    $sql .= ($q ? "AND " : "WHERE ") . "a.curr_date IS NULL ";
  }

  $sql .= "ORDER BY m.fullname ASC ";

  if ($withLimit) {
    $offset = max(0, ($page - 1) * $per_page);
    $sql .= "LIMIT ? OFFSET ? ";
    $types .= "ii";
    array_push($params, $per_page, $offset);
  }

  return [$sql, $types, $params];
}

/* =========================
   AJAX
========================= */
if (isset($_GET['ajax'])) {

  header('Content-Type: application/json');

  $q = trim($_GET['q'] ?? '');
  $filter = $_GET['f'] ?? 'all';
  $page = max(1, (int) ($_GET['p'] ?? 1));
  $perPage = 15;

  [$sqlC, $tC, $pC] = build_query($todays_date, $q, $filter, false);
  $stC = $db->prepare("SELECT COUNT(*) total " . $sqlC);
  $stC->bind_param($tC, ...$pC);
  $stC->execute();
  $total = (int) $stC->get_result()->fetch_assoc()['total'];
  $stC->close();

  [$sqlL, $tL, $pL] = build_query($todays_date, $q, $filter, true, $page, $perPage);
  $stL = $db->prepare("
    SELECT m.*, a.curr_date, a.curr_time
    " . $sqlL
  );

  $stL->bind_param($tL, ...$pL);
  $stL->execute();
  $res = $stL->get_result();

  ob_start();
  $i = 1;

  while ($row = $res->fetch_assoc()) {

    $hasToday = !empty($row['curr_date']);
    $fullname = htmlspecialchars($row['fullname']);
    $userId = (int) $row['user_id'];

    $m = membership_last($db, $userId);

    if ($m && membership_can_access($m)) {
      $services = htmlspecialchars($m['plan_nombre']);
    } elseif ($m) {
      $services = 'Vencida';
    } else {
      $services = 'Sin membresía';
    }

    $canAccess = membership_can_access($m);

    $ac = (int) ($row['attendance_count'] ?? 0);
    $acTxt = $ac == 0 ? 'Ninguna' : ($ac == 1 ? '1 Día' : $ac . ' Días');
    ?>

    <tr>
      <td class="text-center text-muted"><?= $i++; ?></td>
      <td><?= $fullname; ?></td>

      <td class="text-center">
        <span class="badge badge-info"><?= $services; ?></span>
      </td>

      <td class="text-center">
        <span class="badge badge-secondary"><?= $acTxt; ?></span>
      </td>

      <td class="text-center">
        <a href="asistencia_cliente.php?user_id=<?= $userId; ?>" class="btn btn-outline-primary btn-sm">
          Ver asistencia
        </a>
      </td>

      <td class="text-center">

        <?php if (!$canAccess): ?>

          <span class="badge badge-danger">Membresía inactiva</span>

        <?php elseif ($hasToday): ?>

          <div>
            <span class="badge badge-success mb-1">
              <i class="fas fa-check mr-1"></i> Asistió hoy
            </span>

            <div class="small text-muted" style="line-height:1.2;">
              <div>
                <i class="fas fa-calendar-alt mr-1"></i>
                <?= htmlspecialchars($row['curr_date'] ?? ''); ?>
              </div>

              <div>
                <i class="fas fa-clock mr-1"></i>
                <?= htmlspecialchars($row['curr_time'] ?? ''); ?>
              </div>
            </div>


            <div class="btn-group btn-group-sm mt-2">
              <button type="button" class="btn btn-outline-danger btn-delete" data-id="<?= $userId; ?>">
                <i class="fas fa-trash-alt"></i>
              </button>
            </div>

          <?php else: ?>



            <a href="#" class="btn btn-outline-info btn-registrar" data-id="<?= $userId; ?>">
              Registrar
            </a>

          <?php endif; ?>

      </td>
    </tr>

    <?php
  }

  $rows_html = ob_get_clean();

  echo json_encode([
    'rows_html' => $rows_html,
    'pager_html' => '',
    'total' => $total
  ]);
  exit;
}
?>

<?php include __DIR__ . '/theme/sb2/header.php'; ?>
<?php include __DIR__ . '/theme/sb2/sidebar.php'; ?>
<?php include __DIR__ . '/theme/sb2/topbar.php'; ?>

<style>
  /* === MISMOS ESTILOS QUE LA TABLA DE CLIENTES === */
  .members-card {
    border: 0;
    border-radius: 1rem;
    overflow: hidden;
  }

  .members-card .card-header {
    background: linear-gradient(90deg, #4e73df, #1cc88a);
    color: #fff;
  }

  .members-table-wrap {
    max-height: 70vh;
    overflow-y: auto;
  }

  .members-table tbody td {
    font-size: 0.95rem;
    font-weight: normal !important;
    vertical-align: middle;
  }

  .badge-plan {
    border-radius: 999px;
    font-size: 0.85rem;
    font-weight: normal !important;
  }

  .badge-username,
  .badge-service {
    font-weight: normal !important;
    border-radius: 999px;
  }

  .members-actions .btn {
    border-radius: 999px;
  }

  .sticky-header {
    position: sticky;
    top: 0;
    z-index: 2;
  }


  .pagination .page-link {
    cursor: pointer;
  }
</style>

<!-- BEGIN: migrated original content -->
<div class="sb2-content d-flex flex-column min-vh-100">
  <div class="container-fluid flex-grow-1">

    <!-- ===== TÍTULO DE PÁGINA (ESTÁNDAR SISTEMA) ===== -->
    <div class="page-header mb-4">
      <div class="page-header-inner">
        <h1 class="page-title">
          Control de Asistencias
        </h1>
        <p class="page-subtitle">
          Registro diario y seguimiento de asistencias de los clientes
        </p>
      </div>
    </div>

    <!-- Alerts -->
    <?php if (isset($_GET['ok']) && $_GET['ok'] == '1'): ?>
      <div class="alert alert-success text-center" style="border-radius:8px">✅ Asistencia registrada correctamente.</div>
    <?php endif; ?>
    <?php if (isset($_GET['already']) && $_GET['already'] == '1'): ?>
      <div class="alert alert-warning text-center" style="border-radius:8px">ℹ️ Ya registraste tu asistencia hoy.</div>
    <?php endif; ?>

    <!-- Card -->
    <div class="card shadow mb-4 members-card">
      <div class="card-header py-3 d-flex flex-wrap align-items-center justify-content-between">
        <h6 class="m-0">
          <i class="fas fa-table mr-1"></i> Tabla de asistencias
        </h6>

        <form class="form-inline mt-2 mt-sm-0" onsubmit="return false;">

          <div class="form-group mr-sm-2 mb-2 mb-sm-0">
            <select id="filter" class="form-control">
              <option value="all">Todos</option>
              <option value="yes">Con asistencia hoy</option>
              <option value="no">Sin asistencia hoy</option>
            </select>
          </div>


          <div class="attendance-search">
            <i class="fas fa-search"></i>

            <input type="text" id="search" placeholder="Buscar por nombre o membresía…" autocomplete="off">

            <button type="button" id="btnClear" title="Limpiar">
              <i class="fas fa-times"></i>
            </button>
          </div>




        </form>
      </div>


      <div class="card-body members-body">
        <div class="table-responsive members-table-wrap">
          <table class="table table-hover table-sm mb-0 members-table">
            <thead class="thead-light sticky-header">
              <tr>
                <th class="text-center"><i class="fas fa-hashtag"></i></th>
                <th class="text-center">
                  <i class="fas fa-user mr-1"></i>Nombre completo
                </th>
                <th class="text-center">
                  <i class="fas fa-dumbbell mr-1"></i>Membresía
                </th>
                <th class="text-center">
                  <i class="fas fa-chart-line mr-1"></i>Asistencias
                </th>
                <th class="text-center">
                  <i class="fas fa-eye mr-1"></i>Ver asistencias
                </th>
                <th class="text-center">
                  <i class="fas fa-calendar-check mr-1"></i>Asistencia de hoy
                </th>
              </tr>
            </thead>

            <tbody id="members-body">
              <?php
              // Carga inicial de la primera página
              $qInit = '';
              $filterInit = 'all';
              $pageInit = 1;
              $perPage = 15;

              [$sqlC, $tC, $pC] = build_query($todays_date, $qInit, $filterInit, false);
              $stC = $db->prepare("SELECT COUNT(*) total " . $sqlC);
              $stC->bind_param($tC, ...$pC);
              $stC->execute();
              $totalInit = (int) $stC->get_result()->fetch_assoc()['total'];
              $stC->close();

              [$sqlL, $tL, $pL] = build_query($todays_date, $qInit, $filterInit, true, $pageInit, $perPage);
              $stL = $db->prepare("
  SELECT 
    m.*,
    a.curr_date,
    a.curr_time
  " . $sqlL
              );


              $stL->bind_param($tL, ...$pL);
              $stL->execute();
              $result = $stL->get_result();

              $cnt = 1;
              if ($result && $result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                  $hasToday = !empty($row['curr_date']);

                  $fullname = htmlspecialchars($row['fullname']);

                  $m = membership_last($db, (int) $row['user_id']);

                  if ($m && membership_can_access($m)) {
                    $services = htmlspecialchars($m['plan_nombre']);
                  } elseif ($m) {
                    $services = 'Vencida';
                  } else {
                    $services = 'Sin membresía';
                  }


                  $currDate = htmlspecialchars($row['curr_date'] ?? '');
                  $currTime = htmlspecialchars($row['curr_time'] ?? '');
                  $userId = urlencode($row['user_id']);

                  // ===== CONTEO DE ASISTENCIAS (TOTAL) =====
                  $ac = (int) ($row['attendance_count'] ?? 0);
                  if ($ac === 0) {
                    $acTxt = 'Ninguna';
                  } elseif ($ac === 1) {
                    $acTxt = '1 Día';
                  } else {
                    $acTxt = $ac . ' Días';
                  }
                  ?>
                  <tr>
                    <td class="text-center text-muted"><?= $cnt; ?></td>

                    <td>
                      <div><?= $fullname; ?></div>
                    </td>

                    <!-- MEMBRESÍA -->
                    <td class="text-center">
                      <span class="badge badge-info badge-service">
                        <?= $services; ?>
                      </span>
                    </td>

                    <!-- ASISTENCIAS (TOTAL) -->
                    <td class="text-center">
                      <span class="badge badge-secondary badge-pill">
                        <?= htmlspecialchars($acTxt); ?>
                      </span>
                    </td>


                    <!-- VER ASISTENCIAS -->
                    <td class="text-center">
                      <a href="asistencia_cliente.php?user_id=<?= (int) $row['user_id']; ?>"
                        class="btn btn-outline-primary btn-sm" title="Ver historial de asistencias">
                        <i class="fas fa-calendar-alt"></i>
                      </a>
                    </td>

                    <!-- ASISTENCIA DE HOY -->
                    <td class="text-center members-actions">
                      <?php

                      $canAccess = membership_can_access($m);
                      ?>

                      <?php if (!$canAccess): ?>
                        <span class="badge badge-danger">
                          Membresía inactiva
                        </span>

                      <?php elseif ($hasToday): ?>

                        <div>
                          <span class="badge badge-success mb-1">
                            <i class="fas fa-check mr-1"></i> Asistió hoy
                          </span>

                          <div class="small text-muted" style="line-height:1.2;">

                            <div>
                              <i class="fas fa-calendar-alt mr-1"></i>
                              <?= $currDate; ?>
                            </div>

                            <div>
                              <i class="fas fa-clock mr-1"></i>
                              <?= $currTime; ?>
                            </div>
                          </div>


                          <div class="btn-group btn-group-sm mt-2">
                            <button type="button" class="btn btn-outline-danger btn-delete" data-id="<?= $row['user_id']; ?>">
                              <i class="fas fa-trash-alt"></i>
                            </button>
                          </div>

                        <?php else: ?>
                          <div class="btn-group btn-group-sm">
                            <a href="#" class="btn btn-outline-info btn-registrar" data-id="<?= $userId; ?>">
                              <i class="fas fa-map-marker-alt mr-1"></i> Registrar
                            </a>

                          </div>
                        <?php endif; ?>
                    </td>
                  </tr>

                  <?php
                  $cnt++;
                }
              } else {
                echo '<tr><td colspan="6" class="text-center text-muted py-4">No hay miembros para mostrar.</td></tr>';
              }
              ?>
            </tbody>
          </table>
        </div>

        <!-- Paginación inicial -->
        <div id="pager" class="mt-3">
          <?php
          $totalPagesInit = max(1, (int) ceil($totalInit / $perPage));
          if ($totalPagesInit > 1) {
            echo '<nav aria-label="Paginación"><ul class="pagination justify-content-center mb-0">';
            echo '<li class="page-item disabled"><span class="page-link">«</span></li>';
            for ($k = 1; $k <= $totalPagesInit; $k++) {
              echo '<li class="page-item ' . ($k == 1 ? 'active' : '') . '"><a class="page-link" href="#" data-p="' . $k . '">' . $k . '</a></li>';
            }
            echo '<li class="page-item ' . ($totalPagesInit == 1 ? 'disabled' : '') . '"><a class="page-link" href="#" data-p="2">»</a></li>';
            echo '</ul></nav>';
            echo '<div class="text-center small text-muted mt-2">Mostrando ' . ($result->num_rows ? ('1–' . $result->num_rows) : 0) . ' de ' . $totalInit . ' resultados</div>';
          }
          ?>
        </div>
      </div>
    </div>
    <!-- /Card -->

  </div>


  <script>
    // ===== 4) FRONT: BUSCADOR + FILTRO + PAGER (AJAX) =====
    function debounce(fn, delay) { let t; return function () { clearTimeout(t); const a = arguments, c = this; t = setTimeout(() => fn.apply(c, a), delay); } }

    function fetchPage(p = attendanceState.p) {

      attendanceState.p = p;

      const url = new URL(window.location.href);
      url.searchParams.set('ajax', '1');
      url.searchParams.set('q', attendanceState.q);
      url.searchParams.set('f', attendanceState.f);
      url.searchParams.set('p', attendanceState.p);
      url.searchParams.set('pp', attendanceState.pp);

      fetch(url.toString(), {
        method: 'GET',
        credentials: 'same-origin',
        cache: 'no-store'
      })
        .then(r => r.json())
        .then(data => {
          document.getElementById('members-body').innerHTML = data.rows_html || '';
          document.getElementById('pager').innerHTML = data.pager_html || '';
          attachDeleteConfirms();
          attachPager();
        })
        .catch(() => {
          document.getElementById('members-body').innerHTML =
            '<tr><td colspan="6" class="text-danger text-center py-4">Error al cargar datos</td></tr>';
          document.getElementById('pager').innerHTML = '';
        });
    }


    function attachDeleteConfirms() {
      // 🔴 ELIMINAR ASISTENCIA DE HOY
      document.querySelectorAll('.btn-delete').forEach(btn => {
        btn.onclick = function () {
          confirmDelete({
            id: this.dataset.id,
            url: location.pathname,
            param: 'delete_attendance',
            title: '¿Eliminar asistencia?',
            text: 'Se eliminará la asistencia registrada para hoy.',
            successTitle: 'Asistencia eliminada',
            successText: 'La asistencia de hoy fue eliminada correctamente.',
            onSuccess: () => fetchPage(attendanceState.p)

          });
        };
      });

      // 🟢 REGISTRAR ASISTENCIA
      document.querySelectorAll('.btn-registrar').forEach(btn => {
        btn.onclick = function () {
          confirmRedirect({
            url: 'acciones/ver_asistencia.php?id=' + this.dataset.id,
            title: 'Registrar asistencia',
            text: '¿Deseas registrar la asistencia de este cliente?',
            confirmText: 'Sí, registrar'
          });
        };
      });
    }



    function attachPager() {
      document.querySelectorAll('#pager .page-link[data-p]').forEach(a => {
        a.addEventListener('click', e => {
          e.preventDefault();
          const p = parseInt(a.getAttribute('data-p'), 10);
          if (!isNaN(p)) fetchPage(p);
        });
      });
    }

    // BUSCADOR
    document.getElementById('search').addEventListener(
      'keyup',
      debounce(e => {
        attendanceState.q = e.target.value.trim();
        attendanceState.p = 1;
        fetchPage();
      }, 250)
    );



    // FILTRO (🔥 ESTE ERA EL PROBLEMA)
    document.getElementById('filter').addEventListener('change', e => {
      attendanceState.f = e.target.value;
      attendanceState.p = 1;
      fetchPage();
    });

    document.getElementById('btnClear').addEventListener('click', () => {
      document.getElementById('search').value = '';
      attendanceState.q = '';
      attendanceState.p = 1;
      fetchPage();
    });


    // Inicial
    attachDeleteConfirms();
    attachPager();
  </script>
  <script>
    /* ===============================
       ESTADO GLOBAL DE ASISTENCIAS
    =============================== */
    const attendanceState = {
      q: '',
      f: 'all',   // all | yes | no
      p: 1,
      pp: 15
    };


    document.addEventListener('DOMContentLoaded', () => {
      fetchPage(1);
    });


  </script>





  <style>
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
      max-width: 900px;
      margin: 0 auto;
      text-align: center;
      color: #fff;
    }

    .page-title {
      font-size: 1.35rem;
      font-weight: 700;
      margin-bottom: 4px;
    }

    .page-subtitle {
      font-size: .85rem;
      opacity: .9;
      margin-bottom: 0;
    }
  </style>
  <!-- END: migrated original content -->

  <?php include __DIR__ . '/theme/sb2/footer.php'; ?>
  <style>
    /* ===============================
   BUSCADOR PREMIUM – ASISTENCIAS
=============================== */
    .attendance-search {
      display: flex;
      align-items: center;
      gap: 10px;

      background: #ffffff;
      /* una sola caja */
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
      /* 🔥 clave */
      color: #343a40;
      min-width: 240px;
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

    .members-actions .btn-outline-primary,
    .members-table td .btn-outline-primary {
      border-radius: 999px;
      padding: .25rem .6rem;
    }
  </style>