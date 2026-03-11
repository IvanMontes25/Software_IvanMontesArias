<?php
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');
header('Expires: 0');

require_once __DIR__ . '/../core/bootstrap.php';
require_once __DIR__ . '/../core/auth.php';
require_once __DIR__ . '/../config/config.php';

date_default_timezone_set('America/La_Paz');

// ================== CONFIG QR ==================
$QR_TTL = 20;

// Slot temporal
$slot = intdiv(time(), $QR_TTL);
$now = time();
$slotStart = $slot * $QR_TTL;
$remaining = max(0, ($slotStart + $QR_TTL) - $now);
// Firma HMAC (SEGURIDAD REAL)
$sig = hash_hmac('sha256', GYM_ID . ':' . $slot, GYM_SECRET);

// Base URL
$base = rtrim(BASE_URL, '/');

// URL FINAL DE CHECK-IN
$url = "{$base}/checkin.php"
    . "?gym=" . rawurlencode(GYM_ID)
    . "&t={$slot}"
    . "&sig={$sig}"
    . "&_=" . time(); // ⬅️ evita cache visual

// RESPUESTA JSON ÚNICA Y LIMPIA
echo json_encode([
    'success' => true,
    'url' => $url,
    'slot' => $slot,
    'ttl' => $QR_TTL,
    'remaining' => $remaining,
    'ts' => time()
]);

exit;