<?php
// Vista - Variables disponibles desde el controlador
?>
<?php include __DIR__ . '/../../theme/sb2/header.php'; ?>
<?php include __DIR__ . '/../../theme/sb2/sidebar.php'; ?>
<?php include __DIR__ . '/../../theme/sb2/topbar.php'; ?>

<div class="container-fluid sb2-content">

  <div class="row justify-content-center mt-4">
    <div class="col-xl-9 col-lg-10">

      <!-- HERO CARD -->
      <div class="card shadow-lg border-0 mb-4 overflow-hidden">
        <div class="card-body p-0">

          <!-- Header visual -->
          <div class="bg-gradient-danger text-white p-4">
            <div class="d-flex align-items-center">
              <div class="mr-3">
                <div class="rounded-circle bg-white text-danger d-flex align-items-center justify-content-center"
                     style="width:64px;height:64px;">
                  <i class="fas fa-lock fa-2x"></i>
                </div>
              </div>
              <div>
                <h4 class="mb-1 font-weight-bold">
                  Acceso restringido
                </h4>
                <p class="mb-0 opacity-75">
                  Estado de membresía no activa
                </p>
              </div>
            </div>
          </div>

          <!-- Contenido -->
          <div class="p-4">

            <h5 class="font-weight-bold mb-3">
              Hola<?= $fullname ? ', ' . h($fullname) : '' ?> 👋
            </h5>
<?php if ($mensaje_dinamico): ?>
  <div class="alert alert-<?= h($clase_alerta) ?> shadow-sm mb-3">
    <i class="fas fa-bell mr-1"></i>
    <?= $mensaje_dinamico ?>
  </div>
<?php endif; ?>


            <?php if ($estadoReal === 'vencida' && $vence_el): ?>

              <div class="alert alert-danger border-left-danger shadow-sm">
                <i class="fas fa-calendar-times mr-1"></i>
                Tu membresía <strong>venció el <?= date('d/m/Y', strtotime($vence_el)) ?></strong>
                <?= $dias_texto ? '<span class="text-muted">('.h($dias_texto).')</span>' : '' ?>.
              </div>

              <?php if ($paid_date): ?>
                <div class="small text-muted mb-3">
                  <i class="fas fa-receipt mr-1"></i>
                  Último pago registrado el <?= date('d/m/Y', strtotime($paid_date)) ?>
                </div>
              <?php endif; ?>

            <?php else: ?>
              <div class="alert alert-warning border-left-warning shadow-sm">
                <i class="fas fa-info-circle mr-1"></i>
                Actualmente no cuentas con una membresía activa registrada.
              </div>
            <?php endif; ?>

            <!-- Línea divisoria -->
            <hr class="my-4">

            <!-- Mensaje motivador -->
            <div class="row align-items-center">
              <div class="col-md-8">
                <p class="mb-2 font-weight-bold">
                  ¿Qué puedes hacer ahora?
                </p>
                <p class="mb-0 text-muted">
                  Para continuar usando el sistema y acceder al gimnasio,
                  por favor <strong>renueva tu plan en recepción</strong>
                  o comunícate con el administrador.
                </p>
              </div>
              <div class="col-md-4 text-md-right mt-3 mt-md-0">
                <a href="../cerrar_session.php"
                   class="btn btn-outline-secondary btn-sm px-4">
                  <i class="fas fa-sign-out-alt mr-1"></i>
                  Cerrar sesión
                </a>
              </div>
            </div>

          </div>
        </div>
      </div>

      <!-- Nota inferior -->
      <div class="text-center small text-muted">
        <i class="fas fa-shield-alt mr-1"></i>
        Este acceso está protegido por el sistema de control de membresías.
      </div>

    </div>
  </div>

</div>


<?php include __DIR__ . '/../../theme/sb2/footer.php'; ?>
