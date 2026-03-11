?>

<script>
  const CSRF_TOKEN = "<?= $_SESSION['csrf_token'] ?>";
</script>

<style>
  /* === MISMO LOOK & FEEL (como tus tablas pro) === */
  .equipos-card {
    border: 0;
    border-radius: 1rem;
    overflow: hidden;
  }

  .equipos-card .card-header {
    background: linear-gradient(90deg, #4e73df, #1cc88a);
    color: #fff;
  }

  .equipos-table-wrap {
    max-height: 70vh;
    overflow-y: auto;
  }

  .equipos-table thead th {
    font-size: 1rem;
    font-weight: 600;
    vertical-align: middle;
  }

  .equipos-table tbody td {
    font-size: 1.05rem;
    padding: .75rem .5rem;
    vertical-align: middle;
  }

  .sticky-header {
    position: sticky;
    top: 0;
    z-index: 2;
  }

  .equipos-actions .btn {
    border-radius: 999px;
  }

  .badge-soft {
    border-radius: 999px;
    font-weight: 500;
  }

  #footer {
    color: #fff;
  }
</style>

<div class="sb2-content d-flex flex-column min-vh-100">
  <div class="container-fluid flex-grow-1">

    <!-- ===== TÍTULO DE PÁGINA (ESTÁNDAR SISTEMA) ===== -->
    <div class="page-header mb-4">
      <div class="page-header-inner">
        <h1 class="page-title">
          Equipos
        </h1>
        <p class="page-subtitle">
          Inventario y control de equipos del gimnasio
        </p>
      </div>
    </div>

    <!-- Mensajes -->
    <?php if (isset($_GET['success']) && $_GET['success'] == 'registered'): ?>
      <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle mr-2"></i> ¡Equipo registrado exitosamente!
        <button type="button" class="close" data-dismiss="alert" aria-label="Cerrar">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

    <?php elseif (isset($_GET['success']) && $_GET['success'] == 'deleted'): ?>
      <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-trash-alt mr-2"></i> ¡Equipo eliminado correctamente!
        <button type="button" class="close" data-dismiss="alert" aria-label="Cerrar">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

    <?php elseif (isset($_GET['success']) && $_GET['success'] == 'updated'): ?>
      <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle mr-2"></i> ¡Equipo actualizado correctamente!
        <button type="button" class="close" data-dismiss="alert" aria-label="Cerrar">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

    <?php elseif (isset($_GET['error'])): ?>
      <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-triangle mr-2"></i> Ocurrió un error en la operación. Inténtalo nuevamente.
        <button type="button" class="close" data-dismiss="alert" aria-label="Cerrar">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
    <?php endif; ?>

    <!-- Card estilo pro -->
    <div class="card shadow mb-4 equipos-card">
      <div class="card-header py-3 d-flex align-items-center justify-content-between flex-wrap gap-2">

        <!-- IZQUIERDA: TÍTULO -->
        <h6 class="m-0 d-flex align-items-center gap-2">
          <i class="fas fa-table"></i>
          Tabla de equipos
        </h6>

        <!-- DERECHA: REGISTROS + BOTÓN -->
        <div class="d-flex align-items-center gap-3">

          <div class="small d-flex align-items-center">
            <i class="fas fa-clipboard-list mr-1"></i>
            <?= $result->num_rows ?> registros
          </div>

          <a href="registro_equipo.php" class="btn btn-sm btn-light font-weight-bold">
            <i class="fas fa-plus-circle mr-1"></i> Nuevo Equipo
          </a>

        </div>
      </div>


      <div class="card-body">
        <div class="table-responsive equipos-table-wrap">
          <table class="table table-hover table-sm mb-0 equipos-table">
            <thead class="thead-light sticky-header text-center">
              <tr>
                <th><i class="fas fa-hashtag"></i></th>
                <th><i class="fas fa-tag mr-1"></i>Nombre</th>
                <th><i class="fas fa-align-left mr-1"></i>Descripción</th>
                <th><i class="fas fa-boxes mr-1"></i>Cantidad</th>
                <th><i class="fas fa-truck mr-1"></i>Proveedor</th>
                <th><i class="fas fa-phone-alt mr-1"></i>Contacto</th>
                <th><i class="fas fa-calendar-alt mr-1"></i>Fecha compra</th>
                <th><i class="fas fa-coins mr-1"></i>Costo (Bs.)</th>
                <th><i class="fas fa-cogs mr-1"></i>Acciones</th>
              </tr>
            </thead>

            <tbody>
              <?php if ($result && mysqli_num_rows($result) > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                  <tr>
                    <td class="text-center text-muted"><?php echo (int) $row['id']; ?></td>

                    <td>
                      <div class="font-weight-600"><?php echo htmlspecialchars($row['name']); ?></div>
                    </td>

                    <td>
                      <div class="text-muted"><?php echo htmlspecialchars($row['description']); ?></div>
                    </td>

                    <td class="text-center">
                      <span class="badge badge-primary badge-soft">
                        <?php echo (int) $row['quantity']; ?>
                      </span>
                    </td>

                    <td><?php echo htmlspecialchars($row['vendor']); ?></td>

                    <td>
                      <?php
                      $contact = trim((string) $row['contact']);
                      if ($contact !== ''):
                        ?>
                        <a class="text-decoration-none" href="tel:<?php echo htmlspecialchars($contact); ?>">
                          <i class="fas fa-phone-alt mr-1 text-muted"></i><?php echo htmlspecialchars($contact); ?>
                        </a>
                      <?php else: ?>
                        <span class="text-muted">—</span>
                      <?php endif; ?>
                    </td>

                    <td class="text-center"><?php echo htmlspecialchars($row['date']); ?></td>

                    <td class="text-right">
                      <span class="badge badge-success badge-soft">
                        <?php echo number_format((float) $row['amount'], 2); ?>
                      </span>
                    </td>

                    <td class="text-center equipos-actions">
                      <div class="btn-group btn-group-sm" role="group">
                        <a href="edit_equipo_form.php?id=<?php echo (int) $row['id']; ?>" class="btn btn-outline-primary"
                          title="Actualizar">
                          <i class="fas fa-edit"></i>
                        </a>



                        <button type="button" class="btn btn-outline-danger btn-delete-equipo"
                          data-id="<?= (int) $row['id']; ?>" title="Eliminar">
                          <i class="fas fa-trash-alt"></i>
                        </button>

                      </div>
                    </td>
                  </tr>
                <?php endwhile; ?>
              <?php else: ?>
                <tr>
                  <td colspan="9" class="text-center text-muted py-4">
                    No hay equipos registrados
                  </td>
                </tr>
              <?php endif; ?>
            </tbody>

          </table>
        </div>
      </div>
    </div>

  </div>
</div>


<style>
  /* ===============================
   PAGE HEADER (ESTÁNDAR SISTEMA)
=============================== */
  .page-header {
    background: linear-gradient(135deg, #4e73df, #1cc88a);
    border-radius: 1rem;
    padding: 1.3rem 1rem;
    box-shadow: 0 8px 20px rgba(0, 0, 0, .16);
  }

  .page-header-inner {
    max-width: 900px;
    margin: 0 auto;
    text-align: center;
    color: #fff;
  }

  .page-title {
    font-size: 1.35rem;
    font-weight: 700;
    margin-bottom: 4px;
  }

  .page-subtitle {
    font-size: .85rem;
    opacity: .9;
    margin-bottom: 0;
  }

  .card-header .gap-2 {
    gap: .5rem;
  }

  .card-header .gap-3 {
    gap: .75rem;
  }

  .card-header h6 {
    font-size: 1rem;
    font-weight: 600;
  }

  .card-header .btn {
    border-radius: 999px;
    padding: .35rem .8rem;
  }
</style>


<script>
  const CSRF_TOKEN = "<?= $_SESSION['csrf_token'] ?>";
</script>

<?php if (!empty($_SESSION['equipo_success'])): ?>
  <script>
    Swal.fire({
      icon: 'success',
      title: 'Operación exitosa',
      text: '<?= $_SESSION['equipo_success']; ?>',
      confirmButtonColor: '#1cc88a'
    });
  </script>
  <?php unset($_SESSION['equipo_success']); ?>
<?php endif; ?>

<?php if (!empty($_SESSION['equipo_error'])): ?>
  <script>
    Swal.fire({
      icon: 'error',
      title: 'Error',
      text: '<?= $_SESSION['equipo_error']; ?>',
      confirmButtonColor: '#e74a3b'
    });
  </script>
  <?php unset($_SESSION['equipo_error']); ?>
<?php endif; ?>



<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>

  function confirmDelete(opts) {

    Swal.fire({
      title: opts.title,
      text: opts.text,
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#e74a3b',
      cancelButtonColor: '#858796',
      confirmButtonText: 'Eliminar',
      cancelButtonText: 'Cancelar'
    }).then(result => {

      if (!result.isConfirmed) return;

      fetch(opts.url, {
        method: 'POST',
        credentials: 'same-origin',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded'
        },
        body:
          'op=delete'
          + '&delete_id=' + encodeURIComponent(opts.id)
          + '&csrf_token=' + encodeURIComponent(CSRF_TOKEN)
      })
        .then(response => response.json())
        .then(data => {

          if (data.ok) {

            Swal.fire({
              icon: 'success',
              title: opts.successTitle,
              text: opts.successText,
              timer: 1500,
              showConfirmButton: false
            }).then(() => {
              if (opts.onSuccess) opts.onSuccess();
            });

          } else {

            Swal.fire({
              icon: 'error',
              title: 'Error',
              text: data.msg || 'No se pudo eliminar'
            });

          }

        })
        .catch(() => {
          Swal.fire({
            icon: 'error',
            title: 'Error de red',
            text: 'No se pudo conectar con el servidor'
          });
        });

    });
  }

  document.addEventListener('click', function (e) {

    const btn = e.target.closest('.btn-delete-equipo');
    if (!btn) return;

    const id = btn.dataset.id;
    if (!id) return;

    confirmDelete({
      id: id,
      url: 'equipo_actions.php',
      title: '¿Eliminar equipo?',
      text: 'Esta acción eliminará el equipo del inventario.',
      successTitle: 'Eliminado exitosamente',
      successText: 'El equipo fue eliminado correctamente.',
      onSuccess: () => location.reload()
    });

  });

</script>