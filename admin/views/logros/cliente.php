
<div class="container-fluid sb2-content">

  <!-- Encabezado -->
  <div class="logros-hero mb-4 d-flex justify-content-between align-items-center flex-wrap">
  <div>
    <h1 class="h3 mb-0">
      🏆 Logros de <?= h($info['fullname']) ?>
    </h1>
    <small>@<?= h($info['username']) ?></small>
  </div>
  <div class="mt-3 mt-md-0">
    <a href="ver_asistencia.php?uid=<?= (int)$member_id ?>" class="btn btn-light btn-sm mr-2">
      <i class="fas fa-calendar-check"></i> Asistencias
    </a>
    <a href="clientes.php" class="btn btn-outline-light btn-sm">
      <i class="fas fa-users"></i> Clientes
    </a>
  </div>
</div>


  <!-- Tarjetas resumen -->
  <div class="row">

    <!-- Asistencias totales -->
    <div class="col-xl-4 col-md-6 mb-4">
      <div class="card stat-card border-left-success h-100 py-3">

        <div class="card-body">
          <div class="row no-gutters align-items-center">
            <div class="col mr-2">
              <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                Asistencias totales
              </div>
              <div class="h5 mb-0 font-weight-bold text-gray-800">
                <?= (int)$total ?> asistencia(s)
              </div>
            </div>
            <div class="col-auto stat-icon text-success">

              <i class="fas fa-dumbbell fa-2x text-gray-300"></i>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Siguiente logro -->
    <div class="col-xl-4 col-md-6 mb-4">
      <div class="card border-left-info shadow h-100 py-2">
        <div class="card-body">
          <div class="row no-gutters align-items-center">
            <div class="col mr-2">
              <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Siguiente logro</div>
              <?php if ($next): ?>
                <div class="h6 mb-0 font-weight-bold text-gray-800">
                  <?= h($next['label']) ?> (meta: <?= (int)$next['goal'] ?>)
                </div>
                <div class="mt-2">
                  <div class="progress" style="height: 8px;">
                    <div class="progress-bar" role="progressbar"
                         style="width: <?= $progress ?>%;"
                         aria-valuenow="<?= $progress ?>" aria-valuemin="0" aria-valuemax="100"></div>
                  </div>
                  <div class="small text-muted mt-1"><?= $progress ?>% completado</div>
                </div>
              <?php else: ?>
                <div class="h6 mb-0 font-weight-bold text-gray-800">
                  ¡Ha alcanzado todos los logros configurados! 🎉
                </div>
              <?php endif; ?>
            </div>
            <div class="col-auto">
              <i class="fas fa-flag-checkered fa-2x text-gray-300"></i>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Meta inmediata -->
    <div class="col-xl-4 col-md-12 mb-4">
      <div class="card border-left-warning shadow h-100 py-2">
        <div class="card-body">
          <div class="row no-gutters align-items-center">
            <div class="col mr-2">
              <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Meta inmediata</div>
              <?php if ($next): ?>
                <?php $faltan = max(0, (int)$next['goal'] - (int)$total); ?>
                <div class="h6 mb-0 font-weight-bold text-gray-800">
                  Le faltan <?= (int)$faltan ?> asistencia(s) para 
                  <span class="text-warning"><?= h($next['label']) ?></span>
                </div>
              <?php else: ?>
                <div class="h6 mb-0 font-weight-bold text-gray-800">
                  ¡Meta superada! Mantiene muy buena racha 💪
                </div>
              <?php endif; ?>
            </div>
            <div class="col-auto">
              <i class="fas fa-bolt fa-2x text-gray-300"></i>
            </div>
          </div>
        </div>
      </div>
    </div>

  </div>

  <!-- Lista de logros -->
  <div class="row">
    <div class="col-xl-12">
      <div class="card shadow mb-4">
        <div class="card-header py-3">
          <h6 class="m-0 font-weight-bold text-primary">Detalle de logros por asistencias</h6>
        </div>
        <div class="card-body">
          <?php if (empty($achievements)): ?>
            <p class="text-muted mb-0">No hay logros configurados actualmente.</p>
          <?php else: ?>
            <div class="row">
              <?php foreach ($achievements as $a): ?>
                <?php
                  $unlocked = ($total >= $a['goal']);
                  $badge = $unlocked ? 'badge-success' : 'badge-secondary';
                  $text  = $unlocked ? 'text-success'  : 'text-muted';
                ?>
                <div class="col-md-4 mb-3 d-flex">
                  <div class="card achievement <?= $unlocked ? 'unlocked' : 'locked' ?> p-3 flex-fill">

                    <div class="card-body d-flex flex-column">
                      <div class="d-flex align-items-center mb-2">
                        <div class="ach-icon mr-3">

                          <i class="fas <?= h($a['icon']) ?> fa-2x <?= $text ?>"></i>
                        </div>
                        <div>
                          <div class="font-weight-bold <?= $text ?>"><?= h($a['label']) ?></div>
                          <div class="small text-muted">
                            Meta: <?= (int)$a['goal'] ?> asistencia(s)
                          </div>
                          <?php if ($a['discount'] > 0): ?>
                            <div class="badge badge-info p-2 mt-1" style="font-size: 0.75rem;">
                              <i class="fas fa-tags"></i>
                              Descuento: <strong><?= number_format($a['discount'], 2) ?>%</strong>
                            </div>
                          <?php endif; ?>
                        </div>
                      </div>
                      <div class="mt-auto">
                        <?php if ($unlocked): ?>
                          <span class="badge <?= $badge ?>">Desbloqueado</span>
                        <?php else: ?>
                          <?php $rest = max(0, (int)$a['goal'] - (int)$total); ?>
                          <span class="badge <?= $badge ?>">Faltan <?= (int)$rest ?></span>
                        <?php endif; ?>
                      </div>
                    </div>
                  </div>
                </div>
              <?php endforeach; ?>
            </div>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>

</div>

<style>
/* ===== HERO HEADER ===== */
.logros-hero{
  background: linear-gradient(135deg,#4e73df,#1cc88a);
  border-radius: 1rem;
  padding: 1.4rem 1.6rem;
  color:#fff;
  box-shadow:0 10px 28px rgba(0,0,0,.18);
}
.logros-hero h1{ font-weight:800; margin-bottom:.2rem; }
.logros-hero small{ opacity:.9; }

/* ===== STAT CARDS ===== */
.stat-card{
  border-radius:1rem;
  box-shadow:0 8px 22px rgba(0,0,0,.12);
  transition:transform .2s ease, box-shadow .2s ease;
}
.stat-card:hover{
  transform:translateY(-4px);
  box-shadow:0 14px 34px rgba(0,0,0,.18);
}
.stat-icon{
  width:46px;height:46px;
  display:flex;align-items:center;justify-content:center;
  border-radius:50%;
  background:rgba(0,0,0,.05);
}

/* ===== PROGRESS ===== */
.progress{
  border-radius:10px;
  overflow:hidden;
}
.progress-bar{
  background: linear-gradient(90deg,#4e73df,#1cc88a);
}

/* ===== ACHIEVEMENTS ===== */
.achievement{
  border-radius:1rem;
  transition:.2s;
}
.achievement.locked{
  background:#f8f9fc;
  opacity:.75;
}
.achievement.unlocked{
  background:#ffffff;
  border-left:6px solid #20cfc7;
}
.achievement:hover{
  transform:translateY(-3px);
  box-shadow:0 10px 26px rgba(0,0,0,.15);
}
.ach-icon{
  width:48px;height:48px;
  display:flex;
  align-items:center;
  justify-content:center;
  border-radius:50%;
  background:#f1f4ff;
}




</style>


