<!-- ===== PAGE HEADER (ESTÁNDAR SISTEMA) ===== -->


<div class="sb2-content d-flex flex-column min-vh-100">
  <div class="container-fluid flex-grow-1 py-3">

    <!-- ===== PAGE HEADER ===== -->
    <div class="page-header mb-4">
      <div class="page-header-inner">
        <h1 class="page-title">
          Planes de Membresía
        </h1>
        <p class="page-subtitle">
          Gestión de planes, precios, duración y estado
        </p>
      </div>
    </div>


<style>
/* === ESTILO PRO (igual a tus otras tablas) === */
.plan-card{ border:0; border-radius:1rem; overflow:hidden; }
.plan-card .card-header{ background: linear-gradient(90deg,#4e73df,#1cc88a); color:#fff; }
.plan-table-wrap{ max-height: 70vh; overflow:auto; }
.plan-table thead th{ font-size: 1rem; font-weight: 600; vertical-align: middle; }
.plan-table tbody td{ font-size: 1.02rem; padding: .7rem .5rem; vertical-align: middle; }
.sticky-header{ position: sticky; top: 0; z-index: 2; }

.plan-table th, .plan-table td{ text-align:center; vertical-align:middle; }
.plan-table td.text-left, .plan-table th.text-left{ text-align:left !important; }
.plan-table td.text-right, .plan-table th.text-right{ text-align:right !important; }

.badge-pillish{ border-radius:999px; font-weight:500; }

/* Botones compactos bonitos */
.actions .btn{
  font-size:.78rem;
  padding:.25rem .55rem;
  line-height:1.2;
  border-radius:999px;
  white-space:nowrap;
}
.actions .btn i{ margin-right:3px; font-size:.75rem; }
.actions .btn-group .btn{ margin-right:2px; }
.actions .btn-group .btn:last-child{ margin-right:0; }

#footer { color:#fff; }
</style>

<div class="sb2-content d-flex flex-column min-vh-100">
  <div class="container-fluid flex-grow-1 py-3">





    <!-- Card -->
    <div class="card shadow mb-4 plan-card">
      <div class="card-header py-3 d-flex align-items-center justify-content-between flex-wrap gap-2">

  <!-- IZQUIERDA: TÍTULO -->
  <h6 class="m-0 d-flex align-items-center gap-2">
    <i class="fas fa-table"></i>
    Tabla de planes
  </h6>

  <!-- DERECHA: INFO + BOTÓN -->
  <div class="d-flex align-items-center gap-3">

    <span class="badge badge-light badge-pillish d-flex align-items-center">
      <i class="fas fa-info-circle mr-1"></i>
      Ordenado por estado y nombre
    </span>

    <a href="plan_form.php"
       class="btn btn-sm btn-light font-weight-bold btn-new-plan">
      <i class="fas fa-plus-circle mr-1"></i> Nuevo
    </a>

  </div>
</div>


      <div class="card-body">
        <div class="table-responsive plan-table-wrap">
          <table class="table table-hover table-sm mb-0 plan-table">
            <thead class="thead-light sticky-header">
              <tr>
                <th class="text-left"><i class="fas fa-tag mr-1"></i>Nombre</th>
                <th><i class="fas fa-calendar-day mr-1"></i>Duración</th>
                <th class="text-right"><i class="fas fa-coins mr-1"></i>Precio base</th>
                <th><i class="fas fa-door-open mr-1"></i>Tipo acceso</th>
                <th><i class="fas fa-walking mr-1"></i>Visitas máx.</th>
                <th><i class="fas fa-toggle-on mr-1"></i>Estado</th>
                <th style="width:190px;"><i class="fas fa-cogs mr-1"></i>Acciones</th>
              </tr>
            </thead>

            <tbody>
            <?php if ($result && $result->num_rows > 0): ?>
              <?php while($row = $result->fetch_assoc()): ?>
                <?php
                  $activo = !empty($row['estado']);
                  $estadoBadge = $activo
                    ? '<span class="badge badge-success badge-pillish">Activo</span>'
                    : '<span class="badge badge-secondary badge-pillish">Inactivo</span>';
                ?>
                <tr>
                  <td class="text-left"><?= e($row['nombre']); ?></td>
                  <td><?= (int)$row['duracion_dias']; ?> días</td>
                  <td class="text-right"><?= number_format((float)$row['precio_base'], 2, '.', ','); ?> Bs</td>
                  <td><?= e($row['tipo_acceso']); ?></td>
                  <td><?= (int)$row['visitas_max']; ?></td>
                  <td><?= $estadoBadge; ?></td>

                  <td class="actions">
  <div class="btn-group btn-group-sm" role="group">

    <!-- EDITAR -->
    <a href="plan_form.php?id=<?= (int)$row['id']; ?>"
       class="btn btn-outline-warning btn-edit-plan"
       data-nombre="<?= e($row['nombre']); ?>">
      <i class="fas fa-edit"></i> Editar
    </a>

    <!-- DESACTIVAR -->
    <form method="POST" action="plan_eliminar.php" style="display:inline;">
  <?php if(function_exists('csrf_field')) echo csrf_field(); ?>

    <input type="hidden" name="id" value="<?= (int)$row['id'] ?>">
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">

    <button type="submit" class="btn btn-danger btn-sm"
        onclick="return confirm('¿Desactivar este plan?')">
        Desactivar
    </button>
</form>


  </div>
</td>

                </tr>
              <?php endwhile; ?>
            <?php else: ?>
              <tr>
                <td colspan="7" class="text-center text-muted py-4">No hay planes registrados.</td>
              </tr>
            <?php endif; ?>
            </tbody>

          </table>
        </div>

        <div class="text-center small text-muted mt-3">
          Mostrando <?= $result ? (int)$result->num_rows : 0; ?> plan(es)
        </div>
      </div>
    </div>

  </div>

  <div class="row-fluid">
    <div id="footer" class="span12">&copy; La Paz - Bolivia <?= date("Y"); ?></div>
  </div>
</div>


<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
document.addEventListener('DOMContentLoaded', () => {

  /* ===============================
     CONFIRMACIÓN EDITAR PLAN
  =============================== */
  document.querySelectorAll('.btn-edit-plan').forEach(btn => {
    btn.addEventListener('click', function (e) {
      e.preventDefault();

      const nombre = this.dataset.nombre || 'este plan';
      const url = this.getAttribute('href');

      Swal.fire({
        title: 'Editar plan',
        html: `
          <div style="
            width:80px;height:80px;margin:0 auto 12px;
            border-radius:50%;
            background:linear-gradient(135deg,#f6c23e,#f4b619);
            display:flex;align-items:center;justify-content:center;
            box-shadow:0 8px 20px rgba(246,194,62,.45);
          ">
            <i class="fas fa-edit" style="font-size:34px;color:#fff"></i>
          </div>
          <p style="margin:0">
            Editar el plan?<br>
            <strong>${nombre}</strong>
          </p>
        `,
        confirmButtonText: 'Continuar',
        cancelButtonText: 'Cancelar',
        showCancelButton: true,
        confirmButtonColor: '#f6c23e',
        cancelButtonColor: '#858796',
        
      }).then(r => {
        if (r.isConfirmed) {
          window.location.href = url;
        }
      });
    });
  });

  /* ===============================
     CONFIRMACIÓN DESACTIVAR PLAN
  =============================== */
  

document.addEventListener('DOMContentLoaded', () => {

  /* ===============================
     CONFIRMACIÓN NUEVO PLAN
  =============================== */
  const btnNewPlan = document.querySelector('.btn-new-plan');
  if (btnNewPlan) {
    btnNewPlan.addEventListener('click', function (e) {
      e.preventDefault();

      const url = this.getAttribute('href');

      Swal.fire({
        title: 'Crear nuevo plan',
        html: `
          <div style="
            width:80px;height:80px;margin:0 auto 12px;
            border-radius:50%;
            background:linear-gradient(135deg,#4e73df,#224abe);
            display:flex;align-items:center;justify-content:center;
            box-shadow:0 8px 22px rgba(78,115,223,.45);
          ">
            <i class="fas fa-layer-group" style="font-size:34px;color:#fff"></i>
          </div>

          <p style="margin:0">
            Vas a registrar un <strong>nuevo plan de membresía</strong>.
          </p>
          <small class="text-muted">
            Podrás definir duración, precio y tipo de acceso.
          </small>
        `,
        showCancelButton: true,
        confirmButtonText: 'Continuar',
        cancelButtonText: 'Cancelar',
        confirmButtonColor: '#4e73df',
        cancelButtonColor: '#858796',
        
      }).then(r => {
        if (r.isConfirmed) {
          window.location.href = url;
        }
      });
    });
  }

});


</script>

<style>
  /* ===============================
   PAGE HEADER (ESTÁNDAR SISTEMA)
=============================== */
.page-header{
  background: linear-gradient(135deg,#4e73df,#1cc88a);
  border-radius: 1rem;
  padding: 1.3rem 1rem;
  box-shadow: 0 8px 20px rgba(0,0,0,.16);
}

.page-header-inner{
  max-width: 1200px;   /* 🔥 MISMO ANCHO DEL CONTENIDO */
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
.card-header .gap-2 { gap: .5rem; }
.card-header .gap-3 { gap: .75rem; }

.card-header h6{
  font-size: 1rem;
  font-weight: 600;
}

.card-header .btn{
  border-radius: 999px;
  padding: .35rem .8rem;
}

</style>
