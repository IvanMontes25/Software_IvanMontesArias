<?php
// Vista - Variables disponibles desde el controlador
?>
<?php include __DIR__ . '/../../theme/sb2/header.php'; ?>
<?php include __DIR__ . '/../../theme/sb2/sidebar.php'; ?>
<?php include __DIR__ . '/../../theme/sb2/topbar.php'; ?>


<style>
    :root {
        --card-radius: 20px;
        --primary-color: #4e73df;
        --success-color: #1cc88a;
        --bg-light: #f8f9fc;
    }

    .modern-card {
        border: none;
        border-radius: var(--card-radius);
        box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.1);
        background: #fff;
    }

   /* --- NUEVO SISTEMA DE CALENDARIO --- */
    .calendar-container {
        width: 100%; 
    }

    .calendar-grid {
        display: grid;
        grid-template-columns: repeat(7, minmax(0, 1fr)); 
        gap: 6px;
    }

    .calendar-header-day {
        text-align: center;
        font-weight: 700;
        font-size: 0.7rem;
        text-transform: uppercase;
        color: #b7c1d1;
        padding-bottom: 10px;
    }

    .day-cell {
        /* ELIMINADO aspect-ratio para que en PC no se vuelva gigante */
        height: 85px; /* Altura fija y controlada para pantallas grandes (rectangular) */
        background: var(--bg-light);
        border-radius: 10px;
        padding: 6px;
        position: relative;
        transition: all 0.2s ease;
        border: 2px solid transparent;
        display: flex;
        flex-direction: column;
        align-items: center;
        box-sizing: border-box; 
    }

    /* Celda Vacía */
    .day-empty {
        background: transparent;
        border: none;
    }

    .day-number {
        font-weight: 800;
        font-size: 0.9rem;
        color: #5a5c69;
        align-self: flex-start; 
        width: 100%;
        line-height: 1;
    }

    /* Estado: Asistido */
    .day-attended {
        background: rgba(28, 200, 138, 0.12) !important;
        border-color: rgba(28, 200, 138, 0.2);
    }
    .day-attended .day-number { color: var(--success-color); }
    
    /* Estado: Hoy */
    .day-today {
        border-color: var(--primary-color);
        box-shadow: 0 4px 12px rgba(78, 115, 223, 0.2);
        background: #fff;
    }

    .check-icon {
        color: var(--success-color);
        font-size: 0.9rem;
        position: absolute; 
        bottom: 5px;
        right: 5px;
        z-index: 1;
    }

    .time-badge {
        font-size: 0.65rem;
        background: #fff;
        padding: 2px 4px;
        border-radius: 6px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        display: inline-block;
        margin-top: auto; 
        margin-bottom: 5px; 
        z-index: 2; 
    }

    /* Ajustes para móviles */
    @media (max-width: 576px) {
        .calendar-grid { 
            gap: 2px; 
        } 
        
        .calendar-header-day { 
            font-size: 0.6rem; 
            padding-bottom: 6px; 
        }
        
        .day-cell { 
            padding: 4px 2px; 
            height: 60px; /* Reducimos la altura en móvil */
            border-radius: 6px; 
        }
        
        .day-number { 
            font-size: 0.75rem; 
            padding-left: 2px;
        }
        
        .time-badge { 
            font-size: 0.55rem; 
            padding: 1px 2px; 
            margin-bottom: 14px; /* <-- CLAVE: Sube la etiqueta de la hora en móviles */
            letter-spacing: -0.5px; 
            max-width: 95%; 
            overflow: hidden;
            text-align: center;
        }
        
        .check-icon { 
            font-size: 0.75rem; 
            bottom: 2px; 
            right: 2px; 
        }
    }
</style>

<div class="container-fluid">

    <div class="row mb-4 align-items-center">
        <div class="col-12 col-md-6 mb-3 mb-md-0">
            <h1 class="h3 mb-0 text-gray-800 font-weight-bold">Mi Actividad</h1>
            <p class="text-muted small mb-0">Seguimiento de asistencias y metas mensuales.</p>
        </div>
        
        <div class="col-12 col-md-6 d-flex justify-content-md-end">
            <div class="period-selector d-flex align-items-center">
                <a href="?y=<?= $prevY ?>&m=<?= $prevM ?>" class="btn btn-sm btn-circle btn-light border">
                    <i class="fas fa-chevron-left text-primary"></i>
                </a>
                <div class="text-center mx-4">
                    <span class="text-xs text-uppercase text-muted d-block" style="letter-spacing: 1px;">Periodo</span>
                    <strong class="text-primary text-capitalize"><?= h($monthName) ?></strong>
                </div>
                <a href="?y=<?= $nextY ?>&m=<?= $nextM ?>" class="btn btn-sm btn-circle btn-light border">
                    <i class="fas fa-chevron-right text-primary"></i>
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-8 col-lg-7 mb-4">
            <div class="card modern-card">
                <div class="card-header bg-white border-0 pt-4 pb-0 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-dark">
                        <i class="far fa-calendar-check mr-2 text-primary"></i>Calendario de Asistencia
                    </h6>
                    <div class="d-none d-sm-block">
                        <span class="badge badge-light border text-muted"><i class="fas fa-circle text-success mr-1"></i> Días Asistidas</span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="calendar-container">
                        <div class="calendar-grid mb-2">
                            <div class="calendar-header-day">Lun</div>
                            <div class="calendar-header-day">Mar</div>
                            <div class="calendar-header-day">Mié</div>
                            <div class="calendar-header-day">Jue</div>
                            <div class="calendar-header-day">Vie</div>
                            <div class="calendar-header-day">Sáb</div>
                            <div class="calendar-header-day">Dom</div>
                        </div>

                        <div class="calendar-grid">
                            <?php
                            $day = 1;
                            // Celdas vacías iniciales
                            for ($i = 1; $i < $firstDow; $i++) {
                                echo '<div class="day-cell day-empty"></div>';
                            }

                            // Loop de días
                            while ($day <= $daysInMonth) {
                                $dateStr = sprintf('%04d-%02d-%02d', $year, $month, $day);
                                $attended = isset($presentDays[$dateStr]);
                                $isToday  = ($dateStr === $todayStr);
                                
                                $classes = 'day-cell';
                                if ($attended) $classes .= ' day-attended';
                                if ($isToday)  $classes .= ' day-today';

                                echo '<div class="'.$classes.'">';
                                    echo '<span class="day-number">'.$day.'</span>';
                                    
                                    if ($attended) {
                                        echo '<span class="time-badge text-dark font-weight-bold">'.h($presentDays[$dateStr]).'</span>';
                                        echo '<i class="fas fa-check-circle check-icon"></i>';
                                    }
                                echo '</div>';
                                $day++;
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-lg-5">
            <div class="card modern-card mb-4 bg-gradient-primary text-white border-0">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-white-50  text-uppercase mb-1 font-weight-bold">Total del Mes</div>
                            <div class="h1 mb-0 font-weight-bold"><?= $totalMonth ?></div>
                        </div>
                        <div class="bg-white-20 rounded-circle p-3">
                            <i class="fas fa-bolt fa-2x text-white-50"></i>
                        </div>
                    </div>
                    <div class="mt-3 ">
                        <i class="fas fa-chart-line mr-1"></i> 
                        <span>¡<?= $totalMonth > 10 ? 'Excelente ritmo!' : '¡Sigue así!' ?></span>
                    </div>
                </div>
            </div>

            <div class="card modern-card mb-4">
                <div class="card-header bg-white border-0 pt-4">
                    <h6 class="m-0 font-weight-bold text-dark">Frecuencia Diaria</h6>
                </div>
                <div class="card-body">
                    <div style="height: 180px;">
                        <canvas id="attChart"></canvas>
                    </div>
                </div>
            </div>

            <div class="card modern-card mb-4">
                <div class="card-header bg-white border-0 pt-4">
                    <h6 class="m-0 font-weight-bold text-dark">Últimos Ingresos</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive" style="max-height: 250px;">
                        <table class="table table-hover align-middle mb-0">
                            <tbody>
                                <?php if (empty($list)): ?>
                                    <tr><td class="text-center p-4 text-muted small">Sin registros</td></tr>
                                <?php else: ?>
                                    <?php foreach ($list as $row): ?>
                                    <tr>
                                        <td class="pl-4 border-0">
                                            <div class="small font-weight-bold"><?= date('d/m/Y', strtotime($row['curr_date'])) ?></div>
                                            <div class="text-xs text-muted">Entrada</div>
                                        </td>
                                        <td class="text-right pr-4 border-0">
                                            <span class="badge badge-success px-3 py-2">
                                                <?= date('H:i', strtotime((string)$row['curr_time'])) ?>
                                            </span>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../../theme/sb2/footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<?php
$popup = null;

if (isset($_GET['ok'])) {
    $popup = [
        'icon'  => 'success',
        'title' => '¡Asistencia registrada!',
        'text'  => 'Tu ingreso fue registrado correctamente. ¡Buen entrenamiento 💪!'
    ];
}

if (isset($_GET['ya'])) {
    $popup = [
        'icon'  => 'info',
        'title' => 'Asistencia ya registrada',
        'text'  => 'Ya registraste tu asistencia el día de hoy. ¡Nos alegra verte de nuevo!'
    ];
}

if (isset($_GET['error'])) {
    $popup = [
        'icon'  => 'error',
        'title' => 'Ocurrió un problema',
        'text'  => 'No se pudo registrar tu asistencia. Intenta nuevamente.'
    ];
}
?>

<?php if ($popup): ?>
<script>
Swal.fire({
    icon: '<?= $popup['icon'] ?>',
    title: '<?= $popup['title'] ?>',
    text: '<?= $popup['text'] ?>',
    confirmButtonText: 'Aceptar',
    confirmButtonColor: '#4e73df'
});
</script>
<?php endif; ?>
<script>
    // Configuración del Gráfico
    const ctx = document.getElementById('attChart').getContext('2d');
    const labels = Array.from({length: <?= (int)$daysInMonth ?>}, (_,i)=> i+1);
    const data = <?= json_encode(array_values($perDay)) ?>;

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Asistencias',
                data: data,
                backgroundColor: '#4e73df',
                hoverBackgroundColor: '#2e59d9',
                borderRadius: 3,
                barPercentage: 0.6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: "rgb(255,255,255)",
                    bodyColor: "#858796",
                    titleColor: "#6e707e",
                    borderColor: '#dddfeb',
                    borderWidth: 1,
                    padding: 10,
                    displayColors: false,
                    callbacks: {
                        title: function(context) {
                            return 'Día ' + context[0].label;
                        },
                        label: function(context) {
                            return context.raw > 0 ? 'Asistió' : '';
                        }
                    }
                }
            },
            scales: {
                x: {
                    grid: { display: false, drawBorder: false },
                    ticks: { maxTicksLimit: 10 }
                },
                y: {
                    display: false,
                    grid: { display: false },
                    min: 0,
                    max: 1.5 // Para mantener las barras bajas y estéticas
                }
            }
        }
    });
</script>

<?php if ($statusKey === 'por_vencer'): ?>
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