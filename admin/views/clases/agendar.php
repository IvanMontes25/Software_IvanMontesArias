

date_default_timezone_set('America/La_Paz');
$user_id = (int) ($_SESSION['user_id'] ?? 0);  // staffs.user_id
$rol = $_SESSION['rol'] ?? '';

// Obtener tipos de clase
$tipos = [];
$res = $db->query("SELECT id, nombre, color FROM clase_tipos WHERE activo = 1 ORDER BY nombre");
while ($r = $res->fetch_assoc())
    $tipos[] = $r;

// Si es admin, puede agendar para cualquier entrenador
$entrenadores = [];
if ($rol === 'admin') {
    $res2 = $db->query("
    SELECT user_id, fullname FROM staffs
    WHERE LOWER(designation) LIKE '%entrenador%'
    ORDER BY fullname
");
    while ($r = $res2->fetch_assoc())
        $entrenadores[] = $r;
}

// Flash messages
$flash_ok = $_SESSION['flash_ok'] ?? null;
unset($_SESSION['flash_ok']);
$flash_err = $_SESSION['flash_error'] ?? null;
unset($_SESSION['flash_error']);

function e($s)
{
    return htmlspecialchars((string) $s, ENT_QUOTES, 'UTF-8');
}
?>

<div class="sb2-content d-flex flex-column min-vh-100">
    <div class="container-fluid flex-grow-1">

        <!-- ===== PAGE HEADER ESTÁNDAR SISTEMA ===== -->
        <div class="page-header mb-4">
            <div class="page-header-inner">
                <h1 class="page-title">
                    Agendar Nueva Clase
                </h1>
                <p class="page-subtitle">
                    Crea una sesión con fecha, horario y control de cupo
                </p>
            </div>
        </div>

        <?php if ($flash_ok): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle mr-2"></i><?= e($flash_ok) ?>
                <button type="button" class="close" data-dismiss="alert">
                    <span>&times;</span>
                </button>
            </div>
        <?php endif; ?>

        <?php if ($flash_err): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-triangle mr-2"></i><?= e($flash_err) ?>
                <button type="button" class="close" data-dismiss="alert">
                    <span>&times;</span>
                </button>
            </div>
        <?php endif; ?>

        <div class="row justify-content-center">
            <div class="col-lg-8">

                <!-- ===== CARD ESTILO PRO ===== -->
                <div class="card shadow mb-4 equipos-card">

                    <div class="card-header py-3 d-flex align-items-center justify-content-between">
                        <h6 class="m-0 d-flex align-items-center gap-2">
                            <i class="fas fa-calendar-plus"></i>
                            Formulario de Agendamiento
                        </h6>
                    </div>

                    <div class="card-body p-4">

                        <form id="formAgendar" action="clase_agendar_action.php" method="POST">
                            <?php if (function_exists('csrf_field'))
                                echo csrf_field(); ?>

                            <!-- Tipo de Clase -->
                            <div class="form-group">
                                <label class="font-weight-bold">Tipo de Clase *</label>
                                <div class="d-flex flex-wrap gap-2 mt-2">
                                    <?php foreach ($tipos as $t): ?>
                                        <label class="tipo-chip" style="--chip-color: <?= e($t['color']) ?>">
                                            <input type="radio" name="tipo_clase_id" value="<?= $t['id'] ?>" required>
                                            <span class="chip-label"><?= e($t['nombre']) ?></span>
                                        </label>
                                    <?php endforeach; ?>
                                </div>
                            </div>

                            <?php if ($rol === 'admin' && !empty($entrenadores)): ?>
                                <div class="form-group mt-3">
                                    <label class="font-weight-bold">Entrenador *</label>
                                    <select name="entrenador_override" class="form-control rounded-pill" required>
                                        <option value="">Selecciona entrenador…</option>
                                        <?php foreach ($entrenadores as $ent): ?>
                                            <option value="<?= $ent['user_id'] ?>"><?= e($ent['fullname']) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            <?php endif; ?>

                            <!-- Fecha y Horas -->
                            <div class="row mt-3">
                                <div class="col-md-4">
                                    <label class="font-weight-bold">Fecha *</label>
                                    <input type="date" name="fecha" class="form-control rounded-pill" required>
                                </div>

                                <div class="col-md-4">
                                    <label class="font-weight-bold">Hora Inicio *</label>
                                    <input type="time" name="hora_inicio" class="form-control rounded-pill" required>
                                </div>

                                <div class="col-md-4">
                                    <label class="font-weight-bold">Hora Fin *</label>
                                    <input type="time" name="hora_fin" class="form-control rounded-pill" required>
                                </div>
                            </div>

                            <!-- Cupo y Descripción -->
                            <div class="row mt-3">
                                <div class="col-md-4">
                                    <label class="font-weight-bold">Cupo Máximo *</label>
                                    <input type="number" name="cupo_maximo" class="form-control rounded-pill" value="15"
                                        min="1" max="100" required>
                                    <small class="text-muted">Capacidad máxima de la clase</small>
                                </div>

                                <div class="col-md-8">
                                    <label class="font-weight-bold">Descripción (opcional)</label>
                                    <textarea name="descripcion" class="form-control" rows="2"
                                        style="border-radius:1rem;" placeholder="Notas para los alumnos..."></textarea>
                                </div>
                            </div>

                            <!-- Botones -->
                            <div class="d-flex justify-content-end gap-3 mt-4">
                                <a href="mis_clases.php" class="btn btn-outline-secondary rounded-pill px-4">
                                    <i class="fas fa-arrow-left mr-1"></i> Cancelar
                                </a>

                                <button type="submit" class="btn btn-primary rounded-pill px-4">
                                    <i class="fas fa-calendar-plus mr-1"></i> Agendar Clase
                                </button>
                            </div>

                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .tipo-chip {
        position: relative;
        cursor: pointer;
    }

    .tipo-chip input[type="radio"] {
        position: absolute;
        opacity: 0;
    }

    .tipo-chip .chip-label {
        display: inline-block;
        padding: 8px 18px;
        border-radius: 20px;
        border: 2px solid #e2e8f0;
        font-weight: 600;
        font-size: 0.9rem;
        transition: all 0.2s;
        color: #475569;
    }

    .tipo-chip input:checked+.chip-label {
        background: var(--chip-color);
        color: white;
        border-color: var(--chip-color);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        transform: scale(1.05);
    }

    .tipo-chip:hover .chip-label {
        border-color: var(--chip-color);
    }

    /* ===== MISMO ESTILO SISTEMA ===== */

    .equipos-card {
        border: 0;
        border-radius: 1rem;
        overflow: hidden;
    }

    .equipos-card .card-header {
        background: linear-gradient(90deg, #4e73df, #1cc88a);
        color: #fff;
    }

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

    .tipo-chip {
        position: relative;
        cursor: pointer;
    }

    .tipo-chip input[type="radio"] {
        position: absolute;
        opacity: 0;
    }

    .tipo-chip .chip-label {
        display: inline-block;
        padding: 8px 18px;
        border-radius: 999px;
        border: 2px solid #e2e8f0;
        font-weight: 600;
        font-size: 0.9rem;
        transition: all 0.2s;
        color: #475569;
    }

    .tipo-chip input:checked+.chip-label {
        background: var(--chip-color);
        color: white;
        border-color: var(--chip-color);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        transform: scale(1.05);
    }

    .tipo-chip:hover .chip-label {
        border-color: var(--chip-color);
    }
</style>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>

    document.getElementById('formAgendar').addEventListener('submit', function (e) {

        const fechaInput = document.querySelector('[name="fecha"]');
        const inicioInput = document.querySelector('[name="hora_inicio"]');
        const finInput = document.querySelector('[name="hora_fin"]');

        const fecha = fechaInput.value.trim();
        const inicio = inicioInput.value.trim();
        const fin = finInput.value.trim();

        if (fecha === "" || inicio === "" || fin === "") {
            e.preventDefault();
            Swal.fire({
                icon: 'warning',
                title: 'Campos incompletos',
                text: 'Fecha, hora de inicio y hora fin son obligatorios.'
            });
            return;
        }

        if (fin <= inicio) {
            e.preventDefault();
            Swal.fire({
                icon: 'error',
                title: 'Horario inválido',
                text: 'La hora de fin debe ser posterior a la hora de inicio.'
            });
            return;
        }
    });


    // Validar tipo de clase
    const tipoSeleccionado = document.querySelector('[name="tipo_clase_id"]:checked');
    if (!tipoSeleccionado) {
        e.preventDefault();
        Swal.fire({
            icon: 'warning',
            title: 'Falta seleccionar tipo de clase',
            text: 'Debes elegir el tipo de clase antes de continuar.',
            confirmButtonColor: '#4e73df'
        });
        return;
    }
    if (!fecha || !inicio || !fin) {
        e.preventDefault();
        Swal.fire({
            icon: 'warning',
            title: 'Campos incompletos',
            text: 'Completa todos los campos requeridos.',
            confirmButtonColor: '#4e73df'
        });
        return;
    }
    if (fin <= inicio) {
        e.preventDefault();
        Swal.fire({
            icon: 'error',
            title: 'Horario inválido',
            text: 'La hora de fin debe ser posterior a la hora de inicio.',
            confirmButtonColor: '#4e73df'
        });
        return;
    }
});

    const fechaInput = document.querySelector('[name="fecha"]');
    const hoy = new Date();
    hoy.setMinutes(hoy.getMinutes() - hoy.getTimezoneOffset());
    fechaInput.min = hoy.toISOString().split('T')[0];
</script>
