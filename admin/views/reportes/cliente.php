
<div class="sb2-content d-flex flex-column min-vh-100">
require_once __DIR__ . '/../includes/membership_helper.php';

if (!$db instanceof mysqli) {
  die('No hay conexión a la base de datos');
}



$qry = "
SELECT 
  m.user_id,
  m.fullname



FROM members m
ORDER BY m.fullname
";

$result = mysqli_query($db, $qry);
$cnt = 1;

function e($str) {
  return htmlspecialchars((string)$str, ENT_QUOTES, 'UTF-8');
}

?>

<style>
/* === CARD ESTRECHO Y CENTRADO === */
.rep-wrapper{
  display: flex;
  justify-content: center;
}

.rep-card{
  width: 100%;
  max-width: 760px;        /* 🔹 ancho controlado */
  border: 0;
  border-radius: 1rem;
  overflow: hidden;
}

.rep-card .card-header{
  background: linear-gradient(90deg,#4e73df,#1cc88a);
  color:#fff;
}

/* === TABLA === */
.rep-table-wrap{ max-height: 65vh; overflow:auto; }

.rep-table th,
.rep-table td{
  text-align: center;
  vertical-align: middle;
}

.rep-table thead th{
  font-size: 0.95rem;
  font-weight: 600;
}

.rep-table tbody td{
  font-size: 1rem;
  padding: .65rem .4rem;
}

/* Columna nombre un poco más ancha */
.rep-table .col-name{
  text-align: left;
  padding-left: .75rem;
}

/* === BOTÓN === */
.actions .btn{
  font-size: .75rem;
  padding: .25rem .55rem;
  border-radius: 999px;
  white-space: nowrap;
}

.actions .btn i{
  margin-right: 4px;
  font-size: .75rem;
}

.badge-pillish{
  border-radius: 999px;
  font-weight: 500;
}

#footer{ color:#fff; }
</style>

<div class="container-fluid py-3 flex-grow-1">

<!-- ===== HEADER TIPO BANDA (ESTÁNDAR SISTEMA) ===== -->
<div class="page-header mb-4">
  <div class="page-header-inner">
    <h1 class="page-title title-inline">
      Informe clientes
      <i class="fas fa-file-alt ml-2"></i>
    </h1>
    <p class="page-subtitle">
      Listado compacto de informes por cliente
    </p>
  </div>
</div>


  <!-- CARD CENTRADO -->
  <div class="rep-wrapper">
    <div class="card shadow mb-4 rep-card">

      <div class="card-header py-3 d-flex flex-wrap align-items-center justify-content-between">
        <h6 class="m-0">
          <i class="fas fa-table mr-1"></i> Informes disponibles
        </h6>

        <!-- BUSCADOR -->
        <div style="max-width: 300px; width:100%;">
          <div class="input-group input-group-sm">
            <div class="input-group-prepend">
              <span class="input-group-text bg-white border-right-0">
                <i class="fas fa-search text-muted"></i>
              </span>
            </div>
            <input id="tableSearch" type="text" class="form-control bg-light border-left-0"
                   placeholder="Buscar..." autocomplete="off">
            <div class="input-group-append">
              <button id="clearSearch" class="btn btn-outline-light" type="button">
                <i class="fas fa-times"></i>
              </button>
            </div>
          </div>
        </div>
      </div>

      <div class="card-body">
        <div class="table-responsive rep-table-wrap">
          <table id="reportTable" class="table table-hover table-sm mb-0 rep-table">
            <thead class="thead-light sticky-header">
              <tr>
                <th style="width:60px;">#</th>
                <th class="col-name">Nombre completo</th>
                <th style="width:160px;">Membresía</th>
                <th style="width:120px;">Acción</th>
              </tr>
            </thead>
            <tbody>
              <?php if ($result && mysqli_num_rows($result) > 0): ?>
                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                  <tr data-row>
                    <td class="text-muted"><?php echo $cnt++; ?></td>
                    <td class="col-name"><?php echo e($row['fullname']); ?></td>
                    <td>
                      <?php

$m = membership_last($db, (int)$row['user_id']);
$status = membership_status($m);
$planNombre = $m['plan_nombre'] ?? null;

if ($status === 'activa') {
  $badge = 'badge-success';
  $label = $planNombre;
} elseif ($status === 'por_vencer') {
  $badge = 'badge-warning';
  $label = $planNombre . ' (Por vencer)';
} elseif ($status === 'vencida') {
  $badge = 'badge-danger';
  $label = $planNombre . ' (Vencida)';
} else {
  $badge = 'badge-secondary';
  $label = 'Sin membresía';
}
?>
<span class="badge <?= $badge; ?> badge-pillish">
  <?= e($label); ?>
</span>


                    </td>
                    <td class="actions">
                      <a href="ver_reporte_cliente.php?id=<?php echo (int)$row['user_id']; ?>"
                         class="btn btn-outline-primary btn-sm">
                        <i class="fas fa-file"></i> Ver Informe
                      </a>
                    </td>
                  </tr>
                <?php endwhile; ?>
              <?php else: ?>
                <tr>
                  <td colspan="4" class="text-center text-muted py-4">
                    No hay clientes para mostrar.
                  </td>
                </tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>

       
      </div>
    </div>
  </div>

  <!-- FOOTER -->
  <div class="row-fluid">
    <div id="footer" class="span12">&copy; La Paz - Bolivia <?php echo date("Y");?></div>
  </div>

</div>
</div>


<!-- Buscador en vivo -->
<script>
(function() {
  const input = document.getElementById('tableSearch');
  const clear = document.getElementById('clearSearch');
  const rows  = document.querySelectorAll('#reportTable tbody tr[data-row]');

  function normalize(s){
    return (s||'').toString().toLowerCase()
      .normalize('NFD').replace(/[\u0300-\u036f]/g,'');
  }
  function filter() {
    const q = normalize(input.value);
    rows.forEach(tr => {
      tr.style.display = normalize(tr.innerText).includes(q) ? '' : 'none';
    });
  }

  input.addEventListener('input', filter);
  clear.addEventListener('click', () => {
    input.value='';
    filter();
    input.focus();
  });
})();
</script>
<style>
  /* TÍTULOS CON ICONO DESPUÉS DEL TEXTO */
/* ===============================
   PAGE HEADER – ESTÁNDAR SISTEMA
=============================== */
.page-header{
  background: linear-gradient(135deg,#4e73df,#1cc88a);
  border-radius: 1rem;
  padding: 1.4rem 1rem;
  box-shadow: 0 8px 20px rgba(0,0,0,.16);
}

.page-header-inner{
  max-width: 900px;
  margin: 0 auto;
  text-align: center;
  color: #fff;
}

.page-title{
  font-size: 1.35rem;
  font-weight: 700;
  margin-bottom: 4px;
}

.page-subtitle{
  font-size: .85rem;
  opacity: .9;
  margin-bottom: 0;
}

/* ICONO DESPUÉS DEL TEXTO */
.title-inline{
  display:inline-flex;
  align-items:center;
  justify-content:center;
}

.title-inline i{
  font-size:1.05rem;
  opacity:.95;
}

</style>