<?php
// [MVC] bootstrap loaded by entry point
// [MVC] auth loaded by entry point
/* =======================
   AJAX – LISTADO CLIENTES
======================= */
if (isset($_GET['ajax'])) {

  header('Content-Type: text/html; charset=utf-8');
  header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
  header('Pragma: no-cache');

  $cnt = 1;
  $q = trim($_GET['q'] ?? '');

  if ($q !== '') {
    $like = "%{$q}%";

    $stmt = $db->prepare("
      SELECT user_id, fullname, username, gender, contact, dor, ci, correo, amount, services, plan
      FROM members
      WHERE fullname LIKE ? OR username LIKE ? OR gender LIKE ?
         OR contact LIKE ? OR ci LIKE ? OR correo LIKE ? OR services LIKE ? OR dor LIKE ?
      ORDER BY fullname ASC
      LIMIT 300
    ");

    if (!$stmt) {
      echo "<tr><td colspan='12' class='text-danger text-center'>Error en consulta</td></tr>";
      exit;
    }

    $stmt->bind_param('ssssssss', $like, $like, $like, $like, $like, $like, $like, $like);

  } else {

    $stmt = $db->prepare("
      SELECT user_id, fullname, username, gender, contact, dor, ci, correo, amount, services, plan
      FROM members
      ORDER BY fullname ASC
      LIMIT 300
    ");

    if (!$stmt) {
      echo "<tr><td colspan='12' class='text-danger text-center'>Error en consulta</td></tr>";
      exit;
    }
  }

  $stmt->execute();
  $result = $stmt->get_result();

  if (!$result || $result->num_rows === 0) {
    echo "<tr><td colspan='12'><div class='empty-state'>No se encontraron registros.</div></td></tr>";
  } else {
    while ($row = $result->fetch_assoc()) {
      echo "<tr>
              <td class='text-center'>" . $cnt . "</td>
              <td class='text-center'>" . htmlspecialchars($row['fullname']) . "</td>
              <td class='text-center'>@" . htmlspecialchars($row['username']) . "</td>
              <td class='text-center'>" . htmlspecialchars($row['gender']) . "</td>
              <td class='text-center'>" . htmlspecialchars($row['contact']) . "</td>
              <td class='text-center'>" . htmlspecialchars($row['dor']) . "</td>
              <td class='text-center'>" . htmlspecialchars($row['ci']) . "</td>
              <td class='text-center'>" . htmlspecialchars($row['correo']) . "</td>
              <td class='text-center'>Bs. " . htmlspecialchars($row['amount']) . "</td>
              <td class='text-center'>" . htmlspecialchars($row['services']) . "</td>
              <td class='text-center'>" . htmlspecialchars($row['plan']) . " Mes(es)</td>
              <td class='text-center'>
                <a href='edit_clienteform.php?id=" . urlencode($row['user_id']) . "' class='btn btn-success btn-sm list-btn-sm'>
                  <i class='fas fa-edit'></i> Actualizar
                </a>
              </td>
            </tr>";
      $cnt++;
    }
  }

  $stmt->close();
  exit;
}

/* ====== LAYOUT NORMAL ====== */
$pageTitle = 'Panel';
include __DIR__ . '/theme/sb2/header.php';
include __DIR__ . '/theme/sb2/sidebar.php';
include __DIR__ . '/theme/sb2/topbar.php';
?>

<div class="sb2-content">
  <div class="container-fluid">

    <div class="page-header mb-4">
      <div class="page-header-inner">
        <h1 class="page-title text-center">
          Lista Clientes Registrados <i class="fas fa-users"></i>
        </h1>
      </div>
    </div>

    <div class="card shadow mb-4 list-card">
      <div class="card-header list-card-head d-flex justify-content-between align-items-center flex-wrap gap-2">

        <div class="d-flex align-items-center gap-2">
          <span class='icon'><i class='fas fa-th'></i></span>
          <h5 class="mb-0">Tabla de Clientes</h5>
        </div>

        <form id="searchForm" class="list-search d-flex gap-2 align-items-center" onsubmit="return false;">
          <div class="search-wrap position-relative flex-grow-1">
            <i class="fas fa-search search-icon"></i>
            <input type="text" id="search" placeholder="Buscar por nombre, usuario, CI o servicio" autocomplete="off"
              class="form-control form-control-sm search-input">
          </div>
          <button type="button" id="btnSearch" class="btn btn-primary btn-sm">Buscar</button>
          <button type="button" id="btnClear" class="btn btn-light btn-sm">Limpiar</button>
        </form>

      </div>

      <div class="card-body table-responsive list-table-wrap">
        <table class='table table-bordered table-hover list-table'>
          <thead class="thead-dark text-center">
            <tr>
              <th>#</th>
              <th>Nombre Completo</th>
              <th>Usuario</th>
              <th>Género</th>
              <th>Contacto</th>
              <th>Fecha Registro</th>
              <th>CI</th>
              <th>Correo</th>
              <th>Costo</th>
              <th>Membresía</th>
              <th>Plan</th>
              <th>Acción</th>
            </tr>
          </thead>
          <tbody id="members-body">
            <tr>
              <td colspan="12" class="text-center text-muted">Cargando clientes...</td>
            </tr>
          </tbody>
        </table>
      </div>

    </div>
  </div>
</div>

<?php include __DIR__ . '/theme/sb2/footer.php'; ?>

<script>
  (function () {
    const $search = document.getElementById('search');
    const $btnSearch = document.getElementById('btnSearch');
    const $btnClear = document.getElementById('btnClear');
    const $tbody = document.getElementById('members-body');

    function debounce(fn, delay = 250) { let t; return (...args) => { clearTimeout(t); t = setTimeout(() => fn(...args), delay); }; }

    function fetchRows(q = '') {
      const url = new URL(window.location.href);
      url.searchParams.set('ajax', '1');
      url.searchParams.set('q', q);

      $tbody.innerHTML = "<tr><td colspan='12' class='text-center'>Buscando...</td></tr>";

      fetch(url, { cache: 'no-store' })
        .then(r => r.text())
        .then(html => { $tbody.innerHTML = html; })
        .catch(() => { $tbody.innerHTML = "<tr><td colspan='12' class='text-center text-danger'>Error al buscar</td></tr>"; });
    }

    $search.addEventListener('input', debounce(e => fetchRows(e.target.value.trim()), 300));
    $btnSearch.addEventListener('click', () => fetchRows($search.value.trim()));
    $btnClear.addEventListener('click', () => { $search.value = ''; fetchRows(''); });

    document.addEventListener('DOMContentLoaded', () => fetchRows());
  })();
</script>

<style>
  .list-card {
    border-radius: 12px;
    border: 1px solid #e8e8e8;
    background: #fff;
  }

  .list-card-head {
    background: #f7f8fa;
    border-bottom: 1px solid #eaeaea;
    padding: 10px 14px;
  }

  .list-table-wrap {
    max-height: 65vh;
    overflow: auto;
  }

  .list-table thead th {
    text-align: center;
  }

  .list-table tbody td {
    vertical-align: middle;
  }

  .list-table tbody tr:nth-child(odd) {
    background: #fcfcfc;
  }

  .list-table tbody tr:hover {
    background: #f5f7ff;
  }

  .list-btn-sm {
    padding: 6px 10px;
    border-radius: 8px;
  }

  .list-btn-sm i {
    margin-right: 4px;
  }

  .empty-state {
    text-align: center;
    padding: 16px;
    color: #6b7280;
  }

  .search-icon {
    position: absolute;
    left: 10px;
    top: 50%;
    transform: translateY(-50%);
    opacity: .6;
  }

  .search-input {
    padding-left: 32px;
  }
</style>