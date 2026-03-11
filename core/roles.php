<?php
// core/roles.php — Permisos por rol
//
// Cada rol tiene una lista de módulos permitidos.
// Los módulos corresponden a secciones del sidebar.
//
// Módulos disponibles:
//   dashboard, analitica, clientes, asistencias, equipos,
//   pagos, reportes, administracion, inbox, clases

$ROLE_PERMISSIONS = [

    'admin' => [
        'dashboard', 'analitica', 'clientes', 'asistencias',
        'equipos', 'pagos', 'reportes', 'administracion', 'inbox', 'clases',
    ],

    'recepcionista' => [
        'dashboard', 'clientes', 'asistencias', 'pagos',
    ],

    'cajero' => [
        'dashboard', 'clientes', 'pagos',
    ],

    'entrenador' => [
        'clientes', 'clases',
    ],

    'asistente' => [
        'dashboard', 'clientes', 'asistencias',
    ],

    // Rol genérico de staff (fallback)
    'staff' => [
        'dashboard',
    ],
];

/**
 * Devuelve true si el rol actual tiene acceso al módulo indicado.
 */
function puede(string $modulo): bool {
    global $ROLE_PERMISSIONS;
    $rol = $_SESSION['rol'] ?? '';
    if ($rol === 'admin') return true; // admin ve todo
    $permisos = $ROLE_PERMISSIONS[$rol] ?? $ROLE_PERMISSIONS['staff'] ?? [];
    return in_array($modulo, $permisos, true);
}

/**
 * Bloquea el acceso si el rol no tiene permiso para el módulo.
 * Redirige al dashboard con mensaje de error.
 */
function require_modulo(string $modulo): void {
    if (!puede($modulo)) {
        // Si es AJAX, responder 403
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
            strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
            http_response_code(403);
            echo json_encode(['error' => 'No autorizado']);
            exit;
        }
        // Redirigir al dashboard
        $_SESSION['flash_error'] = 'No tienes permiso para acceder a ese módulo.';
        header('Location: dashboard.php');
        exit;
    }
}