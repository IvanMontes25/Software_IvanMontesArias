<?php
/**
 * BaseController — Controlador base MVC
 * Todos los controladores heredan de esta clase.
 */
class BaseController
{
    protected mysqli $db;
    protected array $data = [];

    public function __construct()
    {
        global $db;
        $this->db = $db;
    }

    /**
     * Renderiza una vista con layout completo
     */
    protected function render(string $view, array $data = []): void
    {
        // Siempre pasar $db y aliases para compatibilidad
        global $db, $con, $conn;
        $data = array_merge([
            'db'   => $db,
            'con'  => $con ?? $db,
            'conn' => $conn ?? $db,
        ], $this->data, $data);

        extract($data);

        $pageTitle = $data['pageTitle'] ?? 'Panel';
        $page      = $data['page'] ?? '';

        $viewBase = __DIR__ . '/../views/';

        include $viewBase . 'layout/header.php';
        include $viewBase . 'layout/sidebar.php';
        include $viewBase . 'layout/topbar.php';
        include $viewBase . $view . '.php';
        include $viewBase . 'layout/footer.php';
    }

    protected function renderPartial(string $view, array $data = []): void
    {
        global $db, $con, $conn;
        $data = array_merge(['db' => $db, 'con' => $con ?? $db, 'conn' => $conn ?? $db], $this->data, $data);
        extract($data);
        include __DIR__ . '/../views/' . $view . '.php';
    }

    protected function json(array $data, int $code = 200): void
    {
        http_response_code($code);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data);
        exit;
    }

    protected function redirect(string $url): void
    {
        header("Location: $url");
        exit;
    }

    protected function model(string $name): object
    {
        require_once __DIR__ . '/../models/' . $name . '.php';
        return new $name($this->db);
    }

    protected function requirePost(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            exit('Método no permitido');
        }
    }

    protected function verifyCsrf(): void
    {
        $posted = $_POST['csrf_token'] ?? ($_POST['_csrf'] ?? ($_POST['csrf'] ?? ''));
        $session = $_SESSION['csrf_token'] ?? ($_SESSION['_csrf'] ?? '');
        if (empty($session) || empty($posted) || !hash_equals($session, $posted)) {
            http_response_code(403);
            exit('Token CSRF inválido');
        }
    }

    protected function e($s): string
    {
        return htmlspecialchars((string)($s ?? ''), ENT_QUOTES, 'UTF-8');
    }

    protected function flash(string $key, string $message): void
    {
        $_SESSION[$key] = $message;
    }

    protected function getFlash(string $key): ?string
    {
        $msg = $_SESSION[$key] ?? null;
        unset($_SESSION[$key]);
        return $msg;
    }
}
