<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }

// Toma el nombre completo desde sesión y define un fallback
$fullname = isset($_SESSION['fullname']) && trim($_SESSION['fullname']) !== ''
  ? trim($_SESSION['fullname'])
  : 'Cliente';

// Obtiene el primer nombre de forma segura (soporta espacios múltiples)
$parts = preg_split('/\s+/', $fullname);
$fullname = $parts && isset($parts[0]) ? $parts[0] : $fullname;

// Construye el saludo
$greeting = "¡Bienvenido, {$fullname}!";
?>
<div id="content-wrapper" class="d-flex flex-column">
  <div id="content">
    <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">

      <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
        <i class="fa fa-bars"></i>
      </button>

      <form class="d-none d-sm-inline-block form-inline mr-auto ml-md-3 my-2 my-md-0 mw-100 navbar-search">
        <div class="input-group">
          <input type="text" class="form-control bg-light border-0 small" placeholder="Buscar..."
            aria-label="Search" aria-describedby="basic-addon2">
          <div class="input-group-append">
            <button class="btn btn-primary" type="button">
              <i class="fas fa-search fa-sm"></i>
            </button>
          </div>
        </div>
      </form>

      <ul class="navbar-nav ml-auto">

        <!-- Píldora de saludo (a la izquierda del botón Tema) -->
        <li class="nav-item d-flex align-items-center mr-2">
          <span class="badge badge-pill badge-primary px-3 py-2">
            <?= htmlspecialchars($greeting, ENT_QUOTES, 'UTF-8') ?>
          </span>
        </li>

        <!-- Botón Tema -->
        <li class="nav-item d-flex align-items-center mr-2">
          <button id="themeToggle" class="btn btn-sm btn-light border" type="button" aria-label="Cambiar tema">
            <i class="fas fa-moon"></i> <span class="d-none d-sm-inline ml-1">Tema</span>
          </button>
        </li>

        <!-- Usuario / Dropdown -->
        <li class="nav-item dropdown no-arrow">
          <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <span class="mr-2 d-none d-lg-inline text-gray-600 small">
              <?= htmlspecialchars($fullname, ENT_QUOTES, 'UTF-8') ?>
            </span>
            <i class="fas fa-user-circle fa-2x text-gray-300"></i>
          </a>
          <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="userDropdown">
            <a class="dropdown-item" href="perfil.php">
              <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i> Perfil
            </a>
            <div class="dropdown-divider"></div>
            <a class="dropdown-item" href="../cerrar_session.php">
              <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i> Cerrar sesión
            </a>
          </div>
        </li>

      </ul>
    </nav>
