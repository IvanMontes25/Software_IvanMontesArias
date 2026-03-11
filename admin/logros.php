<?php
require_once __DIR__ . '/../core/bootstrap.php';
require_once __DIR__ . '/../core/auth.php';
require_once __DIR__ . '/../core/roles.php';
require_modulo('administracion');
if (!$db instanceof mysqli) {
  die('No hay conexión a la base de datos');
}
// ===== 2) Manejo de formularios (crear/editar) =====
/* ============================
   1) ELIMINAR
============================ */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete'])) {

  if (!function_exists('csrf_verify') || !csrf_verify($_POST['_csrf'] ?? ($_POST['csrf'] ?? null))) {
    http_response_code(403);
    exit('CSRF');
  }

  $id = filter_input(INPUT_POST, 'delete', FILTER_VALIDATE_INT);

  if ($id && $id > 0) {
    $stmt = $db->prepare("DELETE FROM logros WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
  }

  header("Location: logros.php");
  exit;
}

/* ============================
   2) CREAR / EDITAR
============================ */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

  if (!function_exists('csrf_verify') || !csrf_verify($_POST['_csrf'] ?? ($_POST['csrf'] ?? null))) {
    http_response_code(403);
    exit('CSRF');
  }

  $id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
  $nombre = trim($_POST['nombre'] ?? '');
  $meta = (int) ($_POST['meta_asistencias'] ?? 0);
  $descuento = (float) ($_POST['descuento_porcentaje'] ?? 0);
  $icono = trim($_POST['icono_fa'] ?? '');
  $activo = isset($_POST['activo']) ? 1 : 0;

  if ($id > 0) {
    $stmt = $db->prepare("UPDATE logros
                              SET nombre = ?, meta_asistencias = ?, descuento_porcentaje = ?, icono_fa = ?, activo = ?
                              WHERE id = ?");
    $stmt->bind_param("sidssi", $nombre, $meta, $descuento, $icono, $activo, $id);
  } else {
    $stmt = $db->prepare("INSERT INTO logros (nombre, meta_asistencias, descuento_porcentaje, icono_fa, activo)
                              VALUES (?,?,?,?,?)");
    $stmt->bind_param("sidss", $nombre, $meta, $descuento, $icono, $activo);
  }

  if ($stmt) {
    $stmt->execute();
    $stmt->close();
  }

  header("Location: logros.php");
  exit;
}


// ===== 4) Obtener lista de logros =====
$logros = [];
$res = $db->query("SELECT * FROM logros ORDER BY meta_asistencias ASC");
if ($res) {
  while ($row = $res->fetch_assoc()) {
    $logros[] = $row;
  }
}

$page = 'logros_admin';
?>
<?php include __DIR__ . '/theme/sb2/header.php'; ?>
<?php include __DIR__ . '/theme/sb2/sidebar.php'; ?>
<?php include __DIR__ . '/theme/sb2/topbar.php'; ?>

<div class="sb2-content d-flex flex-column min-vh-100">
  <div class="container-fluid py-3 flex-grow-1">

    <!-- ===== PAGE HEADER ===== -->
    <div class="page-header mb-4">
      <div class="page-header-inner">
        <h1 class="page-title">
          Logros por asistencia <i class="fas fa-medal ml-2"></i>
        </h1>
        <p class="page-subtitle">
          Configura metas de asistencia, descuentos automáticos e insignias
        </p>
      </div>
    </div>



    <style>
      /* ====== ESTILO VISUAL UNIFICADO (sin tocar funcionalidad) ====== */
      .logros-title {
        text-align: center;
        margin: 6px 0 18px;
      }

      .logros-title small {
        color: #6c757d;
      }

      .logros-card {
        border: 0;
        border-radius: 1rem;
        overflow: hidden;
      }

      .logros-card .card-header {
        background: linear-gradient(90deg, #4e73df, #1cc88a);
        color: #fff;
        border-bottom: 0;
      }

      .logros-card .card-header h6 {
        color: #fff;
      }

      .form-control {
        border-radius: 10px;
      }

      /* Tabla */
      .logros-table-wrap {
        max-height: 70vh;
        overflow: auto;
      }

      .logros-table {
        border-collapse: collapse;
        width: 100%;
      }

      .logros-table th,
      .logros-table td {
        border: 1px solid #dee2e6;
        text-align: center;
        vertical-align: middle;
      }

      .logros-table thead th {
        font-size: 0.95rem;
        font-weight: 600;
        background: #5a5c69;
        color: #fff;
        position: sticky;
        top: 0;
        z-index: 2;
      }

      .logros-table tbody td {
        font-size: 1.02rem;
        padding: .65rem .45rem;
      }

      .logros-table tbody tr:hover {
        background: rgba(0, 0, 0, .03);
      }

      .badge-pillish {
        border-radius: 999px;
        font-weight: 500;
      }

      /* Botones compactos (igual estilo que tus otras pantallas) */
      .btn-pill {
        border-radius: 999px !important;
        padding: .28rem .65rem;
        font-size: .78rem;
        line-height: 1.2;
        white-space: nowrap;
      }

      .btn-pill i {
        margin-right: 4px;
        font-size: .75rem;
      }

      .icon-preview {
        width: 32px;
        height: 32px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 999px;
        background: #f1f3f5;
        border: 1px solid rgba(0, 0, 0, .08);
      }
    </style>

    <div class="container-fluid sb2-content">


      <!-- Formulario y tabla -->
      <div class="row">

        <!-- Formulario para crear/editar logro -->
        <div class="col-lg-5">
          <div class="card shadow mb-4 logros-card">
            <div class="card-header py-3">
              <h6 class="m-0">
                <i class="fas fa-plus-circle mr-1"></i> Nuevo / Editar logro
              </h6>
            </div>
            <div class="card-body">
              <form method="post" action="logros.php">

                <input type="hidden" name="id" id="id_logro">
                <input type="hidden" name="csrf" value="<?= csrf_token() ?>">

                <div class="form-group">
                  <label class="font-weight-bold">Nombre del logro</label>
                  <input type="text" name="nombre" id="nombre" class="form-control" required>
                </div>

                <div class="form-group">
                  <label class="font-weight-bold">Meta de asistencias</label>
                  <input type="number" name="meta_asistencias" id="meta_asistencias" class="form-control" required
                    min="1">
                </div>

                <div class="form-group">
                  <label class="font-weight-bold">Descuento (%)</label>
                  <input type="number" step="0.01" name="descuento_porcentaje" id="descuento_porcentaje"
                    class="form-control" min="0" max="100">
                </div>



                <div class="form-group form-check">
                  <input type="checkbox" name="activo" id="activo" class="form-check-input" checked>
                  <label class="form-check-label" for="activo">Activo</label>
                </div>

                <button type="submit" class="btn btn-primary btn-sm btn-pill">
                  <i class="fas fa-save"></i> Guardar
                </button>
              </form>
            </div>
          </div>
        </div>

        <!-- Tabla de logros -->
        <div class="col-lg-7">
          <div class="card shadow mb-4 logros-card">
            <div class="card-header py-3 d-flex justify-content-between align-items-center flex-wrap">
              <h6 class="m-0">
                <i class="fas fa-table mr-1"></i> Lista de logros
              </h6>
              <span class="badge badge-light badge-pillish">
                Total: <?php echo number_format(count($logros)); ?>
              </span>
            </div>

            <div class="card-body">
              <div class="table-responsive logros-table-wrap">
                <table class="table table-sm mb-0 logros-table">
                  <thead>
                    <tr>
                      <th style="width:70px;">#</th>
                      <th>Nombre</th>
                      <th style="width:90px;">Meta</th>
                      <th style="width:120px;">Descuento</th>
                      <th style="width:90px;">Icono</th>
                      <th style="width:90px;">Activo</th>
                      <th style="width:240px;">Acciones</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php if (empty($logros)): ?>
                      <tr>
                        <td colspan="7" class="text-center text-muted py-4">No hay logros aún.</td>
                      </tr>
                    <?php else: ?>
                      <?php foreach ($logros as $l): ?>
                        <?php
                        $iconFa = trim((string) $l['icono_fa']);
                        if ($iconFa === '')
                          $iconFa = 'fa-medal';
                        ?>
                        <tr>
                          <td><?php echo (int) $l['id']; ?></td>
                          <td><?php echo htmlspecialchars($l['nombre']); ?></td>
                          <td><?php echo (int) $l['meta_asistencias']; ?></td>
                          <td><?php echo number_format((float) $l['descuento_porcentaje'], 2); ?>%</td>
                          <td>
                            <span class="icon-preview" title="<?php echo htmlspecialchars($iconFa); ?>">
                              <i class="fas <?php echo htmlspecialchars($iconFa); ?>"></i>
                            </span>
                          </td>
                          <td>
                            <?php if (!empty($l['activo'])): ?>
                              <span class="badge badge-success badge-pillish">Sí</span>
                            <?php else: ?>
                              <span class="badge badge-secondary badge-pillish">No</span>
                            <?php endif; ?>
                          </td>
                          <td>
                            <div class="btn-group btn-group-sm" role="group">
                              <button type="button" class="btn btn-sm btn-outline-secondary btn-pill btn-edit"
                                data-id="<?php echo (int) $l['id']; ?>"
                                data-nombre="<?php echo htmlspecialchars($l['nombre'], ENT_QUOTES); ?>"
                                data-meta="<?php echo (int) $l['meta_asistencias']; ?>"
                                data-desc="<?php echo htmlspecialchars($l['descuento_porcentaje'], ENT_QUOTES); ?>"
                                data-icono="<?php echo htmlspecialchars($l['icono_fa'], ENT_QUOTES); ?>"
                                data-activo="<?php echo (int) $l['activo']; ?>">
                                <i class="fas fa-edit"></i> Editar
                              </button>

                              <form method="POST">

                                <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
                                <input type="hidden" name="delete" value="<?= (int) $l['id']; ?>">

                                <button type="submit" class="btn btn-danger btn-sm">Eliminar</button>
                              </form>

                            </div>
                          </td>
                        </tr>
                      <?php endforeach; ?>
                    <?php endif; ?>
                  </tbody>
                </table>
              </div>

            </div>
          </div>
        </div>

      </div>
    </div>

    <script>
      document.querySelectorAll('.btn-edit').forEach(btn => {
        btn.addEventListener('click', () => {
          document.getElementById('id_logro').value = btn.dataset.id;
          document.getElementById('nombre').value = btn.dataset.nombre;
          document.getElementById('meta_asistencias').value = btn.dataset.meta;
          document.getElementById('descuento_porcentaje').value = btn.dataset.desc;
          document.getElementById('icono_fa').value = btn.dataset.icono;
          document.getElementById('activo').checked = (btn.dataset.activo === '1');

          // Preview icono
          const icon = (btn.dataset.icono || 'fa-medal').trim() || 'fa-medal';
          document.getElementById('iconPreview').className = 'fas ' + icon;

          window.scrollTo({ top: 0, behavior: 'smooth' });
        });
      });

      // Preview mientras escribe
      (function () {
        const input = document.getElementById('icono_fa');
        const preview = document.getElementById('iconPreview');
        if (!input || !preview) return;
        input.addEventListener('input', () => {
          const v = (input.value || 'fa-medal').trim() || 'fa-medal';
          preview.className = 'fas ' + v;
        });
      })();
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

    <?php include __DIR__ . '/theme/sb2/footer.php'; ?>