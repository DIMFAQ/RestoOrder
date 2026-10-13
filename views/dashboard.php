<?php
/**
 * File: views/dashboard.php
 * Unit Kompetensi: 
 * - J.620100.019.02 (Pre-Existing Library: Chart.js)
 * - J.620100.017.02 (Pemrograman Terstruktur)
 */

use App\Helpers\FormatHelper;

$pageTitle = 'Dashboard Restoran';
require_once __DIR__ . '/layouts/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold text-dark m-0">Ringkasan Operasional Restoran</h3>
        <p class="text-muted small m-0">Pantau performa reservasi meja, pemesanan, dan pendapatan secara langsung.</p>
    </div>
    <div>
        <span class="badge bg-primary-subtle text-primary p-2 px-3 border border-primary-subtle">
            <i class="fa-regular fa-calendar-days me-1"></i> <?= FormatHelper::tanggalIndo(date('Y-m-d')) ?>
        </span>
    </div>
</div>

<!-- 4 KPI Cards -->
<div class="row g-3 mb-4">
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card card-stat p-3 h-100">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small fw-semibold text-uppercase">Total Pendapatan</span>
                    <h4 class="fw-bold text-dark mt-1 mb-0"><?= FormatHelper::rupiah($stats['total_revenue']) ?></h4>
                </div>
                <div class="stat-icon bg-success-subtle text-success">
                    <i class="fa-solid fa-rupiah-sign"></i>
                </div>
            </div>
            <div class="mt-3 pt-2 border-top small text-muted">
                <i class="fa-solid fa-check text-success me-1"></i> Dari transaksi berstatus lunas
            </div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card card-stat p-3 h-100">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small fw-semibold text-uppercase">Total Pesanan</span>
                    <h4 class="fw-bold text-dark mt-1 mb-0"><?= number_format($stats['total_orders']) ?></h4>
                </div>
                <div class="stat-icon bg-primary-subtle text-primary">
                    <i class="fa-solid fa-receipt"></i>
                </div>
            </div>
            <div class="mt-3 pt-2 border-top small text-muted">
                <i class="fa-solid fa-clock-rotate-left text-primary me-1"></i> Keseluruhan riwayat order
            </div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card card-stat p-3 h-100">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small fw-semibold text-uppercase">Reservasi Aktif</span>
                    <h4 class="fw-bold text-dark mt-1 mb-0"><?= number_format($stats['active_reservations']) ?></h4>
                </div>
                <div class="stat-icon bg-warning-subtle text-warning">
                    <i class="fa-solid fa-calendar-check"></i>
                </div>
            </div>
            <div class="mt-3 pt-2 border-top small text-muted">
                <i class="fa-solid fa-user-clock text-warning me-1"></i> Terkonfirmasi / Duduk di Meja
            </div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card card-stat p-3 h-100">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small fw-semibold text-uppercase">Meja Tersedia</span>
                    <h4 class="fw-bold text-dark mt-1 mb-0"><?= number_format($stats['available_tables']) ?></h4>
                </div>
                <div class="stat-icon bg-info-subtle text-info">
                    <i class="fa-solid fa-chair"></i>
                </div>
            </div>
            <div class="mt-3 pt-2 border-top small text-muted">
                <i class="fa-solid fa-door-open text-info me-1"></i> Siap ditempati tamu baru
            </div>
        </div>
    </div>
</div>

<!-- Charts Row -->
<div class="row g-4 mb-4">
    <div class="col-12 col-lg-8">
        <div class="card border-0 shadow-sm rounded-4 p-4 h-100 bg-white">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold text-dark m-0"><i class="fa-solid fa-chart-column text-primary me-2"></i>Tren Penjualan (7 Hari Terakhir)</h5>
                <span class="badge bg-light text-secondary border">Chart.js Library</span>
            </div>
            <div style="position: relative; height: 280px;">
                <canvas id="salesTrendChart"></canvas>
            </div>
        </div>
    </div>

    <div class="col-12 col-lg-4">
        <div class="card border-0 shadow-sm rounded-4 p-4 h-100 bg-white">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold text-dark m-0"><i class="fa-solid fa-crown text-warning me-2"></i>Menu Terlaris</h5>
                <span class="badge bg-light text-secondary border">Top 5</span>
            </div>
            <div style="position: relative; height: 280px;">
                <canvas id="topMenuChart"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Recent Reservations & Table Status Preview -->
<div class="row g-4">
    <div class="col-12 col-lg-7">
        <div class="card border-0 shadow-sm rounded-4 bg-white overflow-hidden">
            <div class="card-header bg-white p-3 px-4 border-bottom d-flex justify-content-between align-items-center">
                <h6 class="fw-bold m-0"><i class="fa-solid fa-clock me-2 text-primary"></i>Reservasi Terbaru</h6>
                <a href="index.php?page=reservations" class="btn btn-sm btn-outline-primary rounded-pill px-3">Lihat Semua</a>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light small text-uppercase text-secondary">
                        <tr>
                            <th class="ps-4">Kode</th>
                            <th>Pelanggan</th>
                            <th>Meja</th>
                            <th>Jadwal</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($recentReservations)): ?>
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">Belum ada reservasi tercatat.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($recentReservations as $res): ?>
                                <tr>
                                    <td class="ps-4 fw-bold text-primary"><?= htmlspecialchars($res['reservation_code']) ?></td>
                                    <td>
                                        <div class="fw-semibold text-dark"><?= htmlspecialchars($res['customer_name']) ?></div>
                                        <div class="small text-muted"><?= htmlspecialchars($res['customer_phone']) ?></div>
                                    </td>
                                    <td><span class="badge bg-light text-dark border"><?= htmlspecialchars($res['table_number']) ?></span></td>
                                    <td>
                                        <div class="small fw-semibold"><?= FormatHelper::tanggalIndo($res['reservation_date']) ?></div>
                                        <div class="small text-muted"><?= substr($res['reservation_time'], 0, 5) ?> WIB</div>
                                    </td>
                                    <td><?= FormatHelper::reservationStatusBadge($res['status']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-12 col-lg-5">
        <div class="card border-0 shadow-sm rounded-4 bg-white p-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="fw-bold m-0"><i class="fa-solid fa-chair me-2 text-info"></i>Status Meja Cepat</h6>
                <a href="index.php?page=tables" class="btn btn-sm btn-outline-secondary rounded-pill px-3">Kelola Meja</a>
            </div>
            <div class="row g-2">
                <?php foreach ($tables as $t): ?>
                    <div class="col-6 col-sm-4">
                        <div class="p-2 rounded-3 text-center border <?= ($t['status'] === 'Tersedia') ? 'bg-success-subtle border-success-subtle' : (($t['status'] === 'Dipesan') ? 'bg-warning-subtle border-warning-subtle' : 'bg-danger-subtle border-danger-subtle') ?>">
                            <div class="fw-bold fs-6 text-dark"><?= htmlspecialchars($t['table_number']) ?></div>
                            <div class="small text-muted"><?= $t['capacity'] ?> Kursi</div>
                            <div class="small fw-semibold mt-1">
                                <?= ($t['status'] === 'Tersedia') ? '<span class="text-success">Tersedia</span>' : (($t['status'] === 'Dipesan') ? '<span class="text-warning">Dipesan</span>' : '<span class="text-danger">Terisi</span>') ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js Script Integration (Unit J.620100.019.02) -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // 1. Line Chart Tren Penjualan
    const trendCtx = document.getElementById('salesTrendChart').getContext('2d');
    const trendData = <?= json_encode($salesTrend) ?>;
    
    const labels = trendData.length ? trendData.map(d => d.date) : ['Hari Ini'];
    const dataValues = trendData.length ? trendData.map(d => parseFloat(d.total_revenue)) : [0];

    new Chart(trendCtx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Pendapatan (Rp)',
                data: dataValues,
                borderColor: '#4f46e5',
                backgroundColor: 'rgba(79, 70, 229, 0.1)',
                fill: true,
                tension: 0.3,
                pointRadius: 5,
                pointBackgroundColor: '#4f46e5'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) { return 'Rp ' + value.toLocaleString('id-ID'); }
                    }
                }
            }
        }
    });

    // 2. Doughnut Chart Top Menu
    const menuCtx = document.getElementById('topMenuChart').getContext('2d');
    const topMenuData = <?= json_encode($topMenu) ?>;
    
    const menuLabels = topMenuData.length ? topMenuData.map(m => m.name) : ['Belum Ada Data'];
    const menuSold = topMenuData.length ? topMenuData.map(m => parseInt(m.total_sold)) : [1];

    new Chart(menuCtx, {
        type: 'doughnut',
        data: {
            labels: menuLabels,
            datasets: [{
                data: menuSold,
                backgroundColor: ['#4f46e5', '#10b981', '#f59e0b', '#ef4444', '#06b6d4'],
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: { boxWidth: 12, font: { size: 11 } }
                }
            }
        }
    });
});
</script>

<?php require_once __DIR__ . '/layouts/footer.php'; ?>
