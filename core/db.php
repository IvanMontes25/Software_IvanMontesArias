<?php
// core/db.php — Conexión ÚNICA a la base de datos
// Usa las constantes de config/config.php (cargado por bootstrap.php)

require_once __DIR__ . '/../config/config.php';

$db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($db->connect_error) {
    die('Error de conexión: ' . $db->connect_error);
}

$db->set_charset("utf8mb4");

// Alias de compatibilidad: algunos archivos legacy usan $con o $conn
// Esto permite una transición gradual sin romper nada
$con  = $db;
$conn = $db;
