<?php
/* admin/mis_clases.php — Dashboard del entrenador: sus clases */
require_once __DIR__ . '/../core/bootstrap.php';
require_once __DIR__ . '/../core/auth.php';
require_once __DIR__ . '/../core/roles.php';
require_modulo('clases');

$pageTitle = 'Mis Clases';
include __DIR__ . '/theme/sb2/header.php';
include __DIR__ . '/theme/sb2/sidebar.php';
include __DIR__ . '/theme/sb2/topbar.php';

$user_id = (int)($_SESSION['user_id'] ?? 0);
$rol     = $_SESSION['rol'] ?? '';

function e($s){ return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }

// Flash
$flash_ok  = $_SESSION['flash_ok'] ?? null; unset($_SESSION['flash_ok']);
$flash_err = $_SESSION['flash_error'] ?? null; unset($_SESSION['flash_error']);

// Filtro
$filtro_fecha = $_GET['fecha'] ?? 'futuras';

// Query
$where = "WHERE s.estado != 'cancelada'";
$params = [];
$types  = '';

if ($rol !== 'admin') {
    $where .= " AND s.entrenador_id = ?";
    $params[] = $user_id;
    $types .= 'i';
}

if ($filtro_fecha === 'hoy') {
    $where .= " AND s.fecha = CURDATE()";
} elseif ($filtro_fecha === 'semana') {
    $where .= " AND s.fecha BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 7 DAY)";
} elseif ($filtro_fecha === 'futuras') {
    $where .= " AND s.fecha >= CURDATE()";
} elseif ($filtro_fecha === 'pasadas') {
    $where .= " AND s.fecha < CURDATE()";
}

$sql = "
SELECT s.*, t.nombre AS tipo_nombre, t.color AS tipo_color,
       st.fullname AS entrenador_nombre,
       (SELECT COUNT(*) FROM clases_reservas r
        WHERE r.sesion_id = s.id AND r.estado='confirmada') AS inscritos,
       (SELECT COUNT(*) FROM clases_reservas r
        WHERE r.sesion_id = s.id AND r.estado='en_espera') AS en_espera
FROM clases_sesiones s
JOIN clase_tipos t ON t.id = s.tipo_clase_id
JOIN staffs st ON st.user_id = s.entrenador_id
$where
ORDER BY s.fecha ASC, s.hora_inicio ASC
";

if ($types) {
    $st = $db->prepare($sql);
    $st->bind_param($types, ...$params);
    $st->execute();
    $result = $st->get_result();
} else {
    $result = $db->query($sql);
}

$clases = [];
while ($r = $result->fetch_assoc()) $clases[] = $r;

// Stats de hoy
$stats_hoy = ['total'=>0,'inscritos'=>0,'cupos'=>0];
foreach ($clases as $c) {
    if ($c['fecha'] === date('Y-m-d')) {
        $stats_hoy['total']++;
        $stats_hoy['inscritos'] += (int)$c['inscritos'];
        $stats_hoy['cupos'] += (int)$c['cupo_maximo'];
    }
}
?>

<div class="sb2-content d-flex flex-column min-vh-100">
<div class="container-fluid flex-grow-1">

<!-- HEADER -->
<div class="page-header mb-4">
  <div class="page-header-inner">
    <h1 class="page-title">Mis Clases</h1>
    <p class="page-subtitle">Gestiona tus sesiones agendadas</p>
  </div>
</div>

<div class="card shadow mb-4 equipos-card">
<div class="card-header py-3 d-flex justify-content-between align-items-center">
  <h6 class="m-0"><i class="fas fa-calendar-alt mr-2"></i>Panel de Clases</h6>
  <a href="clase_agendar.php" class="btn btn-light btn-sm rounded-pill font-weight-bold">
    <i class="fas fa-plus-circle mr-1"></i> Agendar Clase
  </a>
</div>

<div class="card-body">

<?php if($flash_ok): ?>
<div class="alert alert-success alert-dismissible fade show">
<i class="fas fa-check-circle mr-2"></i><?= e($flash_ok) ?>
<button type="button" class="close" data-dismiss="alert">&times;</button>
</div>
<?php endif; ?>

<?php if($flash_err): ?>
<div class="alert alert-danger alert-dismissible fade show">
<i class="fas fa-exclamation-triangle mr-2"></i><?= e($flash_err) ?>
<button type="button" class="close" data-dismiss="alert">&times;</button>
</div>
<?php endif; ?>

<!-- Stats -->
<?php if($stats_hoy['total']>0): ?>
<div class="row mb-4">
<?php
$ocupacion = $stats_hoy['cupos']>0
? round($stats_hoy['inscritos']/$stats_hoy['cupos']*100)
: 0;
?>
<div class="col-md-4 mb-3">
<div class="card shadow-sm border-0 text-center p-3">
<div class="small text-primary font-weight-bold">Clases Hoy</div>
<div class="h3"><?= $stats_hoy['total'] ?></div>
</div>
</div>

<div class="col-md-4 mb-3">
<div class="card shadow-sm border-0 text-center p-3">
<div class="small text-success font-weight-bold">Inscritos Hoy</div>
<div class="h3"><?= $stats_hoy['inscritos'] ?></div>
</div>
</div>

<div class="col-md-4 mb-3">
<div class="card shadow-sm border-0 text-center p-3">
<div class="small text-info font-weight-bold">Ocupación Hoy</div>
<div class="h3"><?= $ocupacion ?>%</div>
</div>
</div>
</div>
<?php endif; ?>

<!-- Filtros -->
<div class="d-flex gap-2 mb-3 flex-wrap">
<?php
$filtros=['hoy'=>'Hoy','semana'=>'Esta Semana','futuras'=>'Futuras','pasadas'=>'Pasadas'];
foreach($filtros as $k=>$label):
?>
<a href="?fecha=<?= $k ?>"
class="btn btn-sm <?= $filtro_fecha===$k?'btn-primary':'btn-outline-secondary' ?> rounded-pill px-3">
<?= $label ?>
</a>
<?php endforeach; ?>
</div>

<!-- Tabla -->
<?php if(empty($clases)): ?>
<div class="text-center py-5 text-muted">
<i class="fas fa-calendar-times fa-3x mb-3"></i>
<h5>No hay clases para este filtro</h5>
</div>
<?php else: ?>
<div class="table-responsive">
<table class="table table-hover">
<thead class="thead-light text-center">
<tr>
<th>Tipo</th>
<th>Fecha</th>
<th>Horario</th>
<?php if($rol==='admin'): ?><th>Entrenador</th><?php endif; ?>
<th>Inscritos</th>
<th>Espera</th>
<th>Estado</th>
<th>Acciones</th>
</tr>
</thead>
<tbody>
<?php foreach($clases as $c):
$pct=$c['cupo_maximo']>0
? round(($c['cupo_maximo']-$c['cupo_disponible'])/$c['cupo_maximo']*100)
:0;
$esFutura=strtotime($c['fecha'].' '.$c['hora_inicio'])>time();
?>
<tr>
<td>
<span style="display:inline-block;width:10px;height:10px;border-radius:50%;background:<?=e($c['tipo_color'])?>;"></span>
<?=e($c['tipo_nombre'])?>
</td>

<td><?=date('d/m/Y',strtotime($c['fecha']))?></td>

<td><?=date('H:i',strtotime($c['hora_inicio']))?> - <?=date('H:i',strtotime($c['hora_fin']))?></td>

<?php if($rol==='admin'): ?>
<td><?=e($c['entrenador_nombre'])?></td>
<?php endif; ?>

<td class="text-center">
<strong><?=$c['inscritos']?></strong>/<?=$c['cupo_maximo']?>
<div style="height:4px;background:#e2e8f0;border-radius:4px;margin-top:4px;">
<div style="height:100%;width:<?=$pct?>%;background:<?=$pct>=85?'#ef4444':($pct>=60?'#f59e0b':'#10b981')?>;"></div>
</div>
</td>

<td class="text-center">
<?=$c['en_espera']>0?'<span class="badge badge-warning">'.$c['en_espera'].'</span>':'—'?>
</td>

<td class="text-center">
<span class="badge badge-success"><?=ucfirst($c['estado'])?></span>
</td>

<td class="text-center">
<a href="clase_inscritos.php?id=<?=$c['id']?>"
class="btn btn-sm btn-outline-primary rounded-pill">
<i class="fas fa-users"></i>
</a>
<?php if($esFutura && $c['estado']==='activa'): ?>
<button onclick="cancelarClase(<?=$c['id']?>)"
class="btn btn-sm btn-outline-danger rounded-pill ml-1">
<i class="fas fa-times"></i>
</button>
<?php endif; ?>
</td>
</tr>
<?php endforeach; ?>
</tbody>
</table>
</div>
<?php endif; ?>

</div>
</div>
</div>
</div>

<style>
.equipos-card{border:0;border-radius:1rem;overflow:hidden}
.equipos-card .card-header{
background:linear-gradient(90deg,#4e73df,#1cc88a);
color:#fff}
.page-header{
background:linear-gradient(135deg,#4e73df,#1cc88a);
border-radius:1rem;
padding:1.3rem 1rem;
box-shadow:0 8px 20px rgba(0,0,0,.16)}
.page-header-inner{text-align:center;color:#fff}
.page-title{font-size:1.35rem;font-weight:700;margin-bottom:4px}
.page-subtitle{font-size:.85rem;opacity:.9;margin-bottom:0}
</style>

<script>
function cancelarClase(id){
if(!confirm('¿Seguro que deseas cancelar esta clase?')) return;
const f=document.createElement('form');
f.method='POST';
f.action='clase_cancelar_action.php';
f.innerHTML='<input name="sesion_id" value="'+id+'">'+
'<input name="csrf_token" value="<?=csrf_token()?>">';
document.body.appendChild(f);
f.submit();
}
</script>

<?php include __DIR__ . '/theme/sb2/footer.php'; ?>