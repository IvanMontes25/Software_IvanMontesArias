<?php
/**
 * portal_actions.php — AJAX handler para CRUD del portal
 * Ubicación: /admin/portal_actions.php
 *
 * IMPORTANTE: NO incluye header.php (es solo AJAX, no genera HTML).
 * Hace su propia verificación de CSRF buscando _csrf (igual que header.php).
 */

// ── Cargar sistema ──
// [MVC] bootstrap loaded by entry point
// [MVC] auth loaded by entry point
require_once __DIR__ . '/../core/roles.php';
require_modulo('administracion');

header('Content-Type: application/json; charset=utf-8');

// ── Verificar DB ──
if (!isset($db) || !$db instanceof mysqli || $db->connect_error) {
    echo json_encode(['ok' => false, 'msg' => 'Sin conexión a BD']);
    exit;
}

// ── Verificar CSRF en POST (mismos campos que busca header.php) ──
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tok = $_POST['_csrf'] ?? ($_POST['csrf_token'] ?? '');
    $ses = $_SESSION['csrf_token'] ?? ($_SESSION['_csrf'] ?? '');

    if (empty($ses) || empty($tok) || !hash_equals($ses, $tok)) {
        echo json_encode([
            'ok'  => false,
            'msg' => 'Token CSRF inválido. Recarga la página e intenta de nuevo.'
        ]);
        exit;
    }
}

// ── Helpers ──
function json_ok($data = [])  { echo json_encode(array_merge(['ok' => true],  $data)); exit; }
function json_err($msg)       { echo json_encode(['ok' => false, 'msg' => $msg]); exit; }

// ── Auto-crear tablas si no existen ──
function ensureTables($db) {
    $check = $db->query("SHOW TABLES LIKE 'portal_config'");
    if ($check && $check->num_rows > 0) return; // Ya existen

    $sqls = [
        "CREATE TABLE IF NOT EXISTS portal_config (
            id INT AUTO_INCREMENT PRIMARY KEY,
            clave VARCHAR(100) NOT NULL UNIQUE,
            valor TEXT NOT NULL,
            grupo VARCHAR(50) NOT NULL DEFAULT 'general',
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",

        "CREATE TABLE IF NOT EXISTS portal_features (
            id INT AUTO_INCREMENT PRIMARY KEY,
            icono VARCHAR(60) NOT NULL DEFAULT 'fas fa-star',
            color VARCHAR(100) NOT NULL DEFAULT 'linear-gradient(135deg,#4e73df,#224abe)',
            titulo VARCHAR(120) NOT NULL,
            descripcion TEXT NOT NULL,
            orden INT NOT NULL DEFAULT 0,
            activo TINYINT(1) NOT NULL DEFAULT 1,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",

        "CREATE TABLE IF NOT EXISTS portal_planes (
            id INT AUTO_INCREMENT PRIMARY KEY,
            nombre VARCHAR(80) NOT NULL,
            icono VARCHAR(60) NOT NULL DEFAULT 'fas fa-bolt',
            color VARCHAR(100) NOT NULL DEFAULT 'linear-gradient(135deg,#1cc88a,#17a673)',
            precio DECIMAL(10,2) NOT NULL DEFAULT 0,
            moneda VARCHAR(10) NOT NULL DEFAULT 'Bs',
            duracion VARCHAR(50) NOT NULL DEFAULT '30 días',
            tipo_acceso VARCHAR(80) NOT NULL DEFAULT 'Acceso limitado',
            destacado TINYINT(1) NOT NULL DEFAULT 0,
            orden INT NOT NULL DEFAULT 0,
            activo TINYINT(1) NOT NULL DEFAULT 1,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",

        "CREATE TABLE IF NOT EXISTS portal_plan_beneficios (
            id INT AUTO_INCREMENT PRIMARY KEY,
            plan_id INT NOT NULL,
            texto VARCHAR(200) NOT NULL,
            orden INT NOT NULL DEFAULT 0
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",

        "CREATE TABLE IF NOT EXISTS portal_horarios (
            id INT AUTO_INCREMENT PRIMARY KEY,
            dia VARCHAR(20) NOT NULL,
            dia_orden TINYINT NOT NULL DEFAULT 0,
            hora_inicio VARCHAR(10) NOT NULL,
            hora_fin VARCHAR(10) NOT NULL,
            clase VARCHAR(80) NOT NULL,
            activo TINYINT(1) NOT NULL DEFAULT 1,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",

        "CREATE TABLE IF NOT EXISTS portal_instructores (
            id INT AUTO_INCREMENT PRIMARY KEY,
            nombre VARCHAR(100) NOT NULL,
            cargo VARCHAR(100) NOT NULL,
            descripcion TEXT,
            icono VARCHAR(60) NOT NULL DEFAULT 'fas fa-user',
            color VARCHAR(100) NOT NULL DEFAULT 'linear-gradient(135deg,#4e73df33,#1cc88a33)',
            orden INT NOT NULL DEFAULT 0,
            activo TINYINT(1) NOT NULL DEFAULT 1,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",

        "CREATE TABLE IF NOT EXISTS portal_mensajes (
            id INT AUTO_INCREMENT PRIMARY KEY,
            nombre VARCHAR(100) NOT NULL,
            email VARCHAR(150) NOT NULL,
            telefono VARCHAR(30),
            mensaje TEXT,
            leido TINYINT(1) NOT NULL DEFAULT 0,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4"
    ];

    foreach ($sqls as $sql) {
        if (!$db->query($sql)) {
            json_err('Error creando tablas: ' . $db->error);
        }
    }
}

ensureTables($db);

$action = $_POST['action'] ?? $_GET['action'] ?? '';

// ═══════════ TEST (para debug) ═══════════
if ($action === 'test') {
    json_ok([
        'msg'     => 'Conexión OK',
        'session' => session_id(),
        'tables'  => true
    ]);
}

// ═══════════ CONFIG (key-value) ═══════════
if ($action === 'guardar_config') {
    $campos = $_POST['config'] ?? [];
    if (!is_array($campos) || count($campos) === 0) json_err('No hay datos para guardar');

    $stmt = $db->prepare("INSERT INTO portal_config (clave, valor, grupo) VALUES (?, ?, ?)
                          ON DUPLICATE KEY UPDATE valor = VALUES(valor)");
    if (!$stmt) json_err('Error SQL prepare: ' . $db->error);

    $saved = 0;
    foreach ($campos as $clave => $valor) {
        $clave = trim((string)$clave);
        $valor = trim((string)$valor);
        if (strpos($clave, 'hero') === 0)      $grupo = 'hero';
        elseif (strpos($clave, 'social') === 0) $grupo = 'redes';
        else                                     $grupo = 'contacto';
        $stmt->bind_param('sss', $clave, $valor, $grupo);
        if ($stmt->execute()) $saved++;
    }
    $stmt->close();
    json_ok(['msg' => "Configuración guardada ($saved campos)"]);
}

// ═══════════ FEATURES ═══════════
if ($action === 'guardar_feature') {
    $id   = (int)($_POST['id'] ?? 0);
    $data = [
        trim($_POST['icono'] ?? 'fas fa-star'),
        trim($_POST['color'] ?? 'linear-gradient(135deg,#4e73df,#224abe)'),
        trim($_POST['titulo'] ?? ''),
        trim($_POST['descripcion'] ?? ''),
        (int)($_POST['orden'] ?? 0),
        (int)($_POST['activo'] ?? 1),
    ];
    if ($data[2] === '') json_err('Título requerido');

    if ($id > 0) {
        $st = $db->prepare("UPDATE portal_features SET icono=?,color=?,titulo=?,descripcion=?,orden=?,activo=? WHERE id=?");
        if (!$st) json_err('SQL: ' . $db->error);
        $data[] = $id;
        $st->bind_param('ssssiis', ...$data);
    } else {
        $st = $db->prepare("INSERT INTO portal_features (icono,color,titulo,descripcion,orden,activo) VALUES (?,?,?,?,?,?)");
        if (!$st) json_err('SQL: ' . $db->error);
        $st->bind_param('ssssii', ...$data);
    }
    if (!$st->execute()) json_err('Error: ' . $st->error);
    $nid = $id ?: $st->insert_id;
    $st->close();
    json_ok(['msg' => 'Feature guardada', 'id' => $nid]);
}

if ($action === 'eliminar_feature') {
    $id = (int)($_POST['id'] ?? 0);
    if ($id <= 0) json_err('ID inválido');
    $db->query("DELETE FROM portal_features WHERE id=$id");
    json_ok(['msg' => 'Feature eliminada']);
}

// ═══════════ PLANES ═══════════
if ($action === 'guardar_plan') {
    $id          = (int)($_POST['id'] ?? 0);
    $nombre      = trim($_POST['nombre'] ?? '');
    $icono       = trim($_POST['icono'] ?? 'fas fa-bolt');
    $color       = trim($_POST['color'] ?? 'linear-gradient(135deg,#1cc88a,#17a673)');
    $precio      = (float)($_POST['precio'] ?? 0);
    $moneda      = trim($_POST['moneda'] ?? 'Bs');
    $duracion    = trim($_POST['duracion'] ?? '30 días');
    $tipo_acceso = trim($_POST['tipo_acceso'] ?? '');
    $destacado   = (int)($_POST['destacado'] ?? 0);
    $orden       = (int)($_POST['orden'] ?? 0);
    $activo      = (int)($_POST['activo'] ?? 1);
    if ($nombre === '') json_err('Nombre del plan requerido');

    if ($id > 0) {
        $st = $db->prepare("UPDATE portal_planes SET nombre=?,icono=?,color=?,precio=?,moneda=?,duracion=?,tipo_acceso=?,destacado=?,orden=?,activo=? WHERE id=?");
        if (!$st) json_err('SQL: ' . $db->error);
        $st->bind_param('sssdsssiiis', $nombre,$icono,$color,$precio,$moneda,$duracion,$tipo_acceso,$destacado,$orden,$activo,$id);
    } else {
        $st = $db->prepare("INSERT INTO portal_planes (nombre,icono,color,precio,moneda,duracion,tipo_acceso,destacado,orden,activo) VALUES (?,?,?,?,?,?,?,?,?,?)");
        if (!$st) json_err('SQL: ' . $db->error);
        $st->bind_param('sssdsssiii', $nombre,$icono,$color,$precio,$moneda,$duracion,$tipo_acceso,$destacado,$orden,$activo);
    }
    if (!$st->execute()) json_err('Error: ' . $st->error);
    $nid = $id ?: $st->insert_id;
    $st->close();

    // Beneficios
    $bens = $_POST['beneficios'] ?? [];
    if (is_array($bens)) {
        $db->query("DELETE FROM portal_plan_beneficios WHERE plan_id=$nid");
        $stb = $db->prepare("INSERT INTO portal_plan_beneficios (plan_id,texto,orden) VALUES (?,?,?)");
        if ($stb) {
            $o = 0;
            foreach ($bens as $txt) {
                $txt = trim((string)$txt);
                if ($txt === '') continue;
                $o++;
                $stb->bind_param('isi', $nid, $txt, $o);
                $stb->execute();
            }
            $stb->close();
        }
    }
    json_ok(['msg' => 'Plan guardado', 'id' => $nid]);
}

if ($action === 'eliminar_plan') {
    $id = (int)($_POST['id'] ?? 0);
    if ($id <= 0) json_err('ID inválido');
    $db->query("DELETE FROM portal_plan_beneficios WHERE plan_id=$id");
    $db->query("DELETE FROM portal_planes WHERE id=$id");
    json_ok(['msg' => 'Plan eliminado']);
}

// ═══════════ HORARIOS ═══════════
if ($action === 'guardar_horario') {
    $id          = (int)($_POST['id'] ?? 0);
    $dia         = trim($_POST['dia'] ?? '');
    $dia_orden   = (int)($_POST['dia_orden'] ?? 0);
    $hora_inicio = trim($_POST['hora_inicio'] ?? '');
    $hora_fin    = trim($_POST['hora_fin'] ?? '');
    $clase       = trim($_POST['clase'] ?? '');
    $activo      = (int)($_POST['activo'] ?? 1);
    if ($dia === '' || $clase === '') json_err('Día y clase requeridos');

    if ($id > 0) {
        $st = $db->prepare("UPDATE portal_horarios SET dia=?,dia_orden=?,hora_inicio=?,hora_fin=?,clase=?,activo=? WHERE id=?");
        if (!$st) json_err('SQL: ' . $db->error);
        $st->bind_param('sisssii', $dia,$dia_orden,$hora_inicio,$hora_fin,$clase,$activo,$id);
    } else {
        $st = $db->prepare("INSERT INTO portal_horarios (dia,dia_orden,hora_inicio,hora_fin,clase,activo) VALUES (?,?,?,?,?,?)");
        if (!$st) json_err('SQL: ' . $db->error);
        $st->bind_param('sisssi', $dia,$dia_orden,$hora_inicio,$hora_fin,$clase,$activo);
    }
    if (!$st->execute()) json_err('Error: ' . $st->error);
    $nid = $id ?: $st->insert_id;
    $st->close();
    json_ok(['msg' => 'Horario guardado', 'id' => $nid]);
}

if ($action === 'eliminar_horario') {
    $id = (int)($_POST['id'] ?? 0);
    if ($id <= 0) json_err('ID inválido');
    $db->query("DELETE FROM portal_horarios WHERE id=$id");
    json_ok(['msg' => 'Horario eliminado']);
}

// ═══════════ INSTRUCTORES ═══════════
if ($action === 'guardar_instructor') {
    $id          = (int)($_POST['id'] ?? 0);
    $nombre      = trim($_POST['nombre'] ?? '');
    $cargo       = trim($_POST['cargo'] ?? '');
    $descripcion = trim($_POST['descripcion'] ?? '');
    $icono       = trim($_POST['icono'] ?? 'fas fa-user');
    $color       = trim($_POST['color'] ?? 'linear-gradient(135deg,#4e73df33,#1cc88a33)');
    $orden       = (int)($_POST['orden'] ?? 0);
    $activo      = (int)($_POST['activo'] ?? 1);
    if ($nombre === '') json_err('Nombre requerido');

    if ($id > 0) {
        $st = $db->prepare("UPDATE portal_instructores SET nombre=?,cargo=?,descripcion=?,icono=?,color=?,orden=?,activo=? WHERE id=?");
        if (!$st) json_err('SQL: ' . $db->error);
        $st->bind_param('sssssiis', $nombre,$cargo,$descripcion,$icono,$color,$orden,$activo,$id);
    } else {
        $st = $db->prepare("INSERT INTO portal_instructores (nombre,cargo,descripcion,icono,color,orden,activo) VALUES (?,?,?,?,?,?,?)");
        if (!$st) json_err('SQL: ' . $db->error);
        $st->bind_param('sssssii', $nombre,$cargo,$descripcion,$icono,$color,$orden,$activo);
    }
    if (!$st->execute()) json_err('Error: ' . $st->error);
    $nid = $id ?: $st->insert_id;
    $st->close();
    json_ok(['msg' => 'Instructor guardado', 'id' => $nid]);
}

if ($action === 'eliminar_instructor') {
    $id = (int)($_POST['id'] ?? 0);
    if ($id <= 0) json_err('ID inválido');
    $db->query("DELETE FROM portal_instructores WHERE id=$id");
    json_ok(['msg' => 'Instructor eliminado']);
}

// ═══════════ GET (para modales) ═══════════
if ($action === 'get_feature') {
    $id = (int)($_GET['id'] ?? 0);
    $r = $db->query("SELECT * FROM portal_features WHERE id=$id");
    if (!$r || $r->num_rows === 0) json_err('No encontrado');
    json_ok(['feature' => $r->fetch_assoc()]);
}
if ($action === 'get_plan') {
    $id = (int)($_GET['id'] ?? 0);
    $r = $db->query("SELECT * FROM portal_planes WHERE id=$id");
    if (!$r || $r->num_rows === 0) json_err('No encontrado');
    $plan = $r->fetch_assoc();
    $bens = [];
    $rb = $db->query("SELECT texto FROM portal_plan_beneficios WHERE plan_id=$id ORDER BY orden");
    if ($rb) while ($b = $rb->fetch_assoc()) $bens[] = $b['texto'];
    $plan['beneficios'] = $bens;
    json_ok(['plan' => $plan]);
}
if ($action === 'get_horario') {
    $id = (int)($_GET['id'] ?? 0);
    $r = $db->query("SELECT * FROM portal_horarios WHERE id=$id");
    if (!$r || $r->num_rows === 0) json_err('No encontrado');
    json_ok(['horario' => $r->fetch_assoc()]);
}
if ($action === 'get_instructor') {
    $id = (int)($_GET['id'] ?? 0);
    $r = $db->query("SELECT * FROM portal_instructores WHERE id=$id");
    if (!$r || $r->num_rows === 0) json_err('No encontrado');
    json_ok(['instructor' => $r->fetch_assoc()]);
}

json_err('Acción no reconocida: ' . htmlspecialchars($action));
