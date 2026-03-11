<?php
// Vista - Variables disponibles desde el controlador
?>
<?php include __DIR__ . '/../../theme/sb2/header.php'; ?>
<?php include __DIR__ . '/../../theme/sb2/sidebar.php'; ?>
<?php include __DIR__ . '/../../theme/sb2/topbar.php'; ?>

<style>
    :root {
        --card-radius: 20px;
        --primary-gradient: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
        --gold-gradient: linear-gradient(135deg, #f6d365 0%, #fda085 100%);
    }

    .modern-card {
        border: 0;
        border-radius: var(--card-radius);
        box-shadow: 0 5px 20px rgba(0,0,0,0.05);
        background: #fff;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        overflow: hidden;
    }
    
    /* Hero Section */
    .hero-card {
        background: var(--primary-gradient);
        color: white;
        position: relative;
    }
    .hero-card::after {
        content: '';
        position: absolute;
        top: -20px; right: -20px;
        width: 150px; height: 150px;
        background: rgba(255,255,255,0.1);
        border-radius: 50%;
    }

    /* Achievement Cards */
    .achievement-card {
        text-align: center;
        padding: 1.5rem;
        height: 100%;
        position: relative;
    }
    .achievement-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0,0,0,0.08);
    }
    
    .icon-box-xl {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2.5rem;
        margin: 0 auto 1rem auto;
        transition: all 0.3s;
    }

    /* Estados */
    .locked {
        background: #f8f9fc;
        border: 1px dashed #d1d3e2;
    }
    .locked .icon-box-xl {
        background: #eaecf4;
        color: #b7b9cc;
    }
    .locked .ach-title { color: #858796; }
    
    .unlocked {
        background: #fff;
        border: 1px solid #e3e6f0;
    }
    .unlocked .icon-box-xl {
        background: var(--gold-gradient);
        color: white;
        box-shadow: 0 5px 15px rgba(246, 211, 101, 0.4);
    }
    .unlocked .ach-title { color: #4e73df; font-weight: 800; }

    /* Badges */
    .discount-badge {
        position: absolute;
        top: 15px;
        right: 15px;
        background: #e74a3b;
        color: white;
        font-size: 0.75rem;
        padding: 4px 10px;
        border-radius: 12px;
        font-weight: 700;
        box-shadow: 0 2px 5px rgba(231, 74, 59, 0.3);
    }
</style>

<div class="container-fluid sb2-content">

    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800 font-weight-bold">Sala de Trofeos</h1>
            <p class="mb-0 text-muted">Tu constancia tiene recompensas.</p>
        </div>
        <div>
            <a href="asistencias.php" class="btn btn-light shadow-sm text-primary font-weight-bold">
                <i class="fas fa-history mr-2"></i>Historial
            </a>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-lg-8 mb-4 mb-lg-0">
            <div class="card modern-card hero-card h-100 p-4">
                <div class="d-flex flex-column flex-md-row align-items-center justify-content-between h-100">
                    <div class="mb-4 mb-md-0">
                        <h4 class="font-weight-bold mb-1">¡Sigue así, Campeón!</h4>
                        <p class="mb-4 text-white-50">Cada entrenamiento cuenta para tu siguiente nivel.</p>
                        
                        <?php if ($next): ?>
                            <div class="bg-white text-dark p-3 rounded shadow-sm" style="min-width: 280px; max-width: 400px;">
                                <div class="d-flex justify-content-between small font-weight-bold text-uppercase text-muted mb-2">
                                    <span>Próximo Objetivo: <?= h($nextGoalLabel) ?></span>
                                    <span><?= $progress ?>%</span>
                                </div>
                                <div class="progress mb-2" style="height: 10px; border-radius: 5px; background: #eaecf4;">
                                    <div class="progress-bar bg-primary progress-bar-striped progress-bar-animated" role="progressbar" style="width: <?= $progress ?>%"></div>
                                </div>
                                <div class="small font-weight-bold text-primary">
                                    <i class="fas fa-fire mr-1"></i> Faltan solo <?= $remaining ?> asistencias
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="bg-white text-success p-3 rounded shadow-sm">
                                <h5 class="mb-0 font-weight-bold"><i class="fas fa-crown mr-2"></i> ¡Nivel Máximo Alcanzado!</h5>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="text-center ml-md-4">
                        <div class="display-3 font-weight-bold"><?= $total ?></div>
                        <div class="text-uppercase  font-weight-bold text-white-50" style="letter-spacing: 2px;">Total Asistencias</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card modern-card h-100 p-4 bg-white border-left-success">
                <div class="d-flex align-items-center mb-3">
                    <div class="icon-circle bg-success text-white mr-3" style="width:40px;height:40px;border-radius:50%;display:flex;align-items:center;justify-content:center;">
                        <i class="fas fa-lightbulb"></i>
                    </div>
                    <h6 class="font-weight-bold text-gray-800 mb-0">Tip para subir de nivel</h6>
                </div>
                <p class="text-muted small mb-0">
                    La constancia es la clave. Intenta venir al gimnasio a la misma hora para crear un hábito sólido. ¡Recuerda marcar tu asistencia en recepción!
                </p>
                <hr>
                <div class="d-flex align-items-center justify-content-between mt-auto">
                    <span class="small text-gray-600">Beneficios desbloqueados:</span>
                    <?php 
                        $unlockedCount = 0;
                        foreach($achievements as $a) if($total >= $a['goal']) $unlockedCount++;
                    ?>
                    <span class="badge badge-success px-3 py-2"><?= $unlockedCount ?> / <?= count($achievements) ?></span>
                </div>
            </div>
        </div>
    </div>

    <h5 class="font-weight-bold text-gray-800 mb-3 ml-1">Tus Insignias</h5>
    <div class="row">
        <?php foreach ($achievements as $a): 
            $isUnlocked = ($total >= $a['goal']);
            $cardClass  = $isUnlocked ? 'unlocked' : 'locked';
            $checkIcon  = $isUnlocked ? '<i class="fas fa-check-circle text-success ml-1"></i>' : '';
            
            // Calculo de progreso individual para tarjetas bloqueadas
            $percentCard = 0;
            if (!$isUnlocked) {
                $percentCard = min(100, round(($total / $a['goal']) * 100));
            }
        ?>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card modern-card achievement-card <?= $cardClass ?>">
                
                <?php if ($a['discount'] > 0): ?>
                    <div class="discount-badge" title="Descuento en próxima mensualidad">
                        -<?= (float)$a['discount'] ?>% OFF
                    </div>
                <?php endif; ?>

                <div class="icon-box-xl shadow-sm">
                    <i class="fas <?= h($a['icon']) ?>"></i>
                </div>
                
                <h5 class="ach-title mb-1"><?= h($a['label']) ?> <?= $checkIcon ?></h5>
                <p class="small text-muted mb-3">Meta: <strong><?= $a['goal'] ?></strong> asistencias</p>

                <?php if ($isUnlocked): ?>
                    <span class="badge badge-pill badge-light text-success font-weight-bold px-3 py-2 border">
                        ¡COMPLETADO!
                    </span>
                <?php else: ?>
                    <div class="w-100 px-3">
                        <div class="d-flex justify-content-between small mb-1 text-muted">
                            <span>Progreso</span>
                            <span><?= $percentCard ?>%</span>
                        </div>
                        <div class="progress" style="height: 6px;">
                            <div class="progress-bar bg-secondary" style="width: <?= $percentCard ?>%"></div>
                        </div>
                        <small class="text-muted mt-2 d-block">Faltan <?= $a['goal'] - $total ?></small>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <?php endforeach; ?>
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