<?php
/**
 * File: views/order_create.php
 * Halaman Penuh POS (Point of Sale) untuk Pemesanan Restoran
 * Tanpa Pop-up Modal - Pengalaman Antarmuka Kasir Lengkap & Modern
 */

use App\Helpers\FormatHelper;

$pageTitle = 'Buat Pesanan Baru (Kasir POS)';
require_once __DIR__ . '/layouts/header.php';
?>

<div class="container-fluid px-0">
    <!-- Top Bar Navigation -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <div class="d-flex align-items-center gap-2">
                <a href="index.php?page=orders" class="btn btn-outline-secondary btn-sm rounded-circle d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                    <i class="fa-solid fa-arrow-left"></i>
                </a>
                <h3 class="fw-bold text-dark m-0">Kasir & Pemesanan Baru</h3>
            </div>
            <p class="text-muted small m-0 ms-md-5 mt-1">Pilih menu dari katalog atau tambahkan item langsung ke daftar transaksi pelanggan.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="index.php?page=orders" class="btn btn-outline-secondary rounded-pill px-4">
                <i class="fa-solid fa-xmark me-1"></i> Batal / Kembali
            </a>
            <button type="submit" form="formPosOrder" class="btn btn-primary rounded-pill px-4 shadow-sm">
                <i class="fa-solid fa-check me-1"></i> Simpan Transaksi
            </button>
        </div>
    </div>

    <form action="index.php?page=orders&action=create" method="POST" id="formPosOrder">
        <div class="row g-4">
            <!-- KOLOM KIRI: KATALOG MENU RESTORAN -->
            <div class="col-12 col-xl-7 col-lg-6">
                <div class="card border-0 shadow-sm rounded-4 bg-white p-4 h-100">
                    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3 mb-3">
                        <h5 class="fw-bold text-dark m-0">
                            <i class="fa-solid fa-book-open text-primary me-2"></i>Katalog Menu Restoran
                        </h5>
                        <!-- Search Box Menu -->
                        <div class="input-group" style="max-width: 260px;">
                            <span class="input-group-text bg-light border-end-0 text-muted"><i class="fa-solid fa-magnifying-glass"></i></span>
                            <input type="text" id="searchMenuInput" class="form-control bg-light border-start-0 ps-0" placeholder="Cari nama menu...">
                        </div>
                    </div>

                    <!-- Category Pills -->
                    <div class="d-flex gap-2 overflow-auto pb-2 mb-3" id="categoryFilterBar">
                        <button type="button" class="btn btn-sm btn-dark rounded-pill px-3 cat-btn active" data-cat="all">Semua Menu</button>
                        <button type="button" class="btn btn-sm btn-light border rounded-pill px-3 cat-btn" data-cat="Makanan">Makanan</button>
                        <button type="button" class="btn btn-sm btn-light border rounded-pill px-3 cat-btn" data-cat="Minuman">Minuman</button>
                        <button type="button" class="btn btn-sm btn-light border rounded-pill px-3 cat-btn" data-cat="Snack">Snack</button>
                        <button type="button" class="btn btn-sm btn-light border rounded-pill px-3 cat-btn" data-cat="Dessert">Dessert</button>
                    </div>

                    <!-- Grid Item Menu -->
                    <div class="row g-3" id="menuCardsContainer" style="max-height: 620px; overflow-y: auto; padding-right: 4px;">
                        <?php if (empty($menuItems)): ?>
                            <div class="col-12 text-center py-5 text-muted">
                                <i class="fa-solid fa-utensils fs-1 mb-2 opacity-50"></i>
                                <p>Tidak ada menu yang tersedia saat ini.</p>
                            </div>
                        <?php else: ?>
                            <?php foreach ($menuItems as $m): ?>
                                <div class="col-12 col-sm-6 col-md-4 menu-item-card" data-id="<?= $m['id'] ?>" data-name="<?= strtolower(htmlspecialchars($m['name'])) ?>" data-category="<?= htmlspecialchars($m['category']) ?>">
                                    <div class="card h-100 border rounded-3 p-3 shadow-none hover-card" style="transition: all 0.2s ease; cursor: pointer;" onclick="addItemToOrder(<?= $m['id'] ?>, '<?= addslashes(htmlspecialchars($m['name'])) ?>', <?= (float)$m['price'] ?>)">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <span class="badge bg-light text-secondary border small"><?= htmlspecialchars($m['category']) ?></span>
                                            <div class="text-primary fw-bold small"><i class="fa-solid fa-plus-circle fa-lg"></i></div>
                                        </div>
                                        <h6 class="fw-bold text-dark mb-1 text-truncate" title="<?= htmlspecialchars($m['name']) ?>"><?= htmlspecialchars($m['name']) ?></h6>
                                        <div class="text-primary fw-bold mt-auto pt-2 fs-6">
                                            <?= FormatHelper::rupiah($m['price']) ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- KOLOM KANAN: FORM PELANGGAN & DAFTAR PESANAN (KASIR) -->
            <div class="col-12 col-xl-5 col-lg-6">
                <div class="card border-0 shadow-sm rounded-4 bg-white p-4">
                    <!-- Data Meja & Tamu -->
                    <div class="border-bottom pb-3 mb-3">
                        <h5 class="fw-bold text-dark mb-3">
                            <i class="fa-solid fa-user-check text-primary me-2"></i>Informasi Tamu & Meja
                        </h5>
                        <div class="row g-2">
                            <div class="col-12">
                                <label class="form-label small fw-semibold text-secondary">Pilih Meja Restoran <span class="text-danger">*</span></label>
                                <select name="table_id" class="form-select form-select-sm" required>
                                    <option value="">-- Pilih Meja --</option>
                                    <?php foreach ($tables as $tbl): ?>
                                        <option value="<?= $tbl['id'] ?>">
                                            Meja <?= htmlspecialchars($tbl['table_number']) ?> &mdash; Kapasitas: <?= $tbl['capacity'] ?> org (<?= htmlspecialchars($tbl['status']) ?> - <?= htmlspecialchars($tbl['location']) ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-6">
                                <label class="form-label small fw-semibold text-secondary">Nama Pelanggan <span class="text-danger">*</span></label>
                                <input type="text" name="customer_name" class="form-control form-control-sm" placeholder="Nama tamu" required>
                            </div>
                            <div class="col-6">
                                <label class="form-label small fw-semibold text-secondary">No. HP / WhatsApp <span class="text-danger">*</span></label>
                                <input type="text" name="customer_phone" class="form-control form-control-sm" placeholder="08xxxxxxxxxx" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label small fw-semibold text-secondary">Kaitkan Reservasi (Opsional)</label>
                                <select name="reservation_id" class="form-select form-select-sm">
                                    <option value="">-- Tanpa Reservasi (Walk-in) --</option>
                                    <?php foreach ($reservations as $res): ?>
                                        <?php if (in_array($res['status'], ['Confirmed', 'Seated'])): ?>
                                            <option value="<?= $res['id'] ?>">
                                                <?= htmlspecialchars($res['reservation_code']) ?> &mdash; <?= htmlspecialchars($res['customer_name']) ?> (<?= $res['reservation_time'] ?>)
                                            </option>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Item Pesanan yang Dipilih -->
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h6 class="fw-bold text-dark m-0">
                                <i class="fa-solid fa-basket-shopping text-primary me-2"></i>Daftar Pesanan
                            </h6>
                            <span class="badge bg-light text-secondary border" id="itemCountBadge">0 Item</span>
                        </div>

                        <!-- Container Baris Item Pesanan -->
                        <div id="cartItemsList" class="border rounded-3 p-2 bg-light mb-2" style="min-height: 180px; max-height: 280px; overflow-y: auto;">
                            <!-- Pesan kosong jika belum ada menu dipilih -->
                            <div id="emptyCartNotice" class="text-center py-4 text-muted">
                                <i class="fa-solid fa-hand-pointer fs-3 mb-2 opacity-40"></i>
                                <p class="small m-0">Klik menu pada katalog di sebelah kiri untuk menambahkan ke pesanan.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Kalkulasi & Billing -->
                    <div class="bg-light p-3 rounded-3 border mb-3">
                        <div class="d-flex justify-content-between mb-2 small">
                            <span class="text-secondary">Subtotal Item:</span>
                            <span class="fw-semibold text-dark" id="displaySubtotal">Rp 0</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-2 small">
                            <span class="text-secondary">Diskon / Potongan (Rp):</span>
                            <input type="number" name="discount_amount" id="inputDiscount" class="form-control form-control-sm text-end" style="width: 130px;" value="0" min="0">
                        </div>
                        <div class="d-flex justify-content-between mb-2 small">
                            <span class="text-secondary">Pajak Restoran (10% PPN):</span>
                            <span class="fw-semibold text-muted" id="displayTax">Rp 0</span>
                        </div>
                        <hr class="my-2 border-secondary-subtle">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="fw-bold text-dark fs-6">Grand Total:</span>
                            <span class="fw-bold text-primary fs-4" id="displayGrandTotal">Rp 0</span>
                        </div>
                    </div>

                    <!-- Pembayaran & Konfirmasi -->
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label small fw-semibold text-secondary">Status Pembayaran</label>
                            <select name="payment_status" id="selectPaymentStatus" class="form-select form-select-sm">
                                <option value="Lunas" selected>Lunas Langsung</option>
                                <option value="Belum Bayar">Belum Bayar</option>
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-semibold text-secondary">Metode Bayar</label>
                            <select name="payment_method" id="selectPaymentMethod" class="form-select form-select-sm">
                                <option value="Tunai" selected>Tunai (Cash)</option>
                                <option value="QRIS">QRIS</option>
                                <option value="Transfer">Transfer Bank</option>
                                <option value="Kartu">Debit / Kartu</option>
                            </select>
                        </div>
                    </div>

                    <!-- Kotak Input Uang Tunai & Kembalian (Khusus Pembayaran Tunai) -->
                    <div id="cashCalculationBox" class="p-3 bg-light rounded-3 border mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <label class="form-label small fw-bold text-dark m-0">
                                <i class="fa-solid fa-money-bill-wave text-success me-1"></i> Uang Tunai Diterima (Rp)
                            </label>
                            <button type="button" class="btn btn-sm btn-outline-dark py-0 px-2 rounded-pill" id="btnExactCash" style="font-size: 0.75rem;">
                                <i class="fa-solid fa-hand-holding-dollar me-1"></i> Uang Pas
                            </button>
                        </div>

                        <div class="input-group mb-2">
                            <span class="input-group-text bg-white fw-bold text-secondary">Rp</span>
                            <input type="number" name="cash_received" id="inputCashReceived" class="form-control form-control-lg fw-bold text-end" placeholder="0" min="0" step="500">
                        </div>

                        <!-- Tombol Pecahan Cepat -->
                        <div class="d-flex flex-wrap gap-1 mb-2">
                            <button type="button" class="btn btn-sm btn-white border py-1 px-2 rounded-2 quick-cash-btn small" data-val="10000">10rb</button>
                            <button type="button" class="btn btn-sm btn-white border py-1 px-2 rounded-2 quick-cash-btn small" data-val="20000">20rb</button>
                            <button type="button" class="btn btn-sm btn-white border py-1 px-2 rounded-2 quick-cash-btn small" data-val="50000">50rb</button>
                            <button type="button" class="btn btn-sm btn-white border py-1 px-2 rounded-2 quick-cash-btn small" data-val="100000">100rb</button>
                            <button type="button" class="btn btn-sm btn-white border py-1 px-2 rounded-2 quick-cash-btn small" data-val="200000">200rb</button>
                        </div>

                        <!-- Baris Kembalian -->
                        <input type="hidden" name="change_amount" id="inputChangeAmount" value="0">
                        <div class="d-flex justify-content-between align-items-center pt-2 border-top">
                            <span class="fw-bold text-secondary small">Uang Kembalian:</span>
                            <span class="fw-bold fs-5 text-success" id="displayChangeAmount">Rp 0</span>
                        </div>
                        <div id="cashWarning" class="small text-danger mt-1 d-none fw-semibold">
                            <i class="fa-solid fa-circle-exclamation me-1"></i> Uang tunai kurang: <span id="displayShortage">Rp 0</span>
                        </div>
                    </div>

                    <!-- Tombol Aksi Simpan -->
                    <button type="submit" class="btn btn-primary btn-lg w-100 rounded-pill fw-bold py-2 shadow-sm">
                        <i class="fa-solid fa-receipt me-2"></i>Simpan & Cetak Pesanan
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

<!-- Template & Script Interaktif Kasir POS -->
<script>
// Data keranjang pesanan: { menuId: { id, name, price, qty } }
const cart = {};
let latestGrandTotal = 0;

function formatRupiah(number) {
    return 'Rp ' + Number(number).toLocaleString('id-ID');
}

function addItemToOrder(id, name, price) {
    if (cart[id]) {
        cart[id].qty += 1;
    } else {
        cart[id] = { id: id, name: name, price: price, qty: 1 };
    }
    renderCart();
}

function updateQty(id, delta) {
    if (!cart[id]) return;
    cart[id].qty += delta;
    if (cart[id].qty <= 0) {
        delete cart[id];
    }
    renderCart();
}

function removeItem(id) {
    if (cart[id]) {
        delete cart[id];
        renderCart();
    }
}

function renderCart() {
    const list = document.getElementById('cartItemsList');
    const badge = document.getElementById('itemCountBadge');
    const keys = Object.keys(cart);

    badge.innerText = keys.length + ' Item';

    if (keys.length === 0) {
        list.innerHTML = `
            <div id="emptyCartNotice" class="text-center py-4 text-muted">
                <i class="fa-solid fa-hand-pointer fs-3 mb-2 opacity-40"></i>
                <p class="small m-0">Klik menu pada katalog di sebelah kiri untuk menambahkan ke pesanan.</p>
            </div>`;
        calculateBilling();
        return;
    }

    let html = '';
    keys.forEach(id => {
        const item = cart[id];
        const subtotal = item.qty * item.price;
        html += `
            <div class="d-flex align-items-center justify-content-between p-2 mb-2 bg-white rounded-3 border">
                <input type="hidden" name="menu_items[]" value="${item.id}">
                <input type="hidden" name="quantities[]" value="${item.qty}">
                <div class="flex-grow-1 me-2" style="max-width: 48%;">
                    <div class="fw-semibold text-dark text-truncate small" title="${item.name}">${item.name}</div>
                    <div class="text-muted small">${formatRupiah(item.price)}</div>
                </div>
                <div class="d-flex align-items-center gap-1 me-2">
                    <button type="button" class="btn btn-sm btn-light border p-0 d-flex align-items-center justify-content-center" style="width:26px; height:26px;" onclick="updateQty(${item.id}, -1)">
                        <i class="fa-solid fa-minus small"></i>
                    </button>
                    <span class="fw-bold px-2 small">${item.qty}</span>
                    <button type="button" class="btn btn-sm btn-light border p-0 d-flex align-items-center justify-content-center" style="width:26px; height:26px;" onclick="updateQty(${item.id}, 1)">
                        <i class="fa-solid fa-plus small"></i>
                    </button>
                </div>
                <div class="text-end fw-semibold text-dark small me-2" style="min-width: 75px;">
                    ${formatRupiah(subtotal)}
                </div>
                <div>
                    <button type="button" class="btn btn-sm text-danger p-1" onclick="removeItem(${item.id})" title="Hapus Item">
                        <i class="fa-solid fa-trash-can"></i>
                    </button>
                </div>
            </div>`;
    });

    list.innerHTML = html;
    calculateBilling();
}

function calculateBilling() {
    let subtotal = 0;
    Object.values(cart).forEach(item => {
        subtotal += item.qty * item.price;
    });

    const discountInput = document.getElementById('inputDiscount');
    const discount = parseFloat(discountInput.value || 0);
    const taxable = Math.max(0, subtotal - discount);
    const tax = taxable * 0.10;
    latestGrandTotal = Math.max(0, taxable + tax);

    document.getElementById('displaySubtotal').innerText = formatRupiah(subtotal);
    document.getElementById('displayTax').innerText = formatRupiah(tax);
    document.getElementById('displayGrandTotal').innerText = formatRupiah(latestGrandTotal);

    calculateChange();
}

function calculateChange() {
    const cashInput = document.getElementById('inputCashReceived');
    const displayChange = document.getElementById('displayChangeAmount');
    const inputChange = document.getElementById('inputChangeAmount');
    const warning = document.getElementById('cashWarning');
    const displayShortage = document.getElementById('displayShortage');
    const payMethod = document.getElementById('selectPaymentMethod').value;
    const payStatus = document.getElementById('selectPaymentStatus').value;

    const cashVal = parseFloat(cashInput.value || 0);

    if (payMethod !== 'Tunai' || payStatus !== 'Lunas') {
        inputChange.value = 0;
        displayChange.innerText = 'Rp 0';
        warning.classList.add('d-none');
        return;
    }

    if (cashInput.value === '' || cashVal === 0) {
        inputChange.value = 0;
        displayChange.innerText = 'Rp 0';
        warning.classList.add('d-none');
        return;
    }

    const diff = cashVal - latestGrandTotal;
    if (diff >= 0) {
        inputChange.value = diff;
        displayChange.innerText = formatRupiah(diff);
        displayChange.classList.remove('text-danger');
        displayChange.classList.add('text-success');
        warning.classList.add('d-none');
    } else {
        inputChange.value = 0;
        displayChange.innerText = 'Rp 0';
        displayShortage.innerText = formatRupiah(Math.abs(diff));
        warning.classList.remove('d-none');
    }
}

function toggleCashBox() {
    const payMethod = document.getElementById('selectPaymentMethod').value;
    const payStatus = document.getElementById('selectPaymentStatus').value;
    const box = document.getElementById('cashCalculationBox');

    if (payMethod === 'Tunai' && payStatus === 'Lunas') {
        box.classList.remove('d-none');
    } else {
        box.classList.add('d-none');
    }
    calculateChange();
}

document.addEventListener('DOMContentLoaded', function() {
    // Event listener diskon & kalkulasi
    document.getElementById('inputDiscount').addEventListener('input', calculateBilling);

    // Event listener uang diterima & metode bayar
    const inputCash = document.getElementById('inputCashReceived');
    inputCash.addEventListener('input', calculateChange);

    document.getElementById('selectPaymentMethod').addEventListener('change', toggleCashBox);
    document.getElementById('selectPaymentStatus').addEventListener('change', toggleCashBox);

    // Tombol Uang Pas
    document.getElementById('btnExactCash').addEventListener('click', function() {
        if (latestGrandTotal > 0) {
            inputCash.value = Math.round(latestGrandTotal);
            calculateChange();
        }
    });

    // Tombol Pecahan Cepat
    document.querySelectorAll('.quick-cash-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const val = parseFloat(this.getAttribute('data-val') || 0);
            inputCash.value = val;
            calculateChange();
        });
    });

    // Filter kategori katalog
    const catButtons = document.querySelectorAll('.cat-btn');
    const cards = document.querySelectorAll('.menu-item-card');

    catButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            catButtons.forEach(b => {
                b.classList.remove('btn-dark', 'active');
                b.classList.add('btn-light', 'border');
            });
            btn.classList.add('btn-dark', 'active');
            btn.classList.remove('btn-light', 'border');

            filterMenuCards();
        });
    });

    // Search filter realtime
    const searchInput = document.getElementById('searchMenuInput');
    searchInput.addEventListener('input', filterMenuCards);

    function filterMenuCards() {
        const query = searchInput.value.toLowerCase().trim();
        const activeBtn = document.querySelector('.cat-btn.active');
        const selectedCat = activeBtn ? activeBtn.getAttribute('data-cat') : 'all';

        cards.forEach(card => {
            const cardName = card.getAttribute('data-name');
            const cardCat = card.getAttribute('data-category');

            const matchQuery = !query || cardName.includes(query);
            const matchCat = (selectedCat === 'all' || cardCat === selectedCat);

            if (matchQuery && matchCat) {
                card.style.display = 'block';
            } else {
                card.style.display = 'none';
            }
        });
    }

    // Validasi form sebelum submit
    document.getElementById('formPosOrder').addEventListener('submit', function(e) {
        if (Object.keys(cart).length === 0) {
            e.preventDefault();
            Swal.fire({
                icon: 'warning',
                title: 'Pesanan Masih Kosong',
                text: 'Silakan pilih minimal 1 item menu dari katalog sebelum menyimpan pesanan.',
                confirmButtonColor: '#0f172a'
            });
            return;
        }

        const payMethod = document.getElementById('selectPaymentMethod').value;
        const payStatus = document.getElementById('selectPaymentStatus').value;
        const cashVal = parseFloat(inputCash.value || 0);

        if (payMethod === 'Tunai' && payStatus === 'Lunas') {
            if (cashVal <= 0) {
                e.preventDefault();
                Swal.fire({
                    icon: 'warning',
                    title: 'Masukkan Uang Tunai',
                    text: 'Silakan masukkan jumlah uang tunai yang diterima dari pelanggan.',
                    confirmButtonColor: '#0f172a'
                });
                return;
            }
            if (cashVal < latestGrandTotal) {
                e.preventDefault();
                const shortage = latestGrandTotal - cashVal;
                Swal.fire({
                    icon: 'error',
                    title: 'Uang Tunai Kurang',
                    text: 'Uang yang diterima kurang ' + formatRupiah(shortage) + ' dari total belanja.',
                    confirmButtonColor: '#0f172a'
                });
                return;
            }
        }
    });

    toggleCashBox();
});
</script>

<?php require_once __DIR__ . '/layouts/footer.php'; ?>
