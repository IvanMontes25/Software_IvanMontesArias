



<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= htmlspecialchars($pageTitle ?? 'Panel Cliente') ?> · Cliente</title>

  <!-- SB Admin 2 core (Bootstrap 4 + custom) -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/startbootstrap-sb-admin-2@4.1.4/css/sb-admin-2.min.css" rel="stylesheet">
  <!-- Google Fonts: Nunito (mismos pesos que SB Admin 2) -->
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css?family=Nunito:200,300,400,600,700,800,900&display=swap" rel="stylesheet">

  <style>
    .sb2-content { padding: 1.25rem; }
    .table thead th { border-bottom: 2px solid rgba(0,0,0,.125); }
    .table td, .table th { border-right: 1px solid rgba(0,0,0,.08); }
    .table td:last-child, .table th:last-child { border-right: 0; }
    .table thead tr { background: #f8f9fc; }
    .card { border-radius: 1rem; }
    .navbar-nav .nav-link.active { font-weight: 600; }
 
  </style>

  <style>
  /* ===== THEME TOKENS ===== */
  :root {
    --bg: #f8f9fc;
    --bg-elev: #ffffff;
    --text: #1f2937;
    --text-muted: #6b7280;
    --border: #e5e7eb;

    --sidebar-bg: #1b2a4e;
    --sidebar-text: #d1d5db;
    --sidebar-active: #ffffff22;

    --topbar-bg: #ffffff;
    --topbar-text: #1f2937;

    --primary: #4e73df;
    --primary-700: #2e59d9;

    --table-stripe: #f3f4f6;
    --dropdown-bg: #ffffff;
  }

  html[data-theme="dark"] {
    --bg: #0b1220;
    --bg-elev: #0f172a;   /* slate-900 ~ */
    --text: #e5e7eb;
    --text-muted: #9ca3af;
    --border: #243042;

    --sidebar-bg: #0a1020;
    --sidebar-text: #cbd5e1;
    --sidebar-active: #ffffff12;

    --topbar-bg: #0f172a;
    --topbar-text: #e5e7eb;

    --primary: #0f172a;   /* tono accesible en dark */
    --primary-700: #07255fff;

    --table-stripe: #111827;
    --dropdown-bg: #0f172a;
  }

  /* ===== APP SURFACE ===== */
  body { background: var(--bg); color: var(--text); }

  .sb2-content, .content, .container-fluid { color: var(--text); }
  .card, .modal-content {
    background: var(--bg-elev);
    color: var(--text);
    border: 1px solid var(--border);
  }
  .card .text-muted, .small, .text-gray-600, .text-secondary { color: var(--text-muted) !important; }

  /* ===== NAV/TOPBAR ===== */
  .navbar, .topbar { background: var(--topbar-bg) !important; color: var(--topbar-text) !important; }
  .topbar .nav-link, .navbar .nav-link { color: var(--topbar-text) !important; }

  /* ===== SIDEBAR ===== */
  .sidebar { background: var(--sidebar-bg) !important; }
  .sidebar .nav-item .nav-link, .sidebar .sidebar-brand {
    color: var(--sidebar-text) !important;
  }
  .sidebar .nav-item.active, .sidebar .nav-item .nav-link:hover {
    background: var(--sidebar-active);
  }
  .sidebar .collapse-inner {
    background: transparent;
  }

  /* ===== PRIMARY (gradiente SB Admin 2) ===== */
  .bg-gradient-primary {
    background-image: linear-gradient(180deg, var(--primary) 10%, var(--primary-700) 100%) !important;
  }
  .text-primary { color: var(--primary) !important; }
  .btn-primary {
    background-color: var(--primary);
    border-color: var(--primary-700);
  }
  .btn-outline-primary {
    color: var(--primary);
    border-color: var(--primary);
  }

  /* ===== TABLAS / BORDES ===== */
  .table, .table thead th, .table td, .table th {
    border-color: var(--border) !important;
    color: var(--text);
  }
  .table-striped tbody tr:nth-of-type(odd) {
    background: var(--table-stripe);
  }

  /* ===== DROPDOWNS / MENÚS ===== */
  .dropdown-menu {
    background: var(--dropdown-bg);
    color: var(--text);
    border: 1px solid var(--border);
  }
  .dropdown-item { color: var(--text); }
  .dropdown-item:hover { background: var(--sidebar-active); }

  /* ===== INPUTS ===== */
  .form-control, .custom-select {
    background: var(--bg-elev);
    color: var(--text);
    border: 1px solid var(--border);
  }

  /* ===== FIX: Sidebar móvil (ancho seguro) ===== */
  @media (max-width: 768px) {
    body.sidebar-toggled .sidebar { width: 13rem; }
  }

body, .sidebar, .topbar {
  font-family: 'Nunito', sans-serif !important;
}
/* Sidebar en negrita */
.sidebar .nav-item .nav-link,
.sidebar .sidebar-brand,
.sidebar .collapse-inner .collapse-item,
.sidebar .nav-item .nav-link span {
  font-weight: 700 !important; /* o 600 si prefieres menos grueso */
}

</style>

</head>
<body id="page-top">

<?php if (!empty($_SESSION['aviso_membresia'])): ?>
<div class="alert alert-warning text-center mb-0">
    ⚠️ Tu membresía está vencida. Renueva para acceder a todos los beneficios.
</div>
<?php endif; ?>
  <div id="wrapper">
