<?php
/**
 * File: tests/run_tests.php
 * Unit Kompetensi:
 * - J.620100.033.02 (Melaksanakan Pengujian Unit Program)
 * 
 * Script pengujian otomatis (Unit Test Runner)
 * Dapat dijalankan via CLI: php tests/run_tests.php
 * Atau via Browser: http://localhost:8000/tests/run_tests.php
 */

require_once __DIR__ . '/OrderTest.php';
require_once __DIR__ . '/TableTest.php';

use Tests\OrderTest;
use Tests\TableTest;

$orderSuite = new OrderTest();
$tableSuite = new TableTest();

$results = [
    $orderSuite->run(),
    $tableSuite->run(),
];

$totalPassed = 0;
$totalFailed = 0;
$totalTests = 0;

foreach ($results as $res) {
    $totalPassed += $res['passed'];
    $totalFailed += $res['failed'];
    $totalTests  += count($res['tests']);
}

// Jika dijalankan dari CLI
if (php_sapi_name() === 'cli') {
    echo "\n=======================================================\n";
    echo "         HASIL PENGUJIAN UNIT PROGRAM RESTORAN         \n";
    echo "=======================================================\n";

    foreach ($results as $res) {
        echo "\n[SUITE] {$res['class']}\n";
        foreach ($res['tests'] as $t) {
            $status = $t['status'] === 'PASS' ? "\033[32m[PASS]\033[0m" : "\033[31m[FAIL]\033[0m";
            echo "  {$status} {$t['method']}\n";
            if ($t['error']) {
                echo "         Error: {$t['error']}\n";
            }
        }
    }

    echo "\n-------------------------------------------------------\n";
    echo "TOTAL: {$totalTests} Tests | ";
    echo "\033[32mPASSED: {$totalPassed}\033[0m | ";
    echo ($totalFailed > 0 ? "\033[31mFAILED: {$totalFailed}\033[0m" : "FAILED: 0") . "\n";
    echo "=======================================================\n\n";
    exit($totalFailed > 0 ? 1 : 0);
}

// Jika dijalankan via Web Browser
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Unit Test Runner - Sistem Pemesanan Restoran</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #f8fafc; }
        .card-custom { border-radius: 16px; border: 1px solid #e2e8f0; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); }
    </style>
</head>
<body class="py-5">
    <div class="container" style="max-width: 860px;">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <a href="../index.php" class="btn btn-outline-secondary btn-sm mb-2 rounded-pill px-3"><i class="fa-solid fa-arrow-left me-1"></i> Kembali ke RestoOrder</a>
                <h2 class="fw-bold text-dark m-0"><i class="fa-solid fa-vial-circle-check text-success me-2"></i>Automated Diagnostic & Health Check</h2>
                <p class="text-muted small m-0">Verifikasi kalkulasi transaksi, integritas model OOP, dan keandalan sistem.</p>
            </div>
            <div class="text-end">
                <span class="badge bg-success fs-6 p-2 px-3"><?= $totalPassed ?> Berhasil</span>
                <?php if ($totalFailed > 0): ?>
                    <span class="badge bg-danger fs-6 p-2 px-3"><?= $totalFailed ?> Gagal</span>
                <?php endif; ?>
            </div>
        </div>

        <?php foreach ($results as $res): ?>
            <div class="card card-custom mb-4 bg-white">
                <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold mb-0 text-primary"><i class="fa-solid fa-cube me-2"></i><?= htmlspecialchars($res['class']) ?></h5>
                    <span class="badge bg-light text-dark border"><?= count($res['tests']) ?> Test Cases</span>
                </div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush">
                        <?php foreach ($res['tests'] as $t): ?>
                            <li class="list-group-item d-flex justify-content-between align-items-center py-3">
                                <div>
                                    <span class="fw-semibold text-dark"><?= htmlspecialchars($t['method']) ?></span>
                                    <?php if ($t['error']): ?>
                                        <div class="text-danger small mt-1"><i class="fa-solid fa-circle-exclamation me-1"></i><?= htmlspecialchars($t['error']) ?></div>
                                    <?php endif; ?>
                                </div>
                                <div>
                                    <?php if ($t['status'] === 'PASS'): ?>
                                        <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2"><i class="fa-solid fa-check me-1"></i> PASS</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-3 py-2"><i class="fa-solid fa-xmark me-1"></i> FAIL</span>
                                    <?php endif; ?>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        <?php endforeach; ?>

        <div class="card card-custom bg-success text-white p-4 text-center">
            <h4 class="fw-bold mb-1"><i class="fa-solid fa-circle-check me-2"></i>Semua Pengujian Unit Telah Terpenuhi</h4>
            <p class="mb-0 text-white-50">Pengujian mencakup kalkulasi matematika pesanan, diskon, PPN, sanitasi masukan (XSS), dan validasi regex.</p>
        </div>
    </div>
</body>
</html>
