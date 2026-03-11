<?php
// Vista - Variables disponibles desde el controlador
?>
<?php include __DIR__ . '/../../theme/sb2/header.php'; ?>
<?php include __DIR__ . '/../../theme/sb2/sidebar.php'; ?>
<?php include __DIR__ . '/../../theme/sb2/topbar.php'; ?>

<style>
    /* Estilos complementarios para mantener la esencia de SB Admin 2 pero mejorada */
    .card-announcement {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        border-radius: 8px; /* Radio clásico de bootstrap/sb admin */
        overflow: hidden;
    }
    .card-announcement:hover {
        transform: translateY(-3px);
        box-shadow: 0 .5rem 1rem rgba(0,0,0,.15)!important;
    }

    /* Imagen más compacta (mediana) */
    .news-img-wrap {
        position: relative;
        height: 140px; /* Reducido de 180px para que no se vea tan gigante */
        background-color: #eaecf4;
        overflow: hidden;
    }
    .news-img-wrap img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.3s ease;
    }
    .card-announcement:hover .news-img-wrap img {
        transform: scale(1.03);
    }

    /* Fecha sobre la imagen */
    .date-badge {
        position: absolute;
        top: 10px;
        right: 10px;
        background: rgba(255, 255, 255, 0.9);
        color: #4e73df;
        padding: 3px 10px;
        border-radius: 4px;
        font-weight: 700;
        font-size: 0.75rem;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        z-index: 2;
    }

    /* Limitar el texto a 2 líneas para uniformidad de las cards */
    .text-limit-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    /* Timeline de historial mejorado */
    .timeline-container {
        max-height: 300px; 
        overflow-y: auto; 
        padding-right: 10px;
    }
    .timeline-container::-webkit-scrollbar { width: 6px; }
    .timeline-container::-webkit-scrollbar-track { background: #f8f9fc; }
    .timeline-container::-webkit-scrollbar-thumb { background-color: #dddfeb; border-radius: 4px; }
    .timeline-container::-webkit-scrollbar-thumb:hover { background-color: #b7b9cc; }

    .timeline-node {
        position: relative;
        margin-bottom: 1.5rem;
        padding-left: 1.5rem;
        border-left: 2px solid #eaecf4;
    }
    .timeline-node:last-child { margin-bottom: 0; border-left-color: transparent; }
    .timeline-node::before {
        content: '';
        position: absolute;
        left: -6px;
        top: 0;
        width: 10px;
        height: 10px;
        border-radius: 50%;
        background: #4e73df;
    }
    
    /* Botones de switch (Preferencias) */
    .pref-toggle-label {
        border: 1px solid #d1d3e2;
        padding: 6px 10px;
        cursor: pointer;
        transition: all 0.2s;
        text-align: center;
        font-weight: 600;
        font-size: 0.85rem;
        color: #858796;
        background: #fff;
        border-radius: 4px;
        display: block;
    }
    .pref-toggle-label:hover { background: #f8f9fc; }
    .btn-check:checked + .lbl-on { border-color: #1cc88a; background-color: #1cc88a; color: #fff; }
    .btn-check:checked + .lbl-off { border-color: #e74a3b; background-color: #e74a3b; color: #fff; }
</style>

<div class="container-fluid">

    <div class="d-sm-flex align-items-center justify-content-between mb-4 mt-3">
        <div>
            <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-bullhorn text-primary mr-2"></i> Centro de Novedades</h1>
            <p class="mb-0 text-gray-500 small mt-1">Noticias del gimnasio, anuncios y tus recordatorios personales.</p>
        </div>

        <?php if ($flash): ?>
            <div class="alert alert-<?= $flash_type ?> py-2 px-3 mb-0 shadow-sm border-left-<?= $flash_type ?> mt-3 mt-sm-0">
                <i class="fas fa-info-circle mr-1"></i> <?= h($flash) ?>
            </div>
        <?php endif; ?>
    </div>

    <div class="row">
        
        <div class="col-lg-8 mb-4">
            
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">
                        Publicaciones Recientes 
                        <span class="badge badge-primary badge-counter ml-1"><?= count($announcements) ?></span>
                    </h6>
                    <div class="input-group input-group-sm" style="width: 200px;">
                        <input type="text" id="searchInp" class="form-control bg-light border-0 small" placeholder="Buscar noticia...">
                        <div class="input-group-append">
                            <button class="btn btn-primary" type="button">
                                <i class="fas fa-search fa-sm"></i>
                            </button>
                        </div>
                    </div>
                </div>
                
                <div class="card-body bg-light">
                    <div class="row" id="annList">
                        <?php if (empty($announcements)): ?>
                            <div class="col-12 text-center py-5">
                                <i class="fas fa-inbox fa-3x text-gray-300 mb-3"></i>
                                <p class="text-gray-500 mb-0">No hay publicaciones recientes</p>
                            </div>
                        <?php else: ?>
                            <?php foreach ($announcements as $a): 
                                $id = $a['id'];
                                $date = pretty_date_short($a['date']);
                                $msg = $a['message'];
                                $rawImgs = !empty($a['images']) ? json_decode($a['images'], true) : [];
                                $firstImg = (!empty($rawImgs) && is_array($rawImgs)) ? $rawImgs[0] : null;
                                
                                $searchText = h(mb_strtolower($msg . ' ' . $date, 'UTF-8'));
                            ?>
                            
                            <div class="col-md-6 mb-4 announcement-item" data-text="<?= $searchText ?>">
                                <div class="card shadow-sm h-100 border-0 card-announcement">
                                    
                                    <?php if ($firstImg): ?>
                                        <div class="news-img-wrap">
                                            <img src="../../<?= h($firstImg) ?>" alt="Imagen noticia">
                                            <div class="date-badge"><?= $date ?></div>
                                        </div>
                                    <?php else: ?>
                                        <div class="bg-gradient-primary text-white px-3 py-2 d-flex justify-content-between align-items-center">
                                            <small><i class="far fa-calendar-alt mr-1"></i> <?= $date ?></small>
                                            <i class="fas fa-newspaper text-white-50"></i>
                                        </div>
                                    <?php endif; ?>

                                    <div class="card-body p-3 d-flex flex-column">
                                        <div class="mb-3 flex-grow-1">
                                            <p class="card-text text-gray-800 text-limit-2 text-sm mb-0">
                                                <?= nl2br(h($msg)) ?>
                                            </p>
                                        </div>
                                        <button class="btn btn-light border btn-sm btn-block text-primary font-weight-bold"
                                                data-toggle="modal" data-target="#viewModal" data-id="<?= $id ?>">
                                            Leer más <i class="fas fa-arrow-right fa-sm ml-1"></i>
                                        </button>
                                    </div>
                                </div>

                                <div id="msg-<?= $id ?>" class="d-none">
                                    <div class="mb-3 font-weight-bold text-gray-800 border-bottom pb-2">
                                        <i class="far fa-calendar text-primary mr-1"></i> Publicado el <?= $date ?>
                                    </div>
                                    <div class="text-gray-800 text-md" style="line-height: 1.6;">
                                        <?= nl2br(h($msg)) ?>
                                    </div>
                                    <?php if (!empty($rawImgs)): ?>
                                        <div class="row mt-4">
                                            <?php foreach ($rawImgs as $img): ?>
                                            <div class="col-12 mb-3">
                                                <img src="../../<?= h($img) ?>" class="img-fluid rounded border" alt="Adjunto">
                                            </div>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        <?php endif; ?>

                        <div class="col-12 text-center d-none" id="noResults">
                            <p class="text-gray-500 mt-3"><i class="fas fa-search-minus mr-1"></i> No se encontraron noticias.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            
            <div class="card shadow mb-4 border-bottom-primary">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-bell mr-2"></i>Avisos de Pago</h6>
                </div>
                <div class="card-body">
                    <p class="small text-gray-600 mb-3">
                        ¿Deseas recibir alertas automáticas cuando tu membresía esté por vencer?
                    </p>
                    <form method="post" id="prefForm">
                        <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                        <div class="row no-gutters">
                            <div class="col-6 pr-2">
                                <input type="radio" class="btn-check d-none" name="reminder" id="remOn" value="1" <?= ($reminder==='1')?'checked':'' ?> onchange="this.form.submit()">
                                <label class="pref-toggle-label lbl-on w-100" for="remOn">
                                    <i class="fas fa-check mr-1"></i> Sí, avisarme
                                </label>
                            </div>
                            <div class="col-6 pl-2">
                                <input type="radio" class="btn-check d-none" name="reminder" id="remOff" value="0" <?= ($reminder==='0')?'checked':'' ?> onchange="this.form.submit()">
                                <label class="pref-toggle-label lbl-off w-100" for="remOff">
                                    <i class="fas fa-times mr-1"></i> No
                                </label>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-history mr-2"></i>Historial de Alertas</h6>
                </div>
                <div class="card-body">
                    <?php if (empty($paymentReminders)): ?>
                        <div class="text-center text-gray-500 small py-3">
                            No tienes alertas recientes.
                        </div>
                    <?php else: ?>
                        <div class="timeline-container">
                            <?php foreach ($paymentReminders as $r): ?>
                                <div class="timeline-node">
                                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                        <?= pretty_date_short($r['created_at']) ?>
                                    </div>
                                    <div class="text-sm text-gray-800 bg-light p-2 rounded border-left-info">
                                        <?= nl2br(h($r['message'])) ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

        </div>
    </div>

</div>

<div class="modal fade" id="viewModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg" role="document">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-light">
                <h5 class="modal-title font-weight-bold text-primary">Detalle de la Publicación</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-4" id="modalBody">
                <div class="d-flex justify-content-center my-4">
                    <div class="spinner-border text-primary" role="status"><span class="sr-only">Cargando...</span></div>
                </div>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {

    const modal = document.getElementById('viewModal');
    const modalBody = document.getElementById('modalBody');

    // Cargar contenido en el modal
    document.querySelectorAll('[data-target="#viewModal"]').forEach(btn => {
        btn.addEventListener('click', function () {
            const id = this.getAttribute('data-id');
            const source = document.getElementById('msg-' + id);
            
            modalBody.innerHTML = `<div class="text-center py-4"><div class="spinner-border text-primary"></div></div>`;
            
            setTimeout(() => {
                modalBody.innerHTML = source ? source.innerHTML : `<p class="text-center text-muted">Contenido no disponible.</p>`;
            }, 100); // Pequeño delay para dar sensación de carga
        });
    });

    // Filtro de búsqueda en tiempo real
    const searchInput = document.getElementById('searchInp');
    const items = document.querySelectorAll('.announcement-item');
    const noResults = document.getElementById('noResults');

    if(searchInput) {
        searchInput.addEventListener('keyup', function(e) {
            const term = e.target.value.toLowerCase();
            let visibleCount = 0;

            items.forEach(item => {
                const text = item.getAttribute('data-text'); 
                if(text && text.includes(term)) {
                    item.classList.remove('d-none');
                    visibleCount++;
                } else {
                    item.classList.add('d-none');
                }
            });

            if(visibleCount === 0) {
                noResults.classList.remove('d-none');
            } else {
                noResults.classList.add('d-none');
            }
        });
    }
});

</script>
<style>
/* ====== FIX: modal con contenido largo ====== */
#viewModal .modal-dialog {
  width: 95%;
  max-width: 900px; /* opcional: más amplio que modal-lg */
}

#viewModal .modal-content {
  max-height: calc(100vh - 60px); /* que nunca se salga de la pantalla */
  display: flex;
  flex-direction: column;
}

#viewModal .modal-body {
  overflow-y: auto;              /* scroll vertical */
  max-height: calc(100vh - 200px); /* ajusta header+footer */
  -webkit-overflow-scrolling: touch;
}

/* Imágenes dentro del modal */
#viewModal .modal-body img {
  max-width: 100%;
  height: auto;
}
</style>
<?php include __DIR__ . '/../../theme/sb2/footer.php'; ?>