<?php
require_once __DIR__ . '/../config/config.php';

function n8n_trigger(string $path, array $payload, int $timeoutSeconds = 15): array

{
    $url = rtrim(N8N_BASE, '/') . '/webhook/' . ltrim($path, '/');

    $payload['token'] = N8N_TOKEN;

    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_POST => true,
        CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POSTFIELDS => json_encode($payload),
        CURLOPT_TIMEOUT => $timeoutSeconds,
    ]);

    $body = curl_exec($ch);
    $err  = curl_error($ch);
    $code = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    return [
        'ok' => ($err === '' && $code >= 200 && $code < 300),
        'http_code' => $code,
        'error' => $err,
        'body' => $body,
    ];
}
