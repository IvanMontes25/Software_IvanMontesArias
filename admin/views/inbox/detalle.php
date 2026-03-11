
$page = 'inbox';
$id = (int) ($_GET['id'] ?? 0);

if ($id <= 0) {
  header('Location: inbox.php');
  exit;
}

$db->query("UPDATE admin_inbox SET is_read = 1 WHERE id = $id");

$stmt = $db->prepare("SELECT * FROM admin_inbox WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$data = $stmt->get_result()->fetch_assoc();

if (!$data) {
  header('Location: inbox.php');
  exit;
}

/* Helpers */
function e($s)
{
  return htmlspecialchars($s ?? '', ENT_QUOTES, 'UTF-8');
}

/* Icono por tipo */
$icon = match ($data['tipo']) {
  'pago' => 'fa-money-bill-wave text-success',
  'vencimiento' => 'fa-clock text-danger',
  default => 'fa-bell text-primary'
};

/* Prioridad */
$prioClass = match ($data['prioridad']) {
  'alta' => 'badge-danger',
  'media' => 'badge-warning',
  default => 'badge-secondary'
};
?>

<?php include 'theme/sb2/header.php'; ?>
<?php include 'theme/sb2/sidebar.php'; ?>
<?php include 'theme/sb2/topbar.php'; ?>

<div class="sb2-content d-flex flex-column min-vh-100">
  <div class="container-fluid flex-grow-1 py-3">

    <!-- ===== HEADER ===== -->
    <div class="page-header mb-4">
      <div class="page-header-inner">
        <h1 class="page-title">
          Mensaje <i class="fas fa-envelope-open-text ml-2"></i>
        </h1>
        <p class="page-subtitle">
          Detalle de notificación del sistema
        </p>
      </div>
    </div>

    <!-- ===== BOTÓN VOLVER ===== -->
    <a href="inbox.php" class="btn btn-outline-secondary btn-sm mb-3">
      <i class="fas fa-arrow-left"></i> Volver a Bandeja
    </a>

    <!-- ===== CARD PRINCIPAL ===== -->
    <div class="card inbox-card shadow-lg mb-4">

      <div class="card-header inbox-header">
        <div class="d-flex align-items-center justify-content-between flex-wrap">
          <div>
            <h5 class="m-0">
              <i class="fas <?= $icon ?> mr-2"></i>
              <?= e($data['titulo']) ?>
            </h5>
            <small class="opacity-75">
              <?= ucfirst($data['origen']) ?> ·
              <?= date('d/m/Y H:i', strtotime($data['created_at'])) ?>
            </small>
          </div>

          <div class="mt-2 mt-md-0">
            <span class="badge <?= $prioClass ?> badge-pillish">
              Prioridad <?= ucfirst($data['prioridad']) ?>
            </span>
          </div>
        </div>
      </div>

      <div class="card-body">

        <!-- MENSAJE -->
        <div class="message-box">
          <?= nl2br(e($data['mensaje'])) ?>
        </div>

      </div>
    </div>

  </div>
</div>

<!-- ===== ESTILOS ===== -->
<style>
  /* HEADER */
  .page-header {
    background: linear-gradient(135deg, #4e73df, #1cc88a);
    border-radius: 1rem;
    padding: 1.4rem 1rem;
    box-shadow: 0 10px 25px rgba(0, 0, 0, .18);
  }

  .page-header-inner {
    max-width: 1200px;
    margin: 0 auto;
    text-align: center;
    color: #fff;
  }

  .page-title {
    font-size: 1.4rem;
    font-weight: 700;
    margin-bottom: 4px;
  }

  .page-subtitle {
    font-size: .85rem;
    opacity: .9;
  }

  /* CARD */
  .inbox-card {
    border: none;
    border-radius: 1rem;
    background: #fff;
  }

  .inbox-header {
    background: linear-gradient(90deg, #4e73df, #1cc88a);
    color: #fff;
    border-radius: 1rem 1rem 0 0;
  }

  /* MENSAJE */
  .message-box {
    background: #f8f9fc;
    border-left: 4px solid #4e73df;
    padding: 1.2rem;
    border-radius: .75rem;
    font-size: .95rem;
    line-height: 1.6;
    color: #343a40;
    white-space: pre-line;
  }

  /* BADGE */
  .badge-pillish {
    border-radius: 999px;
    font-weight: 500;
  }
</style>

<?php include 'theme/sb2/footer.php'; ?>