<?php 
$page = isset($page) ? $page : ''; 
$nivel_cliente = "Intermedio";
$progreso_puntos = 75; 
?>

<ul class="navbar-nav sidebar sidebar-dark accordion" id="accordionSidebar">

    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="perfil.php">
        <div class="sidebar-brand-icon">
            <i class="fas fa-dumbbell logo-glow"></i>
        </div>
        <div class="sidebar-brand-text mx-3">Panel <span>Cliente</span></div>
    </a>

    <div class="sidebar-user-stats px-3 mb-3">
        <div class="d-flex justify-content-between mb-1 stats-text">
            <span class="small text-light opacity-75">Nivel: <?= $nivel_cliente ?></span>
            <span class="small text-cyan"><?= $progreso_puntos ?>%</span>
        </div>
        <div class="progress progress-sm" style="height: 6px; background: rgba(255,255,255,0.1); border-radius: 10px;">
            <div class="progress-bar bg-cyan progress-glow" role="progressbar" 
                 style="width: <?= $progreso_puntos ?>%"></div>
        </div>
    </div>

    <hr class="sidebar-divider my-0">

    <li class="nav-item <?= $page === 'perfil' ? 'active' : '' ?>">
        <a class="nav-link" href="perfil.php">
            <i class="fas fa-fw fa-user"></i>
            <span>Mi Perfil</span>
        </a>
    </li>

    <li class="nav-item <?= $page === 'notificaciones' ? 'active' : '' ?>">
        <a class="nav-link" href="recordatorio_cliente.php">
            <i class="fas fa-fw fa-bell"></i>
            <span>Mensajes</span>
            <span class="badge badge-pill badge-cyan ml-auto notification-badge">2</span>
        </a>
    </li>

    <li class="nav-item <?= $page === 'asistencias' ? 'active' : '' ?>">
        <a class="nav-link" href="asistencias.php">
            <i class="fas fa-fw fa-calendar-check"></i>
            <span>Asistencias</span>
        </a>
    </li>

    <li class="nav-item <?= $page === 'clases' ? 'active' : '' ?>">
        <a class="nav-link" href="clases.php">
            <i class="fas fa-fw fa-calendar-alt"></i>
            <span>Reservar Clase</span>
        </a>
    </li>

    <li class="nav-item <?= $page === 'mis_reservas' ? 'active' : '' ?>">
        <a class="nav-link" href="mis_reservas.php">
            <i class="fas fa-fw fa-bookmark"></i>
            <span>Mis Reservas</span>
        </a>
    </li>

    <li class="nav-item <?= $page === 'logros' ? 'active' : '' ?>">
        <a class="nav-link" href="logros.php">
            <i class="fas fa-fw fa-trophy"></i>
            <span>Mis Logros</span>
        </a>
    </li>

    <li class="nav-item <?= $page === 'pagos' ? 'active' : '' ?>">
        <a class="nav-link" href="membresia_pagos.php">
            <i class="fas fa-fw fa-wallet"></i>
            <span>Membresía</span>
        </a>
    </li>

    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseConfig">
            <i class="fas fa-fw fa-cog"></i>
            <span>Ajustes</span>
            
        </a>
        <div id="collapseConfig" class="collapse" data-parent="#accordionSidebar">
            <div class="bg-sub-menu py-2 collapse-inner rounded">
                <a class="collapse-item" href="cambiar_pass.php">Seguridad</a>
                <a class="collapse-item" href="preferencias.php">Preferencias</a>
                <a class="collapse-item text-danger" href="logout.php">Salir</a>
            </div>
        </div>
    </li>

    <hr class="sidebar-divider d-none d-md-block">

    <li class="nav-item <?= $page === 'soporte' ? 'active' : '' ?>">
        <a class="nav-link" href="soporte.php">
            <i class="fas fa-fw fa-life-ring"></i>
            <span>Soporte</span>
        </a>
    </li>

    <div class="text-center d-none d-md-inline mt-4">
        <button class="rounded-circle border-0 shadow-sm" id="sidebarToggle"></button>
    </div>
</ul>

<div class="mobile-bottom-nav d-md-none">
    
    <a href="perfil.php" class="nav-item-mobile <?= $page === 'perfil' ? 'active' : '' ?>">
        <i class="fas fa-user"></i>
        <span>Mi Perfil</span>
    </a>

    <a href="recordatorio_cliente.php" class="nav-item-mobile <?= $page === 'notificaciones' ? 'active' : '' ?>">
        <div class="icon-wrapper">
            <i class="fas fa-bell"></i>
            <span class="badge-dot"></span>
        </div>
        <span>Mensajes</span>
    </a>

    <a href="asistencias.php" class="nav-item-mobile <?= $page === 'asistencias' ? 'active' : '' ?>">
        <i class="fas fa-calendar-check"></i>
        <span>Asistencias</span>
    </a>

    <a href="clases.php" class="nav-item-mobile <?= $page === 'clases' || $page === 'mis_reservas' ? 'active' : '' ?>">
        <i class="fas fa-calendar-alt"></i>
        <span>Clases</span>
    </a>

    <a href="logros.php" class="nav-item-mobile <?= $page === 'logros' ? 'active' : '' ?>">
        <i class="fas fa-trophy"></i>
        <span>Logros</span>
    </a>

    <a href="membresia_pagos.php" class="nav-item-mobile <?= $page === 'pagos' ? 'active' : '' ?>">
        <i class="fas fa-wallet"></i>
        <span>Pagos</span>
    </a>

    <a href="soporte.php" class="nav-item-mobile <?= $page === 'soporte' ? 'active' : '' ?>">
        <i class="fas fa-life-ring"></i>
        <span>Soporte</span>
    </a>

    <a href="#" class="nav-item-mobile" data-toggle="collapse" data-target="#collapseConfig">
        <i class="fas fa-cog"></i>
        <span>Ajustes</span>
    </a>
    
    <div style="min-width: 10px;"></div>
</div>


<style>
/* =========================================
   SISTEMA DE COLORES
========================================= */
:root {
    --sidebar-bg: #0f172a;
    --sidebar-text: #94a3b8;
    --sidebar-accent: #22d3ee;
    --sidebar-hover-bg: rgba(255, 255, 255, 0.08);
    --sidebar-active-bg: rgba(34, 211, 238, 0.15);
    --divider-color: rgba(255, 255, 255, 0.05);
}

#accordionSidebar {
    background-color: var(--sidebar-bg) !important;
    background-image: linear-gradient(180deg, #0f172a 0%, #1e293b 100%) !important;
    border-right: 1px solid var(--divider-color);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

/* =========================================
   ESTILOS DE LINKS (ESCRITORIO)
========================================= */
#accordionSidebar .nav-link {
    color: var(--sidebar-text) !important;
    padding: 0.85rem 1rem;
    margin: 0.2rem 0.6rem;
    border-radius: 12px;
    display: flex;
    align-items: center;
    position: relative;
    transition: all 0.25s ease !important;
}

#accordionSidebar .nav-link i:not(.arrow-icon) {
    width: 1.5rem;
    margin-right: 0.8rem;
    font-size: 1.1rem;
    text-align: center;
    transition: transform 0.3s ease;
}

/* Hover Desktop */
#accordionSidebar .nav-link:hover {
    background-color: var(--sidebar-hover-bg) !important;
    color: #fff !important;
}

#accordionSidebar .nav-link:hover i:not(.arrow-icon) {
    transform: scale(1.15);
    color: var(--sidebar-accent);
}

/* Activo Desktop */
#accordionSidebar .nav-item.active > .nav-link {
    background: var(--sidebar-active-bg) !important;
    color: var(--sidebar-accent) !important;
}

/* Indicador lateral Desktop */
#accordionSidebar .nav-link::before {
    content: '';
    position: absolute;
    left: 0;
    width: 0;
    height: 40%;
    background-color: var(--sidebar-accent);
    border-radius: 0 4px 4px 0;
    transition: width 0.3s ease;
}

#accordionSidebar .nav-link:hover::before {
    width: 4px;
}

/* =========================================
   SIDEBAR COLAPSADO (ESCRITORIO - TOGGLED)
========================================= */
.sidebar.toggled {
    width: 4.8rem !important;
}

.sidebar.toggled .sidebar-brand-text,
.sidebar.toggled .sidebar-user-stats,
.sidebar.toggled .nav-link span,
.sidebar.toggled .arrow-icon,
.sidebar.toggled .notification-badge {
    display: none !important;
}

.sidebar.toggled .nav-item .nav-link {
    width: 3.2rem !important;
    margin: 0.5rem auto !important;
    padding: 0.8rem 0 !important;
    justify-content: center;
}

.sidebar.toggled .nav-item .nav-link i {
    margin-right: 0 !important;
    font-size: 1.25rem !important;
}

/* =========================================
   SUBMENÚS Y DETALLES
========================================= */
.arrow-icon {
    font-size: 0.65rem;
    transition: transform 0.3s ease-in-out !important;
    opacity: 0.6;
}

#accordionSidebar .nav-link:not(.collapsed) .arrow-icon {
    transform: rotate(90deg) !important;
    color: var(--sidebar-accent) !important;
    opacity: 1;
}

.bg-sub-menu {
    background: rgba(255, 255, 255, 0.03) !important;
    margin: 0 0.8rem 0.5rem 2.8rem;
    border-left: 1px solid var(--divider-color);
}

.collapse-item:hover {
    padding-left: 1.3rem !important;
    color: var(--sidebar-accent) !important;
}

.logo-glow {
    color: var(--sidebar-accent);
    filter: drop-shadow(0 0 8px rgba(34, 211, 238, 0.5));
}

.text-cyan { color: var(--sidebar-accent) !important; }
.bg-cyan { background-color: var(--sidebar-accent) !important; }
.badge-cyan { background: #0891b2; color: white; }

#sidebarToggle {
    background-color: rgba(255,255,255,0.05) !important;
    color: var(--sidebar-text) !important;
}

#sidebarToggle:hover {
    color: var(--sidebar-accent) !important;
    background-color: rgba(255,255,255,0.1) !important;
}

/* =========================================
   VISTA MÓVIL (APP STYLE) - CORREGIDO
========================================= */
/* =========================================
   VISTA MÓVIL (APP STYLE) - VERSIÓN SCROLL
========================================= */
@media (max-width: 768px) {
    /* Ocultamos el Sidebar y Topbar toggle original */
    #accordionSidebar, #sidebarToggleTop {
        display: none !important;
    }

    /* Ajuste de contenido para no tapar el final */
    #content-wrapper {
        margin-left: 0 !important;
        padding-bottom: 85px !important; 
    }

    /* CONTENEDOR DE LA BARRA (Habilitar Scroll) */
    .mobile-bottom-nav {
        position: fixed;
        bottom: 0;
        left: 0;
        right: 0;
        height: 70px; /* Un poco más alto para comodidad */
        background: rgba(15, 23, 42, 0.98); 
        backdrop-filter: blur(12px);
        border-top: 1px solid rgba(255, 255, 255, 0.1);
        z-index: 2000;
        padding-bottom: env(safe-area-inset-bottom);
        box-shadow: 0 -5px 20px rgba(0,0,0,0.2);

        /* MÁGICA: Habilita el scroll horizontal */
        display: flex !important;
        justify-content: flex-start; /* Alinea a la izquierda */
        overflow-x: auto; /* Permite deslizar */
        -webkit-overflow-scrolling: touch; /* Suavidad en iPhone */
        
        /* Ocultar la barra de scroll fea */
        scrollbar-width: none; /* Firefox */
        -ms-overflow-style: none;  /* IE 10+ */
    }

    /* Ocultar barra de scroll en Chrome/Safari */
    .mobile-bottom-nav::-webkit-scrollbar { 
        display: none; 
    }

    /* BOTONES INDIVIDUALES */
    .nav-item-mobile {
        /* Tamaño fijo mínimo para que no se aplasten */
        min-width: 75px; 
        flex: 0 0 auto; /* No encoger, no estirar */
        
        height: 100%;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        color: rgba(255, 255, 255, 0.5);
        text-decoration: none !important;
        transition: all 0.2s ease;
    }

    /* Iconos */
    .nav-item-mobile i {
        font-size: 1.3rem !important;
        margin-bottom: 5px;
        background: rgba(255,255,255,0.05); /* Pequeño fondo circular opcional */
        width: 35px;
        height: 35px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 12px;
        transition: all 0.2s;
    }

    .nav-item-mobile span {
        font-size: 0.6rem !important;
        letter-spacing: 0.3px;
        white-space: nowrap;
    }

    /* Estado Activo */
    .nav-item-mobile.active {
        color: var(--sidebar-accent) !important;
    }

    .nav-item-mobile.active i {
        background: rgba(34, 211, 238, 0.15); /* Fondo iluminado al activo */
        color: var(--sidebar-accent);
        transform: translateY(-2px);
        box-shadow: 0 4px 10px rgba(34, 211, 238, 0.2);
    }

    /* Badge */
    .icon-wrapper { position: relative; }
    .badge-dot {
        position: absolute;
        top: 2px;
        right: 2px;
        width: 8px;
        height: 8px;
        background: #ef4444; /* Rojo para notificaciones */
        border-radius: 50%;
        border: 1px solid #0f172a;
        z-index: 10;
    }
}

/* En escritorio ocultamos la barra inferior */
@media (min-width: 769px) {
    .mobile-bottom-nav {
        display: none !important;
    }
}
</style>