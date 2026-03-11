
$hoy = date('Y-m-d');
?>
<?php if (!empty($_SESSION['flash_success'])): ?>
<script>
document.addEventListener("DOMContentLoaded", function() {
    Swal.fire({
        icon: "success",
        title: "¡Éxito!",
        text: "<?php echo $_SESSION['flash_success']; ?>",
        confirmButtonColor: "#4e73df",
        timer: 2500,
        timerProgressBar: true
    });
});
</script>
<?php unset($_SESSION['flash_success']); ?>
<?php endif; ?>
<div class="container-fluid py-4">

  <!-- ===== PAGE HEADER ===== -->
  <div class="page-header mb-4">
    <div class="page-header-inner">
      <h1 class="page-title">Publicaciones del Gimnasio</h1>
      <p class="page-subtitle">
        Comparte novedades, promociones y contenido visual de Gym Body Training
      </p>
    </div>
  </div>

  <!-- ===== CARD CENTRADA ===== -->
  <div class="row justify-content-center">
    <div class="col-xl-7 col-lg-8 col-md-10">

      <div class="card shadow mb-4 post-card">

        <div class="card-header py-3 d-flex align-items-center justify-content-between flex-wrap">
          <h6 class="m-0 d-flex align-items-center">
            <i class="fas fa-pen mr-2"></i>
            Realizar publicación
          </h6>

          <div class="post-header-actions">
            <span class="badge badge-light badge-pillish">
              <i class="fas fa-info-circle mr-1"></i>
              Máx. 1000 caracteres
            </span>

            <a href="admin_publicacion.php"
               class="btn btn-outline-light btn-sm btn-admin-post">
              <i class="fas fa-cogs mr-1"></i>
              Administrar
            </a>
          </div>
        </div>

        <div class="card-body">

          <form id="postForm" action="post_publicacion.php" method="POST" enctype="multipart/form-data" novalidate>

            <!-- Mensaje -->
            <div class="mb-3">
              <label class="form-label font-weight-600">Mensaje</label>
              <textarea class="textarea_editor form-control"
                        name="message"
                        rows="6"
                        maxlength="1000"
                        required
                        placeholder="Escribe tu publicación..."></textarea>

              <div class="d-flex justify-content-between mt-1">
                <small class="text-muted">Máx. 1000 caracteres</small>
                <small id="charCount" class="text-muted">0 / 1000</small>
              </div>
            </div>

            <!-- Fecha + Visibilidad -->
            <div class="row">
              <div class="col-md-6 mb-3">
                <div class="form-card">
                  <label class="form-label font-weight-600 mb-2">Fecha a publicar</label>
                  <input class="form-control"
                         type="date"
                         name="date"
                         min="<?php echo $hoy; ?>"
                         value="<?php echo $hoy; ?>">
                </div>
              </div>

              <div class="col-md-6 mb-3">
                <div class="form-card">
                  <legend class="form-label font-weight-600 mb-2">Visibilidad</legend>

                  <div class="form-check">
                    <input class="form-check-input" type="radio" name="visibility" value="public" checked>
                    <label class="form-check-label">
                      <i class="fas fa-globe-americas mr-1"></i> Público
                    </label>
                  </div>

                  <div class="form-check">
                    <input class="form-check-input" type="radio" name="visibility" value="members">
                    <label class="form-check-label">
                      <i class="fas fa-user-friends mr-1"></i> Solo miembros
                    </label>
                  </div>
                </div>
              </div>
            </div>

            <!-- Botones -->
            <div class="text-center mt-4">
              <button type="submit" class="btn btn-primary rounded-pill">
                <i class="fas fa-paper-plane mr-1"></i> Publicar
              </button>
              <button type="reset" class="btn btn-light ml-2 rounded-pill">
                <i class="fas fa-undo mr-1"></i> Limpiar
              </button>
            </div>

            <?php
              if (empty($_SESSION['csrf_token'])) {
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
              }
            ?>
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

          </form>

        </div>
      </div>

    </div>
  </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<style>
/* === Estilo unificado SB Admin 2 PRO === */
.post-card{ border:0; border-radius:1rem; overflow:hidden; }
.post-card .card-header{
  background: linear-gradient(90deg,#4e73df,#1cc88a);
  color:#fff;
}
.badge-pillish{ border-radius:999px; font-weight:500; }

.form-control{ border-radius:10px; }

.form-card{
  border:1px solid rgba(0,0,0,.08);
  border-radius:14px;
  padding:14px;
  background:#fff;
  box-shadow:0 2px 10px rgba(0,0,0,.04);
}

.form-card legend{
  font-size:1rem;
  margin:0;
  padding:0;
}

.upload-box{
  border:1px solid rgba(0,0,0,.08);
  border-radius:14px;
  padding:14px;
  background:#f9fafb;
}

/* Previews */
#previewGrid .thumb{
  position:relative;
  width:135px;
  height:135px;
  border-radius:12px;
  overflow:hidden;
  box-shadow:0 6px 18px rgba(0,0,0,.08);
  background:#fff;
}
#previewGrid .thumb img{
  width:100%;
  height:100%;
  object-fit:cover;
  display:block;
}
#previewGrid .thumb button{
  position:absolute;
  top:6px;
  right:6px;
  border:0;
  border-radius:8px;
  padding:6px 8px;
  cursor:pointer;
  background:rgba(0,0,0,.55);
  color:#fff;
}

.font-weight-600{ font-weight:600; }
#footer{ color:#fff; }

@media (max-width: 767.98px){
  .form-card .form-check{ margin-bottom:.25rem; }
}
</style>

<!-- Scripts necesarios de tu plantilla -->
<script src="../js/excanvas.min.js"></script>
<script src="../js/matrix.js"></script>
<script src="../js/matrix.dashboard.js"></script>
<script src="../js/matrix.interface.js"></script>
<script src="../js/matrix.chat.js"></script>
<script src="../js/select2.min.js"></script>
<script src="../js/matrix.popover.js"></script>
<script src="../js/matrix.tables.js"></script>
<script src="../js/wysihtml5-0.3.0.js"></script>

<script>
  // Editor
  (function(){
    const ta = document.querySelector('.textarea_editor');
    if (ta && typeof $ !== 'undefined' && $.fn.wysihtml5) {
      $('.textarea_editor').wysihtml5();
    }
  })();

  // Contador de caracteres
  (function(){
    const ta = document.querySelector('textarea[name="message"]');
    const count = document.getElementById('charCount');
    if (!ta || !count) return;
    const max = parseInt(ta.getAttribute('maxlength') || '1000', 10);
    const update = () => {
      const len = ta.value.length;
      count.textContent = `${len} / ${max}`;
    };
    ta.addEventListener('input', update);
    update();
  })();

  // Vista previa y validación de imágenes
  (function(){
    const input = document.getElementById('images');
    const grid = document.getElementById('previewGrid');
    const MAX_FILES = 10;
    const MAX_MB = 5;
    const ALLOWED = ['image/jpeg','image/png','image/webp'];

    let filesState = [];

    function renderGrid() {
      grid.innerHTML = '';
      filesState.forEach((file, idx) => {
        const url = URL.createObjectURL(file);
        const wrap = document.createElement('div');
        wrap.className = 'thumb';
        wrap.innerHTML = `
          <img src="${url}" alt="preview-${idx}">
          <button type="button" title="Quitar" data-idx="${idx}">
            <i class="fas fa-times"></i>
          </button>
        `;
        grid.appendChild(wrap);
      });
    }

    function validateAndAdd(list) {
      const current = filesState.length;
      if (current + list.length > MAX_FILES) {
        alert('Puedes subir hasta ' + MAX_FILES + ' imágenes.');
        return;
      }
      const valid = [];
      for (const f of list) {
        if (!ALLOWED.includes(f.type)) {
          alert('Formato no permitido: ' + f.name);
          continue;
        }
        if (f.size > MAX_MB * 1024 * 1024) {
          alert(`"${f.name}" supera ${MAX_MB} MB.`);
          continue;
        }
        valid.push(f);
      }
      filesState = filesState.concat(valid);
      renderGrid();
    }

    input?.addEventListener('change', (e) => {
      validateAndAdd(Array.from(e.target.files || []));
      input.value = '';
    });

    grid.addEventListener('click', (e) => {
      const btn = e.target.closest('button[data-idx]');
      if (!btn) return;
      const i = parseInt(btn.getAttribute('data-idx'), 10);
      filesState.splice(i, 1);
      renderGrid();
    });

    const form = document.getElementById('postForm');
    form.addEventListener('submit', (e) => {
      const msg = document.querySelector('textarea[name="message"]')?.value.trim() || '';
      if (!msg && filesState.length === 0) {
        e.preventDefault();
        alert('Escribe un mensaje o agrega al menos una imagen.');
        return;
      }

      const dt = new DataTransfer();
      filesState.forEach(f => dt.items.add(f));
      input.files = dt.files;
    });
  })();


  
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
  max-width: 800px;   /* 🔥 MISMO ANCHO QUE EL CONTENIDO */
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
/* ===== HEADER PUBLICACIONES ===== */
.post-header-actions{
  display:flex;
  align-items:center;
  gap:12px;               /* 🔥 separa badge y botón */
}

.post-header-actions .badge{
  font-size:.75rem;
  padding:.35rem .65rem;
  background:rgba(255,255,255,.85);
  color:#1f2937;
}

.btn-admin-post{
  border-radius:999px;
  padding:.35rem .8rem;
  font-size:.75rem;
  font-weight:600;
  white-space:nowrap;
}

.btn-admin-post:hover{
  background:#ffffff;
  color:#4e73df;
}

</style>
</div>


