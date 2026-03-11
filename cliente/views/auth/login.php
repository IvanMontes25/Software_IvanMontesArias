<?php
// Vista: Login
// Variables: $login_error
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8" />
  <title>Ingreso Cliente | Gym Body Training</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />

  <!-- SB Admin 2 stack -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;600;700;800;900&display=swap" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/gh/StartBootstrap/startbootstrap-sb-admin-2@4.1.4/css/sb-admin-2.min.css" rel="stylesheet">

  <style>
    :root{
      /* Paleta/efectos estilo admin/staff */
      --primary:#4e73df; --primary-dark:#224abe;
      --overlay: rgba(8,13,29,.55);
      --soft-shadow: 0 10px 30px rgba(0,0,0,.22);
      --ring: rgba(78,115,223,.35);
      --glass-bg: rgba(255,255,255,.10);
      --glass-stroke: rgba(255,255,255,.18);
      --glass-focus: rgba(255,255,255,.18);
    }
    body{ font-family:'Nunito',sans-serif; min-height:100vh; }

    /* Fondo a pantalla completa — MISMO del admin */
    .bg-auth{
      position:relative; min-height:100vh; display:flex; align-items:center;
      background-image: linear-gradient(var(--overlay), var(--overlay)),
        url('https://images.unsplash.com/photo-1517836357463-d25dfeac3438?q=80&w=1920&auto=format&fit=crop');
      background-size:cover; background-position:center; background-attachment:fixed;
      overflow:hidden; color:#fff;
    }

    /* Partículas */
    .bubble{
      position:absolute; bottom:-80px; width:12px; height:12px; border-radius:50%;
      background: rgba(255,255,255,.12); animation: rise 12s linear infinite;
    }
    @keyframes rise{
      from{ transform: translateY(0) translateX(0); opacity:.4; }
      to{ transform: translateY(-120vh) translateX(80px); opacity:0; }
    }

    /* Card glass con tilt */
    .auth-card{
      backdrop-filter: blur(8px);
      background: var(--glass-bg);
      border:1px solid var(--glass-stroke);
      border-radius: 1.25rem;
      box-shadow: var(--soft-shadow);
      transform-style: preserve-3d;
      transition: transform .15s ease, box-shadow .2s ease;
      color:#fff;
    }
    .auth-card:hover{ transform: translateY(-4px) rotateX(1deg) rotateY(-1deg); }

    .brand{ display:flex; align-items:center; color:#fff; text-decoration:none; font-weight:900; letter-spacing:.5px; }
    .brand img{ width:44px; height:44px; object-fit:contain; margin-right:.5rem; border-radius:10px; }

    /* Inputs flotantes */
    .form-group-modern{ position:relative; margin-bottom:18px; }
    .form-input{
      width:100%; padding:30px 44px 14px 44px; border-radius:12px;
      border:1px solid var(--glass-stroke); background:rgba(255,255,255,.08); color:#fff; outline:none; transition:.2s;
    }
    .form-input:focus{ border-color:var(--primary); background:var(--glass-focus); box-shadow:0 0 0 6px var(--ring); }
    .form-label{ position:absolute; left:44px; top:50%; transform:translateY(-50%); font-size:.95rem; color:rgba(255,255,255,.8); pointer-events:none; transition:.2s; }
    .form-input:focus + .form-label,
    .form-input:not(:placeholder-shown) + .form-label{ top:7px; transform:none; font-size:.75rem; color:#cfe0ff; }
    .field-icon{ position:absolute; left:14px; top:50%; transform:translateY(-50%); color:#fff; opacity:.9; }
    .toggle-pass{ position:absolute; right:12px; top:50%; transform:translateY(-50%); background:transparent; border:0; color:#fff; opacity:.9; cursor:pointer; }

    /* Reveal on scroll */
    [data-reveal]{ opacity:0; transform: translateY(16px); transition: opacity .6s ease, transform .6s ease; }
    [data-reveal].show{ opacity:1; transform:none; }

    /* Botón con spinner */
    .btn-gradient{
      display:inline-flex; align-items:center; justify-content:center; gap:.5rem; width:100%; height:48px;
      border-radius:12px; border:none; background: linear-gradient(90deg,var(--primary),var(--primary-dark)); color:#fff; font-weight:700;
    }
    .btn-gradient:focus{ box-shadow:0 0 0 6px var(--ring); }
    .btn-gradient .spinner{ width:18px; height:18px; border:3px solid rgba(255,255,255,.35); border-top-color:#fff; border-radius:50%; animation:spin 1s linear infinite; display:none; }
    .btn-loading .spinner{ display:inline-block; } .btn-loading .btn-text{ opacity:.7; }
    @keyframes spin{ to{ transform:rotate(360deg); } }

    .sep{ display:flex; align-items:center; text-transform:uppercase; font-size:.75rem; color:#cbd5e1; font-weight:800; }
    .sep:before,.sep:after{ content:""; flex:1; height:1px; background: rgba(255,255,255,.25); margin:0 .75rem; }

    .hint{ font-size:.85rem; margin-top:6px; } .hint.ok{ color:#1cc88a } .hint.warn{ color:#f6c23e } .hint.err{ color:#e74a3b }
    .alert-glass{ background:rgba(231,74,59,.18); border:1px solid rgba(231,74,59,.55); color:#fff; border-radius:12px; }
  </style>
</head>
<body>

  <div class="bg-auth" id="bgAuth">
    <!-- Burbujas (como admin/staff) -->
    <?php for($i=0;$i<18;$i++): ?>
      <span class="bubble" style="left:<?= rand(0,100) ?>%; width:<?= rand(8,18) ?>px; height:<?= rand(8,18) ?>px; animation-duration: <?= rand(10,18) ?>s; animation-delay: -<?= rand(0,12) ?>s; opacity:.<?= rand(2,6) ?>;"></span>
    <?php endfor; ?>

    <div class="container py-5">
      <div class="row align-items-center">
        <!-- Branding / copy -->
        <div class="col-lg-6 mb-5 mb-lg-0" data-reveal>
          <a class="brand mb-3 d-inline-flex" href="../index.html">
            <img src="../images/iconobt.ico" alt="Logo">
            <span>Gym Body Training</span>
          </a>
          <h1 class="display-5 font-weight-extrabold">Ingreso Cliente</h1>
          <p class="lead">Accede a tus clases, membresía y reservas con una interfaz moderna.</p>
          <div class="mini-links">
            <a href="#!" class="mr-3 text-light"><i class="fa-solid fa-shield-halved mr-1"></i> Seguridad</a>
            <a href="#!" class="mr-3 text-light"><i class="fa-solid fa-mobile-screen mr-1"></i> Modo móvil</a>
            <a href="#!" class="text-light"><i class="fa-solid fa-moon mr-1"></i> Modo oscuro (próx.)</a>
          </div>
        </div>

        <!-- Card de acceso -->
        <div class="col-lg-6" data-reveal>
          <div class="auth-card p-4 p-md-5">
            <div class="text-center mb-4">
               <img
    id="loginHero"
    src="../images/login-hero.jpg"
    alt="Gym"
    class="img-fluid"
    style="max-height:160px; object-fit:cover; border-radius:16px; box-shadow:var(--soft-shadow);"
    loading="lazy"
    decoding="async"
    referrerpolicy="no-referrer"
    crossorigin="anonymous"
    onerror="
      (function(img){
        // 1) Intenta fallback remoto confiable
        var remote='../images/GymPortada.jpg';
        if (!img.dataset.triedRemote) { img.dataset.triedRemote=1; img.src=remote; return; }
        // 2) Si también falla, usa un placeholder embebido (nunca rompe)
        img.src='data:image/svg+xml;charset=UTF-8,'+
          encodeURIComponent('<svg xmlns=&quot;http://www.w3.org/2000/svg&quot; width=&quot;800&quot; height=&quot;160&quot;><rect width=&quot;100%&quot; height=&quot;100%&quot; fill=&quot;%232248be&quot;/><text x=&quot;50%&quot; y=&quot;50%&quot; dominant-baseline=&quot;middle&quot; text-anchor=&quot;middle&quot; fill=&quot;white&quot; font-family=&quot;Nunito,Arial&quot; font-size=&quot;22&quot;&gt;Gym Body Training&lt;/text&gt;</svg>');
      })(this);
    "
  >
            </div>

            <?php if (!empty($login_error)): ?>
              <div class="alert alert-glass alert-dismissible fade show" role="alert">
                <?= htmlspecialchars($login_error, ENT_QUOTES, "UTF-8"); ?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Cerrar"><span aria-hidden="true">&times;</span></button>
              </div>
            <?php endif; ?>

            <h5 class="text-center font-weight-bold mb-3">Accede a tu cuenta</h5>
            <p class="text-center text-light mb-4">Usa tus credenciales de cliente.</p>

            <!-- LOGIN FORM -->
            <form id="loginForm" method="POST" action="">
              <div class="form-group-modern">
                <i class="fa fa-user field-icon" aria-hidden="true"></i>
                <input type="text" class="form-input" id="user" name="user" placeholder=" " required autocomplete="username" />
                <label for="user" class="form-label">Usuario</label>
                <div id="userHint" class="hint" aria-live="polite"></div>
              </div>

              <div class="form-group-modern">
                <i class="fa fa-lock field-icon" aria-hidden="true"></i>
                <input type="password" class="form-input" id="pass" name="pass" placeholder=" " minlength="6" required autocomplete="current-password" />
                <label for="pass" class="form-label">Contraseña</label>
                <button class="toggle-pass" type="button" aria-label="Mostrar/ocultar contraseña">
                  <i class="fa fa-eye" id="eyeIcon"></i>
                </button>
                <div id="passHint" class="hint" aria-live="polite"></div>
              </div>

             
              <button id="submitBtn" type="submit" name="login" class="btn btn-gradient">
                <span class="spinner" aria-hidden="true"></span>
                <span class="btn-text">Ingresar como cliente</span>
              </button>

              


              <div class="text-center mt-3">
    <a href="#" id="forgotBtn" class="text-light small">
        <i class="fa-solid fa-key mr-1"></i> ¿Olvidaste tu contraseña?
    </a>
</div>

             


          </div>
          <div class="text-center mt-4" style="color:#e2e8f0;">
            <small>&copy; <span id="year"></span> Gym Body Training - Todos los derechos reservados.</small>
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

    // Reveal on scroll
    const io = new IntersectionObserver((obs)=> {
      obs.forEach(e=>{ if(e.isIntersecting){ e.target.classList.add('show'); } });
    }, { threshold:.12 });
    document.querySelectorAll('[data-reveal]').forEach(el=> io.observe(el));

    // Toggle password
    const pass = document.getElementById('pass');
    const eyeBtn = document.querySelector('.toggle-pass');
    const eyeIcon = document.getElementById('eyeIcon');
    eyeBtn.addEventListener('click', ()=>{
      const t = pass.type === 'password' ? 'text' : 'password';
      pass.type = t;
      eyeIcon.classList.toggle('fa-eye');
      eyeIcon.classList.toggle('fa-eye-slash');
    });

    // Hints
    const user = document.getElementById('user');
    const userHint = document.getElementById('userHint');
    const passHint = document.getElementById('passHint');
    user.addEventListener('input', ()=>{
      if(!user.value){ userHint.textContent=''; userHint.className='hint'; return; }
      const ok = user.value.length >= 3;
      userHint.textContent = ok ? 'Usuario válido' : 'Mínimo 3 caracteres';
      userHint.className = 'hint ' + (ok ? 'ok' : 'err');
    });
    pass.addEventListener('input', ()=>{
      if(!pass.value){ passHint.textContent=''; passHint.className='hint'; return; }
      if(pass.value.length < 6){ passHint.textContent='Debe tener al menos 6 caracteres'; passHint.className='hint err'; }
      else if(pass.value.length < 10){ passHint.textContent='Seguro pero mejorable'; passHint.className='hint warn'; }
      else { passHint.textContent='Contraseña fuerte'; passHint.className='hint ok'; }
    });

    // Spinner submit
    const form = document.getElementById('loginForm');
    const submitBtn = document.getElementById('submitBtn');
    form.addEventListener('submit', ()=> submitBtn.classList.add('btn-loading'));

    // Toggle entre login y registro
    const toRecover = document.getElementById('to-recover');
    const toLogin   = document.getElementById('to-login');
    const loginForm = document.getElementById('loginForm');
    const recover   = document.getElementById('recoverform');
    if (toRecover) {
      toRecover.addEventListener('click', (e)=>{ e.preventDefault(); loginForm.style.display='none'; recover.style.display='block'; });
    }
    if (toLogin) {
      toLogin.addEventListener('click', (e)=>{ e.preventDefault(); loginForm.style.display='block'; recover.style.display='none'; });
    }
  </script>

  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
document.getElementById('forgotBtn').addEventListener('click', function(e){
    e.preventDefault();

    Swal.fire({
        title: 'Recuperar contraseña',
        html: `
            <input type="text" id="swal-user" class="swal2-input" placeholder="Usuario">
            <input type="text" id="swal-ci" class="swal2-input" placeholder="Carnet de Identidad">
        `,
        confirmButtonText: 'Restablecer',
        confirmButtonColor: '#4e73df',
        showCancelButton: true,
        preConfirm: () => {
            return {
                user: document.getElementById('swal-user').value,
                ci: document.getElementById('swal-ci').value
            }
        }
    }).then((result) => {
        if (result.isConfirmed) {
            fetch('forgot_password.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: new URLSearchParams(result.value)
            })
            .then(res => res.json())
            .then(data => {
                if(data.success){
                    Swal.fire({
                        icon:'success',
                        title:'Contraseña generada',
                        html:'Nueva contraseña:<br><strong>'+data.password+'</strong>',
                        confirmButtonColor:'#1cc88a'
                    });
                } else {
                    Swal.fire({
                        icon:'error',
                        title:'Error',
                        text:data.message
                    });
                }
            });
        }
    });
});
</script>

</body>
</html>
