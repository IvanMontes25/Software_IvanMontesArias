<?php
// cliente/controllers/ReciboController.php
// Controlador: Recibo de pago

class ReciboController
{
    private mysqli $db;

    public function __construct(mysqli $db)
    {
        $this->db = $db;
    }

    public function index(): void
    {
        require_once __DIR__ . '/../models/PaymentModel.php';
        $paymentModel = new PaymentModel($this->db);

        $pageTitle = 'Recibo de Pago';
        $page = 'pagos';

        $userId    = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : 0;
        $paymentId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

        if ($userId <= 0) {
            header('Location: ../cerrar_session.php');
            exit;
        }

        $pay   = null;
        $error = '';

        if ($paymentId <= 0) {
            $error = 'ID de pago no válido.';
        } else {
            $pay = $paymentModel->getReceipt($paymentId, $userId);

            if (!$pay) {
                $error = 'Recibo no encontrado o acceso denegado.';
            }
        }

        // Helpers
        function h($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
        function fmt_fecha($s) { return $s ? date('d/m/Y', strtotime($s)) : '—'; }
        function fmt_hora($s) { return $s ? date('H:i', strtotime($s)) : '—'; }

        // Preparar datos del recibo si existe
        $reciboNro = $clienteNombre = $clienteCI = $clienteEmail = '';
        $fechaEmision = $horaEmision = $metodo = $estado = '';
        $total = 0;
        $productos = [];
        $totalProductos = 0;
        $totalPlan = 0;

        if ($pay) {
            $reciboNro    = str_pad((string)$pay['id'], 6, '0', STR_PAD_LEFT);
            $fechaEmision = fmt_fecha($pay['paid_date']);
            $horaEmision  = fmt_hora($pay['paid_date']);

            $clienteNombre = $pay['fullname'];
            $clienteCI     = $pay['ci'] ?? '—';
            $clienteEmail  = $pay['correo'] ?? '—';

            $metodo = ucfirst($pay['method'] ?? 'Efectivo');
            $estado = strtoupper($pay['p_status'] ?? 'PAGADO');

            $total = (float)$pay['amount'];

            // Productos
            if (!empty($pay['productos'])) {
                $arr = json_decode($pay['productos'], true);
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

            $totalPlan = max(0, $total - $totalProductos);
        }

        // Renderizar vista
        include __DIR__ . '/../views/recibo/index.php';
    }
}
