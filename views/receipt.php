<?php
/**
 * File: views/receipt.php
 * Unit Kompetensi: J.620100.017.02 (Struktur Tampilan & Laporan Nota)
 */

use App\Helpers\FormatHelper;

$pageTitle = 'Struk Pesanan ' . htmlspecialchars($order['order_code']);
require_once __DIR__ . '/layouts/header.php';
?>

<div class="row justify-content-center">
    <div class="col-12 col-md-8 col-lg-6">
        <div class="d-flex justify-content-between align-items-center mb-3 no-print">
            <a href="index.php?page=orders" class="btn btn-outline-secondary rounded-pill btn-sm px-3">
                <i class="fa-solid fa-arrow-left me-1"></i> Kembali ke Kasir
            </a>
            <button onclick="window.print()" class="btn btn-primary rounded-pill btn-sm px-3 shadow-sm">
                <i class="fa-solid fa-print me-1"></i> Cetak Struk
            </button>
        </div>

        <div class="card border-0 shadow-sm rounded-4 bg-white p-4 p-md-5" id="printableReceipt">
            <!-- Header Struk -->
            <div class="text-center pb-3 border-bottom mb-4">
                <div class="fs-4 fw-bold text-dark"><i class="fa-solid fa-utensils text-primary me-2"></i>RESTOORDER</div>
                <div class="small text-muted">Jl. Gastronomi No. 88, Jakarta &bull; Telp: (021) 555-7890</div>
                <div class="badge bg-light text-dark border mt-2">NOTA PESANAN & PEMBAYARAN</div>
            </div>

            <!-- Metadata Info -->
            <div class="row g-2 small mb-3">
                <div class="col-6">
                    <span class="text-muted d-block">Nomor Struk:</span>
                    <strong class="text-primary"><?= htmlspecialchars($order['order_code']) ?></strong>
                </div>
                <div class="col-6 text-end">
                    <span class="text-muted d-block">Waktu Transaksi:</span>
                    <strong><?= FormatHelper::tanggalIndo($order['order_date'], true) ?></strong>
                </div>
                <div class="col-6">
                    <span class="text-muted d-block">Pelanggan:</span>
                    <strong><?= htmlspecialchars($order['customer_name']) ?></strong> (<?= htmlspecialchars($order['customer_phone']) ?>)
                </div>
                <div class="col-6 text-end">
                    <span class="text-muted d-block">Meja:</span>
                    <strong>Meja <?= htmlspecialchars($order['table_number']) ?> (<?= htmlspecialchars($order['table_location']) ?>)</strong>
                </div>
            </div>

            <!-- Tabel Rincian Menu -->
            <table class="table table-sm align-middle my-3 border-top border-bottom">
                <thead class="text-secondary small">
                    <tr>
                        <th>Item</th>
                        <th class="text-center">Qty</th>
                        <th class="text-end">Harga</th>
                        <th class="text-end">Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($order['items'] as $item): ?>
                        <tr>
                            <td>
                                <div class="fw-semibold text-dark"><?= htmlspecialchars($item['menu_name']) ?></div>
                                <div class="small text-muted"><?= htmlspecialchars($item['menu_category']) ?></div>
                            </td>
                            <td class="text-center"><?= $item['quantity'] ?></td>
                            <td class="text-end text-muted small"><?= FormatHelper::rupiah($item['unit_price']) ?></td>
                            <td class="text-end fw-semibold"><?= FormatHelper::rupiah($item['subtotal']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <!-- Kalkulasi Total -->
            <div class="small mb-4">
                <div class="d-flex justify-content-between py-1">
                    <span class="text-muted">Subtotal:</span>
                    <span class="fw-semibold"><?= FormatHelper::rupiah($order['total_amount']) ?></span>
                </div>
                <?php if ($order['discount_amount'] > 0): ?>
                    <div class="d-flex justify-content-between py-1 text-danger">
                        <span>Diskon:</span>
                        <span>- <?= FormatHelper::rupiah($order['discount_amount']) ?></span>
                    </div>
                <?php endif; ?>
                <div class="d-flex justify-content-between py-1">
                    <span class="text-muted">Pajak Restoran (10% PPN):</span>
                    <span><?= FormatHelper::rupiah($order['tax_amount']) ?></span>
                </div>
                <div class="d-flex justify-content-between py-2 border-top border-dark mt-2 fs-6">
                    <strong class="text-dark">Grand Total:</strong>
                    <strong class="text-primary"><?= FormatHelper::rupiah($order['grand_total']) ?></strong>
                </div>
                <?php if ($order['payment_status'] === 'Lunas' && ((float)($order['cash_received'] ?? 0) > 0)): ?>
                    <div class="d-flex justify-content-between py-1 border-top pt-2 mt-2">
                        <span class="text-secondary">Tunai Diterima:</span>
                        <span class="fw-semibold text-dark"><?= FormatHelper::rupiah($order['cash_received']) ?></span>
                    </div>
                    <div class="d-flex justify-content-between py-1 text-success">
                        <span class="fw-bold">Kembalian:</span>
                        <span class="fw-bold"><?= FormatHelper::rupiah($order['change_amount'] ?? 0) ?></span>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Status Pembayaran -->
            <div class="p-3 rounded-3 text-center mb-4 <?= ($order['payment_status'] === 'Lunas') ? 'bg-success-subtle text-success' : 'bg-warning-subtle text-warning' ?>">
                <div class="fw-bold fs-6">STATUS: <?= strtoupper($order['payment_status']) ?></div>
                <?php if ($order['payment_status'] === 'Lunas'): ?>
                    <div class="small mt-1 text-muted">Metode Pembayaran: <?= htmlspecialchars($order['payment_method'] ?? 'Tunai') ?></div>
                <?php endif; ?>
            </div>

            <div class="text-center text-muted small pt-3 border-top">
                Terima kasih atas kunjungan Anda di RestoOrder!<br>
                <em>Semoga hidangan kami memuaskan selera Anda.</em>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/layouts/footer.php'; ?>
