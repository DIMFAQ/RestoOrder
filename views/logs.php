<?php
/**
 * File: views/logs.php
 * Pemantauan Log Aktivitas & Diagnostik Sistem
 */

$pageTitle = 'Log Aktivitas & Audit Sistem';
require_once __DIR__ . '/layouts/header.php';
?>

<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
        <h3 class="fw-bold text-dark m-0"><i class="fa-solid fa-file-waveform text-primary me-2"></i>Log Aktivitas & Audit Sistem</h3>
        <p class="text-muted small m-0">Pemantauan real-time riwayat transaksi, lalu lintas request HTTP, dan diagnostik aplikasi.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="index.php?page=logs&action=test_error" class="btn btn-warning rounded-pill btn-sm px-3 text-dark fw-semibold shadow-sm">
            <i class="fa-solid fa-bell me-1"></i> Uji Notifikasi Log
        </a>
        <a href="index.php?page=logs&action=clear" class="btn btn-outline-danger rounded-pill btn-sm px-3">
            <i class="fa-solid fa-eraser me-1"></i> Bersihkan Log
        </a>
    </div>
</div>

<div class="card border-0 shadow-sm rounded-4 bg-white p-4 mb-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h6 class="fw-bold m-0"><i class="fa-solid fa-terminal me-2 text-primary"></i>Berkas Log Aktif (logs/app.log)</h6>
        <span class="badge bg-primary-subtle text-primary border border-primary-subtle"><?= count($logs) ?> Baris Catatan</span>
    </div>

    <div class="bg-dark text-light p-3 rounded-4 shadow-inner" style="font-family: 'Consolas', 'Courier New', monospace; font-size: 0.84rem; max-height: 480px; overflow-y: auto;">
        <?php if (empty($logs)): ?>
            <div class="text-muted text-center py-4">Belum ada catatan aktivitas.</div>
        <?php else: ?>
            <?php foreach ($logs as $log): ?>
                <div class="py-1 border-bottom border-secondary border-opacity-25 font-monospace">
                    <?php if (str_contains($log, '[ERROR]')): ?>
                        <span class="text-danger fw-bold"><i class="fa-solid fa-circle-xmark me-1"></i> <?= htmlspecialchars($log) ?></span>
                    <?php elseif (str_contains($log, '[WARN]')): ?>
                        <span class="text-warning fw-bold"><i class="fa-solid fa-triangle-exclamation me-1"></i> <?= htmlspecialchars($log) ?></span>
                    <?php else: ?>
                        <span class="text-light-50"><i class="fa-solid fa-circle-info text-info me-1"></i> <?= htmlspecialchars($log) ?></span>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<div class="card border-0 shadow-sm rounded-4 bg-light p-4">
    <h6 class="fw-bold text-dark"><i class="fa-solid fa-shield-halved text-success me-2"></i>Tentang Audit & Logging Terpusat:</h6>
    <p class="small text-muted mb-0">
        Sistem audit ini mencatat setiap operasi penting secara otomatis ke berkas <code>logs/app.log</code>, meliputi pembuatan reservasi, transaksi pemesanan kasir, perubahan status meja, pembaruan master menu, serta penanganan error atau exception tak terduga untuk menjaga stabilitas operasional restoran.
    </p>
</div>

<?php require_once __DIR__ . '/layouts/footer.php'; ?>
