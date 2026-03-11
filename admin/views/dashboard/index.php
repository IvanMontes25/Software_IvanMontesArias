
<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>

<div class="anl-wrapper">

  <!-- TITULO -->
  <div class="anl-page-header">
    <div>
      <incipal class="anl-page-title">Dashboard Principal</h1>
        <p class="anl-page-sub">Datos en tiempo real de Gym Body Training</p>
    </div>
    <div class="anl-header-right">
      <span class="anl-live-dot"></span>
      <span class="anl-live-text">En vivo</span>
      <button class="anl-btn-refresh" onclick="location.reload()">
        <i class="fas fa-sync-alt"></i>
      </button>
    </div>
  </div>

  <!-- ====== FILA 1: KPIs (estilo dashboard) ====== -->
  <div class="anl-kpi-row">

    <div class="anl-kpi-card-v2 anl-grad-blue">
      <span>Ingresos del Mes</span>
      <h2>Bs <?= number_format($ingresos_mes, 2) ?></h2>
      <small class="<?= $variacion >= 0 ? 'up' : 'down' ?>">
        <?= $variacion >= 0 ? '▲' : '▼' ?> <?= abs($variacion) ?>% vs mes anterior
      </small>
    </div>

    <div class="anl-kpi-card-v2 anl-grad-green">
      <span>Total Clientes</span>
      <h2><?= $total_clientes ?></h2>
      <small>+<?= $nuevos_mes ?> este mes</small>
    </div>

    <div class="anl-kpi-card-v2 anl-grad-teal">
      <span>Membresías Activas</span>
      <h2><?= $activas ?></h2>
      <small><?= $total_clientes > 0 ? round(($activas / $total_clientes) * 100) : 0 ?>% del total</small>
    </div>

    <div class="anl-kpi-card-v2 anl-grad-yellow">
      <span>Por Vencer (7 días)</span>
      <h2><?= $por_vencer ?></h2>
      <small>requieren atención</small>
    </div>

    <div class="anl-kpi-card-v2 anl-grad-purple">
      <span>Pagos Hoy</span>
      <h2><?= $pagos_hoy ?></h2>
      <small>Bs <?= number_format($ingresos_hoy, 2) ?></small>
    </div>

    <div class="anl-kpi-card-v2 anl-grad-red">
      <span>Tasa Retención</span>
      <h2><?= $tasa_retencion ?>%</h2>
      <small>vs mes anterior</small>
    </div>

  </div>

  <!-- ====== FILA 2: GRÁFICOS GRANDES ====== -->
  <div class="anl-row-2">
    <div class="anl-chart-card anl-chart-wide">
      <div class="anl-chart-header">
        <h3>Ingresos Mensuales</h3>
        <span class="anl-chart-tag">Últimos 12 meses</span>
      </div>
      <div class="anl-bar-wrap"><canvas id="chartIngresosMes"></canvas></div>
    </div>
    <div class="anl-chart-card anl-chart-narrow">
      <div class="anl-chart-header">
        <h3>Ingresos Diarios</h3>
        <span class="anl-chart-tag">Último mes</span>
      </div>
      <div class="anl-bar-wrap"><canvas id="chartIngresosDia"></canvas></div>
    </div>
  </div>

  <!-- ====== FILA 3: DONUTS + SEGMENTACIÓN ====== -->
  <div class="anl-row-3">
    <div class="anl-chart-card anl-donut-card">
      <div class="anl-chart-header">
        <h3>Planes Vendidos</h3>
      </div>
      <div class="anl-donut-wrap"><canvas id="chartPlanes"></canvas></div>
    </div>
    <div class="anl-chart-card anl-donut-card">
      <div class="anl-chart-header">
        <h3>Métodos de Pago</h3>
      </div>
      <div class="anl-donut-wrap"><canvas id="chartMetodos"></canvas></div>
    </div>
    <div class="anl-chart-card anl-donut-card">
      <div class="anl-chart-header">
        <h3>Segmentación de Clientes</h3>
      </div>
      <div class="anl-donut-wrap"><canvas id="chartSegmentos"></canvas></div>
    </div>
  </div>

  <!-- ====== FILA 4: HEATMAP + NUEVOS ====== -->
  <div class="anl-row-2">
    <div class="anl-chart-card anl-chart-wide">
      <div class="anl-chart-header">
        <h3>Actividad por Día de Semana</h3>
        <span class="anl-chart-tag">Pagos e ingresos</span>
      </div>
      <div class="anl-bar-wrap"><canvas id="chartDias"></canvas></div>
    </div>
    <div class="anl-chart-card anl-chart-narrow">
      <div class="anl-chart-header">
        <h3>Clientes Nuevos por Mes</h3>
        <span class="anl-chart-tag">Últimos 6 meses</span>
      </div>
      <div class="anl-bar-wrap"><canvas id="chartNuevos"></canvas></div>
    </div>
  </div>

  <!-- ====== FILA 5: TABLAS ====== -->
  <div class="anl-row-2">
    <div class="anl-chart-card anl-chart-wide">
      <div class="anl-chart-header">
        <h3>Clientes en Riesgo de Abandono</h3>
        <span class="anl-chart-tag anl-tag-red"><?= count($riesgo) ?> detectados</span>
      </div>
      <div class="anl-table-wrap">
        <table class="anl-table">
          <thead>
            <tr>
              <th>Cliente</th>
              <th>Teléfono</th>
              <th>Pagos</th>
              <th>Último Pago</th>
              <th>Días sin pago</th>
              <th>Riesgo</th>
            </tr>
          </thead>
          <tbody>
            <?php if (empty($riesgo)): ?>
              <tr>
                <td colspan="6" class="text-center" style="padding:20px;color:#1cc88a;font-weight:600">
                  <i class="fas fa-check-circle"></i> No hay clientes en riesgo
                </td>
              </tr>
            <?php else:
              foreach ($riesgo as $r): ?>
                <tr>
                  <td><strong><?= htmlspecialchars($r['fullname']) ?></strong></td>
                  <td><?= htmlspecialchars($r['contact'] ?: '—') ?></td>
                  <td class="text-center"><?= $r['total_pagos'] ?></td>
                  <td><?= $r['ultimo_pago'] ?: '—' ?></td>
                  <td class="text-center"><?= $r['dias_sin_pago'] ?></td>
                  <td>
                    <span class="anl-risk-badge <?= (int) $r['riesgo'] >= 70 ? 'anl-risk-high' : 'anl-risk-med' ?>">
                      <?= $r['riesgo'] ?>%
                    </span>
                  </td>
                </tr>
              <?php endforeach; endif; ?>
          </tbody>
        </table>
      </div>
    </div>

    <div class="anl-chart-card anl-chart-narrow">
      <div class="anl-chart-header">
        <h3>Últimos Pagos</h3>
        <span class="anl-chart-tag">Tiempo real</span>
      </div>
      <div class="anl-timeline">
        <?php foreach ($ultimos as $u): ?>
          <div class="anl-tl-item">
            <div class="anl-tl-dot"></div>
            <div class="anl-tl-content">
              <strong><?= htmlspecialchars($u['fullname']) ?></strong>
              <span class="anl-tl-amount">Bs. <?= number_format((float) $u['amount'], 2) ?></span>
              <div class="anl-tl-meta">
                <span><?= htmlspecialchars($u['plan_nombre']) ?></span>
                <span><?= htmlspecialchars($u['method']) ?></span>
                <span><?= $u['paid_date'] ?></span>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>

</div>


<!-- ====== CHART.JS INICIALIZACIÓN ====== -->
<script>
  (function () {
    const COLORS = {
      primary: '#4e73df', success: '#1cc88a', info: '#36b9cc',
      warning: '#f6c23e', danger: '#e74a3b', purple: '#6f42c1',
      orange: '#fd7e14', pink: '#e83e8c', teal: '#20c9a6',
      palette: ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b', '#6f42c1', '#fd7e14', '#e83e8c']
    };

    const defaults = Chart.defaults;
    defaults.font.family = "'Nunito','Segoe UI',Arial,sans-serif";
    defaults.font.size = 12;
    defaults.plugins.legend.labels.usePointStyle = true;
    defaults.plugins.legend.labels.padding = 16;

    // --- Ingresos Mensuales ---
    const meses = <?= $js_ingresos_meses ?>;
    new Chart(document.getElementById('chartIngresosMes'), {
      type: 'bar',
      data: {
        labels: meses.map(m => m.mes),
        datasets: [{
          label: 'Ingresos Bs.',
          data: meses.map(m => parseFloat(m.total)),
          backgroundColor: meses.map((_, i) => i === meses.length - 1 ? COLORS.success : COLORS.primary + '99'),
          borderColor: meses.map((_, i) => i === meses.length - 1 ? COLORS.success : COLORS.primary),
          borderWidth: 2,
          borderRadius: 6,
          borderSkipped: false
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: {
          y: { beginAtZero: true, grid: { color: '#f0f0f0' } },
          x: { grid: { display: false } }
        }
      }
    });

    // --- Ingresos Diarios ---
    const dias = <?= $js_ingresos_diarios ?>;
    new Chart(document.getElementById('chartIngresosDia'), {
      type: 'line',
      data: {
        labels: dias.map(d => d.dia.substring(5)),
        datasets: [{
          label: 'Bs.',
          data: dias.map(d => parseFloat(d.total)),
          borderColor: COLORS.info,
          backgroundColor: COLORS.info + '20',
          fill: true,
          tension: 0.4,
          pointRadius: 3,
          pointBackgroundColor: COLORS.info
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: {
          y: { beginAtZero: true, grid: { color: '#f0f0f0' } },
          x: { grid: { display: false }, ticks: { maxTicksLimit: 8 } }
        }
      }
    });

    // --- Planes Vendidos (Donut) ---
    const planes = <?= $js_planes ?>;
    new Chart(document.getElementById('chartPlanes'), {
      type: 'doughnut',
      data: {
        labels: planes.map(p => p.nombre),
        datasets: [{
          data: planes.map(p => parseInt(p.ventas)),
          backgroundColor: COLORS.palette.slice(0, planes.length),
          borderWidth: 3,
          borderColor: '#fff'
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        cutout: '60%',
        plugins: {
          legend: { position: 'bottom', labels: { padding: 10, font: { size: 11 } } }
        }
      }
    });

    // --- Métodos de Pago (Donut) ---
    const metodos = <?= $js_metodos ?>;
    new Chart(document.getElementById('chartMetodos'), {
      type: 'doughnut',
      data: {
        labels: metodos.map(m => m.method),
        datasets: [{
          data: metodos.map(m => parseInt(m.cantidad)),
          backgroundColor: [COLORS.success, COLORS.primary, COLORS.warning, COLORS.info, COLORS.purple],
          borderWidth: 3,
          borderColor: '#fff'
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        cutout: '60%',
        plugins: { legend: { position: 'bottom', labels: { padding: 10, font: { size: 11 } } } }
      }
    });

    // --- Segmentación (Donut) ---
    const segs = <?= $js_segmentos ?>;
    const segColors = { 'VIP': '#f6c23e', 'Regular': '#4e73df', 'Nuevo': '#1cc88a', 'Dormido': '#858796', 'Perdido': '#e74a3b', 'Sin pagos': '#d1d3e2' };
    new Chart(document.getElementById('chartSegmentos'), {
      type: 'doughnut',
      data: {
        labels: segs.map(s => s.segmento),
        datasets: [{
          data: segs.map(s => parseInt(s.cantidad)),
          backgroundColor: segs.map(s => segColors[s.segmento] || '#ccc'),
          borderWidth: 3,
          borderColor: '#fff'
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        cutout: '60%',
        plugins: { legend: { position: 'bottom', labels: { padding: 10, font: { size: 11 } } } }
      }
    });

    // --- Días de Semana ---
    const diasSem = <?= $js_dias_semana ?>;
    const diasNombres = ['', 'Dom', 'Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb'];
    new Chart(document.getElementById('chartDias'), {
      type: 'bar',
      data: {
        labels: diasSem.map(d => diasNombres[d.dow] || '?'),
        datasets: [
          {
            label: 'Pagos',
            data: diasSem.map(d => parseInt(d.pagos)),
            backgroundColor: COLORS.primary + '90',
            borderColor: COLORS.primary,
            borderWidth: 2,
            borderRadius: 6,
            yAxisID: 'y'
          },
          {
            label: 'Ingresos Bs.',
            data: diasSem.map(d => parseFloat(d.monto)),
            type: 'line',
            borderColor: COLORS.success,
            backgroundColor: COLORS.success + '20',
            tension: 0.4,
            pointRadius: 5,
            pointBackgroundColor: COLORS.success,
            yAxisID: 'y1'
          }
        ]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { position: 'top' } },
        scales: {
          y: { beginAtZero: true, position: 'left', grid: { color: '#f0f0f0' }, title: { display: true, text: 'Pagos' } },
          y1: { beginAtZero: true, position: 'right', grid: { display: false }, title: { display: true, text: 'Bs.' } },
          x: { grid: { display: false } }
        }
      }
    });

    // --- Nuevos por Mes ---
    const nuevos = <?= $js_nuevos_mes ?>;
    new Chart(document.getElementById('chartNuevos'), {
      type: 'bar',
      data: {
        labels: nuevos.map(n => n.mes),
        datasets: [{
          label: 'Nuevos',
          data: nuevos.map(n => parseInt(n.total)),
          backgroundColor: COLORS.purple + '80',
          borderColor: COLORS.purple,
          borderWidth: 2,
          borderRadius: 6
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: {
          y: { beginAtZero: true, ticks: { stepSize: 1 }, grid: { color: '#f0f0f0' } },
          x: { grid: { display: false } }
        }
      }
    });

  })();
</script>

<!-- ====== ESTILOS ====== -->
<style>
  .anl-wrapper {
    padding: 20px 30px 40px 20px;
    /* más aire a la derecha */
    max-width: 100%;
    margin: 0;
  }

  /* --- Header --- */
  .anl-page-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 24px;
  }

  .anl-page-title {
    font-size: 1.6rem;
    font-weight: 800;
    color: #2e3a59;
    margin: 0;
  }

  .anl-page-sub {
    font-size: .85rem;
    color: #858796;
    margin: 2px 0 0;
  }

  .anl-header-right {
    display: flex;
    align-items: center;
    gap: 10px;
  }

  .anl-live-dot {
    width: 10px;
    height: 10px;
    border-radius: 50%;
    background: #1cc88a;
    animation: anl-pulse 1.5s infinite;
  }

  @keyframes anl-pulse {

    0%,
    100% {
      box-shadow: 0 0 0 0 rgba(28, 200, 138, .5);
    }

    50% {
      box-shadow: 0 0 0 8px rgba(28, 200, 138, 0);
    }
  }

  .anl-live-text {
    font-size: .8rem;
    color: #1cc88a;
    font-weight: 700;
  }

  .anl-btn-refresh {
    background: #fff;
    border: 1px solid #d1d3e2;
    border-radius: 8px;
    padding: 6px 12px;
    cursor: pointer;
    color: #4e73df;
    font-size: .9rem;
    transition: all .2s;
  }

  .anl-btn-refresh:hover {
    background: #4e73df;
    color: #fff;
  }

  /* --- KPIs (estilo dashboard.php) --- */
  .anl-kpi-row {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 16px;
    margin-bottom: 24px;
  }

  .anl-kpi-card-v2 {
    border-radius: 16px;
    padding: 20px;
    color: #fff;
    box-shadow: 0 10px 30px rgba(0, 0, 0, .15);
    position: relative;
    overflow: hidden;
    transition: transform .2s, box-shadow .2s;
  }

  .anl-kpi-card-v2:hover {
    transform: translateY(-3px);
    box-shadow: 0 14px 36px rgba(0, 0, 0, .2);
  }

  .anl-kpi-card-v2::after {
    content: "";
    position: absolute;
    right: -40px;
    top: -40px;
    width: 120px;
    height: 120px;
    border-radius: 50%;
    background: rgba(255, 255, 255, .18);
    pointer-events: none;
  }

  .anl-kpi-card-v2 span {
    opacity: .95;
    font-weight: 600;
    letter-spacing: .2px;
    font-size: .85rem;
  }

  .anl-kpi-card-v2 h2 {
    font-weight: 800;
    margin: 8px 0 4px;
    font-size: 1.65rem;
  }

  .anl-kpi-card-v2 small {
    opacity: .85;
    font-size: .78rem;
  }

  .anl-kpi-card-v2 small.up {
    color: #b8ffbf;
    font-weight: 700;
    opacity: 1;
  }

  .anl-kpi-card-v2 small.down {
    color: #ffd0d0;
    font-weight: 700;
    opacity: 1;
  }

  .anl-grad-blue {
    background: linear-gradient(135deg, #4e73df, #224abe);
  }

  .anl-grad-green {
    background: linear-gradient(135deg, #1cc88a, #13855c);
  }

  .anl-grad-teal {
    background: linear-gradient(135deg, #36b9cc, #1a8a9a);
  }

  .anl-grad-yellow {
    background: linear-gradient(135deg, #f6c23e, #dda20a);
    color: #1f1f1f;
  }

  .anl-grad-yellow small {
    color: rgba(0, 0, 0, .6);
  }

  .anl-grad-yellow small.up {
    color: #1a5c00;
  }

  .anl-grad-yellow small.down {
    color: #8b0000;
  }

  .anl-grad-purple {
    background: linear-gradient(135deg, #6f42c1, #4e2d8b);
  }

  .anl-grad-red {
    background: linear-gradient(135deg, #e74a3b, #be2617);
  }

  /* --- Chart Cards --- */
  .anl-chart-card {
    background: #fff;
    border-radius: 14px;
    padding: 20px;
    box-shadow: 0 2px 12px rgba(0, 0, 0, .06);
  }

  .anl-chart-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 14px;
  }

  .anl-chart-header h3 {
    font-size: .95rem;
    font-weight: 700;
    color: #2e3a59;
    margin: 0;
  }

  .anl-chart-tag {
    font-size: .7rem;
    background: #e8eaed;
    color: #5a5c69;
    padding: 3px 10px;
    border-radius: 20px;
    font-weight: 600;
  }

  .anl-tag-red {
    background: #f8d7da;
    color: #721c24;
  }

  /* --- Row layouts --- */
  .anl-row-2 {
    display: grid;
    grid-template-columns: 1.6fr 1fr;
    gap: 16px;
    margin-bottom: 20px;
  }

  .anl-row-3 {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 16px;
    margin-bottom: 20px;
  }

  /* --- Donut sizing fix --- */
  .anl-donut-card {
    display: flex;
    flex-direction: column;
  }

  .anl-donut-wrap {
    flex: 1;
    max-height: 240px;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 0 10px 10px;
  }

  .anl-donut-wrap canvas {
    max-height: 220px !important;
    width: 100% !important;
  }

  /* --- Bar/Line chart sizing fix --- */
  .anl-bar-wrap {
    height: 220px;
    position: relative;
  }

  .anl-bar-wrap canvas {
    max-height: 100% !important;
    width: 100% !important;
  }

  @media (max-width: 992px) {

    .anl-row-2,
    .anl-row-3 {
      grid-template-columns: 1fr;
    }
  }

  /* --- Table --- */
  .anl-table-wrap {
    overflow-x: auto;
  }

  .anl-table {
    width: 100%;
    border-collapse: collapse;
    font-size: .85rem;
  }

  .anl-table th {
    background: #f8f9fc;
    padding: 10px 14px;
    text-align: left;
    font-weight: 700;
    color: #4e73df;
    font-size: .75rem;
    text-transform: uppercase;
    border-bottom: 2px solid #e3e6f0;
  }

  .anl-table td {
    padding: 10px 14px;
    border-bottom: 1px solid #f0f0f0;
    color: #5a5c69;
  }

  .anl-table tr:hover td {
    background: #f8f9fc;
  }

  .anl-risk-badge {
    padding: 4px 12px;
    border-radius: 20px;
    font-weight: 700;
    font-size: .8rem;
    display: inline-block;
    min-width: 50px;
    text-align: center;
  }

  .anl-risk-high {
    background: #f8d7da;
    color: #721c24;
  }

  .anl-risk-med {
    background: #fff3cd;
    color: #856404;
  }

  /* --- Timeline --- */
  .anl-timeline {
    display: flex;
    flex-direction: column;
    gap: 0;
  }

  .anl-tl-item {
    display: flex;
    gap: 12px;
    padding: 10px 0;
    border-bottom: 1px solid #f0f0f0;
  }

  .anl-tl-item:last-child {
    border-bottom: none;
  }

  .anl-tl-dot {
    width: 10px;
    height: 10px;
    border-radius: 50%;
    background: #4e73df;
    margin-top: 5px;
    flex-shrink: 0;
  }

  .anl-tl-item:nth-child(2n) .anl-tl-dot {
    background: #1cc88a;
  }

  .anl-tl-item:nth-child(3n) .anl-tl-dot {
    background: #36b9cc;
  }

  .anl-tl-content {
    flex: 1;
    min-width: 0;
  }

  .anl-tl-content strong {
    font-size: .85rem;
    color: #2e3a59;
  }

  .anl-tl-amount {
    float: right;
    font-weight: 700;
    color: #1cc88a;
    font-size: .85rem;
  }

  .anl-tl-meta {
    font-size: .72rem;
    color: #858796;
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
    margin-top: 2px;
  }

  .anl-tl-meta span {
    background: #f4f4f8;
    padding: 1px 7px;
    border-radius: 10px;
  }
</style>