?>

<div class="sb2-content">
if (!$db instanceof mysqli) {
  die('No hay conexión a la base de datos');
}

?>

<style>
/* Contenedor más compacto */
.eq-wrap{
  max-width: 980px;     /* 👈 controla el ancho del formulario */
  margin: 0 auto;
}

/* Card más moderno */
.eq-card{
  border:0;
  border-radius: 1rem;
  overflow:hidden;
  box-shadow: 0 10px 25px rgba(0,0,0,.08);
}

.eq-card .card-header{
  background: linear-gradient(90deg,#4e73df,#1cc88a);
  color:#fff;
  border-bottom:0;
  padding: 14px 18px;
}

.eq-title{
  display:flex;
  align-items:center;
  justify-content:center;
  gap:10px;
  margin:0;
}

.eq-subtitle{
  opacity:.92;
  margin-top:4px;
}

/* Inputs más finos y prolijos */
.eq-card .form-group label{
  font-weight:600;
  color:#4a4a4a;
  margin-bottom:.35rem;
}

.eq-card .form-control{
  border-radius: .75rem;
  height: 42px;
}

.eq-card input[type="date"].form-control{
  padding-top: .35rem;
}

.eq-card .input-group-text{
  border-radius: .75rem 0 0 .75rem;
  font-weight:700;
}

.eq-card .input-group .form-control{
  border-radius: 0 .75rem .75rem 0;
}

.eq-help{
  font-size:.82rem;
  margin-top:.35rem;
}

/* Separación más compacta */
.eq-card .card-body{
  padding: 18px 18px 16px;
}

.eq-row{
  row-gap: 10px;
}

/* Botones */
.eq-actions{
  display:flex;
  gap:10px;
  justify-content:center;
  flex-wrap:wrap;
  margin-top: 16px;
}

.btn-pill{
  border-radius: 999px !important;
  padding: .5rem 1rem;
}

/* En pantallas grandes: un poquito menos ancho */
@media (min-width: 1200px){
  .eq-wrap{ max-width: 900px; }
}

/* =========================
   MODAL ÉXITO / ERROR
========================= */
.notice-overlay{
  position: fixed;
  inset: 0;
  background: rgba(0,0,0,.45);
  display:flex;
  align-items:center;
  justify-content:center;
  z-index: 1055;
}

.notice-modal{
  background:#fff;
  border-radius: 1rem;
  width: 100%;
  max-width: 390px;
  padding: 28px 22px 24px;
  text-align:center;
  box-shadow: 0 25px 60px rgba(0,0,0,.25);
  animation: popIn .25s ease-out;
}

.notice-icon{
  width:72px;
  height:72px;
  margin:0 auto 14px;
  border-radius:50%;
  display:flex;
  align-items:center;
  justify-content:center;
}

.notice-icon i{
  font-size:32px;
}

.notice-title{
  font-size:1.15rem;
  font-weight:700;
  color:#1f2937;
  margin-bottom:6px;
}

.notice-text{
  font-size:.9rem;
  color:#6b7280;
  margin-bottom:18px;
}

.notice-btn{
  border:none;
  color:#fff;
  padding:.48rem 1.45rem;
  border-radius:999px;
  font-weight:600;
  transition:.2s;
}

.notice-btn:focus{ outline: none; }

@keyframes popIn{
  from{ transform:scale(.9); opacity:0; }
  to{ transform:scale(1); opacity:1; }
}
</style>

<div class="container-fluid">
  <div class="eq-wrap">

    <!-- Card -->
    <div class="card eq-card mb-4">
      <div class="card-header text-center">
        <h6 class="eq-title">
          <i class="fas fa-clipboard-list"></i> Registrar nuevo equipo
        </h6>
        <div class="eq-subtitle ">Campos obligatorios marcados por el sistema</div>
      </div>

      <div class="card-body">
        <form action="equipo_actions.php" method="POST">
    <input type="hidden" name="op" value="create">
    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token']; ?>">

  <?php if(function_exists('csrf_field')) echo csrf_field(); ?>

        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
  

          <div class="row eq-row">
            <div class="col-lg-6">
              <div class="form-group mb-3">
                <label for="ename">Equipo</label>
                <input type="text" class="form-control" id="ename" name="name" placeholder="Nombre del equipo" required>
              </div>

              <div class="form-group mb-3">
                <label for="description">Descripción</label>
                <input type="text" class="form-control" id="description" name="description" placeholder="Descripción corta" required>
              </div>

              <div class="form-group mb-3">
                <label for="date">Fecha de Compra</label>
                <input type="date" class="form-control" id="date" name="date">
                <small class="form-text text-muted eq-help">Por favor mencione la fecha de compra.</small>
              </div>

              <div class="form-group mb-3">
                <label for="quantity">Cantidad</label>
                <input type="number" class="form-control" id="quantity" name="quantity" placeholder="Cantidad de equipos" required>
              </div>
            </div>

            <div class="col-lg-6">
              <div class="form-group mb-3">
                <label for="vendor">Proveedor</label>
                <input type="text" class="form-control" id="vendor" name="vendor" placeholder="Proveedor" required>
              </div>

              <div class="form-group mb-3">
                <label for="address">Dirección</label>
                <input type="text" class="form-control" id="address" name="address" placeholder="Dirección del proveedor" required>
              </div>

              <div class="form-group mb-3">
                <label for="contact">Número de Contacto</label>
                <input type="text" class="form-control" id="contact" name="contact" minlength="8" maxlength="15" placeholder="Ej: 72078787" required>
                <small class="form-text text-muted eq-help">Formato recomendado: solo números.</small>
              </div>

              <div class="form-group mb-3">
                <label for="amount">Costo por Producto</label>
                <div class="input-group">
                  <div class="input-group-prepend">
                    <span class="input-group-text">Bs.</span>
                  </div>
                  <input type="number" class="form-control" id="amount" name="amount" placeholder="269" required>
                </div>
              </div>
            </div>
          </div>

          <!-- Botones -->
          <div class="eq-actions">
            <a href="equipos.php" class="btn btn-secondary btn-pill">
              <i class="fas fa-arrow-left mr-1"></i> Volver
            </a>
            <button type="submit" class="btn btn-success btn-pill">
              <i class="fas fa-save mr-2"></i> Enviar Datos
            </button>
          </div>

        </form>
      </div>
    </div>

  </div>
</div>

<!-- =========================
     MODALES (ÉXITO / ERROR)
========================= -->
<?php if (isset($_GET['success']) && $_GET['success'] == '1'): ?>
  <div class="notice-overlay" id="noticeOverlay">
    <div class="notice-modal">
      <div class="notice-icon" style="background:#e6f9f1;">
        <i class="fas fa-check" style="color:#1cc88a;"></i>
      </div>
      <div class="notice-title">Registro exitoso</div>
      <div class="notice-text">El equipo fue registrado correctamente en el sistema.</div>
      <button class="notice-btn" style="background:#1cc88a;" onclick="closeNotice()">Aceptar</button>
    </div>
  </div>
<?php endif; ?>

<?php if (isset($_GET['error']) && $_GET['error'] == '1'): ?>
  <div class="notice-overlay" id="noticeOverlay">
    <div class="notice-modal">
      <div class="notice-icon" style="background:#ffecec;">
        <i class="fas fa-times" style="color:#e74a3b;"></i>
      </div>
      <div class="notice-title">Ocurrió un error</div>
      <div class="notice-text">No se pudo registrar el equipo. Intenta nuevamente.</div>
      <button class="notice-btn" style="background:#e74a3b;" onclick="closeNotice()">Aceptar</button>
    </div>
  </div>
<?php endif; ?>

<script>
function closeNotice(){
  // Redirigir directamente a la lista de equipos
  window.location.href = 'equipos.php';
}
</script>
  

</div>
