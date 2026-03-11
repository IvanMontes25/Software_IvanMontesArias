

require_once __DIR__ . '/../core/bootstrap.php';
if (!$db instanceof mysqli) {
  die('No hay conexión a la base de datos');
}



// Captura de “flash” por GET opcional (desde staff_agregado.php)
$ok  = isset($_GET['ok'])  ? $_GET['ok']  : null;           // 1 = éxito
$err = isset($_GET['err']) ? trim($_GET['err']) : null;     // mensaje de error opcional
?>
<!-- BEGIN: migrated original content -->
<div class="sb2-content">
  <div id="content">
    <div id="content-header" class="mb-3">
      <div id="breadcrumb">
        
      </div>
      <h1 class="text-center">Registro de Personal <i class="fas fa-briefcase"></i></h1>
    </div>

    <div class="container-fluid">
      <!-- Flash global -->
      <?php if ($ok === '1'): ?>
        <div class="alert alert-success text-center shadow-sm rounded-2" role="alert">
          ✅ El personal fue registrado correctamente.
        </div>
      <?php elseif (!empty($err)): ?>
        <div class="alert alert-danger text-center shadow-sm rounded-2" role="alert">
          ⚠️ Error: <?= htmlspecialchars($err) ?>
        </div>
      <?php endif; ?>

      <div class="row">
        <div class="col-12">
          <div class="card shadow-sm form-card">
            <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
              <h5 class="mb-0"><i class="fas fa-id-card mr-2"></i>Datos del Personal</h5>
              <small class="text-muted">Los campos con * son obligatorios</small>
            </div>

            <div class="card-body">
              <form id="staffForm" action="staff_agregado.php" method="POST" novalidate>
  <?php if(function_exists('csrf_field')) echo csrf_field(); ?>

                <!-- Sección: Cuenta -->
                <div class="section-title">
                  <span class="section-badge"><i class="fas fa-user-shield"></i></span>
                  <h6 class="mb-1">Cuenta</h6>
                  <p class="text-muted mb-3">Credenciales de acceso al sistema</p>
                </div>

                <div class="row g-3">
                  <div class="col-md-6">
                    <label for="fullname" class="form-label">Nombre completo *</label>
                    <input id="fullname" name="fullname" type="text" class="form-control" placeholder="Ej: María López" required>
                    <div class="invalid-feedback">Ingresa el nombre completo.</div>
                  </div>

                  <div class="col-md-6">
                    <label for="username" class="form-label">Usuario *</label>
                    <div class="input-group">
                      <span class="input-group-text">@</span>
                      <input id="username" name="username" type="text" class="form-control" placeholder="Ej: mlopez" required>
                      <div class="invalid-feedback">Ingresa un nombre de usuario.</div>
                    </div>
                  </div>

                  <div class="col-md-6">
                    <label for="password" class="form-label">Contraseña *</label>
                    <input id="password" name="password" type="password" class="form-control" placeholder="Mínimo 6 caracteres" minlength="6" required>
                    <div class="form-text">Usa una contraseña segura (letras, números y símbolos).</div>
                    <div class="invalid-feedback">La contraseña debe tener al menos 6 caracteres.</div>
                  </div>

                  <div class="col-md-6">
                    <label for="password2" class="form-label">Confirmar contraseña *</label>
                    <input id="password2" name="password2" type="password" class="form-control" placeholder="Repítela exactamente" required>
                    <div class="invalid-feedback">Las contraseñas no coinciden.</div>
                  </div>
                </div>

                <hr class="my-4">

                <!-- Sección: Datos personales -->
                <div class="section-title">
                  <span class="section-badge"><i class="fas fa-address-card"></i></span>
                  <h6 class="mb-1">Datos personales</h6>
                  <p class="text-muted mb-3">Información básica de contacto y rol</p>
                </div>

                <div class="row g-3">
                  <div class="col-md-6">
                    <label for="email" class="form-label">Correo electrónico *</label>
                    <input id="email" name="email" type="email" class="form-control" placeholder="nombre@dominio.com" required>
                    <div class="invalid-feedback">Ingresa un correo válido.</div>
                  </div>

                  <div class="col-md-6">
                    <label for="contact" class="form-label">Número de contacto *</label>
                    <input id="contact" name="contact" type="tel" inputmode="numeric" pattern="[0-9]{6,15}" class="form-control" placeholder="Ej: 76543210" required>
                    <div class="form-text">Solo dígitos (6 a 15).</div>
                    <div class="invalid-feedback">Ingresa un teléfono válido (solo números, 6–15 dígitos).</div>
                  </div>

                  <div class="col-md-8">
                    <label for="address" class="form-label">Dirección *</label>
                    <input id="address" name="address" type="text" class="form-control" placeholder="Calle, número, zona" required>
                    <div class="invalid-feedback">Ingresa la dirección.</div>
                  </div>

                  <div class="col-md-4">
                    <label for="gender" class="form-label">Género *</label>
                    <select id="gender" name="gender" class="form-select" required>
                      <option value="" disabled selected>Selecciona…</option>
                      <option value="Masculino">Masculino</option>
                      <option value="Femenino">Femenino</option>
                    </select>
                    <div class="invalid-feedback">Selecciona una opción.</div>
                  </div>

                  <div class="col-md-6">
                    <label for="designation" class="form-label">Rol *</label>
                    <select id="designation" name="designation" class="form-select" required>
                      <option value="" disabled selected>Selecciona…</option>
                      <option value="Cajero">Cajero</option>
                      <option value="Entrenador">Entrenador</option>
                      <option value="Asistente">Asistente</option>
                      <option value="Recepcionista">Recepcionista</option>
                      <option value="Administrador">Administrador</option>
                    </select>
                    <div class="invalid-feedback">Selecciona el rol.</div>
                  </div>
                </div>

                <div class="d-flex justify-content-end gap-2 mt-4">
                  <button type="reset" class="btn btn-light">Limpiar</button>
                  <button type="submit" class="btn btn-primary">
                    Registrar <i class="fas fa-check ml-1"></i>
                  </button>
                </div>
              </form>
            </div>

          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<!-- END: migrated original content -->

<style>
  /* Card y estructura */
  .form-card { border-radius: 12px; overflow: hidden; }
  .form-card .card-header { background:#f7f8fa; border-bottom:1px solid #eaeaea; }
  .section-title { display:flex; align-items:center; gap:10px; }
  .section-badge { display:inline-flex; width:32px; height:32px; border-radius:50%; background:#eef2ff; color:#3b82f6; align-items:center; justify-content:center; }

  /* Inputs */
  .form-control, .form-select { border-radius: 10px; }
  .form-control:focus, .form-select:focus { box-shadow: 0 0 0 .2rem rgba(59,130,246,.15); border-color:#93c5fd; }

  /* Mensajes de error */
  .is-invalid { border-color:#dc3545 !important; }
  .invalid-feedback { display:none; }
  .show-invalid .invalid-feedback { display:block; }
  .show-invalid .is-invalid + .invalid-feedback { display:block; }

  /* Footer */
  #footer { background:#2f2f2f; color:#fff; padding:10px 0; border-top:1px solid #242424; }
</style>

<script>
// ===== Validación mejorada en cliente =====
(function(){
  const form = document.getElementById('staffForm');
  const fullname  = document.getElementById('fullname');
  const username  = document.getElementById('username');
  const password  = document.getElementById('password');
  const password2 = document.getElementById('password2');
  const email     = document.getElementById('email');
  const contact   = document.getElementById('contact');
  const address   = document.getElementById('address');
  const gender    = document.getElementById('gender');
  const role      = document.getElementById('designation');

  function setInvalid(el, condition){
    if (condition) {
      el.classList.add('is-invalid');
    } else {
      el.classList.remove('is-invalid');
    }
  }

  function validEmail(v){
    // Sencillo y suficiente
    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(v);
  }

  function validPhone(v){
    return /^[0-9]{6,15}$/.test(v);
  }

  function validate(){
    let bad = 0;
    form.classList.add('show-invalid');

    setInvalid(fullname,  fullname.value.trim()==='');                bad += (fullname.classList.contains('is-invalid'))?1:0;
    setInvalid(username,  username.value.trim()==='');                bad += (username.classList.contains('is-invalid'))?1:0;
    setInvalid(password,  password.value.length < 6);                 bad += (password.classList.contains('is-invalid'))?1:0;
    setInvalid(password2, password2.value !== password.value || password2.value===''); bad += (password2.classList.contains('is-invalid'))?1:0;
    setInvalid(email,     !validEmail(email.value.trim()));           bad += (email.classList.contains('is-invalid'))?1:0;
    setInvalid(contact,   !validPhone(contact.value.trim()));         bad += (contact.classList.contains('is-invalid'))?1:0;
    setInvalid(address,   address.value.trim()==='');                 bad += (address.classList.contains('is-invalid'))?1:0;
    setInvalid(gender,    !gender.value);                             bad += (gender.classList.contains('is-invalid'))?1:0;
    setInvalid(role,      !role.value);                               bad += (role.classList.contains('is-invalid'))?1:0;

    return bad === 0;
  }

  // Validación en blur para feedback inmediato
  [fullname, username, password, password2, email, contact, address, gender, role].forEach(el=>{
    el.addEventListener('blur', validate);
    el.addEventListener('input', ()=>{ el.classList.remove('is-invalid'); });
  });

  form.addEventListener('submit', function(e){
    if (!validate()){
      e.preventDefault();
      // Mensaje de error global mejorado
      let alert = document.querySelector('.alert-global');
      if (!alert) {
        alert = document.createElement('div');
        alert.className = 'alert alert-danger alert-global text-center shadow-sm rounded-2';
        alert.role = 'alert';
        form.closest('.card').insertBefore(alert, form.closest('.card').firstChild.nextSibling);
      }
      alert.textContent = 'Por favor corrige los campos marcados en rojo.';
      alert.scrollIntoView({ behavior:'smooth', block:'center' });
    }
  });
})();
</script>
