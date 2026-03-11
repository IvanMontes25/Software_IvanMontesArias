<?php
$page = 'auditoria';
$pageTitle = 'Auditoría';
include __DIR__ . '/theme/sb2/header.php';
include __DIR__ . '/theme/sb2/sidebar.php';
include __DIR__ . '/theme/sb2/topbar.php';

require_once __DIR__ . '/../core/bootstrap.php';
require_once __DIR__ . '/../core/auth.php';
require_once __DIR__ . '/../core/roles.php';
require_modulo('administracion');

if (!$db instanceof mysqli) {
    die('No hay conexión a la base de datos');
}

/* ====== FILTROS ====== */
$f_modulo = trim($_GET['modulo'] ?? '');
$f_usuario = trim($_GET['usuario'] ?? '');
$f_desde = trim($_GET['desde'] ?? '');
$f_hasta = trim($_GET['hasta'] ?? '');

/* ====== PAGINACIÓN ====== */
$per_page = 25;
$page_num = max(1, (int) ($_GET['p'] ?? 1));
$offset = ($page_num - 1) * $per_page;

/* ====== CONSTRUIR QUERY ====== */
$where = "WHERE 1=1";
$params = [];
$types = "";

if ($f_modulo !== '') {
    $where .= " AND modulo = ?";
    $params[] = $f_modulo;
    $types .= "s";
}
if ($f_usuario !== '') {
    $where .= " AND (username LIKE ? OR fullname LIKE ?)";
    $like = "%$f_usuario%";
    $params[] = $like;
    $params[] = $like;
    $types .= "ss";
}
if ($f_desde !== '') {
    $where .= " AND created_at >= ?";
    $params[] = $f_desde . ' 00:00:00';
    $types .= "s";
}
if ($f_hasta !== '') {
    $where .= " AND created_at <= ?";
    $params[] = $f_hasta . ' 23:59:59';
    $types .= "s";
}

/* Total */
$sqlCount = "SELECT COUNT(*) FROM audit_log $where";
$stC = $db->prepare($sqlCount);
if ($types !== '') {
    $stC->bind_param($types, ...$params);
}
$stC->execute();
$stC->bind_result($total);
$stC->fetch();
$stC->close();
$total_pages = max(1, ceil($total / $per_page));

/* Datos */
$sqlData = "SELECT id, user_id, username, fullname, rol, accion, descripcion, modulo, ip, created_at
            FROM audit_log $where
            ORDER BY created_at DESC
            LIMIT $per_page OFFSET $offset";
$stD = $db->prepare($sqlData);
if ($types !== '') {
    $stD->bind_param($types, ...$params);
}
$stD->execute();
$rows = $stD->get_result();

/* Módulos disponibles para el filtro */
$modulos_result = $db->query("SELECT DISTINCT modulo FROM audit_log ORDER BY modulo");
$modulos_list = [];
while ($m = $modulos_result->fetch_assoc()) {
    $modulos_list[] = $m['modulo'];
}

/* Mapeo de íconos y colores por acción */
function accion_badge(string $accion): string
{
    $map = [
        'crear_cliente' => ['bg' => '#1cc88a', 'icon' => 'fa-user-plus'],
        'editar_cliente' => ['bg' => '#f6c23e', 'icon' => 'fa-user-edit'],
        'eliminar_cliente' => ['bg' => '#e74a3b', 'icon' => 'fa-user-times'],
        'registrar_pago' => ['bg' => '#4e73df', 'icon' => 'fa-dollar-sign'],
        'registrar_asistencia' => ['bg' => '#36b9cc', 'icon' => 'fa-calendar-check'],
        'borrar_asistencia' => ['bg' => '#e74a3b', 'icon' => 'fa-calendar-times'],
        'crear_staff' => ['bg' => '#1cc88a', 'icon' => 'fa-user-tie'],
        'editar_staff' => ['bg' => '#f6c23e', 'icon' => 'fa-user-cog'],
        'eliminar_staff' => ['bg' => '#e74a3b', 'icon' => 'fa-user-slash'],
        'crear_equipo' => ['bg' => '#1cc88a', 'icon' => 'fa-dumbbell'],
        'crear_plan' => ['bg' => '#1cc88a', 'icon' => 'fa-clipboard-list'],
        'editar_plan' => ['bg' => '#f6c23e', 'icon' => 'fa-clipboard-list'],
        'eliminar_plan' => ['bg' => '#e74a3b', 'icon' => 'fa-clipboard-list'],
    ];
    $info = $map[$accion] ?? ['bg' => '#858796', 'icon' => 'fa-cog'];
    $label = ucwords(str_replace('_', ' ', $accion));
    return '<span class="badge" style="background:' . $info['bg'] . ';color:#fff;padding:5px 10px;border-radius:8px;">
                <i class="fas ' . $info['icon'] . ' mr-1"></i>' . $label . '
            </span>';
}

function modulo_badge(string $modulo): string
{
    $colors = [
        'clientes' => '#4e73df',
        'pagos' => '#1cc88a',
        'asistencias' => '#36b9cc',
        'administracion' => '#e74a3b',
        'equipos' => '#f6c23e',
    ];
    $c = $colors[$modulo] ?? '#858796';
    return '<span class="badge" style="background:' . $c . ';color:#fff;padding:4px 8px;border-radius:6px;">' . ucfirst($modulo) . '</span>';
}
?>

<div class="sb2-content">
    <div id="content">
        <div class="container-fluid py-3">

            <!-- HEADER -->
            <div class="d-sm-flex align-items-center justify-content-between mb-4">
                <h1 class="h3 mb-0 text-gray-800">
                    <i class="fas fa-shield-alt mr-2"></i>Registro de Auditoría
                </h1>
                <span class="badge badge-primary px-3 py-2" style="font-size:.9rem;">
                    <?= number_format($total) ?> registros
                </span>
            </div>

            <!-- FILTROS -->
            <div class="card shadow-sm mb-4" style="border-radius:12px;">
                <div class="card-body py-3">
                    <form method="GET" class="row g-2 align-items-end">
                        <div class="col-md-3 mb-2">
                            <label class="small font-weight-bold">Módulo</label>
                            <select name="modulo" class="form-control form-control-sm" style="border-radius:8px;">
                                <option value="">— Todos —</option>
                                <?php foreach ($modulos_list as $mod): ?>
                                    <option value="<?= htmlspecialchars($mod) ?>" <?= $f_modulo === $mod ? 'selected' : '' ?>>
                                        <?= ucfirst(htmlspecialchars($mod)) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-3 mb-2">
                            <label class="small font-weight-bold">Usuario</label>
                            <input type="text" name="usuario" class="form-control form-control-sm"
                                style="border-radius:8px;" placeholder="Nombre o username"
                                value="<?= htmlspecialchars($f_usuario) ?>">
                        </div>
                        <div class="col-md-2 mb-2">
                            <label class="small font-weight-bold">Desde</label>
                            <input type="date" name="desde" class="form-control form-control-sm"
                                style="border-radius:8px;" value="<?= htmlspecialchars($f_desde) ?>">
                        </div>
                        <div class="col-md-2 mb-2">
                            <label class="small font-weight-bold">Hasta</label>
                            <input type="date" name="hasta" class="form-control form-control-sm"
                                style="border-radius:8px;" value="<?= htmlspecialchars($f_hasta) ?>">
                        </div>
                        <div class="col-md-2 mb-2">
                            <button class="btn btn-primary btn-sm btn-block" style="border-radius:8px;">
                                <i class="fas fa-search mr-1"></i>Filtrar
                            </button>
                            <?php if ($f_modulo || $f_usuario || $f_desde || $f_hasta): ?>
                                <a href="auditoria.php" class="btn btn-outline-secondary btn-sm btn-block mt-1"
                                    style="border-radius:8px;">
                                    Limpiar
                                </a>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>
            </div>

            <!-- TABLA -->
            <div class="card shadow-sm" style="border-radius:12px;">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0" style="font-size:.88rem;">
                            <thead style="background:#f8f9fc;">
                                <tr>
                                    <th style="width:155px;">Fecha / Hora</th>
                                    <th>Quién</th>
                                    <th>Rol</th>
                                    <th>Acción</th>
                                    <th>Descripción</th>
                                    <th>Módulo</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($rows->num_rows === 0): ?>
                                    <tr>
                                        <td colspan="6" class="text-center py-5 text-muted">
                                            <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                                            No hay registros de auditoría aún.
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php while ($r = $rows->fetch_assoc()): ?>
                                        <tr>
                                            <td class="text-muted" style="white-space:nowrap;">
                                                <i class="far fa-clock mr-1"></i>
                                                <?= date('d/m/Y H:i', strtotime($r['created_at'])) ?>
                                            </td>
                                            <td>
                                                <strong><?= htmlspecialchars($r['fullname']) ?></strong>
                                                <br><small class="text-muted">@<?= htmlspecialchars($r['username']) ?></small>
                                            </td>
                                            <td>
                                                <span class="badge badge-light border px-2 py-1" style="border-radius:6px;">
                                                    <?= ucfirst(htmlspecialchars($r['rol'])) ?>
                                                </span>
                                            </td>
                                            <td><?= accion_badge($r['accion']) ?></td>
                                            <td><?= htmlspecialchars($r['descripcion']) ?></td>
                                            <td><?= modulo_badge($r['modulo']) ?></td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- PAGINACIÓN -->
                <?php if ($total_pages > 1): ?>
                    <div class="card-footer d-flex justify-content-between align-items-center py-2">
                        <small class="text-muted">Página <?= $page_num ?> de <?= $total_pages ?></small>
                        <nav>
                            <ul class="pagination pagination-sm mb-0">
                                <?php
                                $qs = http_build_query(array_filter([
                                    'modulo' => $f_modulo,
                                    'usuario' => $f_usuario,
                                    'desde' => $f_desde,
                                    'hasta' => $f_hasta
                                ]));
                                $sep = $qs ? "&" : "";
                                ?>
                                <?php if ($page_num > 1): ?>
                                    <li class="page-item">
                                        <a class="page-link" href="?p=<?= $page_num - 1 ?><?= $sep . $qs ?>">&laquo;</a>
                                    </li>
                                <?php endif; ?>

                                <?php
                                $start = max(1, $page_num - 2);
                                $end = min($total_pages, $page_num + 2);
                                for ($i = $start; $i <= $end; $i++):
                                    ?>
                                    <li class="page-item <?= $i === $page_num ? 'active' : '' ?>">
                                        <a class="page-link" href="?p=<?= $i ?><?= $sep . $qs ?>"><?= $i ?></a>
                                    </li>
                                <?php endfor; ?>

                                <?php if ($page_num < $total_pages): ?>
                                    <li class="page-item">
                                        <a class="page-link" href="?p=<?= $page_num + 1 ?><?= $sep . $qs ?>">&raquo;</a>
                                    </li>
                                <?php endif; ?>
                            </ul>
                        </nav>
                    </div>
                <?php endif; ?>
            </div>

        </div>
    </div>
</div>

<?php include __DIR__ . '/theme/sb2/footer.php'; ?>