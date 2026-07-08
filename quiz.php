<?php
// =====================================================
// api/quiz.php
// GET /api/quiz.php → JSON array soal quiz
// MySQL → PHP → JSON
// =====================================================

require_once __DIR__ . '/config.php';

$pdo  = getDB();
$rows = $pdo->query("SELECT id, pertanyaan, opsi_a, opsi_b, opsi_c, opsi_d, jawaban, fakta FROM quiz ORDER BY id ASC")
             ->fetchAll();

// Format opsi jadi array supaya JS mudah loop
$formatted = array_map(function($row) {
    return [
        'id'         => (int)$row['id'],
        'pertanyaan' => $row['pertanyaan'],
        'opsi'       => [$row['opsi_a'], $row['opsi_b'], $row['opsi_c'], $row['opsi_d']],
        'jawaban'    => (int)$row['jawaban'],
        'fakta'      => $row['fakta'],
    ];
}, $rows);

jsonResponse([
    'status' => 'success',
    'total'  => count($formatted),
    'data'   => $formatted
]);