
<style>
.page-header{
  background: linear-gradient(135deg,#4e73df,#1cc88a);
  border-radius: 1rem;
  padding: 1.3rem 1rem;
  box-shadow: 0 8px 20px rgba(0,0,0,.16);
}
.page-header-inner{
  max-width: 1200px;
  margin: 0 auto;
  text-align: center;
  color: #fff;
}
.page-title{
  font-size: 1.35rem;
  font-weight: 700;
  margin-bottom: 4px;
}
.page-subtitle{
  font-size: .85rem;
  opacity: .9;
  margin-bottom: 0;
}
.progress-custom {
  height: 10px;
  background-color: #eaecf4;
  border-radius: 10px;
  overflow: hidden;
}
#progressbar {
  transition: width 1s linear;
}
.qr-container-box {
  background: #fff;
  padding: 20px;
  border-radius: 15px;
  border: 2px solid #eaecf4;
  display: flex;
  justify-content: center;
  align-items: center;
  min-height: 380px;
}
.qr-img-loading {
  opacity: 0.2;
  transform: scale(0.95);
}
</style>

<div class="sb2-content d-flex flex-column min-vh-100">
<div class="container-fluid flex-grow-1 py-3">

<div class="page-header mb-4">
  <div class="page-header-inner">
    <h1 class="page-title">
      Terminal de Acceso QR <i class="fas fa-qrcode ml-2"></i>
    </h1>
    <p class="page-subtitle">
      Control dinámico de ingresos mediante código QR seguro
    </p>
  </div>
</div>

<div class="row justify-content-center">

<div class="col-xl-6 col-lg-7 mb-4">
<div class="card shadow mb-4">
<div class="card-header py-3 d-flex justify-content-between align-items-center">
  <h6 class="m-0 font-weight-bold text-primary">
    <i class="fas fa-qrcode mr-1"></i> Código QR Activo
  </h6>
  <span class="badge badge-success">En línea</span>
</div>

<div class="card-body text-center">



<div class="qr-container-box mb-3">
  <img id="qr-img" 
       src="https://api.qrserver.com/v1/create-qr-code/?size=<?= $qrSize; ?>x<?= $qrSize; ?>&data=<?= urlencode($url); ?>&_=<?= time(); ?>" 
       alt="Código QR">
</div>

<div class="progress progress-custom mb-3">
  <div id="progressbar" class="progress-bar" role="progressbar" style="width: 100%"></div>
</div>



<div class="d-flex justify-content-between mb-3 small font-weight-bold text-gray-600">
  <span id="contador">Próxima renovación: 20s</span>
  <span id="slot-label">Slot: <?= (int)$slot; ?></span>
</div>

<div class="btn-group w-100 mb-2">
  <button id="btn-refresh" class="btn btn-primary btn-sm">
    <i class="fas fa-sync-alt mr-1"></i> Refrescar
  </button>
  <button id="btn-copy" class="btn btn-outline-primary btn-sm">
    <i class="far fa-copy"></i>
  </button>
  <button onclick="window.print()" class="btn btn-outline-danger btn-sm">
    <i class="fas fa-print"></i>
  </button>
</div>

<div id="msg" class="alert alert-danger mt-3 d-none">
  Error de conexión
</div>

</div>
</div>
</div>

<div class="col-xl-4 col-lg-5 mb-4">
<div class="card shadow mb-4">
<div class="card-header py-3">
  <h6 class="m-0 font-weight-bold text-primary">
    <i class="fas fa-info-circle mr-2"></i>Guía de Recepción
  </h6>
</div>
<div class="card-body">
  <p class="small text-muted">
    Este QR expira cada <?= $QR_TTL ?> segundos para evitar el uso de capturas de pantalla.
  </p>
  <p class="small text-muted">
    El cliente debe escanear directamente desde la cámara de su dispositivo.
  </p>
  <hr>
  <div class="text-center">
    <span class="badge badge-primary px-3 py-2">
      ID Sede: <?= (int)GYM_ID; ?>
    </span>
  </div>
  <div class="text-center mt-2 small text-muted">
    Hora servidor: <span id="last-upd"><?= $nowStr ?></span>
  </div>
</div>
</div>
</div>

</div>
</div>
</div>

<script>
const img = document.getElementById('qr-img');
const contador = document.getElementById('contador');
const progress = document.getElementById('progressbar');
const slotLabel = document.getElementById('slot-label');
const lastUpd = document.getElementById('last-upd');
const msg = document.getElementById('msg');

let totalTime = <?= $QR_TTL ?>;
let timeLeft = totalTime;
let tickInterval = null;

function renderTimer() {
    contador.textContent = `Próxima renovación: ${timeLeft}s`;
    progress.style.width = ((timeLeft / totalTime) * 100) + "%";
}

function startTick() {
    if (tickInterval) clearInterval(tickInterval);

    tickInterval = setInterval(async () => {
        timeLeft = Math.max(0, timeLeft - 1);
        renderTimer();

        if (timeLeft <= 0) {
            await refreshQR();
        }
    }, 1000);
}

async function refreshQR() {
    try {
        msg.classList.add('d-none');

        const r = await fetch('qr_recepcion_refresh.php', { cache: 'no-store' });
        const data = await r.json();

        if (!data.success) throw new Error("Refresh inválido");

        img.src =
            'https://api.qrserver.com/v1/create-qr-code/?size=360x360&data=' +
            encodeURIComponent(data.url) +
            '&_=' + Date.now();

        totalTime = data.ttl;
        timeLeft = data.remaining;

        slotLabel.textContent = "Slot: " + data.slot;
        lastUpd.textContent = new Date(data.ts * 1000).toLocaleTimeString();

        renderTimer();
    } catch (e) {
        msg.classList.remove('d-none');
    }
}

document.getElementById('btn-refresh').addEventListener('click', refreshQR);

window.addEventListener('load', async () => {
    await refreshQR();
    startTick();
});
</script>
