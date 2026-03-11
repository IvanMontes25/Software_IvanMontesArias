<?php
// [MVC] bootstrap loaded by entry point
// [MVC] auth loaded by entry point
require_once __DIR__ . '/../core/roles.php';
require_modulo('administracion');
if (!$db instanceof mysqli) {
  die('No hay conexión a la base de datos');
}

/* ===================== Helpers ===================== */
function e(string $s): string { return htmlspecialchars($s, ENT_QUOTES, 'UTF-8'); }

function columnExists(mysqli $db, string $table, string $column): bool {
  $sql = "SELECT 1 
          FROM INFORMATION_SCHEMA.COLUMNS 
          WHERE TABLE_SCHEMA = DATABASE() 
            AND TABLE_NAME = ? 
            AND COLUMN_NAME = ? 
          LIMIT 1";
  if (!$st = $db->prepare($sql)) return false;
  $st->bind_param("ss", $table, $column);
  $st->execute();
  $st->store_result();
  $ok = $st->num_rows > 0;
  $st->close();
  return $ok;
}

function upsertAdmin(mysqli $db, string $username, string $passwordHash): void {
  // Inserta/actualiza en "admin" en base a username; password hasheado (bcrypt).

  if ($chk = $db->prepare("SELECT user_id FROM admin WHERE username = ? LIMIT 1")) {
    $chk->bind_param("s", $username);
    $chk->execute();
    $chk->store_result();
    if ($chk->num_rows > 0) {
      $chk->bind_result($admin_id);
      $chk->fetch();
      $chk->close();
      if ($upd = $db->prepare("UPDATE admin SET password = ? WHERE user_id = ?")) {
        $upd->bind_param("si", $passwordHash, $admin_id);
        $upd->execute();
        $upd->close();
      }
    } else {
      $chk->close();
      if ($ins = $db->prepare("INSERT INTO admin (username, password) VALUES (?, ?)")) {
        $ins->bind_param("ss", $username, $passwordHash);
        $ins->execute();
        $ins->close();
      }
    }
  }
}

function deleteFromAdminByUsername(mysqli $db, string $username): void {
  if ($del = $db->prepare("DELETE FROM admin WHERE username = ?")) {
    $del->bind_param("s", $username);
    $del->execute();
    $del->close();
  }
}

/* ============== Recolección de datos POST ============== */
/* Nota: usamos nombres comunes. Si tu formulario usa otros, mapea aquí. */
$fullname    = trim($_POST['fullname']    ?? $_POST['name']   ?? '');
$email       = trim($_POST['email']       ?? '');
$mobile = trim($_POST['contact'] ?? $_POST['mobile'] ?? $_POST['phone'] ?? '');

$address     = trim($_POST['address']     ?? '');
$gender      = trim($_POST['gender']      ?? '');
$designation = trim($_POST['designation'] ?? $_POST['role']   ?? '');
$username    = trim($_POST['username']    ?? '');
$passwordRaw = strval($_POST['password']  ?? '');
$status      = trim($_POST['status']      ?? 'Active'); // por defecto
$salary      = trim($_POST['salary']      ?? '');       // opcional
$join_date   = trim($_POST['join_date']   ?? date('Y-m-d')); // opcional

/* Validaciones básicas */
$errors = [];
if ($fullname === '')  $errors[] = 'El nombre completo es requerido.';
if ($username === '')  $errors[] = 'El nombre de usuario es requerido.';
if ($passwordRaw === '') $errors[] = 'La contraseña es requerida.';

/* ¿Usuario ya existe en staffs? (si hay columna username) */
if (empty($errors) && columnExists($db, 'staffs', 'username')) {
  if ($st = $db->prepare("SELECT COUNT(*) FROM staffs WHERE username = ?")) {
    $st->bind_param("s", $username);
    $st->execute();
    $st->bind_result($cnt);
    $st->fetch();
    $st->close();
    if ($cnt > 0) {
      $errors[] = 'El nombre de usuario ya existe en personal.';
    }
  }
}

if (!empty($errors)) {
  // Puedes redirigir con $_SESSION['error'] si tu UI lo usa
  http_response_code(400);
  echo 'Error: ' . e(implode(' | ', $errors));
  exit;
}

/* ============== Armado dinámico del INSERT a "staffs" ============== */
/* Contraseña: en staffs se usa MD5 en tu sistema actual */


$passwordHash = password_hash($passwordRaw, PASSWORD_DEFAULT);


// Campos candidatos (se insertarán solo si existen)
$candidates = [
  'fullname'    => $fullname,
  'name'        => $fullname,   // por si la columna se llama name
  'email'       => $email,

  // 🔥 ESTA ES LA CLAVE
  'contact'     => $mobile,     // ← columna REAL en la tabla

  'mobile'      => $mobile,     // opcional (por compatibilidad futura)
  'phone'       => $mobile,     // opcional

  'address'     => $address,
  'gender'      => $gender,
  'designation' => $designation,
  'username'    => $username,
  'password'    => $passwordHash,
  'status'      => $status,
  'salary'      => $salary,
  'join_date'   => $join_date
];


// Construir lista de columnas que realmente existen
$cols   = [];
$values = [];
$types  = '';
foreach ($candidates as $col => $val) {
  if ($val === '' && $col !== 'salary') {
    // Permitimos salary vacío; otros vacíos suelen ser irrelevantes
    // (ajusta a necesidad)
  }
  if (columnExists($db, 'staffs', $col)) {
    $cols[]   = $col;
    $values[] = $val;
    // Tipos: s para casi todo; si deseas detectar ints, ajusta
    $types   .= 's';
  }
}

if (empty($cols)) {
  http_response_code(500);
  echo 'Error: La tabla "staffs" no tiene columnas compatibles para insertar.';
  exit;
}

$placeholders = implode(',', array_fill(0, count($cols), '?'));
$colsSql      = '`' . implode('`,`', $cols) . '`';
$sqlInsert    = "INSERT INTO staffs ($colsSql) VALUES ($placeholders)";

$ok = false;
if ($st = $db->prepare($sqlInsert)) {
  // bind dinámico
  $bindParams = array_merge([$types], $values);
  $refs = [];
  foreach ($bindParams as $k => $v) { $refs[$k] = &$bindParams[$k]; }
  call_user_func_array([$st, 'bind_param'], $refs);
  $ok = $st->execute();
  $st->close();
}

if (!$ok) {
  http_response_code(500);
  echo 'Error al insertar en "staffs".';
  exit;
}

// ── Auditoría ──
require_once __DIR__ . '/../core/audit.php';
registrar_auditoria($db, 'crear_staff', "Registró al personal $fullname ($designation) - usuario: $username", 'administracion');

/* ======= Sincronización con tabla "admin" según el Rol ======= */
$isAdminRole = (strcasecmp($designation, 'Administrador') === 0 || stripos($designation, 'admin') !== false);

if ($isAdminRole) {
  // Crea/actualiza acceso admin con password en texto plano (como tu login de admin).
  upsertAdmin($db, $username, $passwordHash);
} else {
  // Opcional: elimina de admin si existe (para no dejar privilegios)
  deleteFromAdminByUsername($db, $username);
}

/* Redirección/Salida */
if (!headers_sent()) {
  // Ajusta la ruta a tu listado de personal
  header('Location: ./staffs.php?ok=1');
  exit;
}
echo 'OK';
