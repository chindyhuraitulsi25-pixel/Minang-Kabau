<?php
// =====================================================
// api/gallery.php
// GET /api/gallery.php → JSON array foto gallery
// MySQL → PHP → JSON
// =====================================================

require_once __DIR__ . '/config.php';

$pdo  = getDB();
$rows = $pdo->query("SELECT id, src, alt FROM gallery ORDER BY urutan ASC")
             ->fetchAll();

jsonResponse([
    'status' => 'success',
    'data'   => $rows
]);