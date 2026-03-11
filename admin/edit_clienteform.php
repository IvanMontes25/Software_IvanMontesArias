<?php
require_once __DIR__ . '/../core/bootstrap.php';
require_once __DIR__ . '/../core/auth.php';

$pageTitle = $pageTitle ?? 'Panel';

include __DIR__ . '/theme/sb2/header.php';
include __DIR__ . '/theme/sb2/sidebar.php';
include __DIR__ . '/theme/sb2/topbar.php';


/* ===== Obtener cliente ===== */
$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$row = null;

if ($id > 0) {
  $stmt = $db->prepare("SELECT user_id, fullname, username, gender, contact, ci, correo, dor
FROM members
WHERE user_id = ?
");
  $stmt->bind_param('i', $id);
  $stmt->execute();
  $res = $stmt->get_result();
  if ($res && $res->num_rows === 1) {
    $row = $res->fetch_assoc();
  }
}

if (!empty($_SESSION['pwd_reset_success'])): ?>
  <script>
    Swal.fire({
      icon: 'success',
      title: 'Contraseña restablecida',
      html: 'Nueva contraseña:<br><strong><?= $_SESSION['pwd_reset_success']; ?></strong>',
      confirmButtonColor: '#1cc88a'
    });
  </script>
  <?php unset($_SESSION['pwd_reset_success']); ?>
<?php endif;

/* ===== Normalizar género ===== */
$g = '';
if ($row) {
  $map = ['Male' => 'Masculino', 'Female' => 'Femenino', 'Other' => 'Otro'];
  $g = $map[$row['gender'] ?? ''] ?? $row['gender'];
}
?>

<div class="sb2-content">
  <div class="container-fluid">


    <?php if (!$row): ?>

      <div class="alert alert-warning">
        <strong>No encontrado.</strong> Cliente con ID <code><?php echo (int) $id; ?></code>.
      </div>

    <?php else: ?>

      <?php if (!empty($_SESSION['edit_success'])): ?>
        <script>
          Swal.fire({
            icon: 'success',
            title: 'Actualizado',
            text: '<?php echo addslashes($_SESSION['edit_success']); ?>',
            confirmButtonText: 'Aceptar',
            confirmButtonColor: '#1cc88a',
            allowOutsideClick: false,
            allowEscapeKey: false
          }).then((result) => {
            if (result.isConfirmed) {
              window.location.href = 'clientes.php';
            }
          });
        </script>
        <?php unset($_SESSION['edit_success']); ?>
      <?php endif; ?>


      <?php if (!empty($_SESSION['edit_error'])): ?>
        <script>
          Swal.fire({
            icon: 'error',
            title: 'Error',
            text: '<?php echo addslashes($_SESSION['edit_error']); ?>',
            confirmButtonColor: '#e74a3b'
          });
        </script>
        <?php unset($_SESSION['edit_error']); ?>
      <?php endif; ?>


      <div class="widget-box edit-card">
        <div class="widget-title edit-card-head">
          <span class="icon"><i class="fas fa-user-edit"></i></span>
          <h5>Editar Cliente</h5>
        </div>

        <div class="widget-content edit-card-body">

          <form id="editForm" action="cliente_actions.php" method="POST">
            <input type="hidden" name="op" value="update">

            <?php if (function_exists('csrf_field'))
              echo csrf_field(); ?>



            <div class="edit-grid compact-grid">

              <div class="edit-field edit-2">
                <label>Nombre *</label>
                <input type="text" id="fullname" name="fullname" class="edit-input"
                  value="<?php echo htmlspecialchars($row['fullname']); ?>" required>
              </div>

              <div class="edit-field">
                <label>Usuario *</label>
                <input type="text" id="username" name="username" class="edit-input"
                  value="<?php echo htmlspecialchars($row['username']); ?>" required>
                <small class="text-muted">Sin espacios, mínimo 3 caracteres</small>
              </div>


              <div class="edit-field">
                <label>Género *</label>
                <select id="gender" name="gender" class="edit-input" required>
                  <option value="Masculino" <?php echo $g === 'Masculino' ? 'selected' : ''; ?>>Masculino</option>
                  <option value="Femenino" <?php echo $g === 'Femenino' ? 'selected' : ''; ?>>Femenino</option>
                  <option value="Otro" <?php echo $g === 'Otro' ? 'selected' : ''; ?>>Otro</option>
                </select>
              </div>

              <div class="edit-field">
                <label>Teléfono *</label>
                <input type="tel" id="contact" name="contact" class="edit-input"
                  value="<?php echo htmlspecialchars($row['contact']); ?>" required>
              </div>

              <div class="edit-field">
                <label>CI *</label>
                <input type="text" id="ci" name="ci" class="edit-input"
                  value="<?php echo htmlspecialchars($row['ci']); ?>" required>
              </div>

              <div class="edit-field edit-2">
                <label>Correo *</label>
                <input type="email" id="correo" name="correo" class="edit-input"
                  value="<?php echo htmlspecialchars($row['correo']); ?>" required>
              </div>

              <div class="edit-field">
                <label>F.D.R.</label>
                <input type="date" class="edit-input" value="<?php echo htmlspecialchars($row['dor']); ?>" disabled>
                <input type="hidden" name="dor" value="<?php echo htmlspecialchars($row['dor']); ?>">
              </div>

              <!-- Contraseña -->
              <div class="edit-field edit-2 password-box">
                <label>Contraseña</label>
                <div class="password-inline">
                  <span class="text-muted small">Oculta</span>
                  <button type="button" class="btn btn-outline-warning btn-sm"
                    onclick="if(confirm('¿Restablecer contraseña del cliente?')) document.getElementById('resetPwdForm').submit();">
                    <i class="fas fa-key"></i> Restablecer
                  </button>
                </div>
              </div>

            </div>

            <input type="hidden" name="id" value="<?php echo (int) $row['user_id']; ?>">

            <div class="edit-actions compact-actions">
              <button type="submit" class="btn btn-success">
                <i class="fas fa-save"></i> Guardar
              </button>
            </div>

          </form>
        </div>
      </div>

    <?php endif; ?>

  </div>
</div>
<form id="resetPwdForm" method="POST" action="acciones/reset_password.php" style="display:none">
  <?php if (function_exists('csrf_field'))
    echo csrf_field(); ?>

  <input type="hidden" name="user_id" value="<?php echo (int) $row['user_id']; ?>">
  <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
</form>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<?php include __DIR__ . '/theme/sb2/footer.php'; ?>

<style>
  /* ====== Estilos (SOLO VISUAL, estilo BASE) ====== */

  .edit-card {
    max-width: 850px;
    margin: 0 auto;
    border-radius: 1rem;
    box-shadow: 0 .25rem 1rem rgba(58, 59, 69, .1);
  }

  .edit-card-head {
    background: linear-gradient(135deg, #4e73df, #1cc88a);
    padding: 1rem;
    display: flex;
    align-items: center;
    gap: 10px;
    color: #fff;
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
    color: #4e5d6c
  }

  .edit-input {
    border: 1px solid #d1d3e2;
    border-radius: .5rem;
    padding: .4rem .6rem;
    font-size: .85rem;
    min-height: 36px;
  }

  .edit-input:focus {
    border-color: #4e73df;
    box-shadow: 0 0 0 .15rem rgba(78, 115, 223, .25);
    outline: 0;
  }

  .edit-actions {
    text-align: center;
    margin-top: 12px
  }

  .edit-actions .btn {
    padding: .5rem 1.2rem;
    border-radius: 1.5rem;
    min-width: 140px;
  }

  .password-box {
    border: 1px dashed #f6c23e;
    padding: .5rem .75rem;
    border-radius: .5rem;
    background: #fffdf5;
  }

  .password-inline {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 10px;
  }

  @media (max-width:992px) {
    .edit-grid {
      grid-template-columns: repeat(2, 1fr)
    }
  }

  @media (max-width:576px) {
    .edit-grid {
      grid-template-columns: 1fr
    }
  }
</style>
<!-- ====== Lógica (validación + precio opcional) ====== -->
<script>
  (function () {
    var form = document.getElementById('editForm');

    if (!form) return;

    var fields = {
      fullname: document.getElementById('fullname'),
      username: document.getElementById('username'),
      gender: document.getElementById('gender'),

      contact: document.getElementById('contact'),
      ci: document.getElementById('ci'),
      correo: document.getElementById('correo'),


    };

    function isEmail(v) { return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(v); }
    function noSpaces(v) { return !/\s/.test(v); }
    function setErr(el, msg) { var e = el.closest('.edit-field').querySelector('.edit-error'); if (e) e.textContent = msg || ''; el.classList.add('edit-has-error'); }
    function clrErr(el) { var e = el.closest('.edit-field').querySelector('.edit-error'); if (e) e.textContent = ''; el.classList.remove('edit-has-error'); }

    // Validaciones rápidas
    function vFullname() { var v = fields.fullname.value.trim(); if (v.length < 3) { setErr(fields.fullname, 'Mínimo 3 caracteres.'); return false; } clrErr(fields.fullname); return true; }
    function vUsername() { var v = fields.username.value.trim(); if (v.length < 3) { setErr(fields.username, 'Mínimo 3 caracteres.'); return false; } if (!noSpaces(v)) { setErr(fields.username, 'Sin espacios.'); return false; } clrErr(fields.username); return true; }
    function vGender() { if (!fields.gender.value) { setErr(fields.gender, 'Selecciona una opción.'); return false; } clrErr(fields.gender); return true; }
    function vContact() { var v = fields.contact.value.trim(); if (!/^[0-9]{7,10}$/.test(v)) { setErr(fields.contact, '7–10 dígitos.'); return false; } clrErr(fields.contact); return true; }
    function vCI() { var v = fields.ci.value.trim(); if (v.length < 5) { setErr(fields.ci, 'CI demasiado corto.'); return false; } clrErr(fields.ci); return true; }
    function vCorreo() { var v = fields.correo.value.trim(); if (!isEmail(v)) { setErr(fields.correo, 'Correo no válido.'); return false; } clrErr(fields.correo); return true; }

    // Eventos
    fields.fullname.addEventListener('input', vFullname);
    fields.username.addEventListener('input', vUsername);
    fields.gender.addEventListener('change', vGender);
    fields.contact.addEventListener('input', vContact);
    fields.ci.addEventListener('input', vCI);
    fields.correo.addEventListener('input', vCorreo);

    form.addEventListener('submit', function (e) {
      if (
        !vFullname() ||
        !vUsername() ||
        !vGender() ||
        !vContact() ||
        !vCI() ||
        !vCorreo()
      ) {
        e.preventDefault();
        Swal.fire({
          icon: 'warning',
          title: 'Datos inválidos',
          text: 'Revisa los campos marcados antes de guardar'
        });
      }
    });

  })();
</script>