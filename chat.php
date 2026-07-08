<?php
/**
 * ==========================================================
 *  PROXY KE ANTHROPIC API (Claude)
 * ==========================================================
 *  File ini menerima request dari JavaScript di frontend
 *  (chat AI & AI Tour Planner), lalu meneruskannya ke
 *  api.anthropic.com dengan menyisipkan API key secara aman
 *  dari sisi server (tidak pernah terlihat oleh browser/user).
 * ==========================================================
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Preflight request (CORS)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed. Gunakan POST.']);
    exit;
}

require_once __DIR__ . '/config.php';

if (!defined('ANTHROPIC_API_KEY') || ANTHROPIC_API_KEY === '' || strpos(ANTHROPIC_API_KEY, 'MASUKKAN_API_KEY') !== false) {
    http_response_code(500);
    echo json_encode([
        'error' => 'API key belum diisi.',
        'content' => [
            ['type' => 'text', 'text' => '⚠️ API key Anthropic belum diatur. Buka file api/config.php dan masukkan API key kamu dari https://console.anthropic.com/settings/keys']
        ]
    ]);
    exit;
}

$rawBody = file_get_contents('php://input');
$payload = json_decode($rawBody, true);

if (!is_array($payload)) {
    http_response_code(400);
    echo json_encode(['error' => 'Body request tidak valid, harus berupa JSON.']);
    exit;
}

// Nilai default & batas aman (biar tidak disalahgunakan kalau website online publik)
if (empty($payload['model'])) {
    $payload['model'] = 'claude-sonnet-4-6';
}
if (empty($payload['max_tokens']) || $payload['max_tokens'] > 1500) {
    $payload['max_tokens'] = 1000;
}

$ch = curl_init('https://api.anthropic.com/v1/messages');
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST           => true,
    CURLOPT_POSTFIELDS     => json_encode($payload),
    CURLOPT_HTTPHEADER     => [
        'Content-Type: application/json',
        'x-api-key: ' . ANTHROPIC_API_KEY,
        'anthropic-version: 2023-06-01',
    ],
    CURLOPT_TIMEOUT        => 60,
    CURLOPT_SSL_VERIFYPEER => true,
]);

$response  = curl_exec($ch);
$httpCode  = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlError = curl_error($ch);
curl_close($ch);

if ($response === false) {
    http_response_code(502);
    echo json_encode([
        'error'   => 'Gagal menghubungi Anthropic API: ' . $curlError,
        'content' => [
            ['type' => 'text', 'text' => '⚠️ Server tidak bisa terhubung ke Anthropic. Cek koneksi internet server & pastikan ekstensi cURL PHP aktif.']
        ]
    ]);
    exit;
}

http_response_code($httpCode ?: 200);
echo $response;
