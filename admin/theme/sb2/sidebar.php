<?php
  // Cargar sistema de permisos
  if (!function_exists('puede')) {
      require_once __DIR__ . '/../../../core/roles.php';
  }

  // Helpers para estado activo/abierto
  $isClients = isset($page) && in_array($page, [
      'members','members-entry','members-update','members-remove',
      'member-status','vencimientos','manage-customer-progress','logros'
  ]);

  $isOper = isset($page) && in_array($page, [
      'attendance','view-attendance','qr_admin','list-equip','add-equip'
  ]);

  $isFinance = isset($page) && in_array($page, [
      'payment','planes','vencimientos'
  ]);

  $isReport = isset($page) && in_array($page, [
      'chart','member-repo','c-p-r'
  ]);

  $isAdmin = isset($page) && in_array($page, [
      'staff-management','announcement','logros','auditoria'
  ]);

  $isClases = isset($page) && in_array($page, [
      'mis-clases','clase-agendar','clase-inscritos','clase-tipos'
  ]);
?>

<ul class="navbar-nav sidebar accordion" id="accordionSidebar">

<?php if (puede('inbox')): ?>

<?php endif; ?>

  <a class="sidebar-brand d-flex align-items-center justify-content-center" href="dashboard.php">
    <div class="sidebar-brand-icon rotate-n-15">
      <i class="fas fa-dumbbell logo-glow"></i>
    </div>
    <div class="sidebar-brand-text mx-3">Gym <span>System</span></div>
  </a>

  <hr class="sidebar-divider my-0">
<li class="nav-item <?= ($page=='inbox')?'active':'' ?>">
    <a class="nav-link" href="inbox.php">
        <i class="fas fa-inbox"></i>
        <span>Bandeja</span>
    </a>
</li>
  <!-- Dashboard: todos los roles -->
  <li class="nav-item <?= (isset($page) && $page==='dashboard') ? 'active' : '' ?>">
    <a class="nav-link" href="dashboard.php">
      <i class="fas fa-fw fa-tachometer-alt"></i>
      <span>Dashboard</span>
    </a>
  </li>



<?php if (puede('clientes')): ?>
  <div class="sidebar-heading">Clientes</div>
  <li class="nav-item <?= $isClients ? 'active' : '' ?>">
    <a class="nav-link <?= $isClients ? '' : 'collapsed' ?>" href="#" data-toggle="collapse" data-target="#collapseClientes">
      <i class="fas fa-users"></i>
      <span>Adm. de Clientes</span>
      <i class="fas fa-chevron-right arrow-icon"></i>
    </a>
    <div id="collapseClientes" class="collapse <?= $isClients ? 'show' : '' ?>" data-parent="#accordionSidebar">
      <div class="bg-sub-menu py-2 collapse-inner rounded">
        <h6 class="collapse-header">Gestión:</h6>
        <a class="collapse-item <?= ($page==='members-entry') ? 'active' : '' ?>" href="cliente_entry.php">Inscribir Cliente</a>
        <a class="collapse-item <?= ($page==='members') ? 'active' : '' ?>" href="clientes.php">Lista de Clientes</a>
      </div>
    </div>
  </li>
<?php endif; ?>

<?php if (puede('asistencias') || puede('equipos')): ?>
  <div class="sidebar-heading">Operación diaria</div>
  <li class="nav-item <?= $isOper ? 'active' : '' ?>">
    <a class="nav-link <?= $isOper ? '' : 'collapsed' ?>" href="#" data-toggle="collapse" data-target="#collapseOperacion">
      <i class="fas fa-calendar-check"></i>
      <span>Asistencias & Operación</span>
      <i class="fas fa-chevron-right arrow-icon"></i>
    </a>
    <div id="collapseOperacion" class="collapse <?= $isOper ? 'show' : '' ?>" data-parent="#accordionSidebar">
      <div class="bg-sub-menu py-2 collapse-inner rounded">
      <?php if (puede('asistencias')): ?>
        <h6 class="collapse-header">Asistencias:</h6>
        <a class="collapse-item <?= ($page==='attendance') ? 'active' : '' ?>" href="asistencias.php">Registro</a>
        <a class="collapse-item <?= ($page==='qr_admin') ? 'active' : '' ?>" href="qr_recepcion.php">QR Recepción</a>
      <?php endif; ?>
      <?php if (puede('equipos')): ?>
        <h6 class="collapse-header">Equipos:</h6>
        <a class="collapse-item <?= ($page==='list-equip') ? 'active' : '' ?>" href="equipos.php">Listar Equipos</a>
        <a class="collapse-item <?= ($page==='add-equip') ? 'active' : '' ?>" href="registro_equipo.php">Registrar Equipo</a>
      <?php endif; ?>
      </div>
    </div>
  </li>
<?php endif; ?>

<?php if (puede('clases')): ?>
  <div class="sidebar-heading">Clases</div>
  <li class="nav-item <?= $isClases ? 'active' : '' ?>">
    <a class="nav-link <?= $isClases ? '' : 'collapsed' ?>" href="#" data-toggle="collapse" data-target="#collapseClases">
      <i class="fas fa-calendar-alt"></i>
      <span>Gestión de Clases</span>
      <i class="fas fa-chevron-right arrow-icon"></i>
    </a>
    <div id="collapseClases" class="collapse <?= $isClases ? 'show' : '' ?>" data-parent="#accordionSidebar">
      <div class="bg-sub-menu py-2 collapse-inner rounded">
        <h6 class="collapse-header">Clases:</h6>
        <a class="collapse-item <?= ($page==='mis-clases') ? 'active' : '' ?>" href="mis_clases.php">Clases Agendadas</a>
        <a class="collapse-item <?= ($page==='clase-agendar') ? 'active' : '' ?>" href="clase_agendar.php">Agendar Clase</a>
        <?php if ($_SESSION['rol'] === 'admin'): ?>
        <a class="collapse-item <?= ($page==='clase-tipos') ? 'active' : '' ?>" href="clase_tipos.php">Tipos de Clase</a>
        <?php endif; ?>
      </div>
    </div>
  </li>
<?php endif; ?>

<?php if (puede('pagos')): ?>
  <div class="sidebar-heading">Pagos</div>
  <li class="nav-item <?= $isFinance ? 'active' : '' ?>">
    <a class="nav-link <?= $isFinance ? '' : 'collapsed' ?>" href="#" data-toggle="collapse" data-target="#collapseFinanzas">
      <i class="fas fa-dollar-sign"></i>
      <span>Membresías y Pagos</span>
      <i class="fas fa-chevron-right arrow-icon"></i>
    </a>
    <div id="collapseFinanzas" class="collapse <?= $isFinance ? 'show' : '' ?>" data-parent="#accordionSidebar">
      <div class="bg-sub-menu py-2 collapse-inner rounded">
        <a class="collapse-item <?= ($page==='payment') ? 'active' : '' ?>" href="pagos.php">Pagos</a>
        <a class="collapse-item <?= ($page==='vencimientos') ? 'active' : '' ?>" href="vencimientos.php">Deudas / Vencimientos</a>
      </div>
    </div>
  </li>
<?php endif; ?>

<?php if (puede('reportes')): ?>
  <div class="sidebar-heading">Reportes</div>
  <li class="nav-item <?= $isReport ? 'active' : '' ?>">
    <a class="nav-link <?= $isReport ? '' : 'collapsed' ?>" href="#" data-toggle="collapse" data-target="#collapseReportes">
      <i class="fas fa-file-alt"></i>
      <span>Reportes</span>
      <i class="fas fa-chevron-right arrow-icon"></i>
    </a>
    <div id="collapseReportes" class="collapse <?= $isReport ? 'show' : '' ?>" data-parent="#accordionSidebar">
      <div class="bg-sub-menu py-2 collapse-inner rounded">
        <a class="collapse-item <?= ($page==='chart') ? 'active' : '' ?>" href="reportes.php">Reporte Gráfico</a>
        <a class="collapse-item <?= ($page==='member-repo') ? 'active' : '' ?>" href="reporte_cliente.php">Informe de Clientes</a>
        <a class="collapse-item <?= ($page==='member-repo') ? 'active' : '' ?>" href="reportes_automatizacion.php">Reporte Automatizaciones</a>
      </div>
    </div>
  </li>
<?php endif; ?>

<?php if (puede('administracion')): ?>
  <hr class="sidebar-divider">

  <div class="sidebar-heading">Administración</div>
  <!-- PORTAL WEB -->
  <li class="nav-item <?= (isset($page) && $page==='portal') ? 'active' : '' ?>">
    <a class="nav-link" href="portal_admin.php">
      <i class="fas fa-globe"></i>
      <span>Portal Web</span>
    </a>
  </li>
  <li class="nav-item <?= $isAdmin ? 'active' : '' ?>">
    <a class="nav-link <?= $isAdmin ? '' : 'collapsed' ?>" href="#" data-toggle="collapse" data-target="#collapseAdmin">
      <i class="fas fa-user-cog"></i>
      <span>Admn. General</span>
      <i class="fas fa-chevron-right arrow-icon"></i>
    </a>
    <div id="collapseAdmin" class="collapse <?= $isAdmin ? 'show' : '' ?>" data-parent="#accordionSidebar">
      <div class="bg-sub-menu py-2 collapse-inner rounded">
        <a class="collapse-item <?= ($page==='planes') ? 'active' : '' ?>" href="planes.php">Membresías</a>
        <a class="collapse-item <?= ($page==='staff-management') ? 'active' : '' ?>" href="staffs.php">Personal</a>
        <a class="collapse-item <?= ($page==='announcement') ? 'active' : '' ?>" href="publicaciones.php">Publicaciones</a>
        <a class="collapse-item <?= ($page==='logros') ? 'active' : '' ?>" href="logros.php">Logros</a>
        <a class="collapse-item <?= ($page==='auditoria') ? 'active' : '' ?>" href="auditoria.php">
            <i class="fas fa-shield-alt mr-1"></i>Auditoría
        </a>
      </div>
    </div>
  </li>
<?php endif; ?>

  <hr class="sidebar-divider d-none d-md-block">

  <div class="text-center d-none d-md-inline mt-4">
    <button class="rounded-circle border-0 shadow-sm" id="sidebarToggle"></button>
  </div>
</ul>

<!-- BARRA INFERIOR MÓVIL (solo módulos permitidos) -->
<div class="mobile-bottom-nav d-md-none">
    
    <a href="dashboard.php" class="nav-item-mobile <?= (isset($page) && $page==='dashboard') ? 'active' : '' ?>">
        <i class="fas fa-tachometer-alt"></i>
        <span>Inicio</span>
    </a>

    <?php if (puede('clientes')): ?>
    <a href="clientes.php" class="nav-item-mobile <?= $isClients ? 'active' : '' ?>">
        <i class="fas fa-users"></i>
        <span>Clientes</span>
    </a>
    <?php endif; ?>

    <?php if (puede('asistencias')): ?>
    <a href="asistencias.php" class="nav-item-mobile <?= $isOper ? 'active' : '' ?>">
        <i class="fas fa-calendar-check"></i>
        <span>Asistencias</span>
    </a>

    <a href="qr_recepcion.php" class="nav-item-mobile <?= ($page==='qr_admin') ? 'active' : '' ?>">
        <i class="fas fa-qrcode"></i>
        <span>Lector QR</span>
    </a>
    <?php endif; ?>

    <?php if (puede('clases')): ?>
    <a href="mis_clases.php" class="nav-item-mobile <?= $isClases ? 'active' : '' ?>">
        <i class="fas fa-calendar-alt"></i>
        <span>Clases</span>
    </a>
    <?php endif; ?>

    <?php if (puede('pagos')): ?>
    <a href="pagos.php" class="nav-item-mobile <?= $isFinance ? 'active' : '' ?>">
        <i class="fas fa-dollar-sign"></i>
        <span>Pagos</span>
    </a>
    <?php endif; ?>

    <?php if (puede('reportes')): ?>
    <a href="reportes.php" class="nav-item-mobile <?= $isReport ? 'active' : '' ?>">
        <i class="fas fa-file-alt"></i>
        <span>Reportes</span>
    </a>
    <?php endif; ?>

    <?php if (puede('administracion')): ?>
    <a href="staffs.php" class="nav-item-mobile <?= $isAdmin ? 'active' : '' ?>">
        <i class="fas fa-user-cog"></i>
        <span>Staff</span>
    </a>
    <?php endif; ?>

    <div style="min-width: 15px;"></div>
</div>


<style>
:root {
    --sidebar-bg: #f8fafc;
    --sidebar-text: #64748b;
    --sidebar-accent: #0ea5e9;
    --sidebar-hover-bg: #f1f5f9;
    --sidebar-active-bg: #ffffff;
    --sidebar-active-shadow: 0 4px 12px rgba(0,0,0,0.08);
    --divider-color: rgba(0,0,0,0.06);
}

body.dark-mode {
    --sidebar-bg: #111827; 
    --sidebar-text: #94a3b8;
    --sidebar-accent: #38bdf8;
    --sidebar-hover-bg: #1f2937;
    --sidebar-active-bg: #1f2937;
    --sidebar-active-shadow: 0 4px 20px rgba(0,0,0,0.4);
    --divider-color: rgba(255,255,255,0.05);
}

#accordionSidebar {
    background-color: var(--sidebar-bg) !important;
    border-right: 1px solid var(--divider-color);
    transition: width 0.18s ease-in-out;
}

#accordionSidebar .nav-link {
    color: var(--sidebar-text) !important;
    padding: 0.85rem 1rem; 
    margin: 0.2rem 0.5rem;  
    border-radius: 12px;
    display: flex;
    align-items: center;
    transition: all 0.2s ease;
    width: auto;
    position: relative;
    overflow: hidden;
}

#accordionSidebar .nav-link i:not(.arrow-icon) {
    width: 1.5rem;
    margin-right: 0.8rem;
    font-size: 1.1rem;
    text-align: center;
}

#accordionSidebar .nav-item.active > .nav-link {
    background: var(--sidebar-active-bg);
    color: var(--sidebar-accent) !important;
    box-shadow: var(--sidebar-active-shadow);
}

#accordionSidebar .nav-item.active > .nav-link i {
    color: var(--sidebar-accent);
}

#accordionSidebar .nav-link:hover {
    background-color: rgba(0,0,0,0.05) !important;
    color: var(--sidebar-accent) !important;
}

body.dark-mode #accordionSidebar .nav-link:hover {
    background-color: rgba(255,255,255,0.05) !important;
}

#accordionSidebar .nav-link::before {
    content: '';
    position: absolute;
    left: -12px;
    width: 4px;
    height: 40%;
    background-color: var(--sidebar-accent);
    border-radius: 0 4px 4px 0;
    transition: all 0.3s ease;
    opacity: 0;
}

#accordionSidebar .nav-link:hover::before {
    left: 0;
    opacity: 1;
}

.sidebar.toggled { width: 90px !important; }

.sidebar.toggled .sidebar-brand-text,
.sidebar.toggled .sidebar-heading,
.sidebar.toggled .nav-link span,
.sidebar.toggled .arrow-icon,
.sidebar.toggled .collapse { display: none !important; }

.sidebar.toggled .nav-item { width: 100%; }

.sidebar.toggled .nav-item .nav-link {
    margin: 0.6rem auto !important;
    padding: 0.9rem 0 !important;
    justify-content: center !important;
    width: 60px;
    border-radius: 14px;
}

.sidebar.toggled .nav-item .nav-link i:not(.arrow-icon) {
    margin: 0 !important;
    font-size: 1.3rem;
}

.sidebar.toggled .nav-link:hover { transform: scale(1.08); }

.arrow-icon {
    margin-left: auto;
    font-size: 0.7rem;
    transition: transform 0.3s ease !important;
}

#accordionSidebar .nav-link:not(.collapsed) .arrow-icon {
    transform: rotate(90deg) !important;
    color: var(--sidebar-accent);
}

.bg-sub-menu {
    background: transparent !important;
    border-left: 2px solid var(--divider-color);
    margin-left: 2.5rem;
}

.collapse-item {
    color: var(--sidebar-text) !important;
    display: block;
    padding: 0.5rem 1rem !important;
    text-decoration: none;
    transition: 0.2s;
}

.collapse-item:hover {
    color: var(--sidebar-accent) !important;
    padding-left: 1.3rem !important;
}

.logo-glow {
    color: var(--sidebar-accent);
    filter: drop-shadow(0 0 5px rgba(14, 165, 233, 0.4));
    animation: pulse 3s infinite;
}

@keyframes pulse {
    0% { filter: drop-shadow(0 0 2px rgba(14, 165, 233, 0.2)); }
    50% { filter: drop-shadow(0 0 8px rgba(14, 165, 233, 0.5)); }
    100% { filter: drop-shadow(0 0 2px rgba(14, 165, 233, 0.2)); }
}

@media (max-width: 768px) {
    #accordionSidebar, #sidebarToggleTop { display: none !important; }
    #content-wrapper { margin-left: 0 !important; padding-bottom: 85px !important; }

    .mobile-bottom-nav {
        position: fixed; bottom: 0; left: 0; right: 0; height: 70px;
        background: #ffffff; border-top: 1px solid rgba(0,0,0,0.05);
        z-index: 2000; padding-bottom: env(safe-area-inset-bottom);
        box-shadow: 0 -5px 20px rgba(0,0,0,0.05);
        display: flex !important; justify-content: flex-start;
        overflow-x: auto; -webkit-overflow-scrolling: touch;
        scrollbar-width: none; -ms-overflow-style: none;
    }

    body.dark-mode .mobile-bottom-nav {
        background: #111827; border-top: 1px solid rgba(255,255,255,0.05);
    }

    .mobile-bottom-nav::-webkit-scrollbar { display: none; }

    .nav-item-mobile {
        min-width: 75px; flex: 0 0 auto; height: 100%;
        display: flex; flex-direction: column; align-items: center;
        justify-content: center; color: var(--sidebar-text);
        text-decoration: none !important; transition: all 0.2s ease;
    }

    .nav-item-mobile i {
        font-size: 1.25rem !important; margin-bottom: 5px;
        background: transparent; width: 38px; height: 38px;
        display: flex; align-items: center; justify-content: center;
        border-radius: 12px; transition: all 0.2s;
    }

    .nav-item-mobile span {
        font-size: 0.6rem !important; letter-spacing: 0.3px;
        white-space: nowrap; font-weight: 500;
    }

    .nav-item-mobile.active { color: var(--sidebar-accent) !important; }

    .nav-item-mobile.active i {
        background: rgba(14, 165, 233, 0.1);
        color: var(--sidebar-accent); transform: translateY(-2px);
    }
    
    body.dark-mode .nav-item-mobile.active i {
        background: rgba(56, 189, 248, 0.15);
    }
}

@media (min-width: 769px) {
    .mobile-bottom-nav { display: none !important; }
}

.sidebar .nav-link[data-toggle="collapse"]::after,
.sidebar .nav-link.collapsed::after {
    content: none !important; display: none !important;
}

@media (max-width: 768px) {
    html, body { height: 100%; overflow-x: hidden; }
    body { position: relative; }

    #content-wrapper, #content, .container-fluid, .card, .table-responsive {
        transform: none !important;
    }

    .mobile-bottom-nav {
        position: fixed !important; bottom: 0; left: 0; right: 0;
        z-index: 9999 !important; will-change: transform;
        backface-visibility: hidden; transform: translateZ(0);
    }
}
</style>