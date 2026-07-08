<?php
// =====================================================
// api/cards.php
// GET /api/cards.php?seksi=culture  → JSON cards
// GET /api/cards.php?seksi=food
// GET /api/cards.php?seksi=tourism
// GET /api/cards.php?seksi=clothes
// GET /api/cards.php?seksi=legend
// MySQL → PHP → JSON
// =====================================================

require_once __DIR__ . '/config.php';

$allowed = ['culture', 'food', 'tourism', 'clothes', 'legend'];
$seksi   = $_GET['seksi'] ?? '';

if (!in_array($seksi, $allowed, true)) {
    jsonResponse(['error' => 'Parameter seksi tidak valid. Gunakan: ' . implode(', ', $allowed)], 400);
}

$pdo  = getDB();
$stmt = $pdo->prepare("SELECT id, judul, deskripsi, gambar, video, bookmark FROM cards WHERE seksi = ? ORDER BY id ASC");
$stmt->execute([$seksi]);
$rows = $stmt->fetchAll();

jsonResponse([
    'status' => 'success',
    'seksi'  => $seksi,
    'data'   => $rows
]);