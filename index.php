<?php
// login.php
require_once __DIR__ . '/core/bootstrap.php';

$login_error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {

  $username = isset($_POST['user']) ? trim($_POST['user']) : '';
  $password = isset($_POST['pass']) ? $_POST['pass'] : '';

  // ─────────────────────────────────────────────
  // 1) LOGIN DESDE TABLA STAFFS
  // ─────────────────────────────────────────────
  $stStaff = $db->prepare("
        SELECT user_id, password, designation, fullname
        FROM staffs
        WHERE BINARY username = ?
        LIMIT 1
    ");

  if ($stStaff) {
    $stStaff->bind_param("s", $username);
    $stStaff->execute();
    $stStaff->store_result();

    if ($stStaff->num_rows === 1) {

      $stStaff->bind_result($sid, $shash, $desig, $fname);
      $stStaff->fetch();

      if (password_verify($password, $shash)) {

        // Mapear designation a rol del sistema
        $rolMap = [
          'Administrador' => 'admin',
          'Recepcionista' => 'recepcionista',
          'Cajero' => 'cajero',
          'Entrenador' => 'entrenador',
          'Asistente' => 'asistente',
        ];
        $rolSistema = $rolMap[$desig] ?? 'staff';

        // 🔐 Limpiar sesión previa completamente
        $_SESSION = [];
        session_regenerate_id(true);

        // 🟢 Crear sesión exclusiva staff
        $_SESSION['user_id'] = (int) $sid;
        $_SESSION['rol'] = $rolSistema;
        $_SESSION['username'] = $username;
        $_SESSION['fullname'] = $fname;
        $_SESSION['origen'] = 'staffs';

        $stStaff->close();
        header('Location: admin/dashboard.php');
        exit;
      }
    }

    $stStaff->close();
  }

  // ─────────────────────────────────────────────
  // 2) LOGIN DESDE TABLA ADMIN (fallback)
  // ─────────────────────────────────────────────
  $stmt = $db->prepare("
        SELECT user_id, password
        FROM admin
        WHERE BINARY username = ?
        LIMIT 1
    ");

  if ($stmt) {

    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 1) {

      $stmt->bind_result($uid, $hash);
      $stmt->fetch();

      if (password_verify($password, $hash)) {

        // 🔐 Limpiar sesión previa completamente
        $_SESSION = [];
        session_regenerate_id(true);

        // 🟢 Crear sesión exclusiva admin
        $_SESSION['user_id'] = (int) $uid;
        $_SESSION['rol'] = 'admin';
        $_SESSION['username'] = $username;
        $_SESSION['fullname'] = 'Administrador';
        $_SESSION['origen'] = 'staffs'; // unificado

        $stmt->close();
        header('Location: admin/dashboard.php');
        exit;
      }
    }

    $stmt->close();
  }

  $login_error = "Usuario y/o contraseña inválidos";
}
?>
<!doctype html>
<html lang="es">

<head>
  <meta charset="utf-8">
  <title>Login | Gym Body Training</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- Fuentes / Iconos / Bootstrap / SB Admin 2 -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;600;700;800;900&display=swap"
    rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/gh/StartBootstrap/startbootstrap-sb-admin-2@4.1.4/css/sb-admin-2.min.css"
    rel="stylesheet">

  <style>
    :root {
      /* Paleta SB Admin 2 */
      --primary: #4e73df;
      --primary-dark: #224abe;
      --success: #1cc88a;
      --warning: #f6c23e;
      --white: #ffffff;
      --dark: #0b1221;
      /* Efectos */
      --overlay: rgba(8, 13, 29, .55);
      --soft-shadow: 0 10px 30px rgba(0, 0, 0, .22);
      --ring: rgba(78, 115, 223, .35);
      --glass-bg: rgba(255, 255, 255, .10);
      --glass-stroke: rgba(255, 255, 255, .18);
      --glass-focus: rgba(255, 255, 255, .18);
    }

    body {
      font-family: 'Nunito', sans-serif;
      min-height: 100vh;
    }

    /* HERO fondo */
    .bg-auth {
      position: relative;
      min-height: 100vh;
      display: flex;
      align-items: center;
      background-image: linear-gradient(var(--overlay), var(--overlay)),
        url('https://images.unsplash.com/photo-1517836357463-d25dfeac3438?q=80&w=1920&auto=format&fit=crop');
      /* TODO: tu imagen */
      background-size: cover;
      background-position: center;
      background-attachment: fixed;
      overflow: hidden;
    }

    /* Partículas */
    .bubble {
      position: absolute;
      bottom: -80px;
      width: 12px;
      height: 12px;
      border-radius: 50%;
      background: rgba(255, 255, 255, .12);
      animation: rise 12s linear infinite;
    }

    @keyframes rise {
      from {
        transform: translateY(0) translateX(0);
        opacity: .4;
      }

      to {
        transform: translateY(-120vh) translateX(80px);
        opacity: 0;
      }
    }

    /* Card estilo glass */
    .auth-card {
      backdrop-filter: blur(8px);
      background: var(--glass-bg);
      border: 1px solid var(--glass-stroke);
      border-radius: 1.25rem;
      box-shadow: var(--soft-shadow);
      transform-style: preserve-3d;
      transition: transform .15s ease, box-shadow .2s ease;
      color: #fff;
    }

    .auth-card:hover {
      transform: translateY(-4px) rotateX(1deg) rotateY(-1deg);
    }

    .brand {
      display: flex;
      align-items: center;
      color: #fff;
      text-decoration: none;
      font-weight: 900;
      letter-spacing: .5px;
    }

    .brand img {
      width: 44px;
      height: 44px;
      object-fit: contain;
      margin-right: .5rem;
      border-radius: 10px;
    }

    /* Inputs flotantes */
    .form-group-modern {
      position: relative;
      margin-bottom: 18px;
    }

    .form-input {
      width: 100%;
      padding: 30px 44px 14px 44px;
      border-radius: 12px;
      border: 1px solid var(--glass-stroke);
      background: rgba(255, 255, 255, .08);
      color: #fff;
      outline: none;
      transition: .2s;
    }

    .form-input:focus {
      border-color: var(--primary);
      background: var(--glass-focus);
      box-shadow: 0 0 0 6px var(--ring);
    }

    .form-label {
      position: absolute;
      left: 44px;
      top: 50%;
      transform: translateY(-50%);
      font-size: .95rem;
      color: rgba(255, 255, 255, .8);
      pointer-events: none;
      transition: .2s;
    }

    .form-input:focus+.form-label,
    .form-input:not(:placeholder-shown)+.form-label {
      top: 7px;
      transform: none;
      font-size: .75rem;
      color: #cfe0ff;
    }

    .field-icon {
      position: absolute;
      left: 14px;
      top: 50%;
      transform: translateY(-50%);
      color: #fff;
      opacity: .9;
    }

    .toggle-pass {
      position: absolute;
      right: 12px;
      top: 50%;
      transform: translateY(-50%);
      background: transparent;
      border: 0;
      color: #fff;
      opacity: .9;
      cursor: pointer;
    }

    /* Reveal */
    [data-reveal] {
      opacity: 0;
      transform: translateY(16px);
      transition: opacity .6s ease, transform .6s ease;
    }

    [data-reveal].show {
      opacity: 1;
      transform: none;
    }

    /* Botón con spinner */
    .btn-gradient {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: .5rem;
      width: 100%;
      height: 48px;
      border-radius: 12px;
      border: none;
      background: linear-gradient(90deg, var(--primary), var(--primary-dark));
      color: #fff;
      font-weight: 700;
    }

    .btn-gradient:focus {
      box-shadow: 0 0 0 6px var(--ring);
    }

    .btn-gradient .spinner {
      width: 18px;
      height: 18px;
      border: 3px solid rgba(255, 255, 255, .35);
      border-top-color: #fff;
      border-radius: 50%;
      animation: spin 1s linear infinite;
      display: none;
    }

    .btn-loading .spinner {
      display: inline-block;
    }

    .btn-loading .btn-text {
      opacity: .7;
    }

    @keyframes spin {
      to {
        transform: rotate(360deg);
      }
    }

    .mini-links a {
      color: #e2e8f0;
    }

    .mini-links a:hover {
      color: #fff;
      text-decoration: none;
    }

    .sep {
      display: flex;
      align-items: center;
      text-transform: uppercase;
      font-size: .75rem;
      color: #cbd5e1;
      font-weight: 800;
    }

    .sep:before,
    .sep:after {
      content: "";
      flex: 1;
      height: 1px;
      background: rgba(255, 255, 255, .25);
      margin: 0 .75rem;
    }

    .hint {
      font-size: .85rem;
      margin-top: 6px;
    }

    .hint.ok {
      color: #1cc88a
    }

    .hint.warn {
      color: #f6c23e
    }

    .hint.err {
      color: #e74a3b
    }

    .auth-footer {
      color: #e2e8f0;
      font-size: .9rem;
    }

    .alert-glass {
      background: rgba(231, 74, 59, .18);
      border: 1px solid rgba(231, 74, 59, .55);
      color: #fff;
      border-radius: 12px;
    }
  </style>
</head>

<body>

  <div class="bg-auth" id="bgAuth">
    <!-- Burbujas -->
    <?php for ($i = 0; $i < 18; $i++): ?>
      <span class="bubble"
        style="left:<?= rand(0, 100) ?>%; width:<?= rand(8, 18) ?>px; height:<?= rand(8, 18) ?>px; animation-duration: <?= rand(10, 18) ?>s; animation-delay: -<?= rand(0, 12) ?>s; opacity:.<?= rand(2, 6) ?>;"></span>
    <?php endfor; ?>

    <div class="container py-5">
      <div class="row align-items-center">
        <!-- Branding / copy -->
        <div class="col-lg-6 mb-5 mb-lg-0 text-white" data-reveal>
          <a class="brand mb-3 d-inline-flex" href="#">
            <img src="images/iconobt.ico" alt="Logo">
            <span>Gym Body Training</span>
          </a>
          <h1 class="display-5 font-weight-extrabold">Bienvenido de vuelta</h1>
          <p class="lead">Administra <strong>asistencias</strong>, <strong>planes</strong> y <strong>pagos</strong> en
            un solo lugar.</p>
          <div class="mini-links">
            <a href="#!" class="mr-3"><i class="fa-solid fa-shield-halved mr-1"></i> Seguridad</a>
            <a href="#!" class="mr-3"><i class="fa-solid fa-mobile-screen mr-1"></i> Modo móvil</a>
            <a href="#!"><i class="fa-solid fa-moon mr-1"></i> Modo oscuro (próx.)</a>
          </div>
        </div>

        <!-- Card de acceso -->
        <div class="col-lg-6" data-reveal>
          <div class="auth-card p-4 p-md-5">
            <div class="text-center mb-4">
              <img id="loginHero" src="../images/login-hero.jpg" alt="Gym" class="img-fluid"
                style="max-height:160px; object-fit:cover; border-radius:16px; box-shadow:var(--soft-shadow);"
                loading="lazy" decoding="async" referrerpolicy="no-referrer" crossorigin="anonymous" onerror="
      (function(img){
        // 1) Intenta fallback remoto confiable
        var remote='images/GymPortada.jpg';
        if (!img.dataset.triedRemote) { img.dataset.triedRemote=1; img.src=remote; return; }
        // 2) Si también falla, usa un placeholder embebido (nunca rompe)
        img.src='data:image/svg+xml;charset=UTF-8,'+
          encodeURIComponent('<svg xmlns=&quot;http://www.w3.org/2000/svg&quot; width=&quot;800&quot; height=&quot;160&quot;><rect width=&quot;100%&quot; height=&quot;100%&quot; fill=&quot;%232248be&quot;/><text x=&quot;50%&quot; y=&quot;50%&quot; dominant-baseline=&quot;middle&quot; text-anchor=&quot;middle&quot; fill=&quot;white&quot; font-family=&quot;Nunito,Arial&quot; font-size=&quot;22&quot;&gt;Gym Body Training&lt;/text&gt;</svg>');
      })(this);
    ">
            </div>

            <?php if (!empty($login_error)): ?>
              <div class="alert alert-glass alert-dismissible fade show" role="alert">
                <?= htmlspecialchars($login_error, ENT_QUOTES, "UTF-8"); ?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Cerrar"><span
                    aria-hidden="true">&times;</span></button>
              </div>
            <?php endif; ?>

            <h5 class="text-center font-weight-bold mb-3">Login Administrador | Staff</h5>
            <p class="text-center text-light mb-4">Accede al panel de administración.</p>

            <form id="loginForm" method="POST" action="">
              <div class="form-group-modern">
                <i class="fa fa-user field-icon"></i>
                <input type="text" class="form-input" id="user" name="user" placeholder=" " required
                  autocomplete="username">
                <label for="user" class="form-label">Usuario</label>
                <div id="userHint" class="hint" aria-live="polite"></div>
              </div>

              <div class="form-group-modern">
                <i class="fa fa-lock field-icon"></i>
                <input type="password" class="form-input" id="pass" name="pass" placeholder=" " required
                  autocomplete="current-password">
                <label for="pass" class="form-label">Contraseña</label>
                <button class="toggle-pass" type="button" aria-label="Mostrar/ocultar contraseña">
                  <i class="fa fa-eye" id="eyeIcon"></i>
                </button>
                <div id="passHint" class="hint" aria-live="polite"></div>
              </div>

              <div class="d-flex justify-content-between align-items-center mb-3">
                <span></span>
                <a href="#" class="text-light">¿Olvidaste tu contraseña?</a>
              </div>

              <button id="submitBtn" type="submit" name="login" class="btn btn-gradient">
                <span class="spinner" aria-hidden="true"></span>
                <span class="btn-text">Ingresar </span>
              </button>

              <div class="my-3 sep text-center">o accesos alternos</div>

              <div class="d-flex flex-column flex-md-row">
                <a href="cliente" class="btn btn-outline-light flex-fill mr-md-2 mb-2 mb-md-0">
                  <i class="fa fa-user-circle mr-1"></i> Cliente
                </a>

              </div>
            </form>

          </div>
          <div class="text-center auth-footer mt-4">
            <small>&copy; <span id="year"></span> Gym Body Training — Todos los derechos reservados.</small>
          </div>
        </div>

      </div>
    </div>
  </div>

  <!-- JS -->
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    // Año
    document.getElementById('year').textContent = new Date().getFullYear();

    // Reveal
    const io = new IntersectionObserver((obs) => {
      obs.forEach(e => { if (e.isIntersecting) { e.target.classList.add('show'); } });
    }, { threshold: .12 });
    document.querySelectorAll('[data-reveal]').forEach(el => io.observe(el));

    // Toggle password
    const pass = document.getElementById('pass');
    const eyeBtn = document.querySelector('.toggle-pass');
    const eyeIcon = document.getElementById('eyeIcon');
    eyeBtn.addEventListener('click', () => {
      const t = pass.type === 'password' ? 'text' : 'password';
      pass.type = t;
      eyeIcon.classList.toggle('fa-eye');
      eyeIcon.classList.toggle('fa-eye-slash');
    });

    // Hints
    const user = document.getElementById('user');
    const userHint = document.getElementById('userHint');
    const passHint = document.getElementById('passHint');
    user.addEventListener('input', () => {
      if (!user.value) { userHint.textContent = ''; userHint.className = 'hint'; return; }
      const ok = user.value.length >= 3;
      userHint.textContent = ok ? 'Usuario válido' : 'Mínimo 3 caracteres';
      userHint.className = 'hint ' + (ok ? 'ok' : 'err');
    });


    // Spinner submit
    const form = document.getElementById('loginForm');
    const submitBtn = document.getElementById('submitBtn');
    form.addEventListener('submit', () => submitBtn.classList.add('btn-loading'));


  </script>
</body>

</html>