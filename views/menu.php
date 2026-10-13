<?php
/**
 * File: views/menu.php
 * Unit Kompetensi: J.620100.018.02 (OOP - MenuItem Model), J.620100.021.02 (Database Access)
 */

use App\Helpers\FormatHelper;

$pageTitle = 'Kelola Menu Restoran';
require_once __DIR__ . '/layouts/header.php';
?>

<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
        <h3 class="fw-bold text-dark m-0"><i class="fa-solid fa-bowl-food text-primary me-2"></i>Daftar Menu Restoran</h3>
        <p class="text-muted small m-0">Kelola master hidangan makanan, minuman, hidangan penutup, dan paket hemat.</p>
    </div>
    <div>
        <button type="button" class="btn btn-primary rounded-pill px-4 shadow-sm" data-bs-toggle="modal" data-bs-target="#modalAddMenu">
            <i class="fa-solid fa-plus me-1"></i> Tambah Menu Baru
        </button>
    </div>
</div>

<!-- Nav Kategori Menu -->
<div class="card border-0 shadow-sm rounded-4 bg-white p-3 mb-4">
    <div class="d-flex flex-wrap gap-2">
        <a href="index.php?page=menu" class="btn btn-sm <?= empty($selectedCategory) ? 'btn-primary' : 'btn-outline-secondary' ?> rounded-pill px-3">
            Semua Menu
        </a>
        <a href="index.php?page=menu&category=Makanan" class="btn btn-sm <?= ($selectedCategory === 'Makanan') ? 'btn-primary' : 'btn-outline-secondary' ?> rounded-pill px-3">
            <i class="fa-solid fa-burger me-1"></i> Makanan
        </a>
        <a href="index.php?page=menu&category=Minuman" class="btn btn-sm <?= ($selectedCategory === 'Minuman') ? 'btn-primary' : 'btn-outline-secondary' ?> rounded-pill px-3">
            <i class="fa-solid fa-mug-hot me-1"></i> Minuman
        </a>
        <a href="index.php?page=menu&category=Dessert" class="btn btn-sm <?= ($selectedCategory === 'Dessert') ? 'btn-primary' : 'btn-outline-secondary' ?> rounded-pill px-3">
            <i class="fa-solid fa-ice-cream me-1"></i> Dessert
        </a>
        <a href="index.php?page=menu&category=Paket" class="btn btn-sm <?= ($selectedCategory === 'Paket') ? 'btn-primary' : 'btn-outline-secondary' ?> rounded-pill px-3">
            <i class="fa-solid fa-boxes-stacked me-1"></i> Paket Hemat
        </a>
    </div>
</div>

<!-- Grid Kartu Menu -->
<div class="row g-3">
    <?php if (empty($menuItems)): ?>
        <div class="col-12 text-center py-5 text-muted">
            Belum ada menu pada kategori ini.
        </div>
    <?php else: ?>
        <?php foreach ($menuItems as $m): ?>
            <div class="col-12 col-sm-6 col-md-4 col-xl-3">
                <div class="card border-0 shadow-sm rounded-4 bg-white p-3 h-100 d-flex flex-column justify-content-between">
                    <div>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle"><?= htmlspecialchars($m['category']) ?></span>
                            <span class="badge <?= $m['is_available'] ? 'bg-success-subtle text-success' : 'bg-secondary' ?>">
                                <?= $m['is_available'] ? 'Tersedia' : 'Habis' ?>
                            </span>
                        </div>
                        <h6 class="fw-bold text-dark mb-1"><?= htmlspecialchars($m['name']) ?></h6>
                        <p class="small text-muted mb-3" style="min-height: 40px;">
                            <?= htmlspecialchars($m['description'] ?: 'Tidak ada deskripsi.') ?>
                        </p>
                    </div>
                    <div>
                        <div class="d-flex justify-content-between align-items-center pt-2 border-top">
                            <span class="fw-bold text-primary fs-6"><?= FormatHelper::rupiah($m['price']) ?></span>
                            <button type="button" class="btn btn-sm text-danger" onclick="confirmDelete('index.php?page=menu&action=delete&id=<?= $m['id'] ?>')">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<!-- Modal Tambah Menu -->
<div class="modal fade" id="modalAddMenu" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow rounded-4">
            <form action="index.php?page=menu&action=create" method="POST">
                <div class="modal-header border-bottom">
                    <h5 class="modal-title fw-bold"><i class="fa-solid fa-plus-circle text-primary me-2"></i>Tambah Menu Hidangan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nama Hidangan <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" placeholder="Contoh: Ayam Bakar Madu" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Kategori <span class="text-danger">*</span></label>
                        <select name="category" class="form-select" required>
                            <option value="Makanan">Makanan</option>
                            <option value="Minuman">Minuman</option>
                            <option value="Dessert">Dessert</option>
                            <option value="Paket">Paket Hemat</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Harga Satuan (Rp) <span class="text-danger">*</span></label>
                        <input type="number" name="price" class="form-control" placeholder="35000" min="500" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Deskripsi Hidangan</label>
                        <textarea name="description" class="form-control" rows="2" placeholder="Komposisi atau keterangan rasa menu"></textarea>
                    </div>
                </div>
                <div class="modal-footer border-top">
                    <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4">Simpan Menu</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/layouts/footer.php'; ?>
