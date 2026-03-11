<?php /* AUTO-GENERATED SB Admin 2 wrapper (edit-staff-form mejorado) */ ?>
<?php $pageTitle = isset($pageTitle) ? $pageTitle : 'Panel'; ?>
<?php include __DIR__ . '/theme/sb2/header.php'; ?>
<?php include __DIR__ . '/theme/sb2/sidebar.php'; ?>
<?php include __DIR__ . '/theme/sb2/topbar.php'; ?>

<!-- BEGIN: migrated original content -->
<div class="sb2-content">
  <?php
  require_once __DIR__ . '/../core/bootstrap.php';
  require_once __DIR__ . '/../core/auth.php';
  require_once __DIR__ . '/../core/roles.php';
  require_modulo('administracion');
  if (!$db instanceof mysqli) {
    die('No hay conexión a la base de datos');
  }

  // Estado / mensajes
  $alertType = null;
  $alertMsg = null;
  $staff = null;
  // Mensajes después de actualizar
  if (!empty($_SESSION['edit_success'])) {
    $alertType = 'success';
    $alertMsg = $_SESSION['edit_success'];
    unset($_SESSION['edit_success']);
  }

  if (!empty($_SESSION['edit_error'])) {
    $alertType = 'danger';
    $alertMsg = $_SESSION['edit_error'];
    unset($_SESSION['edit_error']);
  }

  // Validar ID
  $id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
  if ($id <= 0) {
    $alertType = 'danger';
    $alertMsg = 'Falta el parámetro <strong>id</strong> o no es válido.';
  } else {
    // Traer datos del staff con prepared statement
    if (isset($db) && ($db instanceof mysqli)) {
      if ($stmt = $db->prepare("SELECT user_id, fullname, username, gender, contact, address, designation, email FROM staffs WHERE user_id = ? LIMIT 1")) {
        $stmt->bind_param('i', $id);
        if ($stmt->execute()) {
          $res = $stmt->get_result();
          $staff = $res->fetch_assoc();
          $res->free();
          if (!$staff) {
            $alertType = 'warning';
            $alertMsg = 'No se encontró el registro solicitado.';
          }
        } else {
          $alertType = 'danger';
          $alertMsg = 'No se pudo obtener los datos. <span class="small text-muted">' . e($stmt->error) . '</span>';
        }
        $stmt->close();
      } else {
        $alertType = 'danger';
        $alertMsg = 'Error preparando la consulta. <span class="small text-muted">' . e($db->error) . '</span>';
      }
    } else {
      $alertType = 'danger';
      $alertMsg = 'No hay conexión a la base de datos. Revisa <code>core/db.php</code>.';
    }
  }

  // Helper para selected del rol
  function sel($current, $value)
  {
    return (mb_strtolower($current ?? '') === mb_strtolower($value)) ? 'selected' : '';
  }

  ?>

  <div class="container-fluid py-3">
    <!-- Migas + Título -->
    <div id="content-header" class="mb-3">

      <h1 class="text-center mb-0">Actualizar Datos del Personal <i class="fas fa-briefcase"></i></h1>
    </div>

    <?php if ($alertType): ?>
      <div class="alert alert-<?= e($alertType) ?> shadow-sm">
        <i
          class="fas <?= $alertType === 'success' ? 'fa-check-circle' : ($alertType === 'warning' ? 'fa-exclamation-triangle' : 'fa-times-circle') ?>"></i>
        <span class="ml-2"><?= $alertMsg ?></span>
        <div class="mt-2">
          <a href="staffs.php" class="btn btn-sm btn-outline-secondary"><i class="fas fa-arrow-left mr-1"></i>Volver a
            Personal</a>
        </div>
      </div>
    <?php endif; ?>

    <?php if ($staff): ?>
      <!-- Card Form -->
      <div class="card shadow-sm">
        <div class="card-header d-flex align-items-center justify-content-between">
          <h6 class="m-0 font-weight-bold"><i class="fas fa-id-card mr-2"></i> Detalles del Personal</h6>
          <a href="staffs.php" class="btn btn-sm btn-outline-secondary"><i class="fas fa-list mr-1"></i>Listado</a>
        </div>

        <form action="staff_actions.php" method="POST">
          <input type="hidden" name="op" value="update">
          <input type="hidden" name="id" value="<?= (int) $staff['user_id']; ?>">

          <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token']; ?>">

          <div class="form-row">
            <div class="form-group col-md-6">
              <label>Nombre Completo</label>
              <input type="text" class="form-control" name="fullname" value="<?= e($staff['fullname']) ?>" required>
            </div>
            <div class="form-group col-md-6">
              <label>Usuario</label>
              <input type="text" class="form-control" name="username" value="<?= e($staff['username']) ?>" required>
            </div>
          </div>

          <div class="form-row">
            <div class="form-group col-md-6">
              <label>Correo</label>
              <input type="email" class="form-control" name="email" value="<?= e($staff['email']) ?>"
                placeholder="ejemplo@correo.com">
            </div>
            <div class="form-group col-md-6">
              <label>Contraseña</label>
              <input type="password" class="form-control" value="**********" disabled>
              <small class="form-text text-muted">
                Nota: La contraseña se cambia desde el perfil del personal o por un administrador autorizado.
              </small>
            </div>
          </div>

          <div class="form-row">
            <div class="form-group col-md-4">
              <label>Género</label>
              <select name="gender" class="form-control">
                <?php $g = $staff['gender'] ?? ''; ?>
                <option value="" <?= sel($g, '') ?>>Sin especificar</option>
                <option value="Masculino" <?= sel($g, 'Masculino') ?>>Masculino</option>
                <option value="Femenino" <?= sel($g, 'Femenino') ?>>Femenino</option>
                <option value="Otro" <?= sel($g, 'Otro') ?>>Otro</option>
              </select>
            </div>
            <div class="form-group col-md-4">
              <label>Número de Contacto</label>
              <input type="text" class="form-control" name="contact" value="<?= e($staff['contact']) ?>"
                placeholder="Ej: 72078787">
            </div>
            <div class="form-group col-md-4">
              <label>Rol</label>
              <?php $d = $staff['designation'] ?? ''; ?>
              <select name="designation" id="designation" class="form-control">
                <option value="Cajero" <?= sel($d, 'Cajero') ?>>Cajero</option>
                <option value="Entrenador" <?= sel($d, 'Entrenador') ?>>Entrenador</option>
                <option value="Asistente" <?= sel($d, 'Asistente') ?>>Asistente</option>
                <option value="Recepcionista" <?= sel($d, 'Recepcionista') ?>>Recepcionista</option>
                <option value="Administrador" <?= sel($d, 'Administrador') ?>>Administrador</option>
              </select>
            </div>
          </div>

          <div class="form-group">
            <label>Dirección</label>
            <input type="text" class="form-control" name="address" value="<?= e($staff['address']) ?>"
              placeholder="Calle, número, zona">
          </div>

          <div class="text-center">
            <button type="submit" class="btn btn-success">
              <i class="fas fa-save mr-1"></i> Actualizar Datos
            </button>
            <a href="staffs.php" class="btn btn-light border ml-2">
              <i class="fas fa-arrow-left mr-1"></i> Cancelar
            </a>
          </div>
        </form>
      </div>
    </div>
  <?php endif; // $staff ?>

</div>
</div>

<style>
  /* Pulido visual */
  .card {
    border: 0;
    border-radius: 0.75rem;
  }

  .card-header {
    background: #fff;
    border-bottom: 1px solid #eef2f7;
  }

  .form-label,
  label {
    font-weight: 600;
  }

  .alert i {
    width: 1.25rem;
    text-align: center;
  }
</style>

<!-- Scripts mínimos necesarios -->
<script src="../js/matrix.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
  document.querySelector('form').addEventListener('submit', function (e) {

    e.preventDefault();

    Swal.fire({
      title: '¿Confirmar actualización?',
      text: 'Se guardarán los cambios realizados.',
      icon: 'question',
      showCancelButton: true,
      confirmButtonColor: '#1cc88a',
      cancelButtonColor: '#858796',
      confirmButtonText: 'Sí, actualizar',
      cancelButtonText: 'Cancelar'
    }).then(result => {
      if (result.isConfirmed) {
        this.submit();
      }
    });

  });
</script>

<!-- END: migrated original content -->
<?php include __DIR__ . '/theme/sb2/footer.php'; ?>