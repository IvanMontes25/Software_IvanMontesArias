



function e($s){ return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }

/* payment_id */
$payment_id = (isset($_GET['payment_id']) && ctype_digit($_GET['payment_id']))
  ? (int)$_GET['payment_id'] : 0;

if ($payment_id <= 0) {
  echo "<div class='sb2-content'><div class='container-fluid'>
          <div class='alert alert-danger'>Pago no válido.</div>
        </div></div>";
  exit;
}

/* ===============================
   RECIBO = FOTO DEL PAGO
================================ */
$stmt = $db->prepare("
  SELECT
    pay.id,
    pay.user_id,
    pay.start_date,
    pay.paid_date,
    pay.amount,
    pay.plan_id,
    pay.productos,
    pay.status,
    pay.method,
    m.fullname,
    m.username,
    m.ci,
    m.contact,
    pl.nombre AS plan_nombre,
    pl.descripcion,
    pl.duracion_dias
  FROM payments pay
  LEFT JOIN members m ON m.user_id = pay.user_id
  LEFT JOIN planes  pl ON pl.id = pay.plan_id
  WHERE pay.id = ?
  LIMIT 1
");
$stmt->bind_param("i", $payment_id);
$stmt->execute();
$rec = $stmt->get_result()->fetch_assoc();
/* Validación por rol */
if (isset($_SESSION['rol']) && $_SESSION['rol'] === 'cliente') {
  if ((int)$rec['user_id'] !== (int)$_SESSION['user_id']) {
    echo "<div class='sb2-content'><div class='container-fluid'>
            <div class='alert alert-danger'>Acceso no autorizado.</div>
          </div></div>";
    exit;
  }
}

$stmt->close();

if (!$rec) {
  echo "<div class='sb2-content'><div class='container-fluid'>
          <div class='alert alert-warning'>Pago no encontrado.</div>
        </div></div>";
  exit;
}

/* Estado */
$badgeStatus = (strtolower($rec['status']) === 'pagado')
  ? "<span class='badge badge-success px-3 py-2 shadow-sm'>Pagado</span>"
  : "<span class='badge badge-secondary px-3 py-2 shadow-sm'>".e($rec['status'])."</span>";


/* Periodo estimado */
$periodo = '—';

if ($rec['duracion_dias'] && ($rec['start_date'] || $rec['paid_date'])) {
  try {
    $inicio = new DateTime(
      $rec['start_date'] ?: $rec['paid_date']
    );

    $fin = (clone $inicio)->modify('+'.$rec['duracion_dias'].' days');

    $periodo = $inicio->format('Y-m-d').' → '.$fin->format('Y-m-d');
  } catch(Throwable $e){}
}


/* Productos */
$productos = [];
$totalProductos = 0;
if ($rec['productos']) {
  $arr = json_decode($rec['productos'], true);
  if (is_array($arr)) {
    foreach ($arr as $p) {
      $precio = is_numeric($p['precio'] ?? null) ? (float)$p['precio'] : 0;
      $productos[] = [
        'nombre' => $p['nombre'] ?? 'Producto',
        'precio' => $precio
      ];
      $totalProductos += $precio;
    }
  }
}

/* Totales (OPCIÓN A: amount = TOTAL FINAL) */
$totalPedido = (float)$rec['amount'];
$totalPlan   = max(0, $totalPedido - $totalProductos);
/* =========================
   TIPO DE PAGO
========================= */
$tipoPago = '';

if (!empty($rec['plan_nombre']) && !empty($productos)) {
    $tipoPago = 'mixto';
} elseif (!empty($rec['plan_nombre'])) {
    $tipoPago = 'membresia';
} elseif (!empty($productos)) {
    $tipoPago = 'productos';
}

?>


<div class="container-fluid sb2-content">
<div class="actions-bar">
    <a href="pagos_cliente.php?user_id=<?= (int)$rec['user_id'] ?>" class="btn btn-secondary shadow-sm">
        <i class="fas fa-arrow-left mr-2"></i> Volver
    </a>
    <button onclick="window.print()" class="btn btn-primary shadow-sm">
        <i class="fas fa-print mr-2"></i> Imprimir / PDF
    </button>
</div>
<div class="invoice-container">
       
 
<?php if(strtoupper($rec['status']) === 'PAGADO'): ?>
    <div class="stamp">PAGADO</div>
<?php endif; ?>

<div class="invoice-header">
    <div class="invoice-brand">
        <h2>GYM BODY TRAINING</h2>
        <p>Tu centro de entrenamiento integral</p>
    </div>
    <div class="invoice-details">
        <div class="invoice-title">RECIBO</div>
        <div class="invoice-number">#<?= str_pad((string)$rec['id'], 6, '0', STR_PAD_LEFT) ?></div>
        <div style="font-size: 0.9rem; opacity: 0.9; margin-top:5px;">
            <?= date('d/m/Y H:i', strtotime($rec['paid_date'])) ?>
        </div>
    </div>
</div>

<div class="invoice-body">
    <?php if ($tipoPago === 'productos'): ?>
    <div class="invoice-type type-productos">
        <i class="fas fa-shopping-cart mr-2"></i>
        Venta de productos
    </div>
<?php elseif ($tipoPago === 'membresia'): ?>
    <div class="invoice-type type-membresia">
        <i class="fas fa-dumbbell mr-2"></i>
        Pago de membresía
    </div>
<?php elseif ($tipoPago === 'mixto'): ?>
    <div class="invoice-type type-mixto">
        <i class="fas fa-receipt mr-2"></i>
        Pago mixto (Membresía + Productos)
    </div>
<?php endif; ?>

    <div class="info-grid">
        <div class="info-col">
            <h5>Facturado A:</h5>
            <p><strong><?= e($rec['fullname']) ?></strong></p>
            <p>CI/NIT: <?= e($rec['ci']) ?></p>
            <p>Celular: <?= e($rec['contact']) ?></p>
        </div>
        <div class="info-col text-right">
            <h5>Emitido Por:</h5>
            <p><strong>Gym Body Training S.R.L.</strong></p>
            <p>Av. Apumalla 422</p>
            <p>La Paz, Bolivia</p>
        </div>
    </div>


<?php
$method = $rec['method'] ?? '';

switch ($method) {
    case 'Efectivo':
        $icon  = 'fa-money-bill-wave';
        $class = 'text-success';
        break;

    case 'QR':
        $icon  = 'fa-qrcode';
        $class = 'text-primary';
        break;

    case 'Transferencia':
        $icon  = 'fa-university';
        $class = 'text-info';
        break;

    default:
        $icon  = 'fa-question-circle';
        $class = 'text-muted';
        break;
}
?>
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

        <?php if (!empty($rec['plan_nombre'])): ?>
        <tr>
            <td>
                <strong><?= e($rec['plan_nombre']) ?></strong>
            </td>
            <td><?= e($periodo) ?></td>
            <td>
  <span class="<?= $class ?> font-weight-bold">
    <i class="fas <?= $icon ?> mr-1"></i>
    <?= e($method) ?>
  </span>
</td>
            <td class="total-col">
                Bs <?= number_format($totalPlan,2) ?>
            </td>
        </tr>
        <?php endif; ?>

        <?php foreach ($productos as $p): ?>
<tr>
    <td><strong><?= e($p['nombre']) ?></strong></td>
    <td>—</td>
    <td>
        <span class="<?= $class ?> font-weight-bold">
            <i class="fas <?= $icon ?> mr-1"></i>
            <?= e($method) ?>
        </span>
    </td>
    <td class="total-col">
        Bs <?= number_format($p['precio'],2) ?>
    </td>
</tr>
<?php endforeach; ?>

        </tbody>
    </table>

    <div class="invoice-footer">
        <div class="totals-box">
            <div class="totals-row">
                <span>Subtotal:</span>
                <span>Bs <?= number_format($totalPedido, 2) ?></span>
            </div>
            <div class="totals-row">
                <span>Descuento:</span>
                <span>Bs 0.00</span>
            </div>
            <div class="totals-row grand-total">
                <span>TOTAL:</span>
                <span>Bs <?= number_format($totalPedido, 2) ?></span>
            </div>
        </div>
    </div>

</div>

<div class="invoice-legal">
    Gracias por su preferencia.
</div>

</div>
</div>


<style>
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
        font-weight: 800;
        text-transform: uppercase;
        border-radius: 8px;
        transform: rotate(-10deg);
        position: absolute;
        top: 160px;
        right: 40px;
        font-size: 1.5rem;
        opacity: 0.3;
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

@media print{
  .sidebar,.topbar,.btn{display:none!important}
  .sb2-content{margin:0;padding:0}
}

.invoice-type{
    padding: 10px 15px;
    margin-bottom: 25px;
    border-radius: 6px;
    font-weight: 600;
    font-size: 0.9rem;
}

.type-productos{
    background: #eef2ff;
    color: #224abe;
    border-left: 4px solid #4e73df;
}

.type-membresia{
    background: #e6fff6;
    color: #1cc88a;
    border-left: 4px solid #1cc88a;
}

.type-mixto{
    background: #fff4e6;
    color: #f6a821;
    border-left: 4px solid #f6a821;
}

</style>

