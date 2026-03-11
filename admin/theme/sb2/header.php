<?php
// === Admin bootstrap central ===
if (session_status() === PHP_SESSION_NONE) { session_start(); }

// Carga bootstrap/auth del proyecto si existe (ruta estándar del proyecto)
$__coreBootstrap = __DIR__ . '/../../../core/bootstrap.php';
$__coreAuth      = __DIR__ . '/../../../core/auth.php';
if (is_file($__coreBootstrap)) { require_once $__coreBootstrap; }
if (is_file($__coreAuth))      { require_once $__coreAuth; }

// Fallback mínimo: helper de escape
if (!function_exists('e')) {
  function e($s){ return htmlspecialchars((string)($s ?? ''), ENT_QUOTES, 'UTF-8'); }
}

// === CSRF (simple, sin alterar lo visual) ===
if (!function_exists('csrf_token')) {
  function csrf_token(): string {
    if (empty($_SESSION['_csrf'])) {
      $_SESSION['_csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['_csrf'];
  }
}
if (!function_exists('csrf_field')) {
  function csrf_field(): string {
    $t = csrf_token();
    return '<input type="hidden" name="_csrf" value="'.e($t).'">';
  }
}
if (!function_exists('csrf_verify')) {
  function csrf_verify(?string $token): bool {
    return is_string($token) && isset($_SESSION['_csrf']) && hash_equals($_SESSION['_csrf'], $token);
  }
}

// Verificación CSRF para POST (no rompe AJAX si envías _csrf)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $tok = $_POST['_csrf'] ?? ($_POST['csrf_token'] ?? ($_POST['csrf'] ?? ($_SERVER['HTTP_X_CSRF_TOKEN'] ?? null)));
  if (!csrf_verify($tok)) {
    http_response_code(403);
    // Respuesta JSON si corresponde
    if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) || str_contains($_SERVER['HTTP_ACCEPT'] ?? '', 'application/json')) {
      header('Content-Type: application/json; charset=utf-8');
      echo json_encode(['ok' => false, 'error' => 'CSRF']);
      exit;
    }
    die('<div style="padding:16px;font-family:system-ui">Acción bloqueada (CSRF).</div>');
  }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?php echo isset($pageTitle) ? htmlspecialchars($pageTitle) : 'Panel'; ?></title>
  <link href="https://fonts.googleapis.com/css?family=Nunito:200,300,400,600,700,800,900&display=swap" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/startbootstrap-sb-admin-2@4.1.4/css/sb-admin-2.min.css" rel="stylesheet">
  <style>
    body { font-family: 'Nunito', system-ui, -apple-system, 'Segoe UI', Roboto, Arial, sans-serif; }
    .sidebar .nav-item .nav-link span { font-weight: 600; }
    .card { border: 0; border-radius: 1rem; }
    .navbar-nav .nav-item .nav-link { font-weight: 600; }
    /* Guard: evitar que héroes fullscreen antiguos tapen el contenido */
    .container-fluid .login-hero, .container-fluid .hero, .container-fluid .fullscreen {
      position: static !important; min-height: auto !important; background: none !important;
    }
    .container-fluid .login-overlay { display: none !important; }
    .sb2-content > .container, 
.sb2-content > .container-fluid { 
  padding-left: 2rem; 
  padding-right: 2rem; 
}

    body, p, span, a, li, td, th, label, input, button, select, textarea {
  font-family: 'Nunito', system-ui, -apple-system, 'Segoe UI', Roboto, Arial, sans-serif !important;
  font-style: normal !important;
}


/* Ocultar el hamburguesa en desktop, pase lo que pase */
@media (min-width: 992px) {
  #sidebarToggleTop { display: none !important; }
}
/* Alinear hamburguesa con el avatar/Usuario en el topbar */
.topbar .topbar-btn,
.navbar.topbar .topbar-btn {
  height: 4.375rem;            /* igual que .topbar .nav-link */
  display: flex;
  align-items: center;          /* centra verticalmente el ícono */
  padding-top: 0 !important;
  padding-bottom: 0 !important;
  line-height: 1 !important;
}

/* Si por herencia aparece desalineado, fuerza el ícono también */
.topbar .topbar-btn i {
  line-height: 1;
  vertical-align: middle;
}

/* === Tablas unificadas === */
.table {
  background-color: #ffffff; /* fondo entero */
  border-collapse: collapse !important;
}

.table th,
.table td {
  text-align: center;       /* centra todo el contenido */
  vertical-align: middle;   /* centra verticalmente */
}



/* Efecto hover unificado */
.table-hover tbody tr:hover {
  background-color: #f2f2f2; /* gris suave al pasar el mouse */
}

/* Quitar cualquier franja de table-striped si se usa por error */
.table-striped tbody tr:nth-of-type(odd),
.table-striped tbody tr:nth-of-type(even) {
  background-color: #ffffff !important;
}

/* === TITULOS UNIFICADOS === */
.page-header-clean h1,
.page-header-clean .title,
h1.title,
h1.text-center,
h1.h4,
h1 {
  text-align: center !important;
  justify-content: center !important;
  display: flex;
  align-items: center;
  gap: 8px; /* separa icono y texto si los hay */
  
  margin: 0 0 15px;
}

/* Opcional: subtítulos h2/h3 también centrados */
h2, h3 {
  text-align: center !important;
}




</style>



<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body id="page-top">
  <div id="wrapper">
