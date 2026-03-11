<?php
require_once __DIR__ . '/../core/bootstrap.php';
require_once __DIR__ . '/../core/auth.php';
require_once __DIR__ . '/../core/roles.php';
require_modulo('reportes');

$pageTitle = "Reporte de Automatización";
$page = 'auto-repo';
$isReport = true;

// Rango por defecto: últimos 30 días
$desde = $_GET['desde'] ?? date('Y-m-d', strtotime('-30 days'));
$hasta = $_GET['hasta'] ?? date('Y-m-d');

$sql = "
SELECT 
    event_type,
    user_id,
    payment_id,
    message,
    created_at
FROM automation_logs
WHERE event_type = 'pago_confirmado'
AND DATE(created_at) BETWEEN ? AND ?
ORDER BY created_at DESC
";

$stmt = $db->prepare($sql);
$stmt->bind_param("ss", $desde, $hasta);
$stmt->execute();
$result = $stmt->get_result();

$rows = [];
$totalPagos = 0;

while ($r = $result->fetch_assoc()) {
    $rows[] = $r;
    $totalPagos++;
}

include __DIR__ . '/theme/sb2/header.php';
include __DIR__ . '/theme/sb2/sidebar.php';
include __DIR__ . '/theme/sb2/topbar.php';
?>

<div class="container-fluid">

<h1 class="h3 mb-4 text-gray-800">Reporte Bajo Demanda - Pagos Automatizados</h1>

<form method="GET" class="form-inline mb-4">
    <label class="mr-2">Desde:</label>
    <input type="date" name="desde" value="<?= $desde ?>" class="form-control mr-3">

    <label class="mr-2">Hasta:</label>
    <input type="date" name="hasta" value="<?= $hasta ?>" class="form-control mr-3">

    <button class="btn btn-primary mr-2">Consultar</button>

    <a href="?desde=2000-01-01&hasta=<?= date('Y-m-d') ?>" class="btn btn-secondary">
        Ver Todo
    </a>
</form>

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <strong>Total pagos confirmados:</strong> <?= $totalPagos ?>
    </div>

    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>User ID</th>
                        <th>Payment ID</th>
                        <th>Mensaje</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($rows as $row): ?>
                        <tr>
                            <td><?= $row['created_at'] ?></td>
                            <td><?= $row['user_id'] ?></td>
                            <td><?= $row['payment_id'] ?></td>
                            <td><?= htmlspecialchars($row['message']) ?></td>
                        </tr>
                    <?php endforeach; ?>

                    <?php if(empty($rows)): ?>
                        <tr>
                            <td colspan="4" class="text-center">
                                No hay registros en este rango.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

</div>

<?php include __DIR__ . '/theme/sb2/footer.php'; ?>
