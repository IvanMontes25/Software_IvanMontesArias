
<div class="container-fluid">

  <div class="d-sm-flex align-items-center justify-content-between mb-4">
    <div>
      <h1 class="h3 mb-0 text-gray-800">
          
      </h1>
      
    </div>
    <a href="planes.php" class="btn btn-sm btn-secondary plan-btn-back">
      <i class="fas fa-arrow-left mr-1"></i> Volver a planes
    </a>
  </div>

  <div class="row justify-content-center">
    <div class="col-xl-9 col-lg-10 col-md-12">

      <div class="card shadow mb-4 plan-card">
        <div class="card-header d-flex align-items-center justify-content-between plan-card-head">
          <div class="plan-head">
            <h2 class="h5 mb-0 text-light">
              <?= $edit ? 'Editar plan' : 'Crear nuevo plan' ?>
            </h2>
            <p class="mb-0 small text-light-50">
              <?= $edit ? 'Actualiza los datos del plan y guarda los cambios.' : 'Completa los campos para registrar un nuevo plan.' ?>
            </p>
          </div>
          <div class="plan-head-icon d-none d-sm-flex">
            <i class="fas fa-clipboard-list"></i>
          </div>
        </div>

        <div class="card-body plan-card-body">

          <?php if (!empty($error)): ?>
            <div class="alert alert-danger plan-alert">
              <i class="fas fa-exclamation-triangle mr-1"></i> <?= h($error) ?>
            </div>
          <?php endif; ?>

          <form method="post">
  <?php if(function_exists('csrf_field')) echo csrf_field(); ?>

            <input type="hidden" name="id" value="<?= h($plan['id']) ?>">

            <div class="plan-grid">

              <div class="plan-field plan-100">
                <label>Nombre del plan <span class="plan-req">*</span></label>
                <input type="text" name="nombre" class="plan-input"
                       value="<?= h($plan['nombre']) ?>" placeholder="Ej: Mensual Full" required>
                <small class="plan-help">Nombre visible en el sistema.</small>
              </div>

              <div class="plan-field plan-100">
                <label>Descripción</label>
                <textarea name="descripcion" class="plan-input plan-textarea" rows="3"
                          placeholder="Describe qué incluye el plan..."><?= h($plan['descripcion']) ?></textarea>
                <small class="plan-help">Opcional. Sirve para detallar condiciones o beneficios.</small>
              </div>

              <div class="plan-field">
                <label>Duración (en días) <span class="plan-req">*</span></label>
                <input type="number" name="duracion_dias" class="plan-input"
                       value="<?= (int)$plan['duracion_dias'] ?>" min="1" required>
                <small class="plan-help">Ej: 30, 90, 180, 365.</small>
              </div>

              <div class="plan-field">
                <label>Precio base (Bs.) <span class="plan-req">*</span></label>
                <input type="number" step="0.01" name="precio_base" class="plan-input"
                       value="<?= h($plan['precio_base']) ?>" min="0" required>
                <small class="plan-help">Monto sin descuentos.</small>
              </div>

              <div class="plan-field">
                <label>Tipo de acceso</label>
                <input type="text" name="tipo_acceso" class="plan-input"
                       value="<?= h($plan['tipo_acceso']) ?>" placeholder="General / Solo Pesas / Crossfit...">
                <small class="plan-help">Ej: General, Solo Pesas, Crossfit, Full, etc.</small>
              </div>

              <div class="plan-field">
                <label>Visitas máximas</label>
                <input type="number" name="visitas_max" class="plan-input"
                       value="<?= h($plan['visitas_max']) ?>" placeholder="Ilimitado">
                <small class="plan-help">Déjalo vacío si es ilimitado.</small>
              </div>

              <div class="plan-field plan-slim">
                <label>Estado</label>
                <select name="estado" class="plan-input">
                  <option value="activo"   <?= $plan['estado'] == 'activo'   ? 'selected' : '' ?>>Activo</option>
                  <option value="inactivo" <?= $plan['estado'] == 'inactivo' ? 'selected' : '' ?>>Inactivo</option>
                </select>
                <small class="plan-help">Controla si puede venderse/seleccionarse.</small>
              </div>

            </div>

            <div class="plan-actions text-center mt-4">
              <button type="submit" class="btn btn-success btn-lg plan-btn-main">
                <i class="fas fa-save mr-1"></i> Guardar
              </button>
              <a href="planes.php" class="btn btn-outline-secondary btn-lg plan-btn-secondary">
                Cancelar
              </a>
            </div>
          </form>

        </div>
      </div>

    </div>
  </div>

</div>


<style>
/* ====== SOLO VISUAL, estilo “base” ====== */
.plan-card{ border:none; border-radius:1rem; overflow:hidden; }
.plan-card-head{
  background: linear-gradient(135deg, #4e73df, #1cc88a);
  border-bottom:none; padding:1rem 1.25rem;
}
.plan-head h2{ font-weight:600; }
.plan-head-icon{ font-size:2rem; color: rgba(255,255,255,.85); }
.text-light-50{ color: rgba(255,255,255,.85) !important; }

.plan-card-body{ padding:1.75rem; background:#f8f9fc; }

.plan-alert{
  border-radius:.75rem;
  border:1px solid rgba(231,74,59,.25);
}

.plan-grid{
  display:grid;
  grid-template-columns:repeat(auto-fit,minmax(260px,1fr));
  gap:1.1rem;
}
.plan-field{ display:flex; flex-direction:column; }
.plan-100{ grid-column:1 / -1; }
.plan-slim{ max-width:320px; }

.plan-field label{
  font-size:.9rem;
  font-weight:600;
  color:#4e5d6c;
  margin-bottom:.25rem;
}
.plan-req{ color:#e74a3b; font-weight:700; }

.plan-input{
  width:100%;
  padding:.55rem .75rem;
  border-radius:.6rem;
  border:1px solid #d1d3e2;
  font-size:.95rem;
  background:#fff;
  transition: all .15s ease-in-out;
}
.plan-textarea{ min-height:110px; resize:vertical; }

.plan-input:focus{
  border-color:#4e73df;
  box-shadow:0 0 0 .15rem rgba(78,115,223,.25);
  outline:0;
}

.plan-help{
  font-size:.8rem;
  color:#6c757d;
  margin-top:.35rem;
  min-height:16px;
}

.plan-actions{
  display:flex;
  justify-content:center;
  gap:1rem;
  flex-wrap:wrap;
}
.plan-btn-main{
  min-width:210px;
  border-radius:2rem;
  font-weight:600;
  box-shadow:0 .25rem .5rem rgba(28,200,138,.35);
}
.plan-btn-secondary{
  min-width:180px;
  border-radius:2rem;
  font-weight:500;
}
.plan-btn-back{ border-radius:2rem; }

@media (max-width:576px){
  .plan-card-body{ padding:1.25rem; }
}
</style>
