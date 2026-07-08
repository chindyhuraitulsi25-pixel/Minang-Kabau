<?php
// =====================================================
// api/timeline.php
// GET /api/timeline.php → JSON array timeline history
// MySQL → PHP → JSON
// =====================================================

require_once __DIR__ . '/config.php';

$pdo  = getDB();
$rows = $pdo->query("SELECT id, era, icon, judul, deskripsi FROM timeline ORDER BY urutan ASC")
             ->fetchAll();

jsonResponse([
    'status' => 'success',
    'data'   => $rows
]);