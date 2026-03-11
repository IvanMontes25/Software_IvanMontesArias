<?php
header('Content-Type: application/json');

require_once __DIR__ . '/../../core/bootstrap.php';

$data = json_decode(file_get_contents("php://input"), true);

if (!$data) {
    http_response_code(400);
    echo json_encode(['error' => 'JSON invalido']);
    exit;
}

$tipo      = $data['tipo']      ?? 'general';
$titulo    = $data['titulo']    ?? 'Notificacion';
$mensaje   = $data['mensaje']   ?? '';
$origen    = $data['origen']    ?? 'n8n';
$prioridad = $data['prioridad'] ?? 'media';
$payload   = json_encode($data);

$stmt = $db->prepare("
    INSERT INTO admin_inbox 
    (tipo, titulo, mensaje, origen, prioridad, payload)
    VALUES (?,?,?,?,?,?)
");

$stmt->bind_param("ssssss", $tipo, $titulo, $mensaje, $origen, $prioridad, $payload);
$stmt->execute();

echo json_encode(['status' => 'ok']);
