<!-- BEGIN: migrated original content -->
<div class="sb2-content">
<?php
// admin/pagos_historial.php (adaptado al layout de pagos.php)
require_once __DIR__ . '/../core/bootstrap.php';
if (!$db instanceof mysqli) {
  die('No hay conexión a la base de datos');
}
$today = date('Y-m-d');
$from  = isset($_GET['from']) ? $_GET['from'] : date('Y-m-01');
$to    = isset($_GET['to'])   ? $_GET['to']   : $today;
$q     = isset($_GET['q'])    ? trim($_GET['q']) : '';

$rows = pmt_list($db, $from, $to, $q);

// métricas rápidas
$total_pagos = 0.0;
$conteo = is_array($rows) ? count($rows) : 0;
if ($conteo) {
  foreach ($rows as $r) $total_pagos += (float)$r['amount'];
}
?>



  <div id="header"><h1><a href=""></a></h1></div>

  <!-- top bar y sidebar (como en pagos.php) -->
  <?php include __DIR__ . '/includes/topheader.php'; ?>
  <?php $page='payment'; include __DIR__ . '/includes/sidebar.php'; ?>

  <div id="content">
    <div id="content-header">
      <div id="breadcrumb">
        <a href="index.php" class="tip-bottom" title="Ir a Inicio"><i class="fas fa-home"></i> Inicio</a>
        <a href="pagos.php">Pagos</a>
        <a href="#" class="current">Historial</a>
      </div>
      <h1>Historial de Pagos</h1>
    </div>

    <div class="container-fluid" style="margin-top:-28px;">

      <!-- Filtros -->
      <div class="row-fluid">
        <div class="span12">
          <div class="widget-box">
            <div class="widget-title">
              <span class="icon"><i class="fas fa-filter"></i></span>
              <h5>Filtros</h5>
            </div>
            <div class="widget-content">
              <form method="get" class="form-horizontal filters">
                <div class="control-group">
                  <label class="control-label">Desde</label>
                  <div class="controls">
                    <input type="date" name="from" value="<?php echo htmlspecialchars($from); ?>" class="span3">
                  </div>
                </div>
                <div class="control-group">
                  <label class="control-label">Hasta</label>
                  <div class="controls">
                    <input type="date" name="to" value="<?php echo htmlspecialchars($to); ?>" class="span3">
                  </div>
                </div>
                <div class="control-group">
                  <label class="control-label">Buscar (Nombre o CI)</label>
                  <div class="controls">
                    <input type="text" name="q" value="<?php echo htmlspecialchars($q); ?>" class="span5" placeholder="Ej: Ana Pérez o 1234567">
                  </div>
                </div>
                <div class="form-actions">
                  <button class="btn btn-primary" type="submit"><i class="fas fa-search"></i> Filtrar</button>
                  <a class="btn" href="pagos_historial.php"><i class="fas fa-undo"></i> Limpiar</a>
                  <a class="btn btn-inverse" href="pagos.php"><i class="fas fa-arrow-left"></i> Ir a Pagos</a>
                </div>
              </form>

              <!-- Métricas rápidas -->
              <div class="row-fluid metrics">
                <div class="span3">
                  <div class="box">
                    <div class="muted">Pagos en el rango</div>
                    <div class="value"><?php echo (int)$conteo; ?></div>
                  </div>
                </div>
                <div class="span3">
                  <div class="box">
                    <div class="muted">Total (Bs)</div>
                    <div class="value">Bs. <?php echo number_format($total_pagos, 2, '.', ','); ?></div>
                  </div>
                </div>
                <div class="span6">
                  <div class="box">
                    <div class="muted">Rango</div>
                    <div class="value"><?php echo htmlspecialchars($from); ?> &rarr; <?php echo htmlspecialchars($to); ?></div>
                  </div>
                </div>
              </div>

            </div><!-- widget-content -->
          </div><!-- widget-box -->
        </div>
      </div>

      <!-- Tabla de historial --
</div>
<!-- END: migrated original content -->
