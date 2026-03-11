
/* ===== CONSULTA ===== */
$result = null;
$errorMsg = '';

$qry = "SELECT id, date, message FROM announcements ORDER BY date DESC, id DESC";
$result = mysqli_query($db, $qry);

if ($result === false) {
  $errorMsg = 'Ocurrió un error al cargar las publicaciones.';
}
?>

<style>
  .page-header {
    background: linear-gradient(135deg, #4e73df, #1cc88a);
    border-radius: 1rem;
    padding: 1.2rem;
    color: #fff;
    box-shadow: 0 10px 25px rgba(0, 0, 0, .15);
    margin-bottom: 1.5rem;
  }

  .members-card {
    border: 0;
    border-radius: 1rem;
    overflow: hidden;
  }

  .members-table-wrap {
    max-height: 70vh;
    overflow-y: auto;
  }

  .message-preview {
    max-width: 350px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }

  .date-badge {
    background: #e3f2fd;
    color: #1976d2;
    padding: 4px 10px;
    border-radius: 999px;
    font-size: .75rem;
    font-weight: 600;
  }

  .members-actions .btn {
    border-radius: 999px;
    font-size: .8rem;
  }
</style>

<div class="container-fluid">
  <div class="row justify-content-center">
    <div class="col-xl-8 col-lg-9 col-md-11">

      <div class="page-header text-center">
        <h4><i class="fas fa-bullhorn mr-2"></i>Gestión de Publicaciones</h4>
        <small>Administra las publicaciones del sistema</small>
      </div>

      <div class="card shadow members-card">
        <div class="card-header d-flex justify-content-between align-items-center">
          <span><i class="fas fa-table mr-1"></i>Tabla de publicaciones</span>
          <span class="badge badge-primary">
            <?= ($result instanceof mysqli_result) ? mysqli_num_rows($result) : 0 ?>
          </span>
        </div>

        <div class="card-body p-0">
          <div class="table-responsive members-table-wrap">

            <table class="table table-hover mb-0">
              <thead class="thead-light">
                <tr>
                  <th class="text-center">#</th>
                  <th class="text-center">Fecha</th>
                  <th>Mensaje</th>
                  <th class="text-center">Acción</th>
                </tr>
              </thead>
              <tbody>

                <?php if ($result instanceof mysqli_result && mysqli_num_rows($result) > 0): ?>
                  <?php $cnt = 1;
                  while ($row = mysqli_fetch_assoc($result)): ?>

                    <?php
                    $fullMessage = htmlspecialchars($row['message']);
                    $preview = mb_strimwidth($row['message'], 0, 80, '...');
                    ?>

                    <tr>
                      <td class="text-center text-muted"><?= $cnt++; ?></td>

                      <td class="text-center">
                        <span class="date-badge">
                          <?= htmlspecialchars($row['date']); ?>
                        </span>
                      </td>

                      <td>
                        <div class="d-flex justify-content-between align-items-center">
                          <div class="message-preview text-muted">
                            <?= htmlspecialchars($preview); ?>
                          </div>

                          <button class="btn btn-sm btn-outline-primary ml-2" data-toggle="modal" data-target="#viewModal"
                            data-id="<?= (int) $row['id']; ?>">
                            Ver
                          </button>
                        </div>

                        <div id="msg-<?= (int) $row['id']; ?>" class="d-none">
                          <?= nl2br($fullMessage); ?>
                        </div>
                      </td>

                      <td class="text-center members-actions">
                        <a href="acciones/eliminar_publicacion.php?id=<?= (int) $row['id']; ?>"
                          class="btn btn-outline-danger btn-delete">
                          <i class="fas fa-trash-alt"></i>
                        </a>
                      </td>
                    </tr>

                  <?php endwhile; ?>

                <?php else: ?>
                  <tr>
                    <td colspan="4" class="text-center py-5 text-muted">
                      <i class="fas fa-inbox fa-2x mb-2"></i><br>
                      No hay publicaciones registradas
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
</div>

<!-- MODAL -->
<div class="modal fade" id="viewModal">
  <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg">
    <div class="modal-content">
      <div class="modal-header bg-light">
        <h5 class="modal-title text-primary font-weight-bold">Detalle del mensaje</h5>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body" id="modalBody">
        <div class="text-center py-4">
          <div class="spinner-border text-primary"></div>
        </div>
      </div>
      <div class="modal-footer bg-light">
        <button class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>

<script>
  document.addEventListener('DOMContentLoaded', function () {

    document.querySelectorAll('.btn-delete').forEach(btn => {
      btn.addEventListener('click', function (e) {
        if (!confirm('¿Está seguro de eliminar esta publicación?')) {
          e.preventDefault();
        }
      });
    });

    const modalBody = document.getElementById('modalBody');

    document.querySelectorAll('[data-target="#viewModal"]').forEach(btn => {
      btn.addEventListener('click', function () {
        const id = this.getAttribute('data-id');
        const source = document.getElementById('msg-' + id);

        modalBody.innerHTML = source ? source.innerHTML :
          '<p class="text-center text-muted">Contenido no disponible</p>';
      });
    });

  });
</script>
