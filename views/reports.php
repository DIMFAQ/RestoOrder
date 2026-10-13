<?php
/**
 * File: views/reports.php
 * Unit Kompetensi: J.620100.017.02 (Pemrograman Terstruktur), J.620100.021.02 (Database Aggregation)
 */

use App\Helpers\FormatHelper;

$pageTitle = 'Laporan Pemesanan Restoran';
require_once __DIR__ . '/layouts/header.php';
?>

<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4 no-print">
    <div>
        <h3 class="fw-bold text-dark m-0"><i class="fa-solid fa-chart-line text-primary me-2"></i>Laporan Pemesanan & Penjualan</h3>
        <p class="text-muted small m-0">Rekapitulasi pendapatan dan transaksi pemesanan restoran berdasarkan periode waktu.</p>
    </div>
    <div>
        <button onclick="window.print()" class="btn btn-outline-primary rounded-pill px-4 shadow-sm">
            <i class="fa-solid fa-print me-1"></i> Cetak Laporan
        </button>
    </div>
</div>

<!-- Form Filter Rentang Tanggal -->
<div class="card border-0 shadow-sm rounded-4 bg-white p-3 mb-4 no-print">
    <form method="GET" action="index.php" class="row g-3 align-items-end">
        <input type="hidden" name="page" value="reports">
        <div class="col-12 col-md-4">
            <label class="form-label fw-semibold small text-secondary">Tanggal Mulai:</label>
            <input type="date" name="start_date" class="form-control" value="<?= htmlspecialchars($startDate) ?>" required>
        </div>
        <div class="col-12 col-md-4">
            <label class="form-label fw-semibold small text-secondary">Tanggal Sampai:</label>
            <input type="date" name="end_date" class="form-control" value="<?= htmlspecialchars($endDate) ?>" required>
        </div>
        <div class="col-12 col-md-4 d-flex gap-2">
            <button type="submit" class="btn btn-primary rounded-pill px-4 flex-grow-1">
                <i class="fa-solid fa-magnifying-glass me-1"></i> Tampilkan Laporan
            </button>
            <a href="index.php?page=reports" class="btn btn-outline-secondary rounded-pill px-3">
                Reset
            </a>
        </div>
    </form>
</div>

<!-- Header Khusus Cetak (Hanya tampil saat print) -->
<div class="d-none print-only text-center mb-4 pb-3 border-bottom">
    <h3 class="fw-bold m-0">LAPORAN PEMESANAN & PENDAPATAN RESTORAN</h3>
    <p class="m-0 text-muted">Periode: <?= FormatHelper::tanggalIndo($startDate) ?> s/d <?= FormatHelper::tanggalIndo($endDate) ?></p>
    <small>Dicetak pada: <?= date('d/m/Y H:i:s') ?> WIB</small>
</div>

<!-- Ringkasan Statistik Laporan -->
<div class="row g-3 mb-4">
    <div class="col-12 col-md-4">
        <div class="card card-stat p-3 h-100">
            <span class="text-muted small fw-semibold text-uppercase">Total Transaksi</span>
            <h4 class="fw-bold text-dark mt-1 mb-0"><?= number_format($reportSummary['count']) ?> Transaksi</h4>
        </div>
    </div>
    <div class="col-12 col-md-4">
        <div class="card card-stat p-3 h-100">
            <span class="text-muted small fw-semibold text-uppercase">Total Omset (Grand Total)</span>
            <h4 class="fw-bold text-success mt-1 mb-0"><?= FormatHelper::rupiah($reportSummary['total']) ?></h4>
        </div>
    </div>
    <div class="col-12 col-md-4">
        <div class="card card-stat p-3 h-100">
            <span class="text-muted small fw-semibold text-uppercase">Rata-rata Transaksi</span>
            <h4 class="fw-bold text-primary mt-1 mb-0"><?= FormatHelper::rupiah($reportSummary['average']) ?></h4>
        </div>
    </div>
</div>

<!-- Tabel Laporan Transaksi -->
<div class="card border-0 shadow-sm rounded-4 bg-white overflow-hidden">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light small text-uppercase text-secondary">
                <tr>
                    <th class="ps-4">No</th>
                    <th>No. Order</th>
                    <th>Waktu</th>
                    <th>Pelanggan</th>
                    <th>Meja</th>
                    <th>Subtotal</th>
                    <th>Pajak</th>
                    <th>Diskon</th>
                    <th>Grand Total</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($reportData)): ?>
                    <tr>
                        <td colspan="10" class="text-center py-5 text-muted">
                            Tidak ditemukan transaksi pemesanan pada periode <?= htmlspecialchars($startDate) ?> s/d <?= htmlspecialchars($endDate) ?>.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php $no = 1; foreach ($reportData as $row): ?>
                        <tr>
                            <td class="ps-4 text-muted small"><?= $no++ ?></td>
                            <td class="fw-bold text-primary"><?= htmlspecialchars($row['order_code']) ?></td>
                            <td class="small"><?= FormatHelper::tanggalIndo($row['order_date'], true) ?></td>
                            <td><?= htmlspecialchars($row['customer_name']) ?></td>
                            <td><span class="badge bg-light text-dark border"><?= htmlspecialchars($row['table_number']) ?></span></td>
                            <td><?= FormatHelper::rupiah($row['total_amount']) ?></td>
                            <td><?= FormatHelper::rupiah($row['tax_amount']) ?></td>
                            <td><?= FormatHelper::rupiah($row['discount_amount']) ?></td>
                            <td class="fw-bold text-dark"><?= FormatHelper::rupiah($row['grand_total']) ?></td>
                            <td><?= FormatHelper::paymentStatusBadge($row['payment_status']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/layouts/footer.php'; ?>
