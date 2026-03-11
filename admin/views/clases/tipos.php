?>

<style>
    .type-card {
        border-radius: 16px;
        border: none;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        background-color: #fff;
    }

    .type-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.08) !important;
    }

    .type-card.inactivo {
        opacity: 0.6;
        filter: grayscale(0.5);
    }

    .color-dot {
        display: inline-block;
        width: 16px;
        height: 16px;
        border-radius: 50%;
        margin-right: 8px;
        vertical-align: middle;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.15);
    }

    .color-swatch {
        display: inline-block;
        width: 30px;
        height: 30px;
        border-radius: 50%;
        cursor: pointer;
        border: 2px solid transparent;
        transition: transform 0.15s ease, border-color 0.15s ease;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .color-swatch:hover {
        transform: scale(1.15);
        border-color: rgba(0, 0, 0, 0.2);
    }

    .btn-rounded {
        border-radius: 10px;
    }

    .modal-content-rounded {
        border-radius: 16px;
        border: none;
        overflow: hidden;
    }
</style>

<div class="sb2-content d-flex flex-column min-vh-100">
    <div class="container-fluid flex-grow-1">

        <!-- ===== PAGE HEADER ESTÁNDAR SISTEMA ===== -->
        <div class="page-header mb-4">
            <div class="page-header-inner">
                <h1 class="page-title">
                    Tipos de Clase
                </h1>
                <p class="page-subtitle">
                    Configura las categorías disponibles para agendar sesiones
                </p>
            </div>
        </div>

        <div class="card shadow mb-4 equipos-card">

            <div class="card-header py-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
                <h6 class="m-0 d-flex align-items-center gap-2">
                    <i class="fas fa-tags"> </i>
                    Gestión de Tipos de Clase
                </h6>

                <button class="btn btn-light btn-sm font-weight-bold rounded-pill" data-toggle="modal"
                    data-target="#modalCrear">
                    <i class="fas fa-plus-circle mr-1"></i> Nuevo Tipo
                </button>
            </div>

            <div class="card-body">

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

                <?php if (empty($tipos)): ?>
                    <div class="text-center py-5 text-muted">
                        <i class="fas fa-tags fa-3x mb-3 d-block"></i>
                        <h5>No hay tipos registrados</h5>
                        <p>Crea el primero para comenzar a organizar las clases.</p>
                    </div>
                <?php else: ?>

                    <div class="row">
                        <?php foreach ($tipos as $t): ?>
                            <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                                <div class="card shadow-sm h-100 type-card <?= !$t['activo'] ? 'inactivo' : '' ?>"
                                    style="border-top: 6px solid <?= e($t['color']) ?>; border-radius:1rem;">

                                    <div class="card-body d-flex flex-column">

                                        <div class="d-flex justify-content-between align-items-start mb-3">
                                            <div class="d-flex align-items-center">
                                                <span class="color-dot" style="background-color: <?= e($t['color']) ?>;"></span>
                                                <strong style="font-size:1.05rem;"><?= e($t['nombre']) ?></strong>
                                            </div>

                                            <?php if (!$t['activo']): ?>
                                                <span class="badge badge-secondary badge-soft">Inactivo</span>
                                            <?php endif; ?>
                                        </div>

                                        <?php if (!empty($t['descripcion'])): ?>
                                            <p class="text-muted small mb-4 flex-grow-1">
                                                <?= e($t['descripcion']) ?>
                                            </p>
                                        <?php else: ?>
                                            <p class="text-muted small mb-4 flex-grow-1 font-italic opacity-50">
                                                Sin descripción
                                            </p>
                                        <?php endif; ?>

                                        <div class="mt-auto d-flex gap-2">
                                            <button class="btn btn-outline-primary btn-sm rounded-pill flex-fill"
                                                onclick="abrirEditar(<?= e(json_encode($t)) ?>)">
                                                <i class="fas fa-edit mr-1"></i> Editar
                                            </button>

                                            <button class="btn btn-outline-danger btn-sm rounded-pill"
                                                onclick="abrirEliminar(<?= $t['id'] ?>, '<?= e($t['nombre']) ?>')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                <?php endif; ?>

            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalCrear" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content modal-content-rounded shadow">
            <div class="modal-header bg-light" style="border-bottom:1px solid #e2e8f0;">
                <h5 class="modal-title font-weight-bold text-gray-800">
                    <i class="fas fa-plus-circle mr-2 text-primary"></i>Nuevo Tipo de Clase
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="POST">
                <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                <input type="hidden" name="action" value="crear">
                <div class="modal-body p-4">
                    <div class="form-group">
                        <label class="form-label font-weight-bold text-gray-700">Nombre de la clase <span
                                class="text-danger">*</span></label>
                        <input type="text" name="nombre" class="form-control btn-rounded bg-light border-0"
                            placeholder="Ej: Crossfit, Yoga, Spinning..." required maxlength="100">
                    </div>

                    <div class="form-group mt-4">
                        <label class="form-label font-weight-bold text-gray-700">Color distintivo</label>
                        <div class="d-flex align-items-center mb-2">
                            <input type="color" name="color" value="#3b82f6" id="colorCrear"
                                class="p-0 border-0 mr-3 shadow-sm"
                                style="width:45px; height:45px; border-radius:10px; cursor:pointer;">
                            <span class="text-muted small">Haz clic para elegir un color exacto o selecciona uno rápido
                                abajo:</span>
                        </div>
                        <div class="d-flex flex-wrap pt-2" style="gap: 8px;">
                            <?php foreach ($colores as $c): ?>
                                <span class="color-swatch" style="background:<?= $c ?>;"
                                    onclick="document.getElementById('colorCrear').value='<?= $c ?>'"></span>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div class="form-group mt-4">
                        <label class="form-label font-weight-bold text-gray-700">Descripción <span
                                class="text-muted font-weight-normal">(opcional)</span></label>
                        <textarea name="descripcion" class="form-control btn-rounded bg-light border-0" rows="3"
                            placeholder="Escribe una breve descripción de lo que trata esta clase..."
                            maxlength="500"></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light" style="border-top:1px solid #e2e8f0;">
                    <button type="button" class="btn btn-secondary btn-rounded" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary btn-rounded shadow-sm px-4">
                        <i class="fas fa-check mr-2"></i>Guardar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modalEditar" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content modal-content-rounded shadow">
            <div class="modal-header bg-light" style="border-bottom:1px solid #e2e8f0;">
                <h5 class="modal-title font-weight-bold text-gray-800">
                    <i class="fas fa-edit mr-2 text-primary"></i>Editar Tipo de Clase
                </h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <form method="POST">
                <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                <input type="hidden" name="action" value="editar">
                <input type="hidden" name="id" id="editId">
                <div class="modal-body p-4">
                    <div class="form-group">
                        <label class="form-label font-weight-bold text-gray-700">Nombre <span
                                class="text-danger">*</span></label>
                        <input type="text" name="nombre" id="editNombre"
                            class="form-control btn-rounded bg-light border-0" required maxlength="100">
                    </div>

                    <div class="form-group mt-4">
                        <label class="form-label font-weight-bold text-gray-700">Color distintivo</label>
                        <div class="d-flex align-items-center mb-2">
                            <input type="color" name="color" id="editColor" class="p-0 border-0 mr-3 shadow-sm"
                                style="width:45px; height:45px; border-radius:10px; cursor:pointer;">
                        </div>
                        <div class="d-flex flex-wrap pt-2" style="gap: 8px;">
                            <?php foreach ($colores as $c): ?>
                                <span class="color-swatch" style="background:<?= $c ?>;"
                                    onclick="document.getElementById('editColor').value='<?= $c ?>'"></span>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div class="form-group mt-4">
                        <label class="form-label font-weight-bold text-gray-700">Descripción</label>
                        <textarea name="descripcion" id="editDesc" class="form-control btn-rounded bg-light border-0"
                            rows="3" maxlength="500"></textarea>
                    </div>

                    <div class="form-group mt-4 p-3 bg-light rounded">
                        <label class="form-label font-weight-bold text-gray-700 mb-2">Estado de la clase</label>
                        <select name="activo" id="editActivo" class="form-control custom-select btn-rounded">
                            <option value="1">🟢 Activo (Visible para agendar)</option>
                            <option value="0">🔴 Inactivo (Oculto)</option>
                        </select>
                        <small class="text-muted d-block mt-2"><i class="fas fa-info-circle mr-1"></i>Pasa a inactivo si
                            no quieres borrar el historial pero no quieres que se agenden más.</small>
                    </div>
                </div>
                <div class="modal-footer bg-light" style="border-top:1px solid #e2e8f0;">
                    <button type="button" class="btn btn-secondary btn-rounded" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary btn-rounded shadow-sm px-4">
                        <i class="fas fa-save mr-2"></i>Actualizar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modalEliminar" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content modal-content-rounded shadow text-center">
            <div class="modal-body p-4">
                <div class="text-danger mb-3">
                    <i class="fas fa-exclamation-circle fa-4x"></i>
                </div>
                <h5 class="font-weight-bold text-gray-800 mb-3">¿Estás seguro?</h5>
                <p class="text-muted mb-0">Vas a eliminar el tipo de clase <strong id="textoEliminarNombre"
                        class="text-gray-900"></strong>.</p>
                <p class="text-muted small mt-2">Si ya tiene clases agendadas, el sistema no te permitirá borrarlo.</p>
            </div>
            <div class="modal-footer d-flex justify-content-center border-0 pt-0 pb-4">
                <button type="button" class="btn btn-light btn-rounded px-4" data-dismiss="modal">Cancelar</button>
                <form id="formEliminar" method="POST" class="m-0 p-0">
                    <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                    <input type="hidden" name="action" value="eliminar">
                    <input type="hidden" name="id" id="eliminarId">
                    <button type="submit" class="btn btn-danger btn-rounded px-4 shadow-sm">
                        Sí, eliminar
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
<style>
    /* ===== ESTILO UNIFICADO SISTEMA ===== */

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

    .type-card {
        border: none;
        transition: all .2s ease;
    }

    .type-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 20px rgba(0, 0, 0, .12);
    }

    .type-card.inactivo {
        opacity: .6;
        filter: grayscale(.4);
    }

    .badge-soft {
        border-radius: 999px;
    }

    .color-dot {
        display: inline-block;
        width: 14px;
        height: 14px;
        border-radius: 50%;
        margin-right: 8px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, .15);
    }
</style>
<script>
    // Cargar datos en el Modal de Edición
    function abrirEditar(tipo) {
        document.getElementById('editId').value = tipo.id;
        document.getElementById('editNombre').value = tipo.nombre;
        document.getElementById('editColor').value = tipo.color || '#3b82f6';
        document.getElementById('editDesc').value = tipo.descripcion || '';
        document.getElementById('editActivo').value = tipo.activo;
        $('#modalEditar').modal('show');
    }

    // Modal de Eliminación
    function abrirEliminar(id, nombre) {
        document.getElementById('eliminarId').value = id;
        document.getElementById('textoEliminarNombre').innerText = nombre;
        $('#modalEliminar').modal('show');
    }
</script>
