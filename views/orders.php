<?php
/**
 * File: views/orders.php
 * Unit Kompetensi: J.620100.018.02 (OOP - CalculableInterface), J.620100.021.02 (Database Transaksi)
 */

use App\Helpers\FormatHelper;

$pageTitle = 'Pemesanan & Kasir';
require_once __DIR__ . '/layouts/header.php';
?>

<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
        <h3 class="fw-bold text-dark m-0"><i class="fa-solid fa-cash-register text-primary me-2"></i>Pemesanan & Kasir</h3>
        <p class="text-muted small m-0">Kelola riwayat pesanan pelanggan, perbarui status pembayaran, dan cetak bukti transaksi.</p>
    </div>
    <div>
        <a href="index.php?page=orders&action=new" class="btn btn-primary rounded-pill px-4 shadow-sm">
            <i class="fa-solid fa-plus me-1"></i> Buat Pesanan Baru
        </a>
    </div>
</div>

<!-- Tabel Daftar Riwayat Pesanan -->
<div class="card border-0 shadow-sm rounded-4 bg-white overflow-hidden mb-4">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light small text-uppercase text-secondary">
                <tr>
                    <th class="ps-4">No. Order</th>
                    <th>Waktu Pesan</th>
                    <th>Pelanggan</th>
                    <th>Meja</th>
                    <th>Subtotal</th>
                    <th>Pajak (10%)</th>
                    <th>Grand Total</th>
                    <th>Status Bayar</th>
                    <th class="text-end pe-4">Aksi Cepat</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($orders)): ?>
                    <tr>
                        <td colspan="9" class="text-center py-5 text-muted">
                            <i class="fa-solid fa-receipt fs-2 mb-2 opacity-40"></i>
                            <p class="mb-2">Belum ada data transaksi pesanan.</p>
                            <a href="index.php?page=orders&action=new" class="btn btn-sm btn-primary rounded-pill px-3">
                                <i class="fa-solid fa-plus me-1"></i> Catat Pesanan Sekarang
                            </a>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($orders as $o): ?>
                        <tr>
                            <td class="ps-4 fw-bold text-primary"><?= htmlspecialchars($o['order_code']) ?></td>
                            <td>
                                <div class="small fw-semibold"><?= FormatHelper::tanggalIndo($o['order_date'], true) ?></div>
                            </td>
                            <td>
                                <div class="fw-semibold text-dark"><?= htmlspecialchars($o['customer_name']) ?></div>
                                <div class="small text-muted"><?= (!empty($o['customer_phone']) && $o['customer_phone'] !== '-') ? htmlspecialchars($o['customer_phone']) : '<span class="text-secondary">-</span>' ?></div>
                            </td>
                            <td><span class="badge bg-light text-dark border"><?= htmlspecialchars($o['table_number']) ?></span></td>
                            <td><?= FormatHelper::rupiah($o['total_amount']) ?></td>
                            <td class="text-muted"><?= FormatHelper::rupiah($o['tax_amount']) ?></td>
                            <td class="fw-bold text-dark fs-6"><?= FormatHelper::rupiah($o['grand_total']) ?></td>
                            <td>
                                <div class="d-inline-flex align-items-center gap-1">
                                    <?= FormatHelper::paymentStatusBadge($o['payment_status']) ?>
                                    <!-- Dropdown Cepat Ganti Status Langsung di Tabel -->
                                    <div class="dropdown d-inline-block">
                                        <button class="btn btn-sm btn-light border-0 py-0 px-1 text-muted" type="button" data-bs-toggle="dropdown" title="Ubah status bayar">
                                            <i class="fa-solid fa-pen-to-square small"></i>
                                        </button>
                                        <ul class="dropdown-menu shadow-sm border-0 py-1" style="font-size: 0.85rem;">
                                            <li><span class="dropdown-header py-1">Ubah Status Bayar:</span></li>
                                            <li>
                                                <a class="dropdown-item py-1 text-success fw-semibold <?= ($o['payment_status'] === 'Lunas') ? 'active' : '' ?>" 
                                                   href="index.php?page=orders&action=update_status&id=<?= $o['id'] ?>&status=Lunas">
                                                    <i class="fa-solid fa-check me-1"></i> Lunas Langsung
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item py-1 text-warning fw-semibold <?= ($o['payment_status'] === 'Belum Bayar') ? 'active' : '' ?>" 
                                                   href="index.php?page=orders&action=update_status&id=<?= $o['id'] ?>&status=Belum Bayar">
                                                    <i class="fa-solid fa-clock me-1"></i> Belum Bayar
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                                <?php if (!empty($o['payment_method'])): ?>
                                    <div class="small text-muted mt-1"><?= htmlspecialchars($o['payment_method']) ?></div>
                                <?php endif; ?>
                            </td>
                            <td class="text-end pe-4">
                                <div class="btn-group btn-group-sm">
                                    <a href="index.php?page=receipt&id=<?= $o['id'] ?>" class="btn btn-outline-secondary rounded-pill px-3" title="Lihat & Cetak Struk">
                                        <i class="fa-solid fa-receipt me-1"></i> Struk
                                    </a>
                                    <?php if ($o['payment_status'] === 'Belum Bayar'): ?>
                                        <a href="index.php?page=orders&action=pay&id=<?= $o['id'] ?>" class="btn btn-success rounded-pill px-3 ms-1" title="Tandai Sudah Lunas">
                                            <i class="fa-solid fa-check me-1"></i> Lunasi
                                        </a>
                                    <?php endif; ?>
                                    <button type="button" class="btn btn-outline-danger rounded-circle ms-1 d-inline-flex align-items-center justify-content-center" style="width: 31px; height: 31px;" onclick="confirmDeleteOrder(<?= $o['id'] ?>, '<?= $o['order_code'] ?>')" title="Hapus Pesanan">
                                        <i class="fa-solid fa-trash-can small"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
function confirmDeleteOrder(orderId, orderCode) {
    Swal.fire({
        title: 'Hapus Pesanan?',
        text: 'Apakah Anda yakin ingin membatalkan & menghapus pesanan ' + orderCode + '?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#64748b',
        confirmButtonText: 'Ya, Hapus',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = 'index.php?page=orders&action=delete&id=' + orderId;
        }
    });
}
</script>

<?php require_once __DIR__ . '/layouts/footer.php'; ?>
