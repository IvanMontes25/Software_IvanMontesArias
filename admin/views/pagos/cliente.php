
if (!$db instanceof mysqli) {
  die('No hay conexión a la base de datos');
}
// ================= PARÁMETROS =============
$id = (isset($_GET['id']) && ctype_digit($_GET['id'])) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
  header('Location: clientes.php?e=not_found');
  exit();
}

// ================= CLIENTE + ÚLTIMO PAGO ===
$stmt = $db->prepare("
  SELECT
    m.user_id,
    m.fullname,
    p.amount,
    p.paid_date
  FROM members m
  LEFT JOIN payments p
    ON p.user_id = m.user_id
   AND p.paid_date = (
     SELECT MAX(paid_date)
     FROM payments
     WHERE user_id = m.user_id
   )
  WHERE m.user_id = ?
  LIMIT 1
");
$stmt->bind_param("i",$id);
$stmt->execute();
$row = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$row) {
  header('Location: clientes.php?e=not_found');
  exit;
}


// ===== MEMBRESÍA VIGENTE =====
require_once __DIR__ . '/../includes/membership_helper.php';

$m = membership_last($db, $id);

$membresia_vigente = null;
$requiere_membresia = true;

$estado = membership_status($m);

if ($estado === 'activa') {

    $membresia_vigente = [
        'plan_nombre' => $m['plan_nombre'],
        'fecha_fin'   => membership_end_date($m)
    ];

    $requiere_membresia = false;
}


// ================= PLANES ==================
$planes = [];
$stmt = $db->prepare("
  SELECT id,nombre,duracion_dias,precio_base
  FROM planes
  WHERE estado='activo'
  ORDER BY nombre
");
$stmt->execute();
$res = $stmt->get_result();
while($p=$res->fetch_assoc()) $planes[]=$p;
$stmt->close();

// ================= CSRF ====================
if (empty($_SESSION['csrf_token'])) {
  $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrf = $_SESSION['csrf_token'];


?>

<?php include __DIR__.'/theme/sb2/header.php'; ?>
<?php include __DIR__.'/theme/sb2/sidebar.php'; ?>
<?php include __DIR__.'/theme/sb2/topbar.php'; ?>

<div class="sb2-content">
  <div class="container-fluid">
    <div class="row justify-content-center">
      <div class="col-xl-9 col-lg-10 col-md-11">

        <div class="card shadow mb-4">
          <div class="card-header bg-primary text-white text-center">
            <h5 class="m-0">Formulario de Pago</h5>
          </div>

          <div class="card-body">
            <div class="row">

              <!-- ===== COLUMNA IZQUIERDA ===== -->
              <div class="col-md-4 col-lg-3 border-right">

                <div class="text-center mb-3">
                  <img src="../images/logo_gimnasio.jpg" class="img-fluid rounded mb-2" style="max-width:120px" alt="Logo">
                  <h6 class="font-weight-bold mb-0">GYM BODY TRAINING</h6>
                  <small class="text-muted">La Paz - Bolivia</small>
                </div>

                <div class="bg-light rounded p-3">
                  <p class="mb-1"><strong>Cliente:</strong><br><?= htmlspecialchars($row['fullname']) ?></p>

                  <p class="mb-1"><strong>Estado:</strong><br>
                    <?= membership_badge($m); ?>


                  <p class="mb-1"><strong>Plan:</strong><br>
                    <?= $membresia_vigente ? htmlspecialchars($membresia_vigente['plan_nombre']) : '—' ?>
                  </p>

                  <p class="mb-0"><strong>Último pago:</strong><br>
                    <?= !empty($row['paid_date']) ? htmlspecialchars($row['paid_date']) : '—' ?>
                  </p>
                </div>

              </div>

              <!-- ===== COLUMNA DERECHA ===== -->
              <div class="col-md-8 col-lg-9">

                <?php if ($requiere_membresia): ?>
                  <div class="alert alert-danger">
                    Este cliente no tiene membresía activa. <strong>Debe registrar un plan</strong> para continuar.
                  </div>
                <?php elseif ($membresia_vigente): ?>
                  <div class="alert alert-success py-2">
                    Membresía vigente hasta <?= htmlspecialchars($membresia_vigente['fecha_fin']) ?>
                  </div>
                <?php endif; ?>

                <form id="formPago" action="registrar_pago.php" method="post">
<div class="form-group">
 
  <div class="form-group">
    <label class="font-weight-bold mb-3">Método de Pago</label>
    <div class="payment-methods-grid">
        <label class="payment-card-option">
            <input type="radio" name="method" value="Efectivo" checked required>
            <div class="payment-card-design">
                <div class="payment-card-icon"><i class="fas fa-money-bill-wave"></i></div>
                <div class="payment-card-text">
                    <span class="p-title">Efectivo</span>
                    <span class="p-desc">Pago en caja</span>
                </div>
                <i class="fas fa-check-circle p-check"></i>
            </div>
        </label>

        <label class="payment-card-option">
            <input type="radio" name="method" value="Transferencia">
            <div class="payment-card-design">
                <div class="payment-card-icon"><i class="fas fa-university"></i></div>
                <div class="payment-card-text">
                    <span class="p-title">Transferencia</span>
                    <span class="p-desc">Banca móvil</span>
                </div>
                <i class="fas fa-check-circle p-check"></i>
            </div>
        </label>

        <label class="payment-card-option">
            <input type="radio" name="method" value="QR">
            <div class="payment-card-design">
                <div class="payment-card-icon"><i class="fas fa-qrcode"></i></div>
                <div class="payment-card-text">
                    <span class="p-title">QR Simple</span>
                    <span class="p-desc">Escaneo rápido</span>
                </div>
                <i class="fas fa-check-circle p-check"></i>
            </div>
        </label>
    </div>
</div>

                  <input type="hidden" name="csrf" value="<?= htmlspecialchars($csrf) ?>">
                  <input type="hidden" name="user_id" value="<?= (int)$row['user_id'] ?>">
                  <input type="hidden" name="paid_date" value="<?= date('Y-m-d') ?>">
<input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf, ENT_QUOTES) ?>">


                  <!-- meses (por ahora fijo a 1) -->
                 

                  <!-- total (solo referencial; backend debe recalcular) -->
                  <input type="hidden" name="amount" id="amountInput" value="0">

                  <!-- plan seleccionado -->
                  <input type="hidden" name="plan_id" id="plan_id" value="0">

                  <!-- productos JSON -->
                  <input type="hidden" name="productos" id="productosInput" value="[]">

                  <div class="form-check mb-3">
                    <input class="form-check-input"
                           type="checkbox"
                           id="extenderMembresia"
                           <?= $requiere_membresia ? 'checked disabled' : 'checked' ?>>
                    <label class="form-check-label" for="extenderMembresia">
                      Registrar / extender membresía
                      <?php if ($requiere_membresia): ?>
                        <span class="text-danger">(obligatorio)</span>
                      <?php endif; ?>
                    </label>
                  </div>

                  <div class="section-title">
                    <span class="section-label">Planes de membresía</span>
                  </div>

                  <div class="row" id="planesContainer">
  <?php foreach($planes as $i=>$pl): ?>
    <div class="col-md-6 col-lg-4 mb-3">
      <label class="plan-card <?= $i===0 ? 'active' : '' ?>">
        <input type="radio"
       name="plan_select"
       class="plan-radio"

               value="<?= (float)$pl['precio_base'] ?>"
               data-plan-id="<?= (int)$pl['id'] ?>"
               data-plan-nombre="<?= htmlspecialchars($pl['nombre'], ENT_QUOTES) ?>"
               <?= $i===0 ? 'checked' : '' ?>>

        <div class="plan-card-body">
          <div class="plan-badge">
            <?= (int)$pl['duracion_dias'] ?> días
          </div>

          <div class="plan-title">
            <?= htmlspecialchars($pl['nombre']) ?>
          </div>

          <div class="plan-price">
            Bs <?= number_format((float)$pl['precio_base'],2) ?>
          </div>

          <div class="plan-cta">
            Seleccionar plan
          </div>
        </div>
      </label>
    </div>
  <?php endforeach; ?>
</div>


                  <hr>

                  <div class="section-title mt-4">
                    <span class="section-label">Productos</span>
                  </div>

                  <div id="productos-list"></div>

                  <button type="button" class="btn btn-sm btn-outline-primary mt-2" id="addProducto">
                    <i class="fas fa-plus"></i> Agregar producto
                  </button>

                  <hr>

                  <div class="bg-light p-3 rounded border">
                    <div class="d-flex justify-content-between">
                      <span>Subtotal membresía</span>
                      <strong>Bs <span id="subMembresia">0.00</span></strong>
                    </div>
                    <div class="d-flex justify-content-between">
                      <span>Subtotal productos</span>
                      <strong>Bs <span id="subProductos">0.00</span></strong>
                    </div>
                    <hr class="my-2">
                    <div class="d-flex justify-content-between">
                      <span>Total</span>
                      <strong class="text-success h5 mb-0">
                        Bs <span id="totalFinal">0.00</span>
                      </strong>
                    </div>
                  </div>

                  <div class="text-right mt-3">
                    <button type="submit" class="btn btn-success">
                      <i class="fas fa-save"></i> Guardar pago
                    </button>
                    <a href="clientes.php" class="btn btn-secondary">Cancelar</a>
                  </div>

                </form>

              </div>
            </div>
          </div>
        </div>

      </div>
    </div>
  </div>
</div>





<?php include __DIR__.'/theme/sb2/footer.php'; ?>

<!-- ✅ 1) SweetAlert2 DEBE CARGAR ANTES del script que lo usa -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
(function () {
  const requiereMembresia = <?= $requiere_membresia ? 'true' : 'false' ?>;

  document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('formPago');
    if (!form) return;

    const productosList  = document.getElementById('productos-list');
    const addBtn         = document.getElementById('addProducto');
    const chkMembresia   = document.getElementById('extenderMembresia');
    const radios         = document.querySelectorAll('input[name="plan_select"]');

    const amountInput    = document.getElementById('amountInput');
    const productosInput = document.getElementById('productosInput');
    const planIdInput    = document.getElementById('plan_id');

    const subM           = document.getElementById('subMembresia');
    const subP           = document.getElementById('subProductos');
    const totalF         = document.getElementById('totalFinal');

    let planPrecio = 0;
    let submitting = false;

    function recalcular() {
      if (requiereMembresia) chkMembresia.checked = true;

      let total = 0;
      let subMem = 0;
      let subProd = 0;

      if (chkMembresia.checked && planPrecio > 0 && Number(planIdInput.value) > 0) {
        subMem = planPrecio;
        total += planPrecio;
      }

      const productos = [];
      document.querySelectorAll('#productos-list .form-row').forEach(row => {
        const nombre = row.querySelector('.prod-nombre').value.trim();
        const precio = parseFloat(row.querySelector('.prod-precio').value) || 0;
        if (nombre && precio > 0) {
          subProd += precio;
          total += precio;
          productos.push({ nombre, precio });
        }
      });

      subM.textContent = subMem.toFixed(2);
      subP.textContent = subProd.toFixed(2);
      totalF.textContent = total.toFixed(2);

      amountInput.value = total.toFixed(2);
      productosInput.value = JSON.stringify(productos);

      if (!requiereMembresia && !chkMembresia.checked) {
        planIdInput.value = '';
      }
    }

    // Planes
    radios.forEach(radio => {
      radio.addEventListener('change', () => {
        planPrecio = Number(radio.value) || 0;
        planIdInput.value = Number(radio.dataset.planId) || 0;

        document.querySelectorAll('.plan-card').forEach(c => c.classList.remove('active'));
        radio.closest('.plan-card').classList.add('active');

        recalcular();
      });
    });

    if (radios.length) radios[0].dispatchEvent(new Event('change'));

    chkMembresia.addEventListener('change', recalcular);

    addBtn.addEventListener('click', () => {
      const div = document.createElement('div');
      div.className = 'form-row align-items-center mb-1';
      div.innerHTML = `
        <div class="col-6">
          <input type="text" class="form-control form-control-sm prod-nombre" placeholder="Producto">
        </div>
        <div class="col-4">
          <input type="number" step="0.01" min="0" class="form-control form-control-sm prod-precio" placeholder="Bs">
        </div>
        <div class="col-2 text-right">
          <button type="button" class="btn btn-sm btn-outline-danger">&times;</button>
        </div>
      `;
      div.querySelector('button').onclick = () => { div.remove(); recalcular(); };
      div.querySelectorAll('input').forEach(i => i.oninput = recalcular);
      productosList.appendChild(div);
    });

    // ✅ SUBMIT REAL
    form.addEventListener('submit', (e) => {
      if (submitting) return;
      e.preventDefault();

      const total = parseFloat(amountInput.value) || 0;
      if (total <= 0) {
        Swal.fire('Pago vacío', 'Debes seleccionar un plan o productos.', 'warning');
        return;
      }

      Swal.fire({
        title: 'Confirmar pago',
        html: `<strong>Total: Bs ${total.toFixed(2)}</strong>`,
        icon: 'success',
        showCancelButton: true,
        confirmButtonText: 'Guardar pago',
        cancelButtonText: 'Cancelar',
        reverseButtons: true
      }).then(r => {
        if (r.isConfirmed) {
          submitting = true;
          form.submit();
        }
      });
    });

  });
})();
</script>

<style>
  /* ===== RADIO OCULTO PERO FUNCIONAL ===== */
.plan-radio{
  position:absolute;
  opacity:0;
  pointer-events:none;
}
/* ===== PLAN CARD INTERACTIVA (RESTORE) ===== */
.plan-card{
  position: relative;
  display: block;
  border: 2px solid #e3e6f0;
  border-radius: 12px;
  cursor: pointer;
  transition: all .25s ease;
  background: #fff;
}

.plan-card:hover{
  border-color:#4e73df;
  transform: translateY(-2px);
}

.plan-card-body{
  padding:14px;
  border-radius:10px;
  text-align:center;
}

.plan-card input:checked + .plan-card-body,
.plan-card.active .plan-card-body{
  background: linear-gradient(135deg,#4e73df,#1cc88a);
  color:#fff;
}

.plan-card input:checked + .plan-card-body .plan-cta,
.plan-card.active .plan-cta{
  opacity:1;
}

.plan-cta{
  margin-top:6px;
  font-size:.8rem;
  opacity:.8;
}
/* Grid de Métodos de Pago */
.payment-methods-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
    gap: 12px;
    margin-bottom: 1.5rem;
}

.payment-card-option {
    cursor: pointer;
    margin-bottom: 0;
}

.payment-card-option input[type="radio"] {
    position: absolute;
    opacity: 0;
}

.payment-card-design {
    display: flex;
    align-items: center;
    padding: 12px;
    background: #fff;
    border: 2px solid #e3e6f0;
    border-radius: 10px;
    transition: all 0.2s ease;
    position: relative;
}

.payment-card-icon {
    width: 35px;
    height: 35px;
    background: #f8f9fc;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.1rem;
    color: #4e73df;
    margin-right: 10px;
}

.payment-card-text {
    display: flex;
    flex-direction: column;
    line-height: 1.2;
}

.p-title {
    font-weight: 700;
    font-size: 0.85rem;
    color: #4e5d6c;
}

.p-desc {
    font-size: 0.7rem;
    color: #858796;
}

.p-check {
    margin-left: auto;
    color: #1cc88a;
    font-size: 1.1rem;
    opacity: 0;
    transform: scale(0.5);
    transition: all 0.2s ease;
}

/* Estados Seleccionados */
.payment-card-option input[type="radio"]:checked + .payment-card-design {
    border-color: #4e73df;
    background-color: #f4f7ff;
    box-shadow: 0 3px 6px rgba(78, 115, 223, 0.1);
}

.payment-card-option input[type="radio"]:checked + .payment-card-design .payment-card-icon {
    background: #4e73df;
    color: #fff;
}

.payment-card-option input[type="radio"]:checked + .payment-card-design .p-check {
    opacity: 1;
    transform: scale(1);
}

.payment-card-option:hover .payment-card-design {
    border-color: #4e73df;
}
</style>
