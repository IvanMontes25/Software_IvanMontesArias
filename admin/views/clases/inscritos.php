?>

<div class="sb2-content d-flex flex-column min-vh-100">
    <div class="container-fluid flex-grow-1 py-4">

        <!-- HEADER PREMIUM -->
        <div class="premium-header mb-4">



            <div class="d-flex justify-content-between align-items-center flex-wrap">
                <div>
                    <h2 class="premium-title"><?= e($sesion['tipo_nombre']) ?></h2>
                    <p class="premium-subtitle">
                        <?= date('d/m/Y', strtotime($sesion['fecha'])) ?>
                        • <?= date('H:i', strtotime($sesion['hora_inicio'])) ?>
                        - <?= date('H:i', strtotime($sesion['hora_fin'])) ?>
                    </p>
                </div>
                <div class="text-end">
                    <div class="premium-metric">
                        <?= $counts['confirmada'] + $counts['asistio'] ?>
                        <span>/<?= $sesion['cupo_maximo'] ?></span>
                    </div>
                    <small>Inscritos</small>
                </div>

            </div>
        </div>

        <!-- CARD PRINCIPAL -->
        <div class="card premium-card">

            <?php if (empty($inscritos)): ?>
                <div class="text-center py-5 text-muted">
                    <i class="fas fa-users fa-3x mb-3"></i>
                    <h5>No hay inscritos aún</h5>
                </div>
            <?php else: ?>

                <div class="table-responsive">
                    <table class="table premium-table mb-0">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Cliente</th>
                                <th>Contacto</th>
                                <th>Estado</th>
                                <th>Reservó</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $n = 0;
                            foreach ($inscritos as $i):
                                if ($i['estado'] === 'cancelada')
                                    continue;
                                $n++;
                                $initial = strtoupper(substr($i['fullname'], 0, 1));
                                ?>
                                <tr>
                                    <td><?= $n ?></td>

                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-circle"><?= $initial ?></div>
                                            <div class="ml-2">
                                                <div class="fw-semibold"><?= e($i['fullname']) ?></div>
                                                <small class="text-muted">@<?= e($i['username']) ?></small>
                                            </div>
                                        </div>
                                    </td>

                                    <td><?= !empty($i['contact']) ? e($i['contact']) : '—' ?></td>

                                    <td>
                                        <?php
                                        $badgeClass = match ($i['estado']) {
                                            'confirmada' => 'badge-soft-success',
                                            'en_espera' => 'badge-soft-warning',
                                            'asistio' => 'badge-soft-primary',
                                            'no_asistio' => 'badge-soft-danger',
                                            default => 'badge-soft-secondary'
                                        };
                                        ?>
                                        <span class="<?= $badgeClass ?>">
                                            <?= ucfirst($i['estado']) ?>
                                        </span>
                                    </td>

                                    <td>
                                        <small class="text-muted">
                                            <?= date('d/m H:i', strtotime($i['fecha_reserva'])) ?>
                                        </small>
                                    </td>

                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

            <?php endif; ?>
        </div>
        <div class="text-center mt-4">
            <a href="mis_clases.php" class="btn-back-center">
                <i class="fas fa-arrow-left mr-2"></i>
                Volver a Mis Clases
            </a>
        </div>
    </div>
</div>

<style>
    .premium-header {
        background: linear-gradient(135deg, #4e73df, #1cc88a);
        border-radius: 20px;
        padding: 1.8rem;
        color: white;
        box-shadow: 0 12px 30px rgba(0, 0, 0, .15);
    }

    .premium-title {
        font-weight: 700;
        margin-bottom: 4px
    }

    .premium-subtitle {
        opacity: .9;
        margin-bottom: 0
    }

    .premium-metric {
        font-size: 1.8rem;
        font-weight: 700
    }

    .premium-metric span {
        font-size: 1rem;
        opacity: .8
    }

    .premium-card {
        border: none;
        border-radius: 18px;
        box-shadow: 0 8px 25px rgba(0, 0, 0, .08);
        overflow: hidden;
    }

    .premium-table thead {
        background: #f8fafc
    }

    .premium-table th {
        font-size: .75rem;
        text-transform: uppercase;
        color: #64748b;
    }

    .avatar-circle {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        background: linear-gradient(135deg, #4e73df, #1cc88a);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
    }



    .badge-soft-success {
        background: #dcfce7;
        color: #166534;
        padding: 6px 12px;
        border-radius: 20px
    }

    .badge-soft-warning {
        background: #fef3c7;
        color: #92400e;
        padding: 6px 12px;
        border-radius: 20px
    }

    .badge-soft-primary {
        background: #dbeafe;
        color: #1e40af;
        padding: 6px 12px;
        border-radius: 20px
    }

    .badge-soft-danger {
        background: #fee2e2;
        color: #991b1b;
        padding: 6px 12px;
        border-radius: 20px
    }

    .badge-soft-secondary {
        background: #e2e8f0;
        color: #334155;
        padding: 6px 12px;
        border-radius: 20px
    }

    /* Card más grande */
    .premium-card-xl {
        padding: 1.8rem;
    }

    /* Botón centrado */
    .btn-back-center {
        background: linear-gradient(135deg, #4e73df, #1cc88a);
        color: white;
        padding: 10px 22px;
        border-radius: 40px;
        font-weight: 600;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        transition: all .25s ease;
        box-shadow: 0 8px 20px rgba(0, 0, 0, .15);
    }

    .btn-back-center:hover {
        transform: translateY(-3px);
        box-shadow: 0 12px 25px rgba(0, 0, 0, .25);
        color: white;
        text-decoration: none;
    }
</style>
