<?php
require_once __DIR__ . '/../core/bootstrap.php';
require_once __DIR__ . '/../core/auth.php';
require_once __DIR__ . '/../core/roles.php';
require_modulo('administracion');
if (!$db instanceof mysqli) {
  die('No hay conexión a la base de datos');
}
function fail($msg, $back = 'publicaciones.php')
{
  header('Location: ' . $back . '?e=' . urlencode($msg));
  exit;
}

// 1) Validar CSRF
$csrf = $_POST['csrf_token'] ?? '';
if (empty($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $csrf)) {
  fail('Token inválido. Recarga la página e intenta de nuevo.');
}

// 2) Validar DB
if (!($db instanceof mysqli)) {
  fail('No hay conexión a la base de datos.');
}

// 3) Leer campos
$message = trim((string) ($_POST['message'] ?? ''));
$date = (string) ($_POST['date'] ?? date('Y-m-d'));




// Si no hay texto ni archivos -> error
$hasFiles = !empty($_FILES['images']) && is_array($_FILES['images']['name']) && count(array_filter($_FILES['images']['name'])) > 0;
if ($message === '' && !$hasFiles) {
  fail('Escribe un mensaje o agrega al menos una imagen.');
}

if (mb_strlen($message) > 1000) {
  $message = mb_substr($message, 0, 1000);
}

// 4) Subir imágenes (máx 10, máx 5MB c/u, JPG/PNG/WEBP)
$MAX_FILES = 10;
$MAX_BYTES = 5 * 1024 * 1024;

$allowedMime = [
  'image/jpeg' => 'jpg',
  'image/png' => 'png',
  'image/webp' => 'webp',
];

$uploadDirFs = __DIR__ . '/../uploads/publicaciones/';  // filesystem
$uploadDirDb = 'uploads/publicaciones/';                // path para usar en HTML

if (!is_dir($uploadDirFs)) {
  @mkdir($uploadDirFs, 0777, true);
}

$images = [];

if ($hasFiles) {
  $names = $_FILES['images']['name'];
  $tmp = $_FILES['images']['tmp_name'];
  $errors = $_FILES['images']['error'];
  $sizes = $_FILES['images']['size'];

  // contar archivos reales
  $realIdx = [];
  for ($i = 0; $i < count($names); $i++) {
    if (!empty($names[$i]))
      $realIdx[] = $i;
  }
  if (count($realIdx) > $MAX_FILES) {
    fail('Puedes subir hasta 10 imágenes.');
  }

  $finfo = new finfo(FILEINFO_MIME_TYPE);

  foreach ($realIdx as $i) {
    if ($errors[$i] !== UPLOAD_ERR_OK) {
      fail('Error subiendo una imagen. Intenta de nuevo.');
    }
    if ($sizes[$i] > $MAX_BYTES) {
      fail('Una imagen supera 5 MB.');
    }

    $mime = $finfo->file($tmp[$i]);
    if (!isset($allowedMime[$mime])) {
      fail('Formato no permitido. Solo JPG, PNG, WEBP.');
    }

    $ext = $allowedMime[$mime];
    $safeName = 'pub_' . date('Ymd_His') . '_' . bin2hex(random_bytes(6)) . '.' . $ext;

    $dest = $uploadDirFs . $safeName;
    if (!move_uploaded_file($tmp[$i], $dest)) {
      fail('No se pudo guardar una imagen en el servidor.');
    }

    // guardamos ruta relativa para mostrar luego
    $images[] = $uploadDirDb . $safeName;
  }
}

// 5) Guardar en BD
// OJO: Ajusta el nombre de tu tabla y columnas si tu esquema es distinto.
$imagesJson = !empty($images) ? json_encode($images, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) : null;

// Si tu tabla NO tiene estas columnas, dime tu CREATE TABLE y lo adapto.
// Ejemplo de columnas: message, date, visibility, images_json, created_at
$sql = "INSERT INTO announcements (message, date, images_json)
        VALUES (?, ?, ?)";




$stmt = $db->prepare($sql);
if (!$stmt) {
  fail('Error SQL: ' . $db->error);
}

$stmt->bind_param(
  "sss",
  $message,
  $date,
  $imagesJson
);
if ($stmt->execute()) {

  $_SESSION['flash_success'] = "Mensaje publicado correctamente.";

  header("Location: publicaciones.php");
  exit;
}

if (!$stmt->execute()) {
  fail('No se pudo guardar la publicación: ' . $stmt->error);
}

$stmt->close();

// 6) Redirigir con éxito
header('Location: publicaciones.php?ok=1');
exit;
