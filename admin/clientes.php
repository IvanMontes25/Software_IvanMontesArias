<?php

require_once __DIR__ . '/../core/bootstrap.php';
require_once __DIR__ . '/../core/auth.php';
require_once __DIR__ . '/../includes/membership_helper.php';




function e($s)
{
  return htmlspecialchars((string) $s, ENT_QUOTES, 'UTF-8');
}
/* =======================
   AJAX – LISTADO CLIENTES
======================= */
if (isset($_GET['ajax'])) {

  header('Content-Type: application/json; charset=utf-8');

  $q = trim($_GET['q'] ?? '');
  $filtro = trim($_GET['estado'] ?? '');
  $page = max(1, (int) ($_GET['page'] ?? 1));
  $perPage = 35;
  $offset = ($page - 1) * $perPage;
  $today = new DateTime('today');

  // ============================================
  // CONSULTA BASE (SIN LIMIT)
  // ============================================

  $sql = "
    SELECT
      m.user_id,
      m.fullname,
      m.username,
      m.gender,
      m.contact,
      m.dor,
      m.ci,
      pl.nombre AS plan_nombre,
      p.start_date,
      p.paid_date,
      pl.duracion_dias
    FROM members m
    LEFT JOIN payments p
      ON p.id = (
        SELECT id FROM payments
        WHERE user_id = m.user_id
          AND status = 'pagado'
          AND plan_id IS NOT NULL
        ORDER BY id DESC
        LIMIT 1
      )
    LEFT JOIN planes pl ON pl.id = p.plan_id
    ORDER BY m.fullname
  ";

  $res = $db->query($sql);

  $allRows = [];

  while ($r = $res->fetch_assoc()) {

    // ==========================
    // CALCULAR ESTADO
    // ==========================

    $estadoCalc = 'sin_membresia';
    $estadoText = 'Sin membresía';
    $estadoBadge = '<span class="badge badge-secondary">Sin membresía</span>';
    $planNombre = $r['plan_nombre'] ?: 'Sin membresía';

    if ($r['plan_nombre'] && $r['duracion_dias'] > 0) {

      $startRaw = $r['start_date'] ?: $r['paid_date'];

      try {
        $startDate = new DateTime($startRaw);
        $endDate = clone $startDate;
        $endDate->modify("+{$r['duracion_dias']} days");
        $daysLeft = (int) $today->diff($endDate)->format('%r%a');

        if ($daysLeft < 0) {
          $estadoCalc = 'vencida';
          $estadoText = 'Vencida';
          $estadoBadge = '<span class="badge badge-danger">Vencida</span>';
        } elseif ($daysLeft <= 3) {
          $estadoCalc = 'por_vencer';
          $estadoText = 'Por vencer';
          $estadoBadge = '<span class="badge badge-warning">Por vencer</span>';
        } else {
          $estadoCalc = 'activa';
          $estadoText = 'Activa';
          $estadoBadge = '<span class="badge badge-success">Activa</span>';
        }
      } catch (Exception $e) {
      }
    }

    // ==========================
    // FILTRO GLOBAL
    // ==========================

    $busqueda = strtolower($q);

    $campos = [
      strtolower($r['fullname']),
      strtolower($r['username']),
      strtolower($r['ci']),
      strtolower($r['gender']),
      strtolower($planNombre),
      strtolower($estadoText)
    ];

    $coincideBusqueda = true;

    if ($busqueda !== '') {
      $coincideBusqueda = false;
      foreach ($campos as $campo) {
        if (strpos($campo, $busqueda) !== false) {
          $coincideBusqueda = true;
          break;
        }
      }
    }

    // ==========================
    // FILTRO POR ESTADO (SELECT)
    // ==========================

    $coincideEstado = true;
    if ($filtro !== '' && $estadoCalc !== $filtro) {
      $coincideEstado = false;
    }

    if (!$coincideBusqueda || !$coincideEstado)
      continue;

    $allRows[] = [
      'r' => $r,
      'estadoBadge' => $estadoBadge,
      'planNombre' => $planNombre
    ];
  }

  // ============================================
  // PAGINACIÓN EN PHP
  // ============================================

  $total = count($allRows);
  $totalPages = (int) ceil($total / $perPage);
  $slice = array_slice($allRows, $offset, $perPage);

  $rows = [];

  foreach ($slice as $idx => $item) {

    $r = $item['r'];
    $estadoBadge = $item['estadoBadge'];
    $planNombre = $item['planNombre'];
    $uid = (int) $r['user_id'];
    $num = $offset + $idx + 1;

    $rows[] = "
    <tr>
      <td class='text-muted'>$num</td>
      <td>" . e($r['fullname']) . "</td>
      <td class='text-center'><span class='badge badge-username'>@" . e($r['username']) . "</span></td>
      <td class='text-center'>" . e($r['gender']) . "</td>
      <td class='text-center'><span class='phone-muted'>" . e($r['contact']) . "</span></td>
      <td class='text-center'>" . e($r['dor']) . "</td>
      <td class='text-center'>" . e($r['ci']) . "</td>
      <td class='text-center'><span class='badge badge-service'>$planNombre</span></td>
      <td class='text-center'>$estadoBadge</td>
      <td class='text-center'>
        <div class='btn-group btn-group-sm'>
          <a href='perfil_cliente.php?id=$uid' class='btn btn-outline-info'><i class='fas fa-user'></i></a>
          <a href='logros_cliente.php?id=$uid' class='btn btn-outline-success'><i class='fas fa-trophy'></i></a>
          <button class='btn btn-outline-primary btn-edit' data-id='$uid'><i class='fas fa-edit'></i></button>
          <button class='btn btn-outline-danger btn-delete' data-id='$uid'><i class='fas fa-trash'></i></button>
        </div>
      </td>
    </tr>";
  }

  echo json_encode([
    'html' => $rows ? implode('', $rows) : "<tr><td colspan='10' class='text-center text-muted'>Sin registros</td></tr>",
    'total' => $total,
    'page' => $page,
    'totalPages' => $totalPages,
    'perPage' => $perPage,
  ]);

  exit;
}
/* =====================================================
   UI NORMAL
===================================================== */

include __DIR__ . '/theme/sb2/header.php';
include __DIR__ . '/theme/sb2/sidebar.php';
include __DIR__ . '/theme/sb2/topbar.php';



?>
<input type="hidden" id="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">


<div class="container-fluid px-5">


  <!-- ===== TÍTULO DE PÁGINA ===== -->
  <div class="page-header mb-4">
    <div class="page-header-inner">
      <h1 class="page-title">Clientes</h1>
      <p class="page-subtitle">
        Administración y control de clientes registrados en el sistema
      </p>
    </div>
  </div>

  <style>
    /* Header bonito + toolbar */
    .members-card {
      border: 0;
      border-radius: 14px;
      overflow: hidden;
    }

    .members-card>.card-header {
      background: linear-gradient(90deg, #4e73df, #1cc88a);
      color: #fff;
      border-bottom: 0;
    }

    .card-header-toolbar {
      width: 100%;
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 12px;
      flex-wrap: wrap;
    }

    .toolbar-left .muted {
      color: #fff;
      opacity: .95;
    }

    .badge-username {
      background: #eef2f7;
      color: #4e73df;
      font-weight: 600;
    }

    /* Loader/empty */
    .members-card .card-body {
      position: relative;
    }

    /* ✅ importante para overlay */
    .loader {
      position: absolute;
      inset: 0;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      gap: 10px;
      background: rgba(255, 255, 255, .65);
      z-index: 5;
    }

    .loader .spinner {
      width: 28px;
      height: 28px;
      border: 3px solid rgba(0, 0, 0, .1);
      border-top-color: rgba(0, 0, 0, .45);
      border-radius: 50%;
      animation: spin .8s linear infinite;
    }

    @keyframes spin {
      to {
        transform: rotate(360deg);
      }
    }

    .empty {
      text-align: center;
      color: #6c757d;
      padding: 24px 12px;
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

    /* ===============================
   TABLA CLIENTES – ESTILO FORMAL
=============================== */

    /* Teléfono más sobrio */
    .phone-muted {
      color: #4b5563;
      /* gris azulado */
      font-weight: 500;
      letter-spacing: .2px;
    }

    /* Badge de servicio neutro */
    .badge-service {
      background: #f1f5f9;
      /* gris claro */
      color: #334155;
      /* texto sobrio */
      border-radius: 999px;
      font-weight: 600;
      font-size: .75rem;
      padding: .25rem .6rem;
    }
  </style>

  <div class="card shadow mb-4 members-card">
    <div class="card-header py-3">
      <div class="card-header-toolbar">

        <div class="d-flex align-items-center">
          <h6 class="m-0">
            <i class="fas fa-table mr-1"></i> Tabla de clientes
          </h6>
          <span class="ml-3 muted">
            <i class="fas fa-database"></i>
            <strong id="countRows">0</strong> registros
          </span>
        </div>

        <!-- ✅ Buscador (estilo STAFF) -->
        <div class="mt-2 mt-md-0" style="max-width:360px;width:100%;">
          <div class="input-group input-group-sm">
            <div class="input-group-prepend">
              <span class="input-group-text bg-white border-right-0">
                <i class="fas fa-search text-muted"></i>
              </span>
            </div>

            <input type="text" class="form-control bg-light border-left-0"
              placeholder="Buscar por nombre, usuario, servicio o CI…" autocomplete="off" data-search-client>

            <div class="input-group-append">
              <button class="btn btn-outline-light" type="button" title="Limpiar" data-search-clear>
                <i class="fas fa-times"></i>
              </button>
            </div>
          </div>
        </div>

      </div>
    </div>

    <div class="card-body table-responsive">
      <table class="table table-hover table-sm mb-0">
        <thead class="thead-light">
          <tr>
            <th>#</th>
            <th>Nombre</th>
            <th>@ Usuario</th>
            <th>Género</th>
            <th>Teléfono</th>
            <th>Fecha de Registro</th>
            <th>CI</th>
            <th>Servicio</th>
            <th>Estado</th>
            <th>Acciones</th>
          </tr>
        </thead>
        <tbody id="members-body">
          <tr>
            <td colspan="10" class="text-center text-muted">Cargando clientes...</td>
          </tr>
        </tbody>
      </table>
      <div id="pagination" class="mt-3"></div>
      <div id="emptyState" class="empty d-none">
        <i class="fas fa-inbox"></i>
        <p>No se encontraron resultados.</p>
      </div>

      <div id="loader" class="loader d-none">
        <div class="spinner"></div>
        <span>Buscando…</span>
      </div>
    </div>
  </div>

    <?php include __DIR__ . '/theme/sb2/footer.php'; ?>

  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script>
    (function () {
      const input = document.querySelector('[data-search-client]');
      const clear = document.querySelector('[data-search-clear]');
      const tbody = document.getElementById('members-body');
      const loader = document.getElementById('loader');
      const count = document.getElementById('countRows');
      const paginationEl = document.getElementById('pagination');
      const filtroEstado = document.getElementById('filtroEstado');

      let currentPage = 1;

      function setLoading(v) { loader.classList.toggle('d-none', !v); }

      // ── Paginación ──────────────────────────────────────
      function renderPagination(page, totalPages) {
        if (totalPages <= 1) { paginationEl.innerHTML = ''; return; }

        let html = '<ul class="pagination pagination-sm justify-content-center mb-0">';

        // Botón anterior
        html += `<li class="page-item ${page === 1 ? 'disabled' : ''}">
      <a class="page-link" href="#" data-page="${page - 1}">&laquo;</a></li>`;

        // Números de página
        for (let i = 1; i <= totalPages; i++) {
          if (
            i === 1 || i === totalPages ||
            (i >= page - 2 && i <= page + 2)
          ) {
            html += `<li class="page-item ${i === page ? 'active' : ''}">
          <a class="page-link" href="#" data-page="${i}">${i}</a></li>`;
          } else if (i === page - 3 || i === page + 3) {
            html += `<li class="page-item disabled"><span class="page-link">…</span></li>`;
          }
        }

        // Botón siguiente
        html += `<li class="page-item ${page === totalPages ? 'disabled' : ''}">
      <a class="page-link" href="#" data-page="${page + 1}">&raquo;</a></li>`;

        html += '</ul>';
        paginationEl.innerHTML = html;

        // Eventos de click en páginas
        paginationEl.querySelectorAll('[data-page]').forEach(a => {
          a.addEventListener('click', e => {
            e.preventDefault();
            const p = parseInt(a.dataset.page);
            if (p < 1 || p > totalPages) return;
            currentPage = p;
            doSearch();
          });
        });
      }

      // ── Handlers delete/edit ────────────────────────────
      function attachDeleteHandlers() {
        document.querySelectorAll('.btn-delete').forEach(btn => {
          btn.onclick = function () {
            const id = this.dataset.id;
            if (!id) return;
            Swal.fire({
              title: '¿Eliminar cliente?',
              text: 'Esta acción no se puede deshacer.',
              icon: 'warning',
              showCancelButton: true,
              confirmButtonColor: '#e74a3b',
              cancelButtonColor: '#858796',
              confirmButtonText: 'Sí, eliminar',
              cancelButtonText: 'Cancelar',
            }).then(result => {
              if (!result.isConfirmed) return;
              const csrf = document.getElementById('csrf_token')?.value || '';
              fetch('cliente_actions.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'op=delete&csrf_token=' + encodeURIComponent(csrf) + '&delete_id=' + encodeURIComponent(id)
              })
                .then(r => r.json())
                .then(res => {
                  if (res.ok) {
                    Swal.fire({ icon: 'success', title: 'Cliente eliminado', timer: 1600, showConfirmButton: false });
                    doSearch();
                  } else {
                    Swal.fire({ icon: 'error', title: 'Error', text: res.msg || 'No se pudo eliminar' });
                  }
                })
                .catch(() => Swal.fire({ icon: 'error', title: 'Error de red' }));
            });
          };
        });
      }

      function attachEditHandlers() {
        document.querySelectorAll('.btn-edit').forEach(btn => {
          btn.onclick = function () {
            const id = this.dataset.id;
            if (!id) return;
            Swal.fire({
              title: 'Actualizar cliente',
              text: '¿Deseas editar la información de este cliente?',
              icon: 'question',
              showCancelButton: true,
              confirmButtonColor: '#4e73df',
              cancelButtonColor: '#858796',
              confirmButtonText: 'Sí, actualizar',
              cancelButtonText: 'Cancelar',
            }).then(result => {
              if (!result.isConfirmed) return;
              window.location.href = 'edit_clienteform.php?id=' + encodeURIComponent(id);
            });
          };
        });
      }

      // ── Búsqueda principal ──────────────────────────────
      function doSearch() {
        filtroEstado && filtroEstado.addEventListener('change', () => {
          currentPage = 1;
          doSearch();
        });
        const term = input.value.trim();
        setLoading(true);

        const estado = filtroEstado ? filtroEstado.value : '';
        fetch(`?ajax=1&q=${encodeURIComponent(term)}&page=${currentPage}&estado=${encodeURIComponent(estado)}`, { credentials: 'same-origin' })
          .then(r => r.json())
          .then(data => {
            tbody.innerHTML = data.html || '';
            count.textContent = data.total;
            renderPagination(data.page, data.totalPages);
            attachDeleteHandlers();
            attachEditHandlers();
          })
          .catch(() => {
            tbody.innerHTML = "<tr><td colspan='10' class='text-center text-danger'>Error de conexión</td></tr>";
          })
          .finally(() => setLoading(false));
      }

      // Reset página al buscar
      let t;
      input.addEventListener('input', () => {
        clearTimeout(t);
        currentPage = 1;
        t = setTimeout(doSearch, 300);
      });

      clear && clear.addEventListener('click', () => {
        input.value = '';
        currentPage = 1;
        doSearch();
        input.focus();
      });

      doSearch();
    })();
  </script>