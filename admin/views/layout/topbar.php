<div id="content-wrapper" class="d-flex flex-column">
<div id="content">

<nav class="navbar navbar-expand navbar-light topbar mb-4 static-top custom-topbar">

    <!-- Sidebar Toggle -->
    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3 custom-toggle">
        <i class="fa fa-bars"></i>
    </button>

    <!-- SALUDO -->
    <div class="d-none d-sm-inline-block mr-auto ml-md-3 my-2 my-md-0">
        <h1 class="greeting-title mb-0">
            <?php 
                $hora = date('H');
                if ($hora < 12) echo "¡Buenos días! ☀️";
                elseif ($hora < 18) echo "¡Buenas tardes! ⚡";
                else echo "¡Buenas noches! 🌙";
            ?>
        </h1>
        <span class="greeting-sub">
            Panel de control · Gym Body Training
        </span>
    </div>

    <ul class="navbar-nav ml-auto align-items-center">

        <!-- BOTÓN DARK MODE -->
        <li class="nav-item mr-3 d-flex align-items-center">
            <button id="themeToggle" class="theme-toggle-btn">
                <i class="fas fa-moon"></i>
            </button>
        </li>

        <!-- PERFIL -->
        <li class="nav-item dropdown no-arrow">
            <a class="nav-link dropdown-toggle profile-trigger d-flex align-items-center"
               href="#"
               id="userDropdown"
               role="button"
               data-toggle="dropdown">

                <div class="profile-wrapper d-flex align-items-center">

                    <div class="profile-text mr-3 text-right">
                        <span class="profile-name">
                            <?= htmlspecialchars($_SESSION['fullname'] ?? $_SESSION['username'] ?? 'Admin'); ?>
                        </span>
                        <span class="profile-role"><?= ucfirst($_SESSION['rol'] ?? 'Admin') ?></span>
                    </div>

                    <div class="profile-avatar">
                        <img src="https://i.pravatar.cc/100?img=12">
                        <span class="status-dot"></span>
                    </div>

                </div>
            </a>

            <!-- DROPDOWN -->
            <div class="dropdown-menu dropdown-menu-right custom-dropdown shadow-lg">

                <div class="dropdown-header text-center py-4">
                    <img class="rounded-circle mb-3"
                         src="https://i.pravatar.cc/100?img=12"
                         width="70">
                    <h6 class="font-weight-bold mb-0">
                        <?= $_SESSION['username'] ?? 'Usuario'; ?>
                    </h6>
                    <small class="text-muted"><?= ucfirst($_SESSION['rol'] ?? 'Admin') ?></small>
                </div>

                <div class="dropdown-divider"></div>

                <a class="dropdown-item" href="#">
                    <i class="fas fa-user mr-2"></i> Mi Perfil
                </a>

                <a class="dropdown-item" href="#">
                    <i class="fas fa-cogs mr-2"></i> Configuración
                </a>

                <div class="dropdown-divider"></div>

                <a class="dropdown-item logout-item" href="../cerrar_sesion.php">
                    <i class="fas fa-sign-out-alt mr-2"></i> Cerrar sesión
                </a>

            </div>
        </li>

    </ul>
</nav>
<style>
/* ===============================
   VARIABLES LIGHT
=============================== */
:root {
    --bg-body: #f8fafc;
    --bg-topbar: linear-gradient(180deg, #ffffff 0%, #f1f5f9 100%);
    --text-primary: #0f172a;
    --text-secondary: #64748b;
    --border-color: #e2e8f0;
    --dropdown-bg: #ffffff;
}

/* ===============================
   VARIABLES DARK
=============================== */
body.dark-mode {
    --bg-body: #0f172a;
    --bg-topbar: linear-gradient(180deg, #0f172a 0%, #1e293b 100%);
    --text-primary: #f1f5f9;
    --text-secondary: #94a3b8;
    --border-color: #1f2937;
    --dropdown-bg: #1e293b;
}

/* ===============================
   GENERAL
=============================== */
body {
    background: var(--bg-body);
    transition: background 0.3s ease, color 0.3s ease;
}

/* ===============================
   TOPBAR
=============================== */
.custom-topbar {
    position: relative;
    z-index: 2000;
    background: var(--bg-topbar);
    border-bottom: 1px solid var(--border-color);
    padding: 0.8rem 1.5rem;
    transition: 0.3s ease;
}

/* ===============================
   TEXTOS
=============================== */
.greeting-title {
    font-weight: 700;
    font-size: 1.1rem;
    color: var(--text-primary);
}

.greeting-sub {
    font-size: 0.8rem;
    color: var(--text-secondary);
}

.profile-name {
    font-weight: 600;
    font-size: 0.9rem;
    color: var(--text-primary);
}

/* ===============================
   PERFIL
=============================== */
.profile-wrapper {
    gap: 12px;
}

.profile-role {
    display: inline-block;
    font-size: 0.65rem;
    padding: 4px 10px;
    border-radius: 50px;
    background: linear-gradient(135deg,#22d3ee,#3b82f6);
    color: white;
    margin-top: 4px;
}

.profile-avatar {
    position: relative;
    width: 42px;
    height: 42px;
}

.profile-avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    border-radius: 50%;
    border: 2px solid var(--border-color);
    transition: 0.3s;
}

.profile-avatar:hover img {
    transform: scale(1.08);
}

.status-dot {
    position: absolute;
    bottom: 3px;
    right: 3px;
    width: 10px;
    height: 10px;
    background: #22c55e;
    border: 2px solid var(--bg-body);
    border-radius: 50%;
}

/* ===============================
   DROPDOWN
=============================== */
.custom-dropdown {
    background: var(--dropdown-bg);
    border-radius: 16px;
    border: 1px solid var(--border-color);
    overflow: hidden;
    animation: fadeIn 0.2s ease;
    z-index: 3000 !important;
}

.custom-dropdown .dropdown-item {
    font-size: 0.85rem;
    color: var(--text-primary);
    transition: 0.2s;
}

.custom-dropdown .dropdown-item:hover {
    background: rgba(59,130,246,0.1);
    padding-left: 1.6rem;
}

.logout-item {
    color: #ef4444;
    font-weight: 600;
}

/* ===============================
   BOTÓN DARK MODE
=============================== */
.theme-toggle-btn {
    width: 38px;
    height: 38px;
    border-radius: 50%;
    border: none;
    background: var(--border-color);
    color: var(--text-primary);
    transition: 0.3s;
    cursor: pointer;
}

.theme-toggle-btn:hover {
    transform: scale(1.1);
}

/* ===============================
   ANIMACIÓN
=============================== */
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(8px); }
    to { opacity: 1; transform: translateY(0); }
}
</style>
<script>
document.addEventListener("DOMContentLoaded", function () {

    const toggleBtn = document.getElementById("themeToggle");
    const body = document.body;

    const savedTheme = localStorage.getItem("theme");

    if (savedTheme === "dark") {
        body.classList.add("dark-mode");
    } else if (savedTheme === "light") {
        body.classList.add("light-mode");
    }

    updateIcon();

    toggleBtn.addEventListener("click", function () {
        if (body.classList.contains("dark-mode")) {
            body.classList.remove("dark-mode");
            body.classList.add("light-mode");
            localStorage.setItem("theme", "light");
        } else {
            body.classList.remove("light-mode");
            body.classList.add("dark-mode");
            localStorage.setItem("theme", "dark");
        }
        updateIcon();
    });

    function updateIcon() {
        if (body.classList.contains("dark-mode")) {
            toggleBtn.innerHTML = '<i class="fas fa-sun"></i>';
        } else {
            toggleBtn.innerHTML = '<i class="fas fa-moon"></i>';
        }
    }

});
</script>
