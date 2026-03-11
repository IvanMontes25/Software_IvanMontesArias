<?php
// Vista - Variables disponibles desde el controlador
?>
<?php include __DIR__ . '/../../theme/sb2/header.php'; ?>
<?php include __DIR__ . '/../../theme/sb2/sidebar.php'; ?>
<?php include __DIR__ . '/../../theme/sb2/topbar.php'; ?>

<style>
    body { background-color: #f8f9fc; }
    
    .invoice-container {
        max-width: 800px;
        margin: 0 auto 50px auto;
        background: #fff;
        border-radius: 8px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        overflow: hidden;
        position: relative;
    }
    
    /* Header */
    .invoice-header {
        background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
        color: #fff;
        padding: 40px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .invoice-brand h2 { font-weight: 800; margin: 0; letter-spacing: 1px; }
    .invoice-brand p { margin: 5px 0 0 0; opacity: 0.8; font-size: 0.9rem; }
    
    .invoice-details { text-align: right; }
    .invoice-title { font-size: 2rem; font-weight: 900; opacity: 0.3; line-height: 1; text-transform: uppercase; }
    .invoice-number { font-size: 1.2rem; font-weight: bold; margin-top: 10px; }
    
    /* Cuerpo */
    .invoice-body { padding: 40px; }
    
    .info-grid {
        display: flex;
        justify-content: space-between;
        margin-bottom: 40px;
    }
    
    .info-col h5 { font-size: 0.85rem; text-transform: uppercase; color: #b7b9cc; font-weight: 700; margin-bottom: 10px; }
    .info-col p { margin: 0; font-size: 0.95rem; color: #5a5c69; line-height: 1.5; }

    /* Tabla */
    .invoice-table { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
    .invoice-table th { 
        text-align: left; padding: 15px; 
        background: #f8f9fc; color: #5a5c69; 
        text-transform: uppercase; font-size: 0.75rem; font-weight: 700; border-bottom: 2px solid #e3e6f0; 
    }
    .invoice-table td { padding: 15px; border-bottom: 1px solid #e3e6f0; color: #5a5c69; }
    .invoice-table td.total-col { text-align: right; font-weight: bold; color: #2e2f3e; }
    
    /* Footer Totales */
    .invoice-footer { display: flex; justify-content: flex-end; }
    .totals-box { width: 250px; }
    .totals-row { display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #f1f3f9; }
    .totals-row.grand-total { border-bottom: none; border-top: 2px solid #4e73df; padding-top: 15px; margin-top: 5px; }
    .totals-row.grand-total span { font-size: 1.2rem; font-weight: 800; color: #4e73df; }
    
    /* Legal */
    .invoice-legal {
        background: #f8f9fc;
        padding: 20px 40px;
        text-align: center;
        font-size: 0.8rem;
        color: #858796;
        border-top: 1px solid #e3e6f0;
    }

    /* Stamp */
    .stamp {
        display: inline-block;
        padding: 5px 15px;
        border: 2px solid #1cc88a;
        color: #1cc88a;
        font-weight: 1000;
        text-transform: uppercase;
        border-radius: 8px;
        transform: rotate(-10deg);
        position: absolute;
        top: 160px;
        right: 40px;
        font-size: 1.5rem;
        
        z-index: 0;
    }

    .actions-bar { max-width: 800px; margin: 0 auto 20px; display: flex; justify-content: space-between; }

    @media print {
        body * { visibility: hidden; }
        .invoice-container, .invoice-container * { visibility: visible; }
        .invoice-container { 
            position: absolute; left: 0; top: 0; width: 100%; margin: 0; 
            box-shadow: none; border: 1px solid #ddd;
        }
        .actions-bar, #accordionSidebar, nav.topbar, footer { display: none !important; }
        .invoice-header { background: #eee !important; color: #333 !important; -webkit-print-color-adjust: exact; }
    }
</style>

<div class="container-fluid sb2-content">

    <?php if ($error): ?>
        <div class="alert alert-danger shadow-sm my-4">
            <i class="fas fa-exclamation-triangle mr-2"></i> <?= h($error) ?>
            <div class="mt-2"><a href="membresia_pagos.php" class="btn btn-sm btn-outline-danger">Volver</a></div>
        </div>
    <?php else: ?>

    <div class="actions-bar">
        <a href="membresia_pagos.php" class="btn btn-secondary shadow-sm">
            <i class="fas fa-arrow-left mr-2"></i> Volver
        </a>
        <button onclick="window.print()" class="btn btn-primary shadow-sm">
            <i class="fas fa-print mr-2"></i> Imprimir / PDF
        </button>
    </div>

    <div class="invoice-container">
        
        <?php if($estado === 'PAGADO' || $estado === 'COMPLETED' || $estado === 'PAID'): ?>
            <div class="stamp">PAGADO</div>
        <?php endif; ?>

        <div class="invoice-header">
            <div class="invoice-brand">
                <h2>GYM BODY TRAINING</h2>
                <p>Tu centro de entrenamiento integral</p>
            </div>
            <div class="invoice-details">
                <div class="invoice-title">RECIBO</div>
                <div class="invoice-number">#<?= $reciboNro ?></div>
                <div style="font-size: 0.9rem; opacity: 0.9; margin-top:5px;">
                    <?= $fechaEmision ?> &bull; <?= $horaEmision ?>
                </div>
            </div>
        </div>

        <div class="invoice-body">
            
            <div class="info-grid">
                <div class="info-col">
                    <h5>Facturado A:</h5>
                    <p><strong><?= h($clienteNombre) ?></strong></p>
                    <p>CI/NIT: <?= h($clienteCI) ?></p>
                    <p><?= h($clienteEmail) ?></p>
                </div>
                <div class="info-col text-right">
                    <h5>Emitido Por:</h5>
                    <p><strong>Gym Body Training S.R.L.</strong></p>
                    <p>Av. Apumalla 422, Caparazón Mall</p>
                    <p>La Paz, Bolivia</p>
                    <p>+591 62452438</p>
                </div>
            </div>

            <table class="invoice-table">
                <thead>
                    <tr>
                        <th width="50%">Descripción</th>
                        <th width="20%">Periodo</th>
                        <th width="15%">Método</th>
                        <th class="text-right" width="15%">Importe</th>
                    </tr>
                </thead>
                <tbody>

<?php if (!empty($pay['servicio'])): ?>
<tr>
    <td>
        <strong><?= h($pay['servicio']) ?></strong><br>
        <small class="text-muted">Membresía</small>
    </td>
    <td><?= (int)$pay['duracion_dias'] ?> Días</td>
    <td><?= h($metodo) ?></td>
    <td class="total-col">
        Bs <?= number_format($totalPlan, 2) ?>
    </td>
</tr>
<?php endif; ?>

<?php foreach ($productos as $p): ?>
<tr>
    <td>
        <strong><?= h($p['nombre']) ?></strong><br>
        <small class="text-muted">Producto</small>
    </td>
    <td>—</td>
    <td><?= h($metodo) ?></td>
    <td class="total-col">
        Bs <?= number_format($p['precio'], 2) ?>
    </td>
</tr>
<?php endforeach; ?>

</tbody>


            </table>

            <div class="invoice-footer">
                <div class="totals-box">
                    <div class="totals-row">
    <span>Subtotal:</span>
    <span>Bs <?= number_format($total, 2) ?></span>
</div>

                    <div class="totals-row">
                        <span>Descuento:</span>
                        <span>Bs 0.00</span>
                    </div>
                    <div class="totals-row grand-total">
                        <span>TOTAL:</span>
                        <span>Bs <?= number_format($total, 2) ?></span>
                    </div>
                </div>
            </div>

            <div class="mt-5 pt-3 border-top d-flex align-items-center">
                <div style="background: #fff; padding: 5px; border: 1px solid #ddd;">
                    <i class="fas fa-qrcode fa-3x text-dark"></i>
                </div>
                <div class="ml-3 small text-muted">
                    <p class="mb-0"><strong>Código de control:</strong> <?= strtoupper(md5($reciboNro . $fechaEmision)) ?></p>
                    <p class="mb-0">Este documento es un comprobante de pago válido del sistema interno.</p>
                </div>
            </div>

        </div>

        <div class="invoice-legal">
            Gracias por su preferencia. Para cualquier reclamo referente a este recibo, por favor contacte a administración dentro de las 24 horas.<br>
            <strong>www.gymbodytraining.com</strong>
        </div>
    </div>

    <?php endif; ?>

</div>

<?php include __DIR__ . '/../../theme/sb2/footer.php'; ?>