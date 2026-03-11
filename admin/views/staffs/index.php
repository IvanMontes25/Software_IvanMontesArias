
<div class="sb2-content d-flex flex-column min-vh-100">
  <div class="container-fluid py-3 flex-grow-1">

    <!-- ===== PAGE HEADER ===== -->
    <div class="page-header mb-4">
      <div class="page-header-inner">
        <h1 class="page-title">
          Personal del Sistema
        </h1>
        <p class="page-subtitle">
          Gestión de usuarios, roles y cuentas del personal
        </p>
      </div>
    </div>



if (!$db instanceof mysqli) {
  die('No hay conexión a la base de datos');
}

  function e($str) { return htmlspecialchars((string)$str, ENT_QUOTES, 'UTF-8'); }

  function genderBadgeClass($g) {
    $g = mb_strtolower(trim((string)$g));
    return $g === 'masculino' ? 'badge-primary'
      : ($g === 'femenino' ? 'badge-pink'
      : 'badge-secondary');
  }
  function roleBadgeClass($r) {
    $r = mb_strtolower(trim((string)$r));
    if (strpos($r, 'admin') !== false) return 'badge-success';
    if (strpos($r, 'entren') !== false || strpos($r, 'trainer') !== false) return 'badge-info';
    if (strpos($r, 'recep') !== false) return 'badge-warning';
    return 'badge-dark';
  }

  $qry = "SELECT * FROM staffs ORDER BY fullname ASC";
  $result = mysqli_query($db, $qry);
  $totalRows = $result ? mysqli_num_rows($result) : 0;
  $cnt = 1;
?>

<style>
/* === ESTILO PRO (igual al resto del sistema) === */
.staff-card{ border:0; border-radius:1rem; overflow:hidden; }
.staff-card .card-header{
  background: linear-gradient(90deg,#4e73df,#1cc88a);
  color:#fff;
}
.staff-table-wrap{ max-height:70vh; overflow:auto; }
.staff-table thead th{ font-size:1rem; font-weight:600; vertical-align:middle; }
.staff-table tbody td{ font-size:1.02rem; padding:.7rem .5rem; vertical-align:middle; }
.sticky-header{ position:sticky; top:0; z-index:2; }

.staff-table th, .staff-table td{ text-align:center; }
.staff-table td.text-left, .staff-table th.text-left{ text-align:left !important; }

.badge-pillish{ border-radius:999px; font-weight:500; }
.badge-pink{ background-color:#e83e8c; color:#fff; }

/* Botones compactos */
.actions .btn{
  font-size:.78rem;
  padding:.25rem .55rem;
  line-height:1.2;
  border-radius:999px;
  white-space:nowrap;
}
.actions .btn i{ margin-right:3px; font-size:.75rem; }

/* Avatar */
.font-weight-600{ font-weight:600; }
.avatar-sm{
  width:32px; height:32px; border-radius:50%;
  background:#eef2f7; display:inline-flex;
  align-items:center; justify-content:center;
  font-weight:700;
}

/* Líneas suaves */
.staff-table{
  border-collapse:collapse;
  width:100%;
}
.staff-table th, .staff-table td{
  border:1px solid #dee2e6;
}

#footer{ color:#fff; }
</style>

<div class="container-fluid py-3 flex-grow-1">



  <!-- Botón a la derecha -->
  <div class="d-flex justify-content-end mb-3">
    <a href="registro_staff.php"
   class="btn btn-primary btn-sm btn-add-staff"
   style="border-radius:999px;">
   
  <i class="fas fa-user-plus mr-1"></i> Añadir miembro
</a>

  </div>

  <!-- Card -->
  <div class="card shadow mb-4 staff-card">
    <div class="card-header py-3 d-flex flex-wrap align-items-center justify-content-between">
      <div class="d-flex align-items-center flex-wrap">
        <h6 class="m-0 mr-3">
          <i class="fas fa-briefcase mr-1"></i> Tabla de miembros
        </h6>
        <span class="badge badge-light badge-pillish">
          Total: <?php echo number_format((int)$totalRows); ?>
        </span>
      </div>

      <!-- Buscador -->
      <div class="mt-2 mt-md-0" style="max-width: 360px; width:100%;">
        <div class="input-group input-group-sm">
          <div class="input-group-prepend">
            <span class="input-group-text bg-white border-right-0">
              <i class="fas fa-search text-muted"></i>
            </span>
          </div>
          <input id="tableSearch" type="text" class="form-control bg-light border-left-0"
                 placeholder="Buscar por nombre, usuario, rol, correo…">
          <div class="input-group-append">
            <button id="clearSearch" class="btn btn-outline-light" type="button" title="Limpiar">
              <i class="fas fa-times"></i>
            </button>
          </div>
        </div>
      </div>
    </div>

    <div class="card-body">
      <div class="table-responsive staff-table-wrap">
        <table id="staffTable" class="table table-hover table-sm mb-0 staff-table staff-table">
          <thead class="thead-light sticky-header">
            <tr>
              <th style="width:60px;"><i class="fas fa-hashtag"></i></th>
              <th class="text-left"><i class="fas fa-user mr-1"></i>Nombre Completo</th>
              <th><i class="fas fa-at mr-1"></i>Usuario</th>
              <th><i class="fas fa-venus-mars mr-1"></i>Género</th>
              <th><i class="fas fa-id-badge mr-1"></i>Rol</th>
              <th class="text-left"><i class="far fa-envelope mr-1"></i>Correo</th>
              <th class="text-left"><i class="fas fa-map-marker-alt mr-1"></i>Dirección</th>
              <th><i class="fas fa-phone-alt mr-1"></i>Contacto</th>
              <th style="width:190px;"><i class="fas fa-cogs mr-1"></i>Acciones</th>
            </tr>
          </thead>

          <tbody>
          <?php if ($result && $totalRows > 0): ?>
            <?php while ($row = mysqli_fetch_assoc($result)): ?>
              <tr data-row>
                <td class="text-muted"><?php echo $cnt++; ?></td>

                <td class="text-left">
                  <div class="d-flex align-items-center">
                    <div class="avatar-sm mr-2">
                      <span><?php echo mb_strtoupper(mb_substr(e($row['fullname']), 0, 1)); ?></span>
                    </div>
                    <div>
                      <div class="font-weight-600"><?php echo e($row['fullname']); ?></div>
                      <div class="small text-muted">ID: <?php echo e($row['user_id']); ?></div>
                    </div>
                  </div>
                </td>

                <td><span class="text-monospace">@<?php echo e($row['username']); ?></span></td>

                <td>
                  <span class="badge <?php echo genderBadgeClass($row['gender']); ?> badge-pillish">
                    <?php echo e($row['gender']); ?>
                  </span>
                </td>

                <td>
                  <span class="badge <?php echo roleBadgeClass($row['designation']); ?> badge-pillish">
                    <?php echo e($row['designation']); ?>
                  </span>
                </td>

                <td class="text-left">
                  <a href="mailto:<?php echo e($row['email']); ?>" class="text-decoration-none">
                    <?php echo e($row['email']); ?>
                  </a>
                </td>

                <td class="text-left">
                  <span class="d-inline-block text-truncate" style="max-width:280px;">
                    <?php echo e($row['address']); ?>
                  </span>
                </td>

                <td>
                  <a href="tel:<?php echo e($row['contact']); ?>" class="text-decoration-none">
                    <?php echo e($row['contact']); ?>
                  </a>
                </td>

                <td class="actions">
                  <div class="btn-group btn-group-sm" role="group" aria-label="Acciones">
                    <a href="form_act_staff.php?id=<?php echo (int)$row['user_id']; ?>"

   class="btn btn-outline-primary"
   title="Actualizar">
   <i class="fas fa-edit"></i>
</a>


                   <button
  type="button"
  class="btn btn-danger btn-sm btn-delete-staff"
  data-id="<?= (int)$row['user_id']; ?>">
  <i class="fas fa-trash"></i> Eliminar
</button>


</form>


                  </div>
                </td>
              </tr>
            <?php endwhile; ?>
          <?php else: ?>
            <tr>
              <td colspan="9" class="text-center text-muted py-4">
                No hay miembros del personal registrados.
              </td>
            </tr>
          <?php endif; ?>
          </tbody>

        </table>
      </div>

      <div class="small text-muted mt-3 text-center">
        Consejo: escribe en el buscador para filtrar resultados al instante.
      </div>
    </div>
  </div>

</div>
<?php if(isset($_GET['success']) && $_GET['success']=='updated'): ?>
<div class="alert alert-success">
  Staff actualizado correctamente.
</div>
<?php endif; ?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<?php if (isset($_GET['success']) && $_GET['success'] === 'updated'): ?>
<script>
Swal.fire({
    icon: 'success',
    title: 'Actualización exitosa',
    text: 'Staff actualizado correctamente.',
    confirmButtonColor: '#1cc88a'
});
</script>
<?php endif; ?>

<?php if (isset($_GET['error']) && $_GET['error'] === 'update'): ?>
<script>
Swal.fire({
    icon: 'error',
    title: 'Error',
    text: 'No se pudo actualizar el staff.',
    confirmButtonColor: '#e74a3b'
});
</script>
<?php endif; ?>




<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- Buscador simple en vivo -->
<script>
(function() {
  const input = document.getElementById('tableSearch');
  const clear = document.getElementById('clearSearch');
  const rows  = document.querySelectorAll('#staffTable tbody tr[data-row]');

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

  input && input.addEventListener('input', filter);
  clear && clear.addEventListener('click', () => { input.value=''; filter(); input.focus(); });
})();
</script>
<script>
document.addEventListener('DOMContentLoaded', () => {

  /* ===============================
     AÑADIR MIEMBRO
  =============================== */
  const btnAdd = document.querySelector('.btn-add-staff');
  if (btnAdd) {
    btnAdd.addEventListener('click', e => {
      e.preventDefault();
      const url = btnAdd.getAttribute('href');

      Swal.fire({
        title: 'Nuevo miembro del staff',
        html: `
          <div style="
            width:80px;height:80px;margin:0 auto 12px;
            border-radius:50%;
            background:linear-gradient(135deg,#4e73df,#1cc88a);
            display:flex;align-items:center;justify-content:center;
            box-shadow:0 8px 22px rgba(78,115,223,.45);
          ">
            <i class="fas fa-user-plus" style="font-size:34px;color:#fff"></i>
          </div>
          <p>Vas a registrar un <strong>nuevo miembro del personal</strong>.</p>
          <small class="text-muted">
            Podrás asignar rol, usuario y credenciales.
          </small>
        `,
        showCancelButton: true,
        confirmButtonText: 'Continuar',
        cancelButtonText: 'Cancelar',
        confirmButtonColor: '#4e73df',
        cancelButtonColor: '#858796',
        
      }).then(r => r.isConfirmed && (window.location.href = url));
    });
  }

  /* ===============================
     EDITAR MIEMBRO
  =============================== */
  document.querySelectorAll('.btn-edit-staff').forEach(btn => {
    btn.addEventListener('click', e => {
      e.preventDefault();
      const url = btn.getAttribute('href');

      Swal.fire({
        title: 'Editar miembro',
        text: 'Vas a modificar la información de este miembro del personal.',
        icon: 'info',
        showCancelButton: true,
        confirmButtonText: 'Editar',
        cancelButtonText: 'Cancelar',
        confirmButtonColor: '#1cc88a',
        cancelButtonColor: '#858796',
        reverseButtons: false
      }).then(r => r.isConfirmed && (window.location.href = url));
    });
  });

  /* ===============================
     ELIMINAR MIEMBRO
  =============================== */
  document.querySelectorAll('.btn-delete-staff').forEach(btn => {
    btn.addEventListener('click', e => {
      e.preventDefault();
      const url = btn.getAttribute('href');

      Swal.fire({
        title: 'Eliminar miembro',
        html: `
          <div style="
            width:80px;height:80px;margin:0 auto 12px;
            border-radius:50%;
            background:linear-gradient(135deg,#e74a3b,#be2617);
            display:flex;align-items:center;justify-content:center;
            box-shadow:0 8px 22px rgba(231,74,59,.45);
          ">
            <i class="fas fa-user-times" style="font-size:34px;color:#fff"></i>
          </div>
          <p>Esta acción <strong>no se puede deshacer</strong>.</p>
          <small class="text-muted">
            El miembro será eliminado del sistema.
          </small>
        `,
        showCancelButton: true,
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar',
        confirmButtonColor: '#e74a3b',
        cancelButtonColor: '#858796',
        
      }).then(r => r.isConfirmed && (window.location.href = url));
    });
  });

});


</script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>

const CSRF_TOKEN = "<?= $_SESSION['csrf_token'] ?>";

document.addEventListener('click', function(e){

  const btn = e.target.closest('.btn-delete-staff');
  if (!btn) return;

  const id = btn.dataset.id;
  if (!id) return;

  Swal.fire({
    title: '¿Eliminar staff?',
    text: 'Esta acción eliminará el registro.',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#e74a3b',
    cancelButtonColor: '#858796',
    confirmButtonText: 'Eliminar',
    cancelButtonText: 'Cancelar'
  }).then(result => {

    if (!result.isConfirmed) return;

    fetch('staff_actions.php', {
      method: 'POST',
      credentials: 'same-origin',
      headers: {'Content-Type': 'application/x-www-form-urlencoded'},
      body:
        'op=delete'
        + '&delete_id=' + encodeURIComponent(id)
        + '&csrf_token=' + encodeURIComponent(CSRF_TOKEN)
    })
    .then(r => r.json())
    .then(data => {

      if (data.ok) {
        Swal.fire({
          icon: 'success',
          title: 'Eliminado exitosamente',
          timer: 1500,
          showConfirmButton: false
        }).then(() => location.reload());
      } else {
        Swal.fire({icon:'error',title:'Error',text:data.msg});
      }

    });

  });

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
  max-width: 1200px;   /* 🔥 MISMO ANCHO QUE LAS TABLAS */
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

</style>