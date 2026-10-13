<?php
/**
 * File: views/tables.php
 * Unit Kompetensi: J.620100.018.02 (OOP), J.620100.021.02 (Database Access)
 */

use App\Helpers\FormatHelper;

$pageTitle = 'Kelola Meja Restoran';
require_once __DIR__ . '/layouts/header.php';
?>

<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
        <h3 class="fw-bold text-dark m-0"><i class="fa-solid fa-chair text-primary me-2"></i>Kelola Meja Restoran</h3>
        <p class="text-muted small m-0">Atur ketersediaan meja, kapasitas kursi, dan lokasi area restoran.</p>
    </div>
    <div>
        <button type="button" class="btn btn-primary rounded-pill px-4 shadow-sm" data-bs-toggle="modal" data-bs-target="#modalAddTable">
            <i class="fa-solid fa-plus me-1"></i> Tambah Meja Baru
        </button>
    </div>
</div>

<!-- Grid Kartu Meja Visual -->
<div class="row g-3 mb-4">
    <?php foreach ($tables as $t): ?>
        <?php 
            $statusClass = 'table-available';
            if ($t['status'] === 'Dipesan') $statusClass = 'table-reserved';
            if ($t['status'] === 'Terisi') $statusClass = 'table-occupied';
        ?>
        <div class="col-6 col-sm-4 col-md-3 col-xl-2">
            <div class="table-card <?= $statusClass ?> p-3 h-100 d-flex flex-column justify-content-between">
                <div>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="badge bg-white text-dark shadow-sm border fw-bold"><?= htmlspecialchars($t['table_number']) ?></span>
                        <div class="dropdown">
                            <button class="btn btn-sm btn-link text-muted p-0" data-bs-toggle="dropdown">
                                <i class="fa-solid fa-ellipsis-vertical"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                                <li><h6 class="dropdown-header">Ubah Status Meja</h6></li>
                                <li><a class="dropdown-item" href="index.php?page=tables&action=update_status&id=<?= $t['id'] ?>&status=Tersedia"><i class="fa-solid fa-check text-success me-2"></i>Tersedia</a></li>
                                <li><a class="dropdown-item" href="index.php?page=tables&action=update_status&id=<?= $t['id'] ?>&status=Dipesan"><i class="fa-solid fa-clock text-warning me-2"></i>Dipesan</a></li>
                                <li><a class="dropdown-item" href="index.php?page=tables&action=update_status&id=<?= $t['id'] ?>&status=Terisi"><i class="fa-solid fa-users text-danger me-2"></i>Terisi</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <a class="dropdown-item text-danger" href="javascript:void(0)" onclick="confirmDelete('index.php?page=tables&action=delete&id=<?= $t['id'] ?>')">
                                        <i class="fa-solid fa-trash me-2"></i>Hapus Meja
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="text-center py-2">
                        <i class="fa-solid fa-utensils fs-3 text-secondary opacity-50"></i>
                    </div>
                </div>
                <div class="text-center">
                    <div class="small fw-semibold text-dark"><?= htmlspecialchars($t['location']) ?></div>
                    <div class="small text-muted mb-2"><i class="fa-solid fa-user-group me-1"></i><?= $t['capacity'] ?> Kursi</div>
                    <?= FormatHelper::tableStatusBadge($t['status']) ?>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<!-- Modal Tambah Meja -->
<div class="modal fade" id="modalAddTable" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow rounded-4">
            <form action="index.php?page=tables&action=create" method="POST">
                <div class="modal-header border-bottom">
                    <h5 class="modal-title fw-bold"><i class="fa-solid fa-plus-circle text-primary me-2"></i>Tambah Meja Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nomor / Kode Meja <span class="text-danger">*</span></label>
                        <input type="text" name="table_number" class="form-control" placeholder="Contoh: T-09, VIP-01" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Kapasitas Kursi <span class="text-danger">*</span></label>
                        <input type="number" name="capacity" class="form-control" value="4" min="1" max="50" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Area / Lokasi Meja</label>
                        <select name="location" class="form-select">
                            <option value="Indoor - Regular">Indoor - Regular</option>
                            <option value="Indoor - VIP">Indoor - VIP</option>
                            <option value="Indoor - Family">Indoor - Family</option>
                            <option value="Outdoor - Garden">Outdoor - Garden</option>
                            <option value="Outdoor - Terrace">Outdoor - Terrace</option>
                            <option value="Indoor - Meeting Room">Indoor - Meeting Room</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer border-top">
                    <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4">Simpan Meja</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/layouts/footer.php'; ?>
