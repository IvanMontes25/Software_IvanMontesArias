<?php
// ==== Config BD ====
if (!defined('DB_HOST')) define('DB_HOST', 'localhost');
if (!defined('DB_USER')) define('DB_USER', 'root');
if (!defined('DB_PASS')) define('DB_PASS', '');
if (!defined('DB_NAME')) define('DB_NAME', 'gymbodytraining');

/**
 * Detecta IP LAN REAL del servidor (evitando Docker/loopback)
 */
function detect_lan_ip(): ?string
{
    // 0) (Opcional) Override manual de emergencia
    // Cambia a tu IP solo si un día te vuelve a fallar lo dinámico.
    $OVERRIDE = ''; // ej: '192.168.1.37'
    if ($OVERRIDE !== '' && filter_var($OVERRIDE, FILTER_VALIDATE_IP)) {
        return $OVERRIDE;
    }

    // 1) SERVER_ADDR (lo más sano si Apache lo da bien)
    $serverAddr = $_SERVER['SERVER_ADDR'] ?? '';
    if (filter_var($serverAddr, FILTER_VALIDATE_IP) && $serverAddr !== '127.0.0.1') {
        // Preferimos 192.168 o 10
        if (preg_match('/^192\.168\./', $serverAddr) || preg_match('/^10\./', $serverAddr)) {
            return $serverAddr;
        }
        // 172.16-31 podría ser LAN o Docker. Lo dejamos como posible pero no ideal.
        if (preg_match('/^172\.(1[6-9]|2\d|3[0-1])\./', $serverAddr)) {
            // Evitar subredes típicas Docker
            if (!preg_match('/^172\.(17|18|19)\./', $serverAddr)) {
                return $serverAddr;
            }
        }
    }

    // 2) gethostbynamel(gethostname()) filtrando IPs privadas
    $ips = @gethostbynamel(gethostname());
    if (is_array($ips)) {
        // Preferencia: 192.168.*
        foreach ($ips as $ip) {
            if (filter_var($ip, FILTER_VALIDATE_IP) && preg_match('/^192\.168\./', $ip)) return $ip;
        }
        // Luego: 10.*
        foreach ($ips as $ip) {
            if (filter_var($ip, FILTER_VALIDATE_IP) && preg_match('/^10\./', $ip)) return $ip;
        }
        // Luego: 172.16-31 evitando 172.17/18/19
        foreach ($ips as $ip) {
            if (
                filter_var($ip, FILTER_VALIDATE_IP) &&
                preg_match('/^172\.(1[6-9]|2\d|3[0-1])\./', $ip) &&
                !preg_match('/^172\.(17|18|19)\./', $ip)
            ) return $ip;
        }
    }

    // 3) Intento Windows: ipconfig (si shell_exec está habilitado)
    $out = @shell_exec('ipconfig');
    if (is_string($out) && $out !== '') {
        // Saca IPv4 del tipo 192.168.x.x o 10.x.x.x (primera coincidencia)
        if (preg_match('/\b(192\.168\.\d{1,3}\.\d{1,3})\b/', $out, $m)) return $m[1];
        if (preg_match('/\b(10\.\d{1,3}\.\d{1,3}\.\d{1,3})\b/', $out, $m)) return $m[1];
    }

    return null;
}

// ==== BASE URL (para QR + Web) ====
if (!defined('BASE_URL')) {

    $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host   = $_SERVER['HTTP_HOST'] ?? 'localhost';

    // Si accedes por localhost, reemplazar por IP LAN detectada
    if ($host === 'localhost' || $host === '127.0.0.1') {
        $lan = detect_lan_ip();
        if ($lan) $host = $lan;
    }

    $basePath = '/GymBodyTrainingEST';
    define('BASE_URL', $scheme . '://' . $host . $basePath);
}

// ==== Seguridad QR ====
define('GYM_SECRET', 'super_clave_segura_123');
define('GYM_ID', 1);

// ==== n8n ====
define('N8N_BASE', 'http://localhost:5678');
define('N8N_TOKEN', '123');