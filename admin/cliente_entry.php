<?php
require_once __DIR__ . '/../core/bootstrap.php';
require_once __DIR__ . '/../core/auth.php';

/* ============================================================
   AJAX: Si llega petición POST con "ajax_check_dup" responde
   JSON y corta la ejecución (no renderiza el formulario)
============================================================ */
if (
  $_SERVER['REQUEST_METHOD'] === 'POST' &&
  isset($_POST['ajax_check_dup']) &&
  $db instanceof mysqli
) {
  header('Content-Type: application/json; charset=utf-8');

  $field = trim($_POST['field'] ?? '');
  $value = trim($_POST['value'] ?? '');

  $allowed = ['username', 'ci', 'correo', 'contact'];

  if (!in_array($field, $allowed, true) || $value === '') {
    echo json_encode(['duplicado' => false]);
    exit;
  }

  $stmt = $db->prepare("SELECT user_id, fullname FROM members WHERE {$field} = ? LIMIT 1");
  $stmt->bind_param("s", $value);
  $stmt->execute();
  $row = $stmt->get_result()->fetch_assoc();
  $stmt->close();

  if ($row) {
    $labels = [
      'username' => 'usuario',
      'ci' => 'CI',
      'correo' => 'correo electrónico',
      'contact' => 'número de teléfono',
    ];
    echo json_encode([
      'duplicado' => true,
      'mensaje' => 'Este ' . ($labels[$field] ?? $field) . ' ya está registrado para: ' . ($row['fullname'] ?? 'otro cliente'),
    ]);
  } else {
    echo json_encode(['duplicado' => false]);
  }
  exit;
}

?>
<?php include __DIR__ . '/theme/sb2/header.php'; ?>
<?php include __DIR__ . '/theme/sb2/sidebar.php'; ?>
<?php include __DIR__ . '/theme/sb2/topbar.php'; ?>

<div class="container-fluid">
  <div class="row justify-content-center">
    <div class="col-xl-8 col-lg-9 col-md-11">

      <div class="card shadow mb-4 reg-card">
        <div class="card-header d-flex align-items-center justify-content-between reg-card-head">
          <div class="reg-header">
            <h1 class="h4 mb-0 text-light">Registro de nuevo cliente</h1>
            <p class="mb-0 small text-light-50">
              Completa los datos para registrar un nuevo socio del gimnasio.
            </p>
          </div>
          <div class="reg-header-icon d-none d-sm-flex">
            <i class="fas fa-user-plus"></i>
          </div>
        </div>

        <div class="card-body reg-card-body">

          <?php if (!empty($_SESSION['client_error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
              <i class="fas fa-exclamation-circle mr-1"></i>
              <?= htmlspecialchars($_SESSION['client_error']) ?>
              <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
            </div>
            <?php unset($_SESSION['client_error']); ?>
          <?php endif; ?>

          <form id="formRegistro" action="cliente_actions.php" method="POST">
            <input type="hidden" name="op" value="create">

            <?php if (function_exists('csrf_field'))
              echo csrf_field(); ?>

            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">

            <!-- Sección: Datos personales -->
            <div class="reg-section-title">
              <h2 class="h6 mb-0">
                <i class="fas fa-id-card mr-1"></i> Datos personales
              </h2>
              <span class="reg-section-line"></span>
            </div>

            <div class="reg-grid">
              <div class="reg-field">
                <label for="fullname">Nombre Completo <span class="reg-req">*</span></label>
                <input type="text" id="fullname" name="fullname" class="reg-input" placeholder="Nombre y Apellido"
                  required />
                <small class="reg-help"></small><small class="reg-error"></small>
              </div>

              <div class="reg-field">
                <label for="username">Usuario <span class="reg-req">*</span></label>
                <input type="text" id="username" name="username" class="reg-input" placeholder="Usuario asignado"
                  required data-check-dup="username" />
                <small class="reg-help"></small><small class="reg-error"></small>
              </div>

              <div class="reg-field">
                <label for="password">Contraseña <span class="reg-req">*</span></label>
                <input type="password" id="password" name="password" class="reg-input" placeholder="Contraseña inicial"
                  required />
                <small class="reg-help">Por ejemplo, el CI o una clave temporal.</small><small
                  class="reg-error"></small>
              </div>

              <div class="reg-field">
                <label for="dor">Fecha de Registro</label>
                <input type="date" id="dor" name="dor" class="reg-input" />
                <small class="reg-help">Si la dejas vacía, se toma la fecha de hoy.</small><small
                  class="reg-error"></small>
              </div>

              <div class="reg-field">
                <label for="gender">Género</label>
                <select id="gender" name="gender" class="reg-input">
                  <option value="">-- Selecciona --</option>
                  <option value="Masculino">Masculino</option>
                  <option value="Femenino">Femenino</option>
                  <option value="Otro">Otro</option>
                </select>
                <small class="reg-help"></small><small class="reg-error"></small>
              </div>
            </div>

            <!-- Sección: Contacto -->
            <div class="reg-section-title mt-3">
              <h2 class="h6 mb-0">
                <i class="fas fa-phone-alt mr-1"></i> Datos de contacto
              </h2>
              <span class="reg-section-line"></span>
            </div>

            <div class="reg-grid">
              <div class="reg-field">
                <label for="ci">Cédula de Identidad (CI) <span class="reg-req">*</span></label>
                <input type="text" id="ci" name="ci" class="reg-input" placeholder="Número de CI" required
                  data-check-dup="ci" />
                <small class="reg-help"></small><small class="reg-error"></small>
              </div>

              <div class="reg-field">
                <label for="correo">Correo electrónico <span class="reg-req">*</span></label>
                <input type="email" id="correo" name="correo" class="reg-input" placeholder="correo@ejemplo.com"
                  required data-check-dup="correo" />
                <small class="reg-help"></small><small class="reg-error"></small>
              </div>

              <div class="reg-field">
                <label for="contact">Teléfono / Celular</label>
                <input type="text" id="contact" name="contact" class="reg-input" placeholder="Teléfono de contacto"
                  data-check-dup="contact" />
                <small class="reg-help"></small><small class="reg-error"></small>
              </div>
            </div>


            <!-- Botones -->
            <div class="form-actions reg-actions text-center mt-4">
              <button type="submit" id="btnRegistrar" class="btn btn-success btn-lg reg-btn-main">
                <i class="fas fa-paper-plane mr-1"></i> Registrar Cliente
              </button>
              <a href="clientes.php" class="btn btn-outline-secondary btn-lg reg-btn-secondary">
                <i class="fas fa-users mr-1"></i> Ver clientes
              </a>
            </div>

          </form>
        </div>
      </div>
    </div>
  </div>
</div>

<?php include __DIR__ . '/theme/sb2/footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- ====== VALIDACION EN TIEMPO REAL (se consulta a este mismo archivo) ====== -->
<script>
  (function () {
    document.addEventListener('DOMContentLoaded', function () {

      var checkFields = document.querySelectorAll('[data-check-dup]');
      var dupState = {};
      var timers = {};

      checkFields.forEach(function (input) {
        var field = input.getAttribute('data-check-dup');
        dupState[field] = false;

        input.addEventListener('blur', function () { checkDuplicate(input, field); });
        input.addEventListener('input', function () {
          clearTimeout(timers[field]);
          timers[field] = setTimeout(function () { checkDuplicate(input, field); }, 500);
        });
      });

      function checkDuplicate(input, field) {
        var value = input.value.trim();
        var errorEl = input.closest('.reg-field').querySelector('.reg-error');

        if (value === '') { clearState(input, errorEl, field); return; }
        if (field === 'username' && value.length < 3) return;
        if (field === 'ci' && value.length < 5) return;
        if (field === 'contact' && value.length < 7) return;

        var fd = new FormData();
        fd.append('ajax_check_dup', '1');
        fd.append('field', field);
        fd.append('value', value);

        // Se envía a este mismo archivo (cliente_entry.php)
        fetch('cliente_entry.php', { method: 'POST', body: fd })
          .then(function (r) { return r.json(); })
          .then(function (data) {
            if (data.duplicado) {
              input.classList.add('reg-input-error');
              input.classList.remove('reg-input-ok');
              errorEl.innerHTML = '<i class="fas fa-exclamation-triangle"></i> ' + data.mensaje;
              errorEl.style.display = 'block';
              dupState[field] = true;
            } else {
              input.classList.remove('reg-input-error');
              input.classList.add('reg-input-ok');
              errorEl.innerHTML = '';
              errorEl.style.display = 'none';
              dupState[field] = false;
            }
          })
          .catch(function () { clearState(input, errorEl, field); });
      }

      function clearState(input, errorEl, field) {
        input.classList.remove('reg-input-error', 'reg-input-ok');
        errorEl.innerHTML = '';
        errorEl.style.display = 'none';
        dupState[field] = false;
      }

      // ====== BLOQUEAR SUBMIT SI HAY DUPLICADOS ======
      document.getElementById('formRegistro').addEventListener('submit', function (e) {
        var camposDup = [];
        var labels = { username: 'Usuario', ci: 'CI', correo: 'Correo', contact: 'Teléfono' };

        for (var key in dupState) {
          if (dupState[key]) camposDup.push(labels[key] || key);
        }

        if (camposDup.length > 0) {
          e.preventDefault();
          Swal.fire({
            icon: 'error',
            title: 'Datos duplicados',
            html: 'Los siguientes campos ya están registrados:<br><strong>' +
              camposDup.join(', ') +
              '</strong><br><br>Corrige los campos marcados en rojo antes de continuar.',
            confirmButtonColor: '#e74a3b'
          });
        }
      });

    });
  })();
</script>

<style>
  /* Card general */
  .reg-card {
    border: none;
    border-radius: 1rem;
    overflow: hidden;
  }

  .reg-card-head {
    background: linear-gradient(135deg, #4e73df, #1cc88a);
    border-bottom: none;
    padding-top: 1rem;
    padding-bottom: 1rem;
  }

  .reg-header h1 {
    font-weight: 600;
  }

  .reg-header-icon {
    font-size: 2rem;
    color: rgba(255, 255, 255, 0.85);
  }

  .reg-card-body {
    padding: 1.4rem 1.6rem;
  }

  /* Secciones */
  .reg-section-title {
    display: flex;
    align-items: center;
    margin-bottom: .75rem;
    margin-top: .25rem;
  }

  .reg-section-title h2 {
    font-weight: 700;
    font-size: .95rem;
    color: #4e73df;
    text-transform: uppercase;
    letter-spacing: .04em;
  }

  .reg-section-title i {
    color: #4e73df;
  }

  .reg-section-line {
    flex: 1;
    height: 1px;
    background: #e3e6f0;
    margin-left: .75rem;
  }

  /* Campos */
  .reg-req {
    color: #e74a3b;
  }

  .reg-error {
    color: #e74a3b;
    font-size: 0.8rem;
    display: none;
  }

  .reg-error i {
    margin-right: 3px;
  }

  .reg-help {
    font-size: 0.8rem;
    color: #6c757d;
    display: block;
  }

  .reg-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
    grid-gap: .85rem;
  }

  .reg-field {
    margin-bottom: 0.3rem;
  }

  .reg-field label {
    font-size: .9rem;
    font-weight: 600;
    color: #4e5d6c;
    margin-bottom: .25rem;
  }

  .reg-input {
    width: 100%;
    padding: .42rem .65rem;
    border-radius: .5rem;
    border: 1px solid #d1d3e2;
    font-size: .88rem;
    background-color: #fff;
    transition: all .2s ease-in-out;
  }

  .reg-input:focus {
    border-color: #4e73df;
    box-shadow: 0 0 0 0.15rem rgba(78, 115, 223, 0.25);
    outline: 0;
  }

  /* === ESTADOS DE VALIDACION === */
  .reg-input-error {
    border-color: #e74a3b !important;
    background-color: #fff5f5 !important;
    box-shadow: 0 0 0 0.15rem rgba(231, 74, 59, 0.2) !important;
  }

  .reg-input-ok {
    border-color: #1cc88a !important;
    background-color: #f0fdf4 !important;
    box-shadow: 0 0 0 0.15rem rgba(28, 200, 138, 0.15) !important;
  }

  .reg-field.reg-100 {
    margin-top: .75rem;
    grid-column: 1 / -1;
  }

  /* Botones */
  .reg-actions {
    display: flex;
    justify-content: center;
    gap: 1rem;
    flex-wrap: wrap;
  }

  .reg-btn-main {
    min-width: 190px;
    border-radius: 2rem;
    font-weight: 600;
    box-shadow: 0 .25rem .5rem rgba(28, 200, 138, 0.35);
  }

  .reg-btn-secondary {
    min-width: 160px;
    border-radius: 2rem;
    font-weight: 500;
  }

  .reg-btn-main i,
  .reg-btn-secondary i {
    font-size: .9rem;
  }

  .text-light-50 {
    color: rgba(255, 255, 255, 0.85) !important;
  }

  @media (max-width: 576px) {
    .reg-card-body {
      padding: 1.25rem;
    }

    .reg-section-title h2 {
      font-size: .95rem;
    }
  }
</style>