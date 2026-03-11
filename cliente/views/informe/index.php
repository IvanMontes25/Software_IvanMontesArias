<?php
// Vista: Mi Informe - Variables disponibles desde el controlador
?>
<?php include __DIR__ . '/../../theme/sb2/header.php'; ?>
<?php include __DIR__ . '/../../theme/sb2/sidebar.php'; ?>
<?php include __DIR__ . '/../../theme/sb2/topbar.php'; ?>

<style>
    body { background-color: #f8f9fc; }
    
    .report-card {
        max-width: 850px;
        margin: 0 auto;
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.08);
        overflow: hidden;
        border: 1px solid #e3e6f0;
    }

    /* Header del Reporte */
    .report-header {
        background: linear-gradient(135deg, #2c3e50 0%, #4a69bd 100%);
        color: white;
        padding: 30px;
        position: relative;
    }
    
    .report-brand h2 { font-weight: 800; margin: 0; font-size: 1.5rem; letter-spacing: 1px; }
    .report-brand p { margin: 5px 0 0 0; opacity: 0.8; font-size: 0.85rem; }

    /* Estado Badge Grande */
    .status-badge-lg {
        background: rgba(255,255,255,0.2);
        backdrop-filter: blur(5px);
        padding: 10px 20px;
        border-radius: 50px;
        border: 1px solid rgba(255,255,255,0.3);
        display: flex;
        align-items: center;
        gap: 10px;
    }

    /* Grid de Información */
    .info-section { padding: 30px; }
    
    .info-group-label {
        text-transform: uppercase;
        font-size: 0.75rem;
        color: #858796;
        font-weight: 700;
        margin-bottom: 5px;
        letter-spacing: 0.5px;
    }
    
    .info-group-value {
        font-size: 1rem;
        color: #2e384d;
        font-weight: 600;
        border-bottom: 1px solid #f1f3f9;
        padding-bottom: 5px;
        margin-bottom: 15px;
    }

    /* Tabla Estilizada */
    .modern-table { width: 100%; border-collapse: separate; border-spacing: 0; }
    .modern-table th { 
        background: #f8f9fc; color: #5a5c69; font-weight: 700; 
        text-transform: uppercase; font-size: 0.75rem; padding: 12px 15px;
        border-bottom: 2px solid #eaecf4; text-align: left;
    }
    .modern-table td { padding: 12px 15px; border-bottom: 1px solid #eaecf4; color: #5a5c69; }
    
    /* Footer */
    .report-footer {
        background: #f8f9fc;
        padding: 20px 30px;
        border-top: 1px solid #eaecf4;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    /* Botones Flotantes */
    .action-bar {
        max-width: 850px;
        margin: 0 auto 20px auto;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    /* Impresión */
    @media print {
        body * { visibility: hidden; }
        .report-card, .report-card * { visibility: visible; }
        .report-card { 
            position: absolute; left: 0; top: 0; width: 100%; margin: 0; 
            box-shadow: none; border: 1px solid #ccc;
        }
        .action-bar, nav, footer, .sidebar { display: none !important; }
        .report-header { background: #333 !important; color: #fff !important; -webkit-print-color-adjust: exact; }
        .badge { border: 1px solid #000; color: #000; }
    }
</style>

<div class="container-fluid sb2-content">

    <div class="action-bar">
        <div>
            <?php if ($modo === 'admin'): ?>
                <a href="reporte_cliente.php" class="btn btn-secondary shadow-sm">
                    <i class="fas fa-arrow-left mr-2"></i> Volver a Lista
                </a>
            <?php else: ?>
                <a href="perfil.php" class="btn btn-secondary shadow-sm">
                    <i class="fas fa-house mr-2"></i> Inicio
                </a>
            <?php endif; ?>
        </div>
        <button onclick="window.print()" class="btn btn-primary shadow-sm">
            <i class="fas fa-print mr-2"></i> Imprimir Informe
        </button>
    </div>

    <div class="report-card">
        
        <div class="report-header d-flex justify-content-between align-items-center">
            <div class="report-brand">
                <h2>GYM BODY TRAINING</h2>
                <p>Av. Apumalla 422, Caparazón Mall Center, La Paz</p>
                <p><i class="fas fa-envelope mr-1"></i> gymbodytraining23@gmail.com</p>
            </div>
            
            <div class="text-right">
                <div class="status-badge-lg">
                    <i class="fas <?= $estadoIcon ?> fa-lg"></i>
                    <span class="font-weight-bold text-uppercase"><?= $estadoTexto ?></span>
                </div>
                <div class="mt-2  ">
                    Generado el: <?= date('d/m/Y H:i') ?>
                </div>
            </div>
        </div>

        <div class="info-section">
            
            <h6 class="text-primary font-weight-bold mb-4 border-bottom pb-2">
                <i class="fas fa-user-circle mr-2"></i>Información del Cliente
            </h6>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="info-group-label">Nombre Completo</div>
                    <div class="info-group-value"><?= e($member['fullname']) ?></div>
                </div>
                <div class="col-md-3">
                    <div class="info-group-label">Cédula de Identidad</div>
                    <div class="info-group-value"><?= e($member['ci']) ?></div>
                </div>
                <div class="col-md-3">
                    <div class="info-group-label">ID de Sistema</div>
                    <div class="info-group-value font-weight-bold"><?= $membresiaID ?></div>
                </div>
                
                <div class="col-md-6">
                    <div class="info-group-label">Contacto / Teléfono</div>
                    <div class="info-group-value"><?= e($member['contact'] ?? '—') ?></div>
                </div>
                <div class="col-md-3">
                    <div class="info-group-label">Miembro Desde</div>
                    <div class="info-group-value"><?= date('d/m/Y', strtotime($member['dor'])) ?></div>
                </div>
                <div class="col-md-3">
                    <div class="info-group-label">Asistencias Totales</div>
                    <div class="info-group-value"><?= $asistencias ?> visitas</div>
                </div>
            </div>

            <h6 class="text-primary font-weight-bold mb-4 mt-4 border-bottom pb-2">
                <i class="fas fa-id-card-alt mr-2"></i>Detalle de Membresía Actual
            </h6>

            <table class="modern-table mb-4">
                <thead>
                    <tr>
                        <th width="30%">Plan Contratado</th>
                        <th width="20%">Último Pago</th>
                        <th width="20%">Fecha Pago</th>
                        <th width="20%">Vencimiento</th>
                        <th width="10%">Días Restantes</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <strong class="text-dark"><?= e($planNombre) ?></strong>
                            <div class="small text-muted">Duración: <?= $duracionDias ?> días</div>
                        </td>
                        <td>
                            <strong class="text-dark"><?= $importe ?></strong>
                        </td>
                        <td><?= $fechaPago ?></td>
                        <td>
                            <?php if ($fechaFin): ?>
                                <span class="text-dark font-weight-bold"><?= date('d/m/Y', strtotime($fechaFin)) ?></span>
                            <?php else: ?>
                                —
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($diasRestantes > 0): ?>
                                <span class="badge badge-success px-2 py-1"><?= $diasRestantes ?> días</span>
                            <?php elseif ($diasRestantes == 0): ?>
                                <span class="badge badge-warning px-2 py-1">Vence Hoy</span>
                            <?php else: ?>
                                <span class="badge badge-danger px-2 py-1">Vencido (<?= abs($diasRestantes) ?>)</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                </tbody>
            </table>

            <div class="alert alert-light border mt-2 small text-muted">
                <i class="fas fa-info-circle mr-1"></i>
                Nota: La fecha de vencimiento se calcula automáticamente sumando la duración del plan a la fecha del último pago registrado.
            </div>

        </div>

        <div class="report-footer">
            <div style="font-size: 0.8rem; color: #858796; max-width: 60%;">
                Este documento es un reporte generado automáticamente por el sistema de gestión Gym Body Training. 
                No requiere firma para su validez informativa interna.
            </div>
            <div class="text-center">
                <img src="/GymBodyTrainingEST/images/sello_gimnasio.png" 
                     alt="Sello Aprobado" 
                     style="height: 80px; opacity: 0.8;">
                <div style="font-size: 0.7rem; color: #aaa; margin-top: 5px;">Sello Digital</div>
            </div>
        </div>

    </div>
</div>

<?php include __DIR__ . '/../../theme/sb2/footer.php'; ?>