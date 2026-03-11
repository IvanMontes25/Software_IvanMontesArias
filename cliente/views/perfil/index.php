<?php
// Vista: Perfil
// Variables disponibles desde PerfilController:
//   $page, $user_id, $member, $fullname, $initial, $membershipId,
//   $m, $statusKey, $daysLeft, $planesTxt, $planName,
//   $estadoUI, $estadoBadge, $percent, $progressColor,
//   $precioBs, $asistencias
?>
<?php include __DIR__ . '/../../theme/sb2/header.php'; ?>
<?php include __DIR__ . '/../../theme/sb2/sidebar.php'; ?>
<?php include __DIR__ . '/../../theme/sb2/topbar.php'; ?>

<style>
    :root {
        --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        --card-radius: 20px;
    }
    
    .profile-header-card {
        background: var(--primary-gradient);
        color: white;
        border-radius: var(--card-radius);
        padding: 2rem;
        box-shadow: 0 10px 25px -5px rgba(118, 75, 162, 0.4);
        position: relative;
        overflow: hidden;
    }

    /* Círculo decorativo de fondo */
    .profile-header-card::before {
        content: '';
        position: absolute;
        top: -50px;
        right: -50px;
        width: 200px;
        height: 200px;
        background: rgba(255,255,255,0.1);
        border-radius: 50%;
    }

    .avatar-circle {
        width: 100px;
        height: 100px;
        background: rgba(255,255,255,0.95);
        color: #764ba2;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2.5rem;
        font-weight: 800;
        box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        border: 4px solid rgba(255,255,255,0.3);
    }

    .modern-card {
        border: none;
        border-radius: var(--card-radius);
        background: #fff;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        height: 100%;
    }

    .modern-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    }

    .stat-icon-box {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
    }

    .info-label {
        font-size: 0.85rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #8898aa;
        margin-bottom: 0.25rem;
        font-weight: 600;
    }
    
    .info-value {
        font-size: 1rem;
        font-weight: 600;
        color: #333;
    }
    
    .badge-soft {
        padding: 0.5em 1em;
        border-radius: 30px;
        font-weight: 600;
    }

    /* Botón personalizado "Mi Informe" */
    .btn-report-light {
        background: rgba(255, 255, 255, 0.2);
        border: 1px solid rgba(255, 255, 255, 0.4);
        color: white;
        padding: 5px 15px;
        border-radius: 20px;
        font-size: 0.85rem;
        transition: all 0.2s;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        margin-top: 10px;
    }
    .btn-report-light:hover {
        background: rgba(255, 255, 255, 0.35);
        color: white;
        text-decoration: none;
        transform: translateY(-1px);
    }
</style>

<div class="container-fluid">

    <div class="row mb-4">
        <div class="col-12">
            <div class="profile-header-card d-flex flex-column flex-md-row align-items-center">
                <div class="mr-md-4 mb-3 mb-md-0">
                    <div class="avatar-circle">
                        <?= $initial ?>
                    </div>
                </div>
                <div class="text-center text-md-left flex-grow-1">
                    <h2 class="font-weight-bold mb-1">
    Hola, <?= e($member['fullname'] ?? 'Usuario') ?> !
</h2>
                    <p class="mb-2 opacity-8">ID de Membresía: <strong><?= e($membershipId) ?></strong></p>
                    
                    <div class="d-flex align-items-center justify-content-center justify-content-md-start mb-2">
                        <span class="badge badge-<?= $estadoBadge ?> badge-soft text-uppercase mr-3">
                            <?= e($estadoUI) ?>
                        </span>
                        
                        <a href="mi_informe.php" class="btn-report-light">
                            <i class="fas fa-file-alt mr-2"></i> Mi Informe
                        </a>
                    </div>

                </div>
                <div class="text-center text-md-right mt-3 mt-md-0 pl-md-4 border-left-white-50">
                    <div class="h5 mb-0 font-weight-bold"><?= $precioBs === '—' ? '—' : 'Bs ' . $precioBs ?></div>
                    <small class="text-white-50">Último Pago</small>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card modern-card p-4">
                <div class="d-flex align-items-center">
                    <div class="stat-icon-box bg-primary text-white mr-3">
                        <i class="fas fa-dumbbell"></i>
                    </div>
                    <div>
                        <div class="info-label">Plan Actual</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?= e($planName) ?></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card modern-card p-4">
                <div class="d-flex align-items-center mb-2">
                    <div class="stat-icon-box bg-info text-white mr-3">
                        <i class="fas fa-hourglass-half"></i>
                    </div>
                    <div>
                        <div class="info-label">Tiempo Restante</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?= e($planesTxt) ?></div>
                    </div>
                </div>
                <?php if($m): ?>
                <div class="progress" style="height: 6px; border-radius:3px;">
                    <div class="progress-bar <?= $progressColor ?>" role="progressbar" style="width: <?= $percent ?>%"></div>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card modern-card p-4">
                <div class="d-flex align-items-center">
                    <div class="stat-icon-box bg-warning text-white mr-3">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div>
                        <div class="info-label">Asistencias</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?= e($asistencias) ?></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card modern-card p-4">
                <div class="d-flex align-items-center">
                    <div class="stat-icon-box bg-success text-white mr-3">
                        <i class="fas fa-wallet"></i>
                    </div>
                    <div>
                        <div class="info-label">Estado Pago</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            <?= ($precioBs !== '—') ? 'Al día' : 'Pendiente' ?>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card modern-card mb-4">
                <div class="card-header bg-transparent border-0 pt-4 px-4 pb-0">
                    <h5 class="font-weight-bold text-primary mb-0">
                        <i class="fas fa-user-circle mr-2"></i>Información Personal
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <div class="info-label">Cédula de Identidad</div>
                            <div class="info-value"><?= e($member['ci'] ?? '—') ?></div>
                        </div>
                        <div class="col-md-6 mb-4">
                            <div class="info-label">Teléfono / Contacto</div>
                            <div class="info-value"><?= e($member['contact'] ?? '—') ?></div>
                        </div>
                        <div class="col-md-6 mb-4">
                            <div class="info-label">Correo Electrónico</div>
                            <div class="info-value"><?= e($member['correo'] ?? '—') ?></div>
                        </div>
                        <div class="col-md-6 mb-4">
                            <div class="info-label">Género</div>
                            <div class="info-value"><?= e($member['gender'] ?? '—') ?></div>
                        </div>
                        <div class="col-md-6 mb-4">
                            <div class="info-label">Usuario de Sistema</div>
                            <div class="info-value">@<?= e($member['username'] ?? '—') ?></div>
                        </div>
                        <div class="col-md-6 mb-4">
                            <div class="info-label">Miembro Desde</div>
                            <div class="info-value">
                                <?= !empty($member['dor']) ? date('d/m/Y', strtotime($member['dor'])) : '—' ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card modern-card mb-4 bg-light text-center p-4">
                <h6 class="text-muted mb-3">Código QR de Acceso</h6>
                <div class="bg-white p-3 d-inline-block rounded shadow-sm mx-auto mb-3">
                     <i class="fas fa-qrcode fa-5x text-dark"></i>
                </div>
                <p class="small text-muted mb-0">Usa este código en la entrada</p>
            </div>
        </div>
    </div>

</div>
<?php include __DIR__ . '/../../theme/sb2/footer.php'; ?>

<?php if ($statusKey === 'por_vencer'): ?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
Swal.fire({
    icon: 'warning',
    title: '⚠️ Membresía por vencer',
    html: 'Tu membresía vence en <strong><?= (int)$daysLeft ?> día<?= ((int)$daysLeft !== 1) ? "s" : "" ?></strong>.<br>Renueva tu plan para seguir entrenando sin interrupciones.',
    confirmButtonText: 'Entendido',
    confirmButtonColor: '#f6c23e',
    showCloseButton: true
});
</script>
<?php endif; ?>