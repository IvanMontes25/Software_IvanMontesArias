<?php
require_once __DIR__ . '/../core/bootstrap.php';
require_once __DIR__ . '/../core/auth.php';
require_once __DIR__ . '/../core/roles.php';
require_modulo('reportes');
if (!$db instanceof mysqli) {
  die('No hay conexión a la base de datos');
}

/* ====== Datos desde BD ====== */
// Género
$genderRows = [];
$q = mysqli_query($db, "SELECT gender, COUNT(*) AS number FROM members GROUP BY gender");
while ($r = mysqli_fetch_assoc($q)) {
  $label = trim((string)$r['gender']) !== '' ? $r['gender'] : 'Sin dato';
  $genderRows[] = [$label, (int)$r['number']];
}

// Servicios (planes)
$svcRows = [];

$sql = "
  SELECT pl.nombre AS servicio, COUNT(*) AS number
  FROM payments p
  INNER JOIN planes pl ON pl.id = p.plan_id
  WHERE pl.estado = 'activo'
  GROUP BY pl.id, pl.nombre
  ORDER BY number DESC
";

$q2 = mysqli_query($db, $sql);

if ($q2) {
  while ($r = mysqli_fetch_assoc($q2)) {
    $svcRows[] = [$r['servicio'], (int)$r['number']];
  }
} else {
  error_log(mysqli_error($db));
}



$ingRows = []; // [['2025-01', 1200], ...]
$gasRows = []; // [['2025-01',  500], ...]

?>
<?php $pageTitle = isset($pageTitle) ? $pageTitle : 'Panel'; ?>
<?php include __DIR__ . '/theme/sb2/header.php'; ?>
<?php include __DIR__ . '/theme/sb2/sidebar.php'; ?>
<?php include __DIR__ . '/theme/sb2/topbar.php'; ?>

<div class="sb2-content">
  <!-- Header vacío original (lo dejo para no romper nada del theme) -->
  <div id="header"><h1><a href="dashboard.html"></a></h1></div>

  <div id="content" class="reports-wrap">

<!-- ===== HEADER ESTÁNDAR DEL SISTEMA ===== -->
<div class="page-header mb-4">
  <div class="page-header-inner">
    <h1 class="page-title title-inline">
      Panel de Informes
      <i class="fas fa-chart-pie ml-2"></i>
    </h1>
    <p class="page-subtitle">
      Visualiza ingresos, gastos, clientes y servicios en un solo lugar
    </p>
  </div>
</div>


    <!-- ====== HERO / ENCABEZADO ====== -->
    <div class="reports-hero">
      <div class="reports-hero__left">
        
        <h1> Panel de Informes</h1>
        <div class="reports-subtitle">Visualiza ingresos, gastos, clientes y servicios en un solo lugar.</div>
      </div>
      <div class="reports-hero__right">
        <span class="badge-soft"><i class="fas fa-clock"></i> <?php echo date('Y-m-d H:i'); ?></span>
      </div>
    </div>

    <!-- ====== CARD: Ingresos vs Gastos ====== -->
    <div class="container-fluid">
      <div class="row-fluid">
        <div class="span12">
          <div class="report-card">
            <div class="report-card__head">
              <div>
                <div class="report-card__kicker">Finanzas</div>
                <div class="report-card__title">
                  Informe de Ingresos y Gastos <i class="fas fa-chart-bar"></i>
                </div>
                <div class="report-card__hint">Filtra por rango mensual para comparar tendencias.</div>
              </div>

              <!-- Filtro de mes (YYYY-MM) -->
              <div class="report-filters">
                <div class="filter-item">
                  <label for="fromMonth">Desde</label>
                  <input id="fromMonth" type="month" class="filter-input">
                </div>

                <div class="filter-item">
                  <label for="toMonth">Hasta</label>
                  <input id="toMonth" type="month" class="filter-input">
                </div>

                <button id="applyRange" class="btn btn-mini btn-primary btn-apply">
                  <i class="fas fa-filter"></i> Aplicar
                </button>
              </div>
            </div>

            <div class="report-card__body">
              <div class="chart-box chart-box--sm">
                <canvas id="moneyChart"></canvas>
              </div>
              <div class="report-note">
                <i class="fas fa-info-circle"></i>
                Si aún no conectaste ingresos/gastos, el gráfico quedará en cero (normal).
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- ====== CARD: Clientes por Género ====== -->
    <div class="container-fluid">
      <div class="row-fluid">
        <div class="span12">
          <div class="report-card">
            <div class="report-card__head">
              <div>
                <div class="report-card__kicker">Clientes</div>
                <div class="report-card__title">
                  Informe de Clientes Registrados <i class="fas fa-user-friends"></i>
                </div>
                <div class="report-card__hint">Distribución por género (con total al centro).</div>
              </div>
            </div>

            <div class="report-card__body">
              <div class="chart-box chart-box--md">
                <canvas id="genderChart"></canvas>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- ====== CARD: Servicios ====== -->
    <div class="container-fluid">
      <div class="row-fluid">
        <div class="span12">
          <div class="report-card">
            <div class="report-card__head">
              <div>
                <div class="report-card__kicker">Servicios</div>
                <div class="report-card__title">
                  Informe de Servicios <i class="fas fa-dumbbell"></i>
                </div>
                <div class="report-card__hint">Ranking por cantidad de membresías.</div>
              </div>
            </div>

            <div class="report-card__body">
              <div class="chart-box chart-box--sm">
                <canvas id="svcChart"></canvas>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Footer-part -->
    <div class="row-fluid">
      <div id="footer" class="span12 footer-pro">
        &copy; La Paz - Bolivia <?php echo date("Y");?>
      </div>
    </div>
  </div><!-- /#content -->
</div><!-- /.sb2-content -->

<style>
  /* ======= Estética general (sin romper SB2/Matrix) ======= */
  .reports-wrap{
    padding: 14px 10px 22px 10px;
  }

  .reports-hero{
    max-width: 1100px;
    margin: 10px auto 18px auto;
    padding: 16px 16px;
    border-radius: 18px;
    background: linear-gradient(135deg, rgba(59,130,246,.18), rgba(34,197,94,.12));
    border: 1px solid rgba(255,255,255,.10);
    box-shadow: 0 10px 24px rgba(0,0,0,.12);
    display:flex;
    align-items:flex-start;
    justify-content:space-between;
    gap: 12px;
  }
  .reports-kicker{
    font-size: 12px;
    opacity:.85;
    letter-spacing:.2px;
    display:flex;
    align-items:center;
    gap:8px;
  }
  .reports-title{
    margin: 6px 0 4px 0;
    font-size: 22px;
    line-height: 1.2;
    font-weight: 800;
  }
  .reports-subtitle{
    font-size: 13px;
    opacity:.85;
  }
  .badge-soft{
    display:inline-flex;
    align-items:center;
    gap:8px;
    padding: 8px 10px;
    border-radius: 999px;
    font-size: 12px;
    background: rgba(255,255,255,.70);
    color:#0f172a;
    box-shadow: 0 8px 18px rgba(0,0,0,.10);
    white-space: nowrap;
  }

  .report-card{
    max-width: 1100px;
    margin: 0 auto 18px auto;
    background: #fff;
    border-radius: 18px;
    box-shadow: 0 10px 24px rgba(0,0,0,0.12);
    overflow: hidden;
    transition: transform .18s ease, box-shadow .18s ease;
  }
  .report-card:hover{
    transform: translateY(-2px);
    box-shadow: 0 14px 28px rgba(0,0,0,0.16);
  }

  .report-card__head{
    padding: 14px 16px;
    border-bottom: 1px solid rgba(15,23,42,.08);
    display:flex;
    align-items:flex-start;
    justify-content:space-between;
    gap: 14px;
    flex-wrap: wrap;
  }
  .report-card__kicker{
    font-size: 12px;
    font-weight: 700;
    color: #64748b;
    text-transform: uppercase;
    letter-spacing: .5px;
  }
  .report-card__title{
    font-size: 16px;
    font-weight: 800;
    color: #0f172a;
    margin-top: 2px;
  }
  .report-card__hint{
    font-size: 12px;
    color: #64748b;
    margin-top: 4px;
  }

  .report-card__body{
    padding: 14px 16px 16px 16px;
  }

  .chart-box{
    position: relative;
    width: 100%;
    margin: 0 auto;
  }
  .chart-box--sm{ height: 310px; }
  .chart-box--md{ height: 460px; }

  .report-filters{
    display:flex;
    align-items:flex-end;
    gap: 10px;
    flex-wrap: wrap;
    justify-content:flex-end;
  }
  .filter-item{
    display:flex;
    flex-direction: column;
    gap: 4px;
  }
  .filter-item label{
    font-size: 12px;
    color:#64748b;
    margin:0;
  }
  .filter-input{
    padding: 7px 10px;
    border-radius: 12px;
    border: 1px solid rgba(15,23,42,.15);
    outline: none;
    background: #fff;
    min-width: 165px;
  }
  .filter-input:focus{
    border-color: rgba(59,130,246,.55);
    box-shadow: 0 0 0 3px rgba(59,130,246,.18);
  }

  .btn-apply{
    border-radius: 12px !important;
    padding: 7px 10px !important;
    display:inline-flex;
    align-items:center;
    gap:8px;
  }

  .report-note{
    margin-top: 10px;
    font-size: 12px;
    color:#64748b;
    display:flex;
    align-items:center;
    gap:8px;
  }

  .footer-pro{
    color: #fff !important;
    opacity: .95;
  }

  /* Responsive simple para pantallas chicas */
  @media (max-width: 640px){
    .reports-hero{ border-radius: 16px; }
    .filter-input{ min-width: 140px; }
    .chart-box--sm{ height: 280px; }
    .chart-box--md{ height: 420px; }
  }
</style>

<!-- Chart.js v4 -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3"></script>
<script>
  // ===== Datos desde PHP (a JS) =====
  const GENDER_ROWS = <?php echo json_encode($genderRows, JSON_UNESCAPED_UNICODE); ?>;
  const SVC_ROWS    = <?php echo json_encode($svcRows   , JSON_UNESCAPED_UNICODE); ?>;
  const ING_ROWS    = <?php echo json_encode($ingRows   , JSON_UNESCAPED_UNICODE); ?>;
  const GAS_ROWS    = <?php echo json_encode($gasRows   , JSON_UNESCAPED_UNICODE); ?>;

  // Helpers
  function makeGradients(ctx){
    const mk=(a,b)=>{const g=ctx.createLinearGradient(0,0,0,260);g.addColorStop(0,a);g.addColorStop(1,b);return g};
    return [
      mk('#60a5fa','#3b82f6'), mk('#34d399','#22c55e'), mk('#fbbf24','#f59e0b'),
      mk('#f87171','#ef4444'), mk('#a78bfa','#8b5cf6'), mk('#22d3ee','#06b6d4'), mk('#f472b6','#ec4899')
    ];
  }

  const baseOpts = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
      legend: { position: 'bottom', labels: { usePointStyle: true, pointStyle: 'circle' } },
      tooltip: { intersect: false, mode: 'index' }
    }
  };

  // ===== Pie/Doughnut: Género con total al centro =====
  (function(){
    const el = document.getElementById('genderChart');
    if (!el) return;
    const ctx = el.getContext('2d');

    const labels = (GENDER_ROWS||[]).map(r => r[0]);
    const data   = (GENDER_ROWS||[]).map(r => r[1]);
    const grads  = makeGradients(ctx);

    const centerText = {
      id: 'centerText',
      afterDraw(chart){
        const {ctx, chartArea:{left, right, top, bottom, width, height}} = chart;
        const ds = chart.data.datasets[0] || {data:[]};
        const total = ds.data.reduce((a,b)=>a+b,0);

        ctx.save();
        ctx.font = '800 26px system-ui, -apple-system, Segoe UI, Roboto';
        ctx.fillStyle = '#0f172a';
        ctx.textAlign = 'center';
        ctx.textBaseline = 'middle';
        ctx.fillText(String(total), left + width/2, top + height/2 - 8);

        ctx.font = '700 12px system-ui, -apple-system, Segoe UI, Roboto';
        ctx.fillStyle = '#64748b';
        ctx.fillText('Total', left + width/2, top + height/2 + 16);
        ctx.restore();
      }
    };

    new Chart(ctx, {
      type: 'doughnut',
      data: { labels, datasets: [{ data, backgroundColor: grads.slice(0, labels.length), borderWidth: 0, hoverOffset: 6 }]},
      options: { ...baseOpts, cutout:'58%' },
      plugins: [centerText]
    });
  })();

  // ===== Barras horizontales: Servicios =====
  (function(){
    const el = document.getElementById('svcChart');
    if (!el) return;
    const ctx = el.getContext('2d');
    const grads = makeGradients(ctx);

    const labels = (SVC_ROWS||[]).map(r => r[0]);
    const data   = (SVC_ROWS||[]).map(r => r[1]);

    new Chart(ctx, {
      type: 'bar',
      data: { labels, datasets: [{ label: 'Membresías', data, backgroundColor: grads.slice(0, labels.length), borderRadius: 8 }]},
      options: {
        ...baseOpts,
        indexAxis: 'y',
        scales: {
          x: { beginAtZero: true, grid: { drawBorder: false } },
          y: { grid: { display: false } }
        }
      }
    });
  })();

  // ===== Líneas: Ingresos vs Gastos con filtro simple =====
  (function(){
    const el = document.getElementById('moneyChart');
    if (!el) return;
    const ctx = el.getContext('2d');

    const baseIn = ING_ROWS || [];
    const baseGa = GAS_ROWS || [];

    function build(labels, mapIn, mapGa){
      return {
        labels,
        datasets: [
          { label: 'Ingresos', data: labels.map(m => Number(mapIn[m]||0)), tension:.35, fill:false },
          { label: 'Gastos',   data: labels.map(m => Number(mapGa[m]||0)), tension:.35, fill:false }
        ]
      };
    }

    function computeData(from=null, to=null){
      let meses = Array.from(new Set([...baseIn.map(r=>r[0]), ...baseGa.map(r=>r[0])])).sort();
      if (from) meses = meses.filter(m => m >= from);
      if (to)   meses = meses.filter(m => m <= to);
      const mapIn = Object.fromEntries(baseIn.map(r=>[r[0], r[1]]));
      const mapGa = Object.fromEntries(baseGa.map(r=>[r[0], r[1]]));
      return build(meses, mapIn, mapGa);
    }

    const chart = new Chart(ctx, {
      type:'line',
      data: computeData(),
      options:{
        ...baseOpts,
        scales: {
          x: { grid: { display: false } },
          y: { beginAtZero: true, grid: { drawBorder: false } }
        }
      }
    });

    // UI
    const $from = document.getElementById('fromMonth');
    const $to   = document.getElementById('toMonth');
    const $btn  = document.getElementById('applyRange');

    function apply(){
      const from = ($from && $from.value) ? $from.value : null; // 'YYYY-MM'
      const to   = ($to   && $to.value)   ? $to.value   : null;
      const d = computeData(from, to);
      chart.data = d;
      chart.update();
    }
    if ($btn) $btn.addEventListener('click', apply);
  })();
</script>

<?php include __DIR__ . '/theme/sb2/footer.php'; ?>
<style>
  /* Ocultar hero antiguo (título duplicado) */
.reports-hero{
  display:none;
}
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