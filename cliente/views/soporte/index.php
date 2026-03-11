<?php
// Vista: Soporte
// Variables: $page, $pageTitle
?>
<?php include __DIR__ . '/../../theme/sb2/header.php'; ?>
<?php include __DIR__ . '/../../theme/sb2/sidebar.php'; ?>
<?php include __DIR__ . '/../../theme/sb2/topbar.php'; ?>

<style>
    :root {
        --card-radius: 18px;
        --primary-gradient: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
        --whatsapp-gradient: linear-gradient(135deg, #25D366 0%, #128C7E 100%);
    }

    .modern-card {
        border: 0;
        border-radius: var(--card-radius);
        box-shadow: 0 5px 20px rgba(0,0,0,0.05);
        background: #fff;
        overflow: hidden;
        transition: transform 0.2s ease;
    }

    /* Sección de Contacto */
    .contact-card {
        background: #fff;
        height: 100%;
        position: relative;
    }
    
    .icon-box-lg {
        width: 60px;
        height: 60px;
        border-radius: 15px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.8rem;
        margin-bottom: 1rem;
    }

    .btn-whatsapp {
        background: var(--whatsapp-gradient);
        color: white;
        border: 0;
        box-shadow: 0 4px 15px rgba(37, 211, 102, 0.4);
        transition: all 0.2s;
    }
    .btn-whatsapp:hover {
        transform: translateY(-2px);
        color: white;
        box-shadow: 0 6px 20px rgba(37, 211, 102, 0.6);
    }

    /* Acordeón FAQ Moderno */
    .custom-accordion .card {
        border: 1px solid #f1f3f9;
        margin-bottom: 10px;
        border-radius: 12px !important;
        box-shadow: none;
        overflow: hidden;
    }
    
    .custom-accordion .card-header {
        background: #fff;
        border: 0;
        padding: 0;
    }

    .custom-accordion .btn-link {
        color: #5a5c69;
        font-weight: 600;
        text-decoration: none;
        padding: 1.2rem 1.5rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        width: 100%;
        text-align: left;
        transition: 0.2s;
    }

    .custom-accordion .btn-link:hover {
        background: #f8f9fc;
        color: #4e73df;
    }

    .custom-accordion .btn-link[aria-expanded="true"] {
        color: #4e73df;
        background: #f0f4ff;
    }

    .custom-accordion .btn-link .icon-toggle {
        font-size: 0.8rem;
        transition: transform 0.3s;
    }
    
    .custom-accordion .btn-link[aria-expanded="true"] .icon-toggle {
        transform: rotate(180deg);
    }

    /* Toast Notificación */
    #customToast {
        visibility: hidden;
        min-width: 250px;
        background-color: #333;
        color: #fff;
        text-align: center;
        border-radius: 50px;
        padding: 12px;
        position: fixed;
        z-index: 9999;
        left: 50%;
        bottom: 30px;
        transform: translateX(-50%);
        font-size: 0.9rem;
        box-shadow: 0 5px 15px rgba(0,0,0,0.3);
        opacity: 0;
        transition: opacity 0.3s, bottom 0.3s;
    }
    #customToast.show {
        visibility: visible;
        opacity: 1;
        bottom: 50px;
    }
</style>

<div class="container-fluid sb2-content">

    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800 font-weight-bold">Centro de Soporte</h1>
            <p class="mb-0 text-muted">Estamos aquí para ayudarte. Contáctanos o busca tu respuesta.</p>
        </div>
    </div>

    <div class="row">

        <div class="col-lg-5 mb-4">
            <div class="card modern-card contact-card p-4">
                <h5 class="font-weight-bold text-dark mb-4">Canales de Atención</h5>
                
                <div class="p-3 rounded mb-4" style="background: #f0fff4; border: 1px solid #c6f6d5;">
                    <div class="d-flex align-items-center mb-3">
                        <div class="icon-box-lg bg-white shadow-sm text-success mr-3">
                            <i class="fab fa-whatsapp"></i>
                        </div>
                        <div>
                            <h6 class="font-weight-bold text-success mb-1">WhatsApp Soporte</h6>
                            <small class="text-muted">Respuesta rápida (07:00 - 21:00)</small>
                        </div>
                    </div>
                    <a href="https://wa.me/59162452438?text=Hola,%20necesito%20ayuda%20con%20mi%20membres%C3%ADa" 
                       target="_blank" 
                       class="btn btn-whatsapp btn-block font-weight-bold py-2">
                       <i class="fab fa-whatsapp mr-2"></i> Iniciar Chat
                    </a>
                </div>

                <div class="d-flex align-items-center justify-content-between p-3 bg-light rounded mb-3">
                    <div class="d-flex align-items-center">
                        <div class="mr-3 text-primary" style="font-size: 1.5rem;">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <div style="overflow: hidden;">
                            <div class="small text-muted font-weight-bold text-uppercase">Correo Electrónico</div>
                            <div class="font-weight-bold text-dark text-truncate" style="max-width: 180px;">gymbodytraining23@gmail.com</div>
                        </div>
                    </div>
                    <button class="btn btn-sm btn-outline-primary rounded-circle" 
                            onclick="copyText('gymbodytraining23@gmail.com')" 
                            title="Copiar correo">
                        <i class="fas fa-copy"></i>
                    </button>
                </div>

                <div class="d-flex align-items-center justify-content-between p-3 bg-light rounded">
                    <div class="d-flex align-items-center">
                        <div class="mr-3 text-info" style="font-size: 1.5rem;">
                            <i class="fas fa-phone-alt"></i>
                        </div>
                        <div>
                            <div class="small text-muted font-weight-bold text-uppercase">Llamadas</div>
                            <div class="font-weight-bold text-dark">+591 62452438</div>
                        </div>
                    </div>
                    <button class="btn btn-sm btn-outline-info rounded-circle" 
                            onclick="copyText('+59162452438')" 
                            title="Copiar número">
                        <i class="fas fa-copy"></i>
                    </button>
                </div>

            </div>
        </div>

        <div class="col-lg-7">
            <div class="card modern-card p-4">
                <h5 class="font-weight-bold text-dark mb-4">Preguntas Frecuentes</h5>
                
                <div class="accordion custom-accordion" id="faqAccordion">
                    
                    <div class="card">
                        <div class="card-header" id="headingOne">
                            <button class="btn btn-link" type="button" data-toggle="collapse" data-target="#collapseOne" aria-expanded="true">
                                <span><i class="fas fa-dumbbell mr-2 text-primary opacity-5"></i> ¿Cómo renuevo mi membresía?</span>
                                <i class="fas fa-chevron-down icon-toggle"></i>
                            </button>
                        </div>
                        <div id="collapseOne" class="collapse show" data-parent="#faqAccordion">
                            <div class="card-body text-muted pt-0 px-4 pb-3">
                                Puedes renovarla directamente en recepción. Si tienes habilitada la opción de pagos en línea, ve a la sección "Pagos" en el menú lateral. También puedes escribirnos por WhatsApp para enviarte el código QR de pago.
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header" id="headingTwo">
                            <button class="btn btn-link collapsed" type="button" data-toggle="collapse" data-target="#collapseTwo">
                                <span><i class="fas fa-qrcode mr-2 text-primary opacity-5"></i> Problemas con el acceso (QR)</span>
                                <i class="fas fa-chevron-down icon-toggle"></i>
                            </button>
                        </div>
                        <div id="collapseTwo" class="collapse" data-parent="#faqAccordion">
                            <div class="card-body text-muted pt-0 px-4 pb-3">
                                Si el molinete no lee tu QR:
                                <ol class="mb-0 pl-3 mt-1">
                                    <li>Aumenta el brillo de tu pantalla al máximo.</li>
                                    <li>Asegúrate de tener conexión a internet para cargar el código dinámico.</li>
                                    <li>Si el problema persiste, solicita el ingreso manual con tu CI en recepción.</li>
                                </ol>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header" id="headingThree">
                            <button class="btn btn-link collapsed" type="button" data-toggle="collapse" data-target="#collapseThree">
                                <span><i class="fas fa-key mr-2 text-primary opacity-5"></i> ¿Cómo cambio mi contraseña?</span>
                                <i class="fas fa-chevron-down icon-toggle"></i>
                            </button>
                        </div>
                        <div id="collapseThree" class="collapse" data-parent="#faqAccordion">
                            <div class="card-body text-muted pt-0 px-4 pb-3">
                                Ve a la sección <strong>Mi Perfil</strong> haciendo clic en tu foto en la esquina superior derecha. Allí encontrarás la pestaña de seguridad para actualizar tu clave.
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header" id="headingFour">
                            <button class="btn btn-link collapsed" type="button" data-toggle="collapse" data-target="#collapseFour">
                                <span><i class="fas fa-bell-slash mr-2 text-primary opacity-5"></i> No recibo notificaciones</span>
                                <i class="fas fa-chevron-down icon-toggle"></i>
                            </button>
                        </div>
                        <div id="collapseFour" class="collapse" data-parent="#faqAccordion">
                            <div class="card-body text-muted pt-0 px-4 pb-3">
                                Asegúrate de tener activado el interruptor en la página de <strong>Notificaciones</strong>. También revisa tu carpeta de Spam en el correo electrónico y marca nuestra dirección como segura.
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>

    </div>
</div>

<div id="customToast"><i class="fas fa-check-circle mr-2"></i> Copiado al portapapeles</div>

<script>
    function copyText(text) {
        // Método moderno y robusto para copiar
        if (navigator.clipboard && window.isSecureContext) {
            navigator.clipboard.writeText(text).then(() => {
                showToast("Copiado: " + text);
            }).catch(err => {
                fallbackCopy(text);
            });
        } else {
            fallbackCopy(text);
        }
    }

    function fallbackCopy(text) {
        // Método antiguo por si falla el clipboard API
        let textArea = document.createElement("textarea");
        textArea.value = text;
        textArea.style.position = "fixed";
        textArea.style.left = "-9999px";
        document.body.appendChild(textArea);
        textArea.focus();
        textArea.select();
        try {
            document.execCommand('copy');
            showToast("Copiado: " + text);
        } catch (err) {
            showToast("Error al copiar");
        }
        document.body.removeChild(textArea);
    }

    function showToast(message) {
        var x = document.getElementById("customToast");
        x.innerHTML = '<i class="fas fa-check-circle mr-2"></i> ' + message;
        x.className = "show";
        setTimeout(function(){ x.className = x.className.replace("show", ""); }, 3000);
    }
</script>

<?php include __DIR__ . '/../../theme/sb2/footer.php'; ?>