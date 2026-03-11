
<?php
// ── Estos ya fueron cargados por header.php, pero require_once los ignora si ya existen ──
require_once __DIR__ . '/../core/bootstrap.php';

if (!$db instanceof mysqli) die('Sin conexión');

// Helper escape (header.php ya lo define, este es fallback)
if (!function_exists('esc')) {
    function esc($s) { return htmlspecialchars((string)($s ?? ''), ENT_QUOTES, 'UTF-8'); }
}

// ── Obtener token CSRF (el que usa bootstrap.php) ──
$csrf = $_SESSION['csrf_token'] ?? ($_SESSION['_csrf'] ?? '');
if (empty($csrf)) {
    // Generar uno si no existe
    $csrf = csrf_token();
}

// ── Cargar config ──
$cfg = [];
$r = $db->query("SELECT clave, valor FROM portal_config");
if ($r) while ($row = $r->fetch_assoc()) $cfg[$row['clave']] = $row['valor'];
function c($k, $d='') { global $cfg; return $cfg[$k] ?? $d; }

// ── Cargar datos ──
$features     = $db->query("SELECT * FROM portal_features ORDER BY orden");
$planes       = $db->query("SELECT * FROM portal_planes ORDER BY orden");
$horarios     = $db->query("SELECT * FROM portal_horarios ORDER BY dia_orden, hora_inicio");
$instructores = $db->query("SELECT * FROM portal_instructores ORDER BY orden");

$bensByPlan = [];
$rb = $db->query("SELECT plan_id, texto FROM portal_plan_beneficios ORDER BY orden");
if ($rb) while ($b = $rb->fetch_assoc()) $bensByPlan[(int)$b['plan_id']][] = $b['texto'];

$horariosByDia = [];
if ($horarios) { $horarios->data_seek(0); while ($h = $horarios->fetch_assoc()) $horariosByDia[$h['dia']][] = $h; }
?>

<div class="sb2-content d-flex flex-column min-vh-100">
<div class="container-fluid flex-grow-1 py-3">

<!-- HEADER -->
<div class="page-header mb-4">
  <div class="page-header-inner">
    <h1 class="page-title"><i class="fas fa-globe mr-2"></i> Editor del Portal Web</h1>
    <p class="page-subtitle">Administra el contenido visible en el sitio público</p>
  </div>
</div>

<!-- ALERTA DEBUG (oculta por defecto, se muestra si hay error) -->
<div id="debugAlert" class="alert alert-danger d-none" role="alert" style="word-break:break-all;"></div>

<div class="text-center mb-4">
  <a href="../portal.php" target="_blank" class="btn btn-sm btn-success font-weight-bold" style="border-radius:50px;padding:8px 24px;">
    <i class="fas fa-external-link-alt mr-1"></i> Ver Portal Público
  </a>
  <button onclick="testConexion()" class="btn btn-sm btn-outline-info font-weight-bold ml-2" style="border-radius:50px;padding:8px 24px;">
    <i class="fas fa-plug mr-1"></i> Probar conexión
  </button>
</div>

<!-- TABS -->
<ul class="nav nav-tabs portal-tabs mb-0" id="portalTabs" role="tablist">
  <li class="nav-item"><a class="nav-link active" data-toggle="tab" href="#tab-hero"><i class="fas fa-home mr-1"></i><span class="d-none d-sm-inline">Portada</span> Principal</a></li>
  <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#tab-features"><i class="fas fa-star mr-1"></i> Features</a></li>
  <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#tab-planes"><i class="fas fa-tags mr-1"></i> Planes</a></li>
  <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#tab-horarios"><i class="fas fa-calendar-alt mr-1"></i> Horarios</a></li>
  <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#tab-instructores"><i class="fas fa-users mr-1"></i> Instructores</a></li>
  <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#tab-contacto"><i class="fas fa-map-marker-alt mr-1"></i> Contacto</a></li>
</ul>

<div class="card shadow border-0" style="border-radius:0 0 1rem 1rem;">
<div class="card-body p-4">
<div class="tab-content">

<!-- ═══════════ TAB HERO ═══════════ -->
<div class="tab-pane fade show active" id="tab-hero">
  <h5 class="font-weight-bold mb-3"><i class="fas fa-bullhorn text-primary mr-2"></i>Sección Portada Principal</h5>
  <form id="formHero">
    <div class="row">
      <div class="col-12 mb-3"><label class="font-weight-bold">Badge superior</label><input type="text" class="form-control" name="config[hero_badge]" value="<?= e(c('hero_badge')) ?>"></div>
      <div class="col-md-4 mb-3"><label class="font-weight-bold">Título línea 1</label><input type="text" class="form-control" name="config[hero_titulo_1]" value="<?= e(c('hero_titulo_1')) ?>"></div>
      <div class="col-md-4 mb-3"><label class="font-weight-bold">Título línea 2</label><input type="text" class="form-control" name="config[hero_titulo_2]" value="<?= e(c('hero_titulo_2')) ?>"></div>
      <div class="col-md-4 mb-3"><label class="font-weight-bold">Título línea 3</label><input type="text" class="form-control" name="config[hero_titulo_3]" value="<?= e(c('hero_titulo_3')) ?>"></div>
      <div class="col-12 mb-3"><label class="font-weight-bold">Descripción</label><textarea class="form-control" name="config[hero_descripcion]" rows="2"><?= e(c('hero_descripcion')) ?></textarea></div>
    </div>
    <h6 class="font-weight-bold mt-2 mb-2">Estadísticas</h6>
    <div class="row">
      <?php for ($i=1;$i<=4;$i++): ?>
      <div class="col-md-3 mb-3">
        <label>Número #<?=$i?></label><input type="text" class="form-control" name="config[hero_stat_<?=$i?>_num]" value="<?= e(c("hero_stat_{$i}_num")) ?>">
        <label class="mt-1">Etiqueta</label><input type="text" class="form-control" name="config[hero_stat_<?=$i?>_label]" value="<?= e(c("hero_stat_{$i}_label")) ?>">
      </div>
      <?php endfor; ?>
    </div>
    <button type="submit" class="btn btn-primary mt-3" style="border-radius:50px;"><i class="fas fa-save mr-1"></i> Guardar Hero</button>
  </form>
</div>

<!-- ═══════════ TAB FEATURES ═══════════ -->
<div class="tab-pane fade" id="tab-features">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="font-weight-bold mb-0"><i class="fas fa-th-large text-success mr-2"></i>Features</h5>
    <button class="btn btn-sm btn-primary" style="border-radius:50px;" onclick="openFeatureModal(0)"><i class="fas fa-plus mr-1"></i> Nueva</button>
  </div>
  <div class="table-responsive"><table class="table table-hover"><thead class="thead-light"><tr><th width="50">Ord</th><th>Icono</th><th>Título</th><th>Estado</th><th width="130">Acciones</th></tr></thead><tbody>
  <?php if($features){$features->data_seek(0);while($f=$features->fetch_assoc()):?>
  <tr><td><?=$f['orden']?></td><td><i class="<?=e($f['icono'])?>" style="font-size:1.2rem"></i></td><td class="font-weight-bold"><?=e($f['titulo'])?></td>
  <td><?=$f['activo']?'<span class="badge badge-success">Activo</span>':'<span class="badge badge-secondary">Inactivo</span>'?></td>
  <td><button class="btn btn-sm btn-outline-warning" onclick="openFeatureModal(<?=$f['id']?>)"><i class="fas fa-edit"></i></button> <button class="btn btn-sm btn-outline-danger" onclick="deleteItem('feature',<?=$f['id']?>)"><i class="fas fa-trash"></i></button></td></tr>
  <?php endwhile;}?></tbody></table></div>
</div>

<!-- ═══════════ TAB PLANES ═══════════ -->
<div class="tab-pane fade" id="tab-planes">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="font-weight-bold mb-0"><i class="fas fa-layer-group text-warning mr-2"></i>Planes</h5>
    <button class="btn btn-sm btn-primary" style="border-radius:50px;" onclick="openPlanModal(0)"><i class="fas fa-plus mr-1"></i> Nuevo</button>
  </div>
  <div class="table-responsive"><table class="table table-hover"><thead class="thead-light"><tr><th width="50">Ord</th><th>Nombre</th><th>Precio</th><th>Duración</th><th>Dest.</th><th>Estado</th><th width="130">Acciones</th></tr></thead><tbody>
  <?php if($planes){$planes->data_seek(0);while($p=$planes->fetch_assoc()):?>
  <tr><td><?=$p['orden']?></td><td class="font-weight-bold"><i class="<?=e($p['icono'])?> mr-1"></i><?=e($p['nombre'])?></td>
  <td><?=number_format($p['precio'],2)?> <?=e($p['moneda'])?></td><td><?=e($p['duracion'])?></td>
  <td><?=$p['destacado']?'<span class="badge badge-warning">★</span>':'—'?></td>
  <td><?=$p['activo']?'<span class="badge badge-success">Activo</span>':'<span class="badge badge-secondary">Inactivo</span>'?></td>
  <td><button class="btn btn-sm btn-outline-warning" onclick="openPlanModal(<?=$p['id']?>)"><i class="fas fa-edit"></i></button> <button class="btn btn-sm btn-outline-danger" onclick="deleteItem('plan',<?=$p['id']?>)"><i class="fas fa-trash"></i></button></td></tr>
  <?php endwhile;}?></tbody></table></div>
</div>

<!-- ═══════════ TAB HORARIOS ═══════════ -->
<div class="tab-pane fade" id="tab-horarios">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="font-weight-bold mb-0"><i class="fas fa-clock text-info mr-2"></i>Horarios</h5>
    <button class="btn btn-sm btn-primary" style="border-radius:50px;" onclick="openHorarioModal(0)"><i class="fas fa-plus mr-1"></i> Nuevo</button>
  </div>
  <div class="table-responsive"><table class="table table-hover"><thead class="thead-light"><tr><th>Día</th><th>Inicio</th><th>Fin</th><th>Clase</th><th>Estado</th><th width="130">Acciones</th></tr></thead><tbody>
  <?php foreach($horariosByDia as $dia=>$items):foreach($items as $h):?>
  <tr><td><span class="badge badge-dark"><?=e($dia)?></span></td><td><?=e($h['hora_inicio'])?></td><td><?=e($h['hora_fin'])?></td>
  <td class="font-weight-bold"><?=e($h['clase'])?></td>
  <td><?=$h['activo']?'<span class="badge badge-success">Activo</span>':'<span class="badge badge-secondary">Inactivo</span>'?></td>
  <td><button class="btn btn-sm btn-outline-warning" onclick="openHorarioModal(<?=$h['id']?>)"><i class="fas fa-edit"></i></button> <button class="btn btn-sm btn-outline-danger" onclick="deleteItem('horario',<?=$h['id']?>)"><i class="fas fa-trash"></i></button></td></tr>
  <?php endforeach;endforeach;?></tbody></table></div>
</div>

<!-- ═══════════ TAB INSTRUCTORES ═══════════ -->
<div class="tab-pane fade" id="tab-instructores">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="font-weight-bold mb-0"><i class="fas fa-user-shield text-danger mr-2"></i>Instructores</h5>
    <button class="btn btn-sm btn-primary" style="border-radius:50px;" onclick="openInstructorModal(0)"><i class="fas fa-plus mr-1"></i> Nuevo</button>
  </div>
  <div class="table-responsive"><table class="table table-hover"><thead class="thead-light"><tr><th width="50">Ord</th><th>Nombre</th><th>Cargo</th><th>Estado</th><th width="130">Acciones</th></tr></thead><tbody>
  <?php if($instructores){$instructores->data_seek(0);while($inst=$instructores->fetch_assoc()):?>
  <tr><td><?=$inst['orden']?></td><td class="font-weight-bold"><?=e($inst['nombre'])?></td><td><?=e($inst['cargo'])?></td>
  <td><?=$inst['activo']?'<span class="badge badge-success">Activo</span>':'<span class="badge badge-secondary">Inactivo</span>'?></td>
  <td><button class="btn btn-sm btn-outline-warning" onclick="openInstructorModal(<?=$inst['id']?>)"><i class="fas fa-edit"></i></button> <button class="btn btn-sm btn-outline-danger" onclick="deleteItem('instructor',<?=$inst['id']?>)"><i class="fas fa-trash"></i></button></td></tr>
  <?php endwhile;}?></tbody></table></div>
</div>

<!-- ═══════════ TAB CONTACTO ═══════════ -->
<div class="tab-pane fade" id="tab-contacto">
  <h5 class="font-weight-bold mb-3"><i class="fas fa-address-book text-primary mr-2"></i>Contacto y Redes</h5>
  <form id="formContacto">
    <div class="row">
      <div class="col-md-6 mb-3"><label class="font-weight-bold">Dirección</label><textarea class="form-control" name="config[direccion]" rows="2"><?=e(c('direccion'))?></textarea></div>
      <div class="col-md-6 mb-3"><label class="font-weight-bold">URL Google Maps embed</label><input type="text" class="form-control" name="config[mapa_embed]" value="<?=e(c('mapa_embed'))?>"><small class="text-muted">Link de "Insertar mapa"</small></div>
      <div class="col-md-6 mb-3"><label class="font-weight-bold">Horario L-S</label><input type="text" class="form-control" name="config[horario_ls]" value="<?=e(c('horario_ls'))?>"></div>
      <div class="col-md-6 mb-3"><label class="font-weight-bold">Horario Domingos</label><input type="text" class="form-control" name="config[horario_dom]" value="<?=e(c('horario_dom'))?>"></div>
      <div class="col-md-3 mb-3"><label class="font-weight-bold">Teléfono 1</label><input type="text" class="form-control" name="config[telefono_1]" value="<?=e(c('telefono_1'))?>"></div>
      <div class="col-md-3 mb-3"><label class="font-weight-bold">Teléfono 2</label><input type="text" class="form-control" name="config[telefono_2]" value="<?=e(c('telefono_2'))?>"></div>
      <div class="col-md-3 mb-3"><label class="font-weight-bold">Email 1</label><input type="text" class="form-control" name="config[email_1]" value="<?=e(c('email_1'))?>"></div>
      <div class="col-md-3 mb-3"><label class="font-weight-bold">Email 2</label><input type="text" class="form-control" name="config[email_2]" value="<?=e(c('email_2'))?>"></div>
    </div>
    <h6 class="font-weight-bold mt-3 mb-2"><i class="fas fa-share-alt mr-1"></i> Redes Sociales</h6>
    <div class="row">
      <div class="col-md-3 mb-3"><label><i class="fab fa-facebook mr-1"></i>Facebook</label><input type="text" class="form-control" name="config[social_facebook]" value="<?=e(c('social_facebook'))?>"></div>
      <div class="col-md-3 mb-3"><label><i class="fab fa-instagram mr-1"></i>Instagram</label><input type="text" class="form-control" name="config[social_instagram]" value="<?=e(c('social_instagram'))?>"></div>
      <div class="col-md-3 mb-3"><label><i class="fab fa-tiktok mr-1"></i>TikTok</label><input type="text" class="form-control" name="config[social_tiktok]" value="<?=e(c('social_tiktok'))?>"></div>
      <div class="col-md-3 mb-3"><label><i class="fab fa-whatsapp mr-1"></i>WhatsApp</label><input type="text" class="form-control" name="config[social_whatsapp]" value="<?=e(c('social_whatsapp'))?>"></div>
    </div>
    <button type="submit" class="btn btn-primary mt-3" style="border-radius:50px;"><i class="fas fa-save mr-1"></i> Guardar Contacto</button>
  </form>
</div>

</div></div></div>
</div></div>

<!-- ═══════════ MODALES ═══════════ -->
<div class="modal fade" id="modalFeature" tabindex="-1"><div class="modal-dialog"><div class="modal-content">
<div class="modal-header bg-primary text-white"><h5 class="modal-title" id="modalFeatureTitle">Feature</h5><button type="button" class="close text-white" data-dismiss="modal">&times;</button></div>
<form id="formFeature"><div class="modal-body">
<input type="hidden" name="id" id="feat_id">
<div class="form-group"><label class="font-weight-bold">Título</label><input type="text" class="form-control" name="titulo" id="feat_titulo" required></div>
<div class="row"><div class="col-6"><label class="font-weight-bold">Icono</label><input type="text" class="form-control" name="icono" id="feat_icono" placeholder="fas fa-dumbbell"></div><div class="col-6"><label class="font-weight-bold">Orden</label><input type="number" class="form-control" name="orden" id="feat_orden" value="0"></div></div>
<div class="form-group mt-2"><label class="font-weight-bold">Color gradiente</label><input type="text" class="form-control" name="color" id="feat_color" value="linear-gradient(135deg,#4e73df,#224abe)"></div>
<div class="form-group"><label class="font-weight-bold">Descripción</label><textarea class="form-control" name="descripcion" id="feat_descripcion" rows="3"></textarea></div>
<div class="form-check"><input type="checkbox" class="form-check-input" id="feat_activo" value="1" checked><label class="form-check-label" for="feat_activo">Activo</label></div>
</div><div class="modal-footer"><button type="submit" class="btn btn-primary"><i class="fas fa-save mr-1"></i> Guardar</button></div></form></div></div></div>

<div class="modal fade" id="modalPlan" tabindex="-1"><div class="modal-dialog modal-lg"><div class="modal-content">
<div class="modal-header bg-warning text-white"><h5 class="modal-title" id="modalPlanTitle">Plan</h5><button type="button" class="close text-white" data-dismiss="modal">&times;</button></div>
<form id="formPlan"><div class="modal-body">
<input type="hidden" name="id" id="plan_id">
<div class="row">
<div class="col-md-4"><label class="font-weight-bold">Nombre</label><input type="text" class="form-control" name="nombre" id="plan_nombre" required></div>
<div class="col-md-3"><label class="font-weight-bold">Precio</label><input type="number" step="0.01" class="form-control" name="precio" id="plan_precio"></div>
<div class="col-md-2"><label class="font-weight-bold">Moneda</label><input type="text" class="form-control" name="moneda" id="plan_moneda" value="Bs"></div>
<div class="col-md-3"><label class="font-weight-bold">Duración</label><input type="text" class="form-control" name="duracion" id="plan_duracion" value="30 días"></div>
</div>
<div class="row mt-2">
<div class="col-md-4"><label class="font-weight-bold">Icono</label><input type="text" class="form-control" name="icono" id="plan_icono" placeholder="fas fa-bolt"></div>
<div class="col-md-4"><label class="font-weight-bold">Tipo acceso</label><input type="text" class="form-control" name="tipo_acceso" id="plan_tipo_acceso"></div>
<div class="col-md-2"><label class="font-weight-bold">Orden</label><input type="number" class="form-control" name="orden" id="plan_orden" value="0"></div>
<div class="col-md-2 text-center"><label class="font-weight-bold d-block">Destacado</label><input type="checkbox" id="plan_destacado" value="1" style="transform:scale(1.5);margin-top:10px"></div>
</div>
<div class="form-group mt-2"><label class="font-weight-bold">Color</label><input type="text" class="form-control" name="color" id="plan_color" value="linear-gradient(135deg,#1cc88a,#17a673)"></div>
<hr><label class="font-weight-bold"><i class="fas fa-check-circle mr-1"></i>Beneficios</label>
<div id="plan_beneficios_container"></div>
<button type="button" class="btn btn-sm btn-outline-success mt-2" onclick="addBeneficio()"><i class="fas fa-plus mr-1"></i> Agregar</button>
<div class="form-check mt-3"><input type="checkbox" class="form-check-input" id="plan_activo" value="1" checked><label class="form-check-label" for="plan_activo">Activo</label></div>
</div><div class="modal-footer"><button type="submit" class="btn btn-primary"><i class="fas fa-save mr-1"></i> Guardar</button></div></form></div></div></div>

<div class="modal fade" id="modalHorario" tabindex="-1"><div class="modal-dialog"><div class="modal-content">
<div class="modal-header bg-info text-white"><h5 class="modal-title" id="modalHorarioTitle">Horario</h5><button type="button" class="close text-white" data-dismiss="modal">&times;</button></div>
<form id="formHorario"><div class="modal-body">
<input type="hidden" name="id" id="hor_id">
<div class="row"><div class="col-6"><label class="font-weight-bold">Día</label>
<select class="form-control" name="dia" id="hor_dia"><option value="LUN" data-o="1">Lunes</option><option value="MAR" data-o="2">Martes</option><option value="MIÉ" data-o="3">Miércoles</option><option value="JUE" data-o="4">Jueves</option><option value="VIE" data-o="5">Viernes</option><option value="SÁB" data-o="6">Sábado</option><option value="DOM" data-o="7">Domingo</option></select></div>
<div class="col-6"><label class="font-weight-bold">Clase</label><input type="text" class="form-control" name="clase" id="hor_clase" required></div></div>
<div class="row mt-2"><div class="col-6"><label class="font-weight-bold">Hora inicio</label><input type="time" class="form-control" name="hora_inicio" id="hor_inicio" required></div>
<div class="col-6"><label class="font-weight-bold">Hora fin</label><input type="time" class="form-control" name="hora_fin" id="hor_fin" required></div></div>
<div class="form-check mt-3"><input type="checkbox" class="form-check-input" id="hor_activo" value="1" checked><label class="form-check-label" for="hor_activo">Activo</label></div>
</div><div class="modal-footer"><button type="submit" class="btn btn-primary"><i class="fas fa-save mr-1"></i> Guardar</button></div></form></div></div></div>

<div class="modal fade" id="modalInstructor" tabindex="-1"><div class="modal-dialog"><div class="modal-content">
<div class="modal-header bg-danger text-white"><h5 class="modal-title" id="modalInstructorTitle">Instructor</h5><button type="button" class="close text-white" data-dismiss="modal">&times;</button></div>
<form id="formInstructor"><div class="modal-body">
<input type="hidden" name="id" id="inst_id">
<div class="form-group"><label class="font-weight-bold">Nombre</label><input type="text" class="form-control" name="nombre" id="inst_nombre" required></div>
<div class="form-group"><label class="font-weight-bold">Cargo</label><input type="text" class="form-control" name="cargo" id="inst_cargo"></div>
<div class="form-group"><label class="font-weight-bold">Descripción</label><textarea class="form-control" name="descripcion" id="inst_descripcion" rows="2"></textarea></div>
<div class="row"><div class="col-6"><label class="font-weight-bold">Icono</label><input type="text" class="form-control" name="icono" id="inst_icono" value="fas fa-user"></div><div class="col-6"><label class="font-weight-bold">Orden</label><input type="number" class="form-control" name="orden" id="inst_orden" value="0"></div></div>
<div class="form-check mt-3"><input type="checkbox" class="form-check-input" id="inst_activo" value="1" checked><label class="form-check-label" for="inst_activo">Activo</label></div>
</div><div class="modal-footer"><button type="submit" class="btn btn-primary"><i class="fas fa-save mr-1"></i> Guardar</button></div></form></div></div></div>

<!-- ESTILOS -->
<style>
.page-header{background:linear-gradient(135deg,#4e73df,#1cc88a);border-radius:1rem;padding:1.3rem 1rem;box-shadow:0 8px 20px rgba(0,0,0,.16)}
.page-header-inner{max-width:1200px;margin:0 auto;text-align:center;color:#fff}
.page-title{font-size:1.35rem;font-weight:700;margin-bottom:4px}
.page-subtitle{font-size:.85rem;opacity:.9;margin-bottom:0}
.portal-tabs{border-bottom:0;background:#fff;border-radius:1rem 1rem 0 0;padding:8px 12px 0;box-shadow:0 -2px 10px rgba(0,0,0,.04);flex-wrap:wrap}
.portal-tabs .nav-link{font-weight:700;font-size:.82rem;color:#858796;border:0;border-bottom:3px solid transparent;padding:10px 12px;transition:.2s}
.portal-tabs .nav-link.active{color:#4e73df;border-bottom-color:#4e73df;background:transparent}
.beneficio-row{display:flex;gap:8px;margin-bottom:8px;align-items:center}
.beneficio-row input{flex:1}
</style>

<!-- ═══════════════════════════════════════════════
     JAVASCRIPT — Usa fetch() + URLSearchParams
     Mismo patrón que staffs.php del sistema
═══════════════════════════════════════════════ -->
<script>

var CSRF = "<?= $csrf ?>";
var API  = "portal_actions.php";

// ── Función POST genérica ──
function apiPost(params) {
    // params es un objeto simple {action:'x', id:1, ...}
    // Lo convertimos a URL-encoded string (como staffs.php)
    var body = '_csrf=' + encodeURIComponent(CSRF);
    for (var k in params) {
        if (!params.hasOwnProperty(k)) continue;
        var val = params[k];
        if (Array.isArray(val)) {
            // Para arrays como beneficios[]
            for (var i = 0; i < val.length; i++) {
                body += '&' + encodeURIComponent(k + '[]') + '=' + encodeURIComponent(val[i]);
            }
        } else {
            body += '&' + encodeURIComponent(k) + '=' + encodeURIComponent(val);
        }
    }

    console.log('[Portal] POST →', API, body.substring(0, 200));

    return fetch(API, {
        method: 'POST',
        credentials: 'same-origin',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: body
    })
    .then(function(resp) {
        console.log('[Portal] Status:', resp.status);
        if (!resp.ok) {
            return resp.text().then(function(txt) {
                console.error('[Portal] Response body:', txt.substring(0, 500));
                throw new Error('HTTP ' + resp.status + ': ' + txt.substring(0, 100));
            });
        }
        return resp.text().then(function(txt) {
            console.log('[Portal] Raw response:', txt.substring(0, 300));
            try { return JSON.parse(txt); }
            catch(e) { throw new Error('No es JSON: ' + txt.substring(0, 200)); }
        });
    });
}

function apiGet(action, id) {
    var url = API + '?action=' + action + '&id=' + id;
    console.log('[Portal] GET →', url);
    return fetch(url, { credentials: 'same-origin' })
    .then(function(r) { return r.json(); });
}

function showOk(msg) { Swal.fire({icon:'success',title:msg,timer:1500,showConfirmButton:false}); }
function showErr(msg) {
    Swal.fire({icon:'error',title:'Error',text:msg});
    document.getElementById('debugAlert').textContent = msg;
    document.getElementById('debugAlert').classList.remove('d-none');
}

// ── Test conexión ──
function testConexion() {
    apiPost({action:'test'}).then(function(d) {
        if (d.ok) Swal.fire({icon:'success',title:'¡Conexión OK!',text:'Tablas listas. Session: '+d.session,timer:3000});
        else showErr(d.msg);
    }).catch(function(e) { showErr(e.message); });
}

// ── Recolectar campos config[...] de un form ──
function getConfigParams(form) {
    var params = { action: 'guardar_config' };
    var inputs = form.querySelectorAll('input[name^="config["], textarea[name^="config["]');
    for (var i = 0; i < inputs.length; i++) {
        var name = inputs[i].name; // config[hero_badge]
        var key = name.replace('config[', '').replace(']', ''); // hero_badge
        params['config[' + key + ']'] = inputs[i].value;
    }
    return params;
}

// ── Beneficios helpers ──
function addBeneficio(txt) {
    txt = txt || '';
    var div = document.createElement('div');
    div.className = 'beneficio-row';
    div.innerHTML = '<input type="text" class="form-control form-control-sm" value="'+txt.replace(/"/g,'&quot;')+'" placeholder="Beneficio...">'
        + ' <button type="button" class="btn btn-sm btn-outline-danger" onclick="this.parentElement.remove()"><i class="fas fa-times"></i></button>';
    document.getElementById('plan_beneficios_container').appendChild(div);
}

function getBeneficios() {
    var arr = [];
    var rows = document.querySelectorAll('#plan_beneficios_container .beneficio-row input');
    for (var i = 0; i < rows.length; i++) {
        var v = rows[i].value.trim();
        if (v) arr.push(v);
    }
    return arr;
}

// ── Delete genérico ──
function deleteItem(tipo, id) {
    Swal.fire({title:'¿Eliminar?',text:'No se puede deshacer',icon:'warning',showCancelButton:true,confirmButtonColor:'#e74a3b',cancelButtonColor:'#858796',confirmButtonText:'Sí, eliminar',cancelButtonText:'Cancelar'
    }).then(function(r) {
        if (!r.isConfirmed) return;
        apiPost({action:'eliminar_'+tipo, id:id}).then(function(d) {
            if (d.ok) { showOk(d.msg||'Eliminado'); setTimeout(function(){location.reload()},1000); }
            else showErr(d.msg);
        }).catch(function(e){showErr(e.message)});
    });
}

// ═══════════ FEATURES ═══════════
function openFeatureModal(id) {
    if (id > 0) {
        apiGet('get_feature',id).then(function(r) {
            if (!r.ok) return showErr(r.msg);
            var f=r.feature;
            document.getElementById('feat_id').value=f.id;
            document.getElementById('feat_titulo').value=f.titulo;
            document.getElementById('feat_icono').value=f.icono;
            document.getElementById('feat_color').value=f.color;
            document.getElementById('feat_descripcion').value=f.descripcion;
            document.getElementById('feat_orden').value=f.orden;
            document.getElementById('feat_activo').checked=(f.activo==1);
            document.getElementById('modalFeatureTitle').textContent='Editar Feature';
            $('#modalFeature').modal('show');
        });
    } else {
        document.getElementById('formFeature').reset();
        document.getElementById('feat_id').value=0;
        document.getElementById('feat_activo').checked=true;
        document.getElementById('modalFeatureTitle').textContent='Nueva Feature';
        $('#modalFeature').modal('show');
    }
}

// ═══════════ PLANES ═══════════
function openPlanModal(id) {
    document.getElementById('plan_beneficios_container').innerHTML='';
    if (id > 0) {
        apiGet('get_plan',id).then(function(r) {
            if (!r.ok) return showErr(r.msg);
            var p=r.plan;
            document.getElementById('plan_id').value=p.id;
            document.getElementById('plan_nombre').value=p.nombre;
            document.getElementById('plan_icono').value=p.icono;
            document.getElementById('plan_color').value=p.color;
            document.getElementById('plan_precio').value=p.precio;
            document.getElementById('plan_moneda').value=p.moneda;
            document.getElementById('plan_duracion').value=p.duracion;
            document.getElementById('plan_tipo_acceso').value=p.tipo_acceso;
            document.getElementById('plan_destacado').checked=(p.destacado==1);
            document.getElementById('plan_orden').value=p.orden;
            document.getElementById('plan_activo').checked=(p.activo==1);
            (p.beneficios||[]).forEach(function(b){addBeneficio(b)});
            if(!p.beneficios||p.beneficios.length===0) addBeneficio('');
            document.getElementById('modalPlanTitle').textContent='Editar Plan';
            $('#modalPlan').modal('show');
        });
    } else {
        document.getElementById('formPlan').reset();
        document.getElementById('plan_id').value=0;
        document.getElementById('plan_activo').checked=true;
        addBeneficio('');
        document.getElementById('modalPlanTitle').textContent='Nuevo Plan';
        $('#modalPlan').modal('show');
    }
}

// ═══════════ HORARIOS ═══════════
function openHorarioModal(id) {
    if (id > 0) {
        apiGet('get_horario',id).then(function(r) {
            if (!r.ok) return showErr(r.msg);
            var h=r.horario;
            document.getElementById('hor_id').value=h.id;
            document.getElementById('hor_dia').value=h.dia;
            document.getElementById('hor_clase').value=h.clase;
            document.getElementById('hor_inicio').value=h.hora_inicio;
            document.getElementById('hor_fin').value=h.hora_fin;
            document.getElementById('hor_activo').checked=(h.activo==1);
            document.getElementById('modalHorarioTitle').textContent='Editar Horario';
            $('#modalHorario').modal('show');
        });
    } else {
        document.getElementById('formHorario').reset();
        document.getElementById('hor_id').value=0;
        document.getElementById('hor_activo').checked=true;
        document.getElementById('modalHorarioTitle').textContent='Nuevo Horario';
        $('#modalHorario').modal('show');
    }
}

// ═══════════ INSTRUCTORES ═══════════
function openInstructorModal(id) {
    if (id > 0) {
        apiGet('get_instructor',id).then(function(r) {
            if (!r.ok) return showErr(r.msg);
            var i=r.instructor;
            document.getElementById('inst_id').value=i.id;
            document.getElementById('inst_nombre').value=i.nombre;
            document.getElementById('inst_cargo').value=i.cargo;
            document.getElementById('inst_descripcion').value=i.descripcion;
            document.getElementById('inst_icono').value=i.icono;
            document.getElementById('inst_orden').value=i.orden;
            document.getElementById('inst_activo').checked=(i.activo==1);
            document.getElementById('modalInstructorTitle').textContent='Editar Instructor';
            $('#modalInstructor').modal('show');
        });
    } else {
        document.getElementById('formInstructor').reset();
        document.getElementById('inst_id').value=0;
        document.getElementById('inst_activo').checked=true;
        document.getElementById('modalInstructorTitle').textContent='Nuevo Instructor';
        $('#modalInstructor').modal('show');
    }
}

// ═══════════ FORM SUBMITS ═══════════
document.addEventListener('DOMContentLoaded', function() {

    // ── Config (Hero) ──
    document.getElementById('formHero').addEventListener('submit', function(ev) {
        ev.preventDefault();
        var params = getConfigParams(this);
        apiPost(params).then(function(d) {
            if (d.ok) showOk(d.msg||'Guardado');
            else showErr(d.msg);
        }).catch(function(e){showErr(e.message)});
    });

    // ── Config (Contacto) ──
    document.getElementById('formContacto').addEventListener('submit', function(ev) {
        ev.preventDefault();
        var params = getConfigParams(this);
        apiPost(params).then(function(d) {
            if (d.ok) showOk(d.msg||'Guardado');
            else showErr(d.msg);
        }).catch(function(e){showErr(e.message)});
    });

    // ── Feature ──
    document.getElementById('formFeature').addEventListener('submit', function(ev) {
        ev.preventDefault();
        apiPost({
            action:'guardar_feature',
            id: document.getElementById('feat_id').value,
            titulo: document.getElementById('feat_titulo').value,
            icono: document.getElementById('feat_icono').value,
            color: document.getElementById('feat_color').value,
            descripcion: document.getElementById('feat_descripcion').value,
            orden: document.getElementById('feat_orden').value,
            activo: document.getElementById('feat_activo').checked ? 1 : 0
        }).then(function(d) {
            if (d.ok){showOk(d.msg);setTimeout(function(){location.reload()},1200);}
            else showErr(d.msg);
        }).catch(function(e){showErr(e.message)});
    });

    // ── Plan ──
    document.getElementById('formPlan').addEventListener('submit', function(ev) {
        ev.preventDefault();
        apiPost({
            action:'guardar_plan',
            id: document.getElementById('plan_id').value,
            nombre: document.getElementById('plan_nombre').value,
            icono: document.getElementById('plan_icono').value,
            color: document.getElementById('plan_color').value,
            precio: document.getElementById('plan_precio').value,
            moneda: document.getElementById('plan_moneda').value,
            duracion: document.getElementById('plan_duracion').value,
            tipo_acceso: document.getElementById('plan_tipo_acceso').value,
            destacado: document.getElementById('plan_destacado').checked ? 1 : 0,
            orden: document.getElementById('plan_orden').value,
            activo: document.getElementById('plan_activo').checked ? 1 : 0,
            beneficios: getBeneficios()
        }).then(function(d) {
            if (d.ok){showOk(d.msg);setTimeout(function(){location.reload()},1200);}
            else showErr(d.msg);
        }).catch(function(e){showErr(e.message)});
    });

    // ── Horario ──
    document.getElementById('formHorario').addEventListener('submit', function(ev) {
        ev.preventDefault();
        var sel = document.getElementById('hor_dia');
        apiPost({
            action:'guardar_horario',
            id: document.getElementById('hor_id').value,
            dia: sel.value,
            dia_orden: sel.options[sel.selectedIndex].getAttribute('data-o') || 0,
            hora_inicio: document.getElementById('hor_inicio').value,
            hora_fin: document.getElementById('hor_fin').value,
            clase: document.getElementById('hor_clase').value,
            activo: document.getElementById('hor_activo').checked ? 1 : 0
        }).then(function(d) {
            if (d.ok){showOk(d.msg);setTimeout(function(){location.reload()},1200);}
            else showErr(d.msg);
        }).catch(function(e){showErr(e.message)});
    });

    // ── Instructor ──
    document.getElementById('formInstructor').addEventListener('submit', function(ev) {
        ev.preventDefault();
        apiPost({
            action:'guardar_instructor',
            id: document.getElementById('inst_id').value,
            nombre: document.getElementById('inst_nombre').value,
            cargo: document.getElementById('inst_cargo').value,
            descripcion: document.getElementById('inst_descripcion').value,
            icono: document.getElementById('inst_icono').value,
            orden: document.getElementById('inst_orden').value,
            activo: document.getElementById('inst_activo').checked ? 1 : 0
        }).then(function(d) {
            if (d.ok){showOk(d.msg);setTimeout(function(){location.reload()},1200);}
            else showErr(d.msg);
        }).catch(function(e){showErr(e.message)});
    });

});
</script>

