<?php
// =====================================================
// api/tips.php
// GET /api/tips.php → JSON array tips wisata
// MySQL → PHP → JSON
// =====================================================

require_once __DIR__ . '/config.php';

$pdo  = getDB();
$rows = $pdo->query("SELECT id, icon, judul, isi FROM tips ORDER BY id ASC")
             ->fetchAll();

jsonResponse([
    'status' => 'success',
    'data'   => $rows
]);