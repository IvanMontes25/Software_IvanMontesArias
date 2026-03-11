<?php
// cliente/controllers/ClaseController.php
// Controlador: Gestión de clases y reservas del cliente
// CORREGIDO: usa user_id consistente con members.user_id

class ClaseController
{
    private mysqli $db;

    public function __construct(mysqli $db)
    {
        $this->db = $db;
    }

    /**
     * Página: Clases Disponibles
     */
    public function disponibles(): void
    {
        require_once __DIR__ . '/../models/ClaseModel.php';
        require_once dirname(__DIR__, 2) . '/includes/membership_helper.php';

        $claseModel = new ClaseModel($this->db);
        $user_id    = (int)$_SESSION['user_id']; // members.user_id
        $page       = 'clases';

        // Verificar membresía activa
        $m = membership_last($this->db, $user_id);
        $statusKey = membership_status($m);
        $membresia_activa = in_array($statusKey, ['activa', 'por_vencer']);

        // Filtros opcionales
        $filtro_tipo       = isset($_GET['tipo']) ? (int)$_GET['tipo'] : null;
        $filtro_entrenador = isset($_GET['entrenador']) ? (int)$_GET['entrenador'] : null;

        // Datos
        $clases       = $claseModel->getDisponibles($filtro_tipo, $filtro_entrenador);
        $tipos        = $claseModel->getTiposClase();
        $entrenadores = $claseModel->getEntrenadores();
        $reservas_activas = $claseModel->contarReservasActivas($user_id);

        // IDs de sesiones donde el cliente ya tiene reserva activa
        $mis_reservas_raw = $claseModel->getMisReservas($user_id);
        $sesiones_reservadas = [];
        foreach ($mis_reservas_raw as $r) {
            if (in_array($r['reserva_estado'], ['confirmada', 'en_espera'])) {
                $sesiones_reservadas[] = (int)$r['sesion_id'];
            }
        }

        function e($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }

        include __DIR__ . '/../views/clases/disponibles.php';
    }

    /**
     * Página: Mis Reservas (historial)
     */
    public function misReservas(): void
    {
        require_once __DIR__ . '/../models/ClaseModel.php';

        $claseModel = new ClaseModel($this->db);
        $user_id    = (int)$_SESSION['user_id'];
        $page       = 'mis_reservas';

        $reservas = $claseModel->getMisReservas($user_id);

        function e($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }

        include __DIR__ . '/../views/clases/mis_reservas.php';
    }

    /**
     * Acción: Reservar clase (POST via AJAX)
     */
    public function reservar(): void
    {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['ok' => false, 'msg' => 'Método no permitido']);
            return;
        }

        $token = $_POST['csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
        if (!csrf_verify($token)) {
            echo json_encode(['ok' => false, 'msg' => 'Token inválido. Recarga la página.']);
            return;
        }

        require_once __DIR__ . '/../models/ClaseModel.php';
        require_once dirname(__DIR__, 2) . '/includes/membership_helper.php';

        $claseModel = new ClaseModel($this->db);
        $user_id    = (int)$_SESSION['user_id'];
        $sesion_id  = (int)($_POST['sesion_id'] ?? 0);

        if ($sesion_id <= 0) {
            echo json_encode(['ok' => false, 'msg' => 'Sesión no válida.']);
            return;
        }

        // Verificar membresía
        $m = membership_last($this->db, $user_id);
        $statusKey = membership_status($m);
        if (!in_array($statusKey, ['activa', 'por_vencer'])) {
            echo json_encode([
                'ok'  => false,
                'msg' => 'Tu membresía no está activa. Renuévala para reservar clases.',
                'redirect' => 'membresia_pagos.php'
            ]);
            return;
        }

        $result = $claseModel->reservar($sesion_id, $user_id);

        // Auditoría
        if ($result['ok']) {
            require_once dirname(__DIR__, 2) . '/core/audit.php';
            if (function_exists('registrar_auditoria')) {
                registrar_auditoria($this->db, 'reservar_clase',
                    "Cliente #$user_id reservó sesión #$sesion_id", 'clases');
            }
        }

        echo json_encode($result);
    }

    /**
     * Acción: Cancelar reserva (POST via AJAX)
     */
    public function cancelar(): void
    {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['ok' => false, 'msg' => 'Método no permitido']);
            return;
        }

        $token = $_POST['csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
        if (!csrf_verify($token)) {
            echo json_encode(['ok' => false, 'msg' => 'Token inválido.']);
            return;
        }

        require_once __DIR__ . '/../models/ClaseModel.php';

        $claseModel  = new ClaseModel($this->db);
        $user_id     = (int)$_SESSION['user_id'];
        $reserva_id  = (int)($_POST['reserva_id'] ?? 0);

        if ($reserva_id <= 0) {
            echo json_encode(['ok' => false, 'msg' => 'Reserva no válida.']);
            return;
        }

        $result = $claseModel->cancelar($reserva_id, $user_id);

        if ($result['ok']) {
            require_once dirname(__DIR__, 2) . '/core/audit.php';
            if (function_exists('registrar_auditoria')) {
                registrar_auditoria($this->db, 'cancelar_reserva',
                    "Cliente #$user_id canceló reserva #$reserva_id", 'clases');
            }
        }

        echo json_encode($result);
    }

    /**
     * Acción: Entrar a lista de espera (POST via AJAX)
     */
    public function listaEspera(): void
    {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['ok' => false, 'msg' => 'Método no permitido']);
            return;
        }

        $token = $_POST['csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
        if (!csrf_verify($token)) {
            echo json_encode(['ok' => false, 'msg' => 'Token inválido.']);
            return;
        }

        require_once __DIR__ . '/../models/ClaseModel.php';

        $claseModel = new ClaseModel($this->db);
        $user_id    = (int)$_SESSION['user_id'];
        $sesion_id  = (int)($_POST['sesion_id'] ?? 0);

        $result = $claseModel->entrarListaEspera($sesion_id, $user_id);
        echo json_encode($result);
    }
}
