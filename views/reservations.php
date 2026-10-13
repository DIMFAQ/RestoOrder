<?php
/**
 * File: views/reservations.php
 * Unit Kompetensi: J.620100.018.02 (OOP), J.620100.021.02 (Database Relasional)
 */

use App\Helpers\FormatHelper;

$pageTitle = 'Reservasi Meja Restoran';
require_once __DIR__ . '/layouts/header.php';
?>

<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
        <h3 class="fw-bold text-dark m-0"><i class="fa-solid fa-calendar-check text-primary me-2"></i>Kelola Reservasi Meja</h3>
        <p class="text-muted small m-0">Pencatatan data pemesanan meja pelanggan beserta jadwal kedatangan.</p>
    </div>
    <div class="d-flex gap-2">
        <button type="button" class="btn btn-primary rounded-pill px-4 shadow-sm" data-bs-toggle="modal" data-bs-target="#modalAddReservation">
            <i class="fa-solid fa-plus me-1"></i> Buat Reservasi Baru
        </button>
    </div>
</div>

<!-- Filter Bar -->
<div class="card border-0 shadow-sm rounded-4 bg-white p-3 mb-4">
    <form method="GET" action="index.php" class="row g-2 align-items-center">
        <input type="hidden" name="page" value="reservations">
        <div class="col-auto">
            <span class="fw-semibold text-secondary small"><i class="fa-solid fa-filter me-1"></i>Filter Tanggal:</span>
        </div>
        <div class="col-auto">
            <input type="date" name="filter_date" class="form-control form-control-sm" value="<?= htmlspecialchars($filterDate ?? '') ?>">
        </div>
        <div class="col-auto">
            <button type="submit" class="btn btn-sm btn-primary rounded-pill px-3">Terapkan</button>
            <?php if (!empty($filterDate)): ?>
                <a href="index.php?page=reservations" class="btn btn-sm btn-outline-secondary rounded-pill px-3">Reset</a>
            <?php endif; ?>
        </div>
    </form>
</div>

<!-- Tabel Daftar Reservasi -->
<div class="card border-0 shadow-sm rounded-4 bg-white overflow-hidden">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light small text-uppercase text-secondary">
                <tr>
                    <th class="ps-4">Kode Reservasi</th>
                    <th>Data Pelanggan</th>
                    <th>Meja & Lokasi</th>
                    <th>Jadwal Kedatangan</th>
                    <th>Tamu</th>
                    <th>Status</th>
                    <th>Catatan Khusus</th>
                    <th class="text-end pe-4">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($reservations)): ?>
                    <tr>
                        <td colspan="8" class="text-center py-5 text-muted">
                            <i class="fa-regular fa-folder-open fs-2 d-block mb-2 text-secondary opacity-50"></i>
                            Belum ada data reservasi <?= !empty($filterDate) ? 'pada tanggal ini' : '' ?>.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($reservations as $r): ?>
                        <tr>
                            <td class="ps-4 fw-bold text-primary"><?= htmlspecialchars($r['reservation_code']) ?></td>
                            <td>
                                <div class="fw-semibold text-dark"><?= htmlspecialchars($r['customer_name']) ?></div>
                                <div class="small text-muted"><i class="fa-solid fa-phone me-1"></i><?= htmlspecialchars($r['customer_phone']) ?></div>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border fw-bold"><?= htmlspecialchars($r['table_number']) ?></span>
                                <div class="small text-muted"><?= htmlspecialchars($r['table_location']) ?> (Max <?= $r['table_capacity'] ?> Org)</div>
                            </td>
                            <td>
                                <div class="fw-semibold text-dark"><?= FormatHelper::tanggalIndo($r['reservation_date']) ?></div>
                                <div class="small text-muted"><i class="fa-regular fa-clock me-1"></i><?= substr($r['reservation_time'], 0, 5) ?> WIB</div>
                            </td>
                            <td><span class="badge bg-secondary-subtle text-secondary"><i class="fa-solid fa-users me-1"></i><?= $r['guest_count'] ?></span></td>
                            <td><?= FormatHelper::reservationStatusBadge($r['status']) ?></td>
                            <td class="small text-muted"><?= htmlspecialchars($r['notes'] ?: '-') ?></td>
                            <td class="text-end pe-4">
                                <div class="btn-group">
                                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle rounded-pill px-3" data-bs-toggle="dropdown">
                                        Update Status
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                                        <li><a class="dropdown-item" href="index.php?page=reservations&action=update_status&id=<?= $r['id'] ?>&status=Seated"><i class="fa-solid fa-user-check text-info me-2"></i>Tamu Duduk (Seated)</a></li>
                                        <li><a class="dropdown-item" href="index.php?page=reservations&action=update_status&id=<?= $r['id'] ?>&status=Completed"><i class="fa-solid fa-circle-check text-success me-2"></i>Selesai (Completed)</a></li>
                                        <li><a class="dropdown-item text-danger" href="index.php?page=reservations&action=update_status&id=<?= $r['id'] ?>&status=Cancelled"><i class="fa-solid fa-circle-xmark text-danger me-2"></i>Batalkan (Cancelled)</a></li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Buat Reservasi -->
<div class="modal fade" id="modalAddReservation" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow rounded-4">
            <form action="index.php?page=reservations&action=create" method="POST">
                <div class="modal-header border-bottom">
                    <h5 class="modal-title fw-bold"><i class="fa-solid fa-calendar-plus text-primary me-2"></i>Form Pemesanan Meja Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <h6 class="fw-bold text-secondary mb-3"><i class="fa-solid fa-user me-1"></i> 1. Informasi Pelanggan</h6>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Nama Lengkap <span class="text-danger">*</span></label>
                            <input type="text" name="customer_name" class="form-control" placeholder="Nama pemesan" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Nomor WhatsApp / HP <span class="text-danger">*</span></label>
                            <input type="text" name="customer_phone" class="form-control" placeholder="08xxxxxxxxxx" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Email (Opsional)</label>
                            <input type="email" name="customer_email" class="form-control" placeholder="nama@email.com">
                        </div>
                    </div>

                    <h6 class="fw-bold text-secondary mb-3"><i class="fa-solid fa-utensils me-1"></i> 2. Detail Reservasi & Meja</h6>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Pilih Meja Tersedia <span class="text-danger">*</span></label>
                            <select name="table_id" class="form-select" required>
                                <option value="">-- Pilih Meja --</option>
                                <?php foreach ($availableTables as $at): ?>
                                    <option value="<?= $at['id'] ?>">Meja <?= htmlspecialchars($at['table_number']) ?> (Kapasitas <?= $at['capacity'] ?> Orang - <?= htmlspecialchars($at['location']) ?>)</option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Jumlah Tamu <span class="text-danger">*</span></label>
                            <input type="number" name="guest_count" class="form-control" value="2" min="1" max="50" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Tanggal Reservasi <span class="text-danger">*</span></label>
                            <input type="date" name="reservation_date" class="form-control" value="<?= date('Y-m-d') ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Jam Kedatangan <span class="text-danger">*</span></label>
                            <input type="time" name="reservation_time" class="form-control" value="19:00" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Catatan Tambahan</label>
                            <textarea name="notes" class="form-control" rows="2" placeholder="Contoh: Kursi bayi, dekorasi ulang tahun, dll."></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top">
                    <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4">Simpan Reservasi</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/layouts/footer.php'; ?>
