?>

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
        height: 85px;
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

    .day-attended {
        background: rgba(28, 200, 138, 0.12) !important;
        border-color: rgba(28, 200, 138, 0.2);
    }

    .day-attended .day-number {
        color: var(--success-color);
    }

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
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        display: inline-block;
        margin-top: auto;
        margin-bottom: 5px;
        z-index: 2;
    }

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
            height: 60px;
            border-radius: 6px;
        }

        .day-number {
            font-size: 0.75rem;
            padding-left: 2px;
        }

        .time-badge {
            font-size: 0.55rem;
            padding: 1px 2px;
            margin-bottom: 14px;
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

    /* PAGE HEADER (estandar sistema) */
    .page-header {
        background: linear-gradient(135deg, #4e73df, #1cc88a);
        border-radius: 1rem;
        padding: 1.3rem 1rem;
        box-shadow: 0 8px 20px rgba(0, 0, 0, .16);
    }

    .page-header-inner {
        max-width: 900px;
        margin: 0 auto;
        text-align: center;
        color: #fff;
    }

    .page-title {
        font-size: 1.35rem;
        font-weight: 700;
        margin-bottom: 4px;
    }

    .page-subtitle {
        font-size: .85rem;
        opacity: .9;
        margin-bottom: 0;
    }
</style>

<div class="container-fluid">

    <!-- PAGE HEADER (estilo sistema) -->
    <div class="page-header mb-4">
        <div class="page-header-inner">
            <h1 class="page-title">
                Asistencias de <?= h($clienteNombre) ?> <i class="fas fa-calendar-check ml-2"></i>
            </h1>
            <p class="page-subtitle">
                Historial y seguimiento de asistencias del cliente
            </p>
        </div>
    </div>

    <!-- Navegador de periodo -->
    <div class="d-flex justify-content-center align-items-center mb-4">
        <a href="?user_id=<?= $user_id ?>&y=<?= $prevY ?>&m=<?= $prevM ?>"
            class="btn btn-sm btn-circle btn-light border">
            <i class="fas fa-chevron-left text-primary"></i>
        </a>
        <div class="text-center mx-4">
            <span class="text-xs text-uppercase text-muted d-block" style="letter-spacing: 1px;">Periodo</span>
            <strong class="text-primary text-capitalize"><?= h($monthName) ?></strong>
        </div>
        <a href="?user_id=<?= $user_id ?>&y=<?= $nextY ?>&m=<?= $nextM ?>"
            class="btn btn-sm btn-circle btn-light border">
            <i class="fas fa-chevron-right text-primary"></i>
        </a>
    </div>

    <div class="row">
        <!-- CALENDARIO -->
        <div class="col-xl-8 col-lg-7 mb-4">
            <div class="card modern-card">
                <div class="card-header bg-white border-0 pt-4 pb-0 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-dark">
                        <i class="far fa-calendar-check mr-2 text-primary"></i>Calendario de Asistencia
                    </h6>
                    <div class="d-none d-sm-block">
                        <span class="badge badge-light border text-muted">
                            <i class="fas fa-circle text-success mr-1"></i> Dias Asistidos
                        </span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="calendar-container">
                        <div class="calendar-grid mb-2">
                            <div class="calendar-header-day">Lun</div>
                            <div class="calendar-header-day">Mar</div>
                            <div class="calendar-header-day">Mie</div>
                            <div class="calendar-header-day">Jue</div>
                            <div class="calendar-header-day">Vie</div>
                            <div class="calendar-header-day">Sab</div>
                            <div class="calendar-header-day">Dom</div>
                        </div>

                        <div class="calendar-grid">
                            <?php
                            $day = 1;
                            for ($i = 1; $i < $firstDow; $i++) {
                                echo '<div class="day-cell day-empty"></div>';
                            }

                            while ($day <= $daysInMonth) {
                                $dateStr = sprintf('%04d-%02d-%02d', $year, $month, $day);
                                $attended = isset($presentDays[$dateStr]);
                                $isToday = ($dateStr === $todayStr);

                                $classes = 'day-cell';
                                if ($attended)
                                    $classes .= ' day-attended';
                                if ($isToday)
                                    $classes .= ' day-today';

                                echo '<div class="' . $classes . '">';
                                echo '<span class="day-number">' . $day . '</span>';
                                if ($attended) {
                                    echo '<span class="time-badge text-dark font-weight-bold">' . h($presentDays[$dateStr]) . '</span>';
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

        <!-- SIDEBAR DERECHO -->
        <div class="col-xl-4 col-lg-5">

            <!-- KPI: Total del Mes -->
            <div class="card modern-card mb-4 bg-gradient-primary text-white border-0">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-white-50 text-uppercase mb-1 font-weight-bold">Total del Mes</div>
                            <div class="h1 mb-0 font-weight-bold"><?= $totalMonth ?></div>
                        </div>
                        <div class="bg-white-20 rounded-circle p-3">
                            <i class="fas fa-bolt fa-2x text-white-50"></i>
                        </div>
                    </div>
                    <div class="mt-3">
                        <i class="fas fa-chart-line mr-1"></i>
                        <span><?= $totalMonth > 10 ? 'Excelente ritmo!' : 'En progreso' ?></span>
                    </div>
                </div>
            </div>

            <!-- KPI: Total Historico -->
            <div class="card modern-card mb-4 bg-gradient-success text-white border-0">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-white-50 text-uppercase mb-1 font-weight-bold">Total Historico</div>
                            <div class="h1 mb-0 font-weight-bold"><?= $totalAsistencias ?></div>
                        </div>
                        <div class="bg-white-20 rounded-circle p-3">
                            <i class="fas fa-trophy fa-2x text-white-50"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Grafico Frecuencia -->
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

            <!-- Ultimos Ingresos -->
            <div class="card modern-card mb-4">
                <div class="card-header bg-white border-0 pt-4">
                    <h6 class="m-0 font-weight-bold text-dark">Ultimos Ingresos</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive" style="max-height: 250px;">
                        <table class="table table-hover align-middle mb-0">
                            <tbody>
                                <?php if (empty($list)): ?>
                                    <tr>
                                        <td class="text-center p-4 text-muted small">Sin registros este mes</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($list as $row): ?>
                                        <tr>
                                            <td class="pl-4 border-0">
                                                <div class="small font-weight-bold">
                                                    <?= date('d/m/Y', strtotime($row['curr_date'])) ?></div>
                                                <div class="text-xs text-muted">Entrada</div>
                                            </td>
                                            <td class="text-right pr-4 border-0">
                                                <span class="badge badge-success px-3 py-2">
                                                    <?= h($row['curr_time']) ?>
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

    <!-- Boton volver -->
    <div class="text-center mb-4">
        <a href="perfil_cliente.php?user_id=<?= $user_id ?>" class="btn btn-outline-primary">
            <i class="fas fa-arrow-left mr-1"></i> Volver al perfil
        </a>
    </div>
</div>


<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script>
    const ctx = document.getElementById('attChart').getContext('2d');
    const labels = Array.from({ length: <?= (int) $daysInMonth ?> }, (_, i) => i + 1);
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
                        title: function (context) {
                            return 'Dia ' + context[0].label;
                        },
                        label: function (context) {
                            return context.raw > 0 ? 'Asistio' : '';
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
                    max: 1.5
                }
            }
        }
    });
</script>