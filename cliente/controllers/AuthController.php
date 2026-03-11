<?php
// cliente/controllers/AuthController.php
// Controlador: Login, Logout, Forgot Password, Cambiar Password Obligatorio

class AuthController
{
    private mysqli $db;

    public function __construct(mysqli $db)
    {
        $this->db = $db;
    }

    /**
     * POST /cliente/index.php — Procesa el login
     */
    public function login(): void
    {
        require_once __DIR__ . '/../models/MemberModel.php';
        $memberModel = new MemberModel($this->db);

        // Guardar destino post-login (QR)
        if (!empty($_GET['next'])) {
            $_SESSION['after_login'] = $_GET['next'];
        }

        $login_error = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $username = trim($_POST['user'] ?? '');
            $password = trim($_POST['pass'] ?? '');

            if ($username === '' || $password === '') {
                $login_error = "Debes ingresar usuario y contraseña.";
            } else {

                $member = $memberModel->findByUsername($username);

                if (!$member) {
                    $login_error = "Usuario o contraseña incorrectos.";
                } elseif (!password_verify($password, $member['password'])) {
                    $login_error = "Usuario o contraseña incorrectos.";
                } else {

                    // 🔐 Limpiar sesión previa completamente (evita cruce staff/cliente)
                    // ✅ FIX: Preservar destino QR antes de limpiar la sesión
                    $pendingRedirect = $_SESSION['after_login'] ?? null;

                    $_SESSION = [];
                    session_regenerate_id(true);

                    // 🟢 Crear sesión exclusiva de cliente
                    $_SESSION['user_id']  = (int)$member['user_id'];
                    $_SESSION['rol']      = 'cliente';
                    $_SESSION['origen']   = 'members';
                    $_SESSION['fullname'] = $member['fullname'] ?? '';

                    // ✅ FIX: Restaurar destino QR si existía
                    if ($pendingRedirect !== null) {
                        $_SESSION['after_login'] = $pendingRedirect;
                    }

                    // Cambio obligatorio de contraseña
                    if ((int)$member['must_change_password'] === 1) {
                        header("Location: pags_cliente/cambiar_password_obligatorio.php");
                        exit;
                    }

                    // Bloqueo inmediato post-login por membresía
                    require_once __DIR__ . '/../includes/cliente_data.php';
                    $clienteData = obtenerClienteData($this->db, (int)$member['user_id']);

                    if (!empty($clienteData) && !in_array($clienteData['estado'] ?? '', ['activa', 'por_vencer'], true)) {
                        header('Location: pags_cliente/membresia_vencida.php');
                        exit;
                    }

                    // Redirección QR
                    if (!empty($_SESSION['after_login'])) {
                        $destino = urldecode($_SESSION['after_login']);
                        unset($_SESSION['after_login']);
                        header("Location: $destino");
                        exit;
                    }

                    // Redirección normal
                    header("Location: pags_cliente/perfil.php");
                    exit;
                }
            }
        }

        // Pasar datos a la vista
        include __DIR__ . '/../views/auth/login.php';
    }

    /**
     * POST /cliente/forgot_password.php — Genera nueva contraseña (JSON API)
     */
    public function forgotPassword(): void
    {
        require_once __DIR__ . '/../models/MemberModel.php';
        $memberModel = new MemberModel($this->db);

        header('Content-Type: application/json');

        $user = trim($_POST['user'] ?? '');
        $ci   = trim($_POST['ci'] ?? '');

        if ($user === '' || $ci === '') {
            echo json_encode(['success' => false, 'message' => 'Datos incompletos']);
            exit;
        }

        $userId = $memberModel->findByUsernameAndCI($user, $ci);

        if (!$userId) {
            echo json_encode(['success' => false, 'message' => 'Datos incorrectos']);
            exit;
        }

        $newPlain = substr(str_shuffle(
            'ABCDEFGHJKLMNPQRSTUVWXYZabcdefghjkmnpqrstuvwxyz23456789'
        ), 0, 8);

        $newHash = password_hash($newPlain, PASSWORD_DEFAULT);
        $memberModel->updatePassword($userId, $newHash, 1);

        echo json_encode([
            'success'  => true,
            'password' => $newPlain
        ]);
        exit;
    }

    /**
     * GET|POST /cliente/pags_cliente/cambiar_password_obligatorio.php
     */
    public function cambiarPasswordObligatorio(): void
    {
        require_once __DIR__ . '/../models/MemberModel.php';
        $memberModel = new MemberModel($this->db);

        if (!isset($_SESSION['user_id'])) {
            header("Location: ../index.php");
            exit;
        }

        if (isset($_SESSION['pwd_changed'])) {
            // Evita que vuelva a cargar formulario tras éxito
        }

        $userId = (int)$_SESSION['user_id'];
        $error = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $pass1 = trim($_POST['pass1'] ?? '');
            $pass2 = trim($_POST['pass2'] ?? '');

            if ($pass1 === '' || $pass2 === '') {
                $error = "Debes completar ambos campos.";
            } elseif (strlen($pass1) < 6) {
                $error = "La contraseña debe tener mínimo 6 caracteres.";
            } elseif ($pass1 !== $pass2) {
                $error = "Las contraseñas no coinciden.";
            } else {

                $hash = password_hash($pass1, PASSWORD_DEFAULT);
                $memberModel->updatePassword($userId, $hash, 0);

                session_regenerate_id(true);
                $_SESSION['pwd_changed'] = true;

                header("Location: cambiar_password_obligatorio.php");
                exit;
            }
        }

        // Pasar datos a la vista
        include __DIR__ . '/../views/auth/cambiar_password_obligatorio.php';
    }

    /**
     * GET /cliente/cerrar_session.php — Cierra sesión
     */
    public function logout(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $_SESSION = [];

        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }

        session_destroy();

        header('Location: index.php');
        exit;
    }
}