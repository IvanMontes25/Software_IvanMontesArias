<?php
require_once __DIR__ . '/../core/bootstrap.php';
require_once __DIR__ . '/../core/auth.php';
require_once __DIR__ . '/../core/roles.php';
require_modulo('equipos');

if (!$db instanceof mysqli) {
  die('No hay conexión a la base de datos');
}

/* ===== VALIDAR ID ANTES DE CUALQUIER OUTPUT ===== */
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id || $id <= 0) {
  header("Location: equipos.php");
  exit;
}

/* ===== Obtener registro ===== */
$row = null;

$stmt = mysqli_prepare($db, "SELECT id, name, description, date, quantity, contact, vendor, address, amount FROM equipment WHERE id = ?");
if ($stmt) {
  mysqli_stmt_bind_param($stmt, "i", $id);
  mysqli_stmt_execute($stmt);
  $res = mysqli_stmt_get_result($stmt);
  $row = $res ? mysqli_fetch_assoc($res) : null;
  mysqli_stmt_close($stmt);
}

if (!$row) {
  header("Location: equipos.php");
  exit;
}

/* ===== AHORA SÍ CARGAMOS EL LAYOUT ===== */
$pageTitle = $pageTitle ?? 'Panel';

include __DIR__ . '/theme/sb2/header.php';
include __DIR__ . '/theme/sb2/sidebar.php';
include __DIR__ . '/theme/sb2/topbar.php';
?>


<div class="sb2-content">
  <div class="container-fluid py-3">

    <?php if (!$row): ?>
      <div class="alert alert-warning d-flex align-items-center" role="alert">
        <i class="fas fa-exclamation-triangle mr-2"></i>
        No se encontró el equipo solicitado o el ID no es válido.
      </div>
      <a href="equipos.php" class="btn btn-secondary">
        <i class="fas fa-arrow-left mr-1"></i> Volver
      </a>

    <?php else: ?>

      <!-- MENSAJES ESTÁNDAR (SweetAlert) -->
      <?php if (!empty($_SESSION['edit_success'])): ?>
        <script>
          window.addEventListener('DOMContentLoaded', () => {
            Swal.fire({
              icon: 'success',
              title: 'Actualizado',
              text: '<?php echo addslashes($_SESSION['edit_success']); ?>',
              confirmButtonText: 'Aceptar',
              confirmButtonColor: '#1cc88a',
              allowOutsideClick: false,
              allowEscapeKey: false
            }).then((r) => {
              if (r.isConfirmed) window.location.href = 'equipos.php';
            });
          });
        </script>
        <?php unset($_SESSION['edit_success']); ?>
      <?php endif; ?>

      <?php if (!empty($_SESSION['edit_error'])): ?>
        <script>
          window.addEventListener('DOMContentLoaded', () => {
            Swal.fire({
              icon: 'error',
              title: 'Error',
              text: '<?php echo addslashes($_SESSION['edit_error']); ?>',
              confirmButtonText: 'Entendido',
              confirmButtonColor: '#e74a3b'
            });
          });
        </script>
        <?php unset($_SESSION['edit_error']); ?>
      <?php endif; ?>

      <!--  CARD ESTÁNDAR (misma línea visual que clientes) -->
      <div class="widget-box edit-card">
        <div class="widget-title edit-card-head">
          <span class="icon"><i class="fas fa-tools"></i></span>
          <h5>Editar Equipo</h5>
        </div>

        <div class="widget-content edit-card-body">


          <?php if (!empty($_SESSION['success'])): ?>
            <div class="alert alert-success">
              <?= $_SESSION['success'];
              unset($_SESSION['success']); ?>
            </div>
          <?php endif; ?>

          <?php if (!empty($_SESSION['error'])): ?>
            <div class="alert alert-danger">
              <?= $_SESSION['error'];
              unset($_SESSION['error']); ?>
            </div>
          <?php endif; ?>


          <form id="equipoForm" action="equipo_actions.php" method="POST">

            <input type="hidden" name="op" value="update">
            <input type="hidden" name="id" value="<?= (int) $row['id']; ?>">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">


            <?php if (function_exists('csrf_field'))
              echo csrf_field(); ?>


            <div class="edit-grid compact-grid">

              <div class="edit-field edit-2">
                <label>Nombre del Equipo *</label>
                <input type="text" name="name" class="edit-input"
                  value="<?php echo htmlspecialchars($row['name'] ?? '', ENT_QUOTES); ?>" required>
              </div>

              <div class="edit-field">
                <label>Cantidad *</label>
                <input type="number" name="quantity" class="edit-input" min="0"
                  value="<?php echo (int) ($row['quantity'] ?? 0); ?>" required>
              </div>

              <div class="edit-field edit-2">
                <label>Descripción *</label>
                <input type="text" name="description" class="edit-input"
                  value="<?php echo htmlspecialchars($row['description'] ?? '', ENT_QUOTES); ?>" required>
              </div>

              <div class="edit-field">
                <label>Fecha de Compra</label>
                <input type="date" name="date" class="edit-input"
                  value="<?php echo htmlspecialchars($row['date'] ?? '', ENT_QUOTES); ?>">
              </div>

              <div class="edit-field">
                <label>Contacto *</label>
                <input type="text" name="contact" class="edit-input"
                  value="<?php echo htmlspecialchars($row['contact'] ?? '', ENT_QUOTES); ?>" required>
              </div>

              <div class="edit-field">
                <label>Proveedor *</label>
                <input type="text" name="vendor" class="edit-input"
                  value="<?php echo htmlspecialchars($row['vendor'] ?? '', ENT_QUOTES); ?>" required>
              </div>

              <div class="edit-field edit-2">
                <label>Dirección *</label>
                <input type="text" name="address" class="edit-input"
                  value="<?php echo htmlspecialchars($row['address'] ?? '', ENT_QUOTES); ?>" required>
              </div>

              <div class="edit-field">
                <label>Total (Bs.) *</label>
                <input type="number" name="amount" class="edit-input" step="0.01" min="0"
                  value="<?php echo htmlspecialchars((string) ($row['amount'] ?? ''), ENT_QUOTES); ?>" required>
              </div>

            </div>

            <div class="edit-actions compact-actions">
              <a href="equipos.php" class="btn btn-outline-secondary mr-2">
                <i class="fas fa-arrow-left"></i> Volver
              </a>
              <button id="submitBtn" type="submit" class="btn btn-success">
                <i class="fas fa-save mr-1"></i> Guardar
              </button>
            </div>
          </form>

        </div>
      </div>

    <?php endif; ?>

  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
  document.getElementById('equipoForm')?.addEventListener('submit', function (e) {

    e.preventDefault();

    Swal.fire({
      title: '¿Actualizar equipo?',
      text: 'Se guardarán los cambios realizados.',
      icon: 'question',
      showCancelButton: true,
      confirmButtonColor: '#4e73df',
      cancelButtonColor: '#858796',
      confirmButtonText: 'Sí, actualizar',
      cancelButtonText: 'Cancelar'
    }).then((result) => {

      if (result.isConfirmed) {
        this.submit();
      }

    });

  });

</script>

<?php include __DIR__ . '/theme/sb2/footer.php'; ?>

<style>
  /* ===== ESTÁNDAR FORM (compacto + centrado) ===== */
  .edit-card {
    max-width: 850px;
    margin: 0 auto;
    border-radius: 1rem;
    box-shadow: 0 .25rem 1rem rgba(58, 59, 69, .1);
    overflow: hidden;
    background: #fff;
  }

  .edit-card-head {
    background: linear-gradient(135deg, #4e73df, #1cc88a);
    padding: 1rem;
    display: flex;
    align-items: center;
    gap: 10px;
    color: #fff;
  }

  .edit-card-head .icon {
    width: 40px;
    height: 40px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border-radius: 12px;
    background: rgba(255, 255, 255, .18);
  }

  .edit-card-body {
    padding: 1.25rem;
    background: #f8f9fc
  }

  .edit-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: .75rem;
  }

  .edit-2 {
    grid-column: span 2
  }

  .edit-field {
    display: flex;
    flex-direction: column
  }

  .edit-field label {
    font-size: .8rem;
    font-weight: 600;
    color: #4e5d6c;
    margin-bottom: 2px
  }

  .edit-input {
    border: 1px solid #d1d3e2;
    border-radius: .5rem;
    padding: .4rem .6rem;
    font-size: .85rem;
    min-height: 36px;
    background: #fff;
  }

  .edit-input:focus {
    border-color: #4e73df;
    box-shadow: 0 0 0 .15rem rgba(78, 115, 223, .25);
    outline: 0;
  }

  .edit-actions {
    text-align: center;
    margin-top: 12px;
    display: flex;
    justify-content: center;
    gap: 10px;
    flex-wrap: wrap
  }

  .edit-actions .btn {
    padding: .5rem 1.2rem;
    border-radius: 1.5rem;
    min-width: 140px
  }

  @media (max-width:992px) {
    .edit-grid {
      grid-template-columns: repeat(2, 1fr)
    }

    .edit-2 {
      grid-column: span 2
    }
  }

  @media (max-width:576px) {
    .edit-grid {
      grid-template-columns: 1fr
    }

    .edit-2 {
      grid-column: span 1
    }
  }
</style>