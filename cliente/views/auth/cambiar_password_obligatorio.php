<?php
// Vista: Cambiar Password Obligatorio
// Variables: $error, $userId
?>
<!doctype html>
<html lang="es">
<head>
<meta charset="utf-8">
<title>Cambio obligatorio de contraseña</title>
<meta name="viewport" content="width=device-width, initial-scale=1">

<link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;600;700;800&display=swap" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
body{
    font-family:'Nunito',sans-serif;
    min-height:100vh;
    background: linear-gradient(rgba(8,13,29,.6),rgba(8,13,29,.6)),
    url('https://images.unsplash.com/photo-1517836357463-d25dfeac3438?q=80&w=1920&auto=format&fit=crop');
    background-size:cover;
    background-position:center;
    display:flex;
    align-items:center;
    justify-content:center;
}

.change-card{
    backdrop-filter: blur(8px);
    background: rgba(255,255,255,.08);
    border:1px solid rgba(255,255,255,.15);
    border-radius:1.25rem;
    box-shadow:0 10px 30px rgba(0,0,0,.25);
    padding:2rem;
    width:100%;
    max-width:430px;
    color:#fff;
}

.change-card h4{
    font-weight:800;
}

.form-control{
    border-radius:12px;
    background:rgba(255,255,255,.1);
    border:1px solid rgba(255,255,255,.2);
    color:#fff;
}

.form-control:focus{
    border-color:#4e73df;
    box-shadow:0 0 0 4px rgba(78,115,223,.25);
    background:rgba(255,255,255,.15);
    color:#fff;
}

.btn-gradient{
    border:none;
    border-radius:12px;
    height:45px;
    font-weight:700;
    background: linear-gradient(90deg,#4e73df,#224abe);
    color:#fff;
}

.alert{
    border-radius:12px;
}
</style>
</head>

<body>

<div class="change-card">

    <div class="text-center mb-4">
        <i class="fa-solid fa-shield-halved fa-2x mb-3"></i>
        <h4>Cambio obligatorio de contraseña</h4>
        <p class="small text-light">
            Tu contraseña fue restablecida por el administrador.  
            Debes crear una nueva para continuar.
        </p>
    </div>

    <?php if ($error): ?>
        <div class="alert alert-danger text-center">
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <form method="POST">

        <div class="form-group">
            <label>Nueva contraseña</label>
            <input type="password" name="pass1" class="form-control" required minlength="6">
        </div>

        <div class="form-group">
            <label>Confirmar contraseña</label>
            <input type="password" name="pass2" class="form-control" required minlength="6">
        </div>

        <button type="submit" class="btn btn-gradient btn-block">
            <i class="fa-solid fa-key mr-1"></i> Actualizar contraseña
        </button>

    </form>
</div>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<?php if (!empty($_SESSION['pwd_changed'])): ?>
<script>
Swal.fire({
    icon: 'success',
    title: 'Contraseña actualizada',
    text: 'Tu contraseña fue restablecida correctamente.',
    confirmButtonText: 'Aceptar',
    confirmButtonColor: '#4e73df',
    allowOutsideClick: false,
    allowEscapeKey: false
}).then((result) => {
    if (result.isConfirmed) {
        window.location.href = "perfil.php";
    }
});
</script>
<?php unset($_SESSION['pwd_changed']); ?>
<?php endif; ?>


</body>
</html>
