// Helper Frontend untuk Sistem Pemesanan Restoran

// Format angka ke format Rupiah
function formatRupiah(number) {
    return 'Rp ' + new Intl.NumberFormat('id-ID').format(number);
}

// Konfirmasi Hapus Data dengan SweetAlert2 (Unit J.620100.019.02 Pre-Existing Library)
function confirmDelete(url, title = 'Apakah Anda yakin ingin menghapus data ini?') {
    Swal.fire({
        title: title,
        text: "Data yang sudah dihapus tidak dapat dikembalikan!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#64748b',
        confirmButtonText: '<i class="fa-solid fa-trash me-1"></i> Ya, Hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = url;
        }
    });
}
