<?php
/**
 * File: app/Helpers/FormatHelper.php
 * Unit Kompetensi:
 * - J.620100.017.02 (Mengimplementasikan Pemrograman Terstruktur - Fungsi Pembantu Modular)
 */

namespace App\Helpers;

class FormatHelper
{
    /**
     * Memformat angka menjadi format mata uang Rupiah.
     *
     * @param float|int $amount
     * @return string
     */
    public static function rupiah(float|int $amount): string
    {
        return 'Rp ' . number_format($amount, 0, ',', '.');
    }

    /**
     * Memformat tanggal ke bahasa Indonesia (contoh: 12 Oktober 2026).
     *
     * @param string $datetime
     * @param bool $withTime
     * @return string
     */
    public static function tanggalIndo(string $datetime, bool $withTime = false): string
    {
        if (empty($datetime)) {
            return '-';
        }

        $bulan = [
            1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
            'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
        ];

        $timestamp = strtotime($datetime);
        $tgl = date('d', $timestamp);
        $bln = $bulan[(int)date('m', $timestamp)];
        $thn = date('Y', $timestamp);

        $result = "{$tgl} {$bln} {$thn}";

        if ($withTime) {
            $jam = date('H:i', $timestamp);
            $result .= " {$jam} WIB";
        }

        return $result;
    }

    /**
     * Mendapatkan badge status meja dengan style bootstrap.
     */
    public static function tableStatusBadge(string $status): string
    {
        switch ($status) {
            case 'Tersedia':
                return '<span class="badge bg-success-subtle text-success border border-success-subtle"><i class="fa-solid fa-circle-check me-1"></i> Tersedia</span>';
            case 'Dipesan':
                return '<span class="badge bg-warning-subtle text-warning border border-warning-subtle"><i class="fa-solid fa-clock me-1"></i> Dipesan</span>';
            case 'Terisi':
                return '<span class="badge bg-danger-subtle text-danger border border-danger-subtle"><i class="fa-solid fa-users me-1"></i> Terisi</span>';
            default:
                return '<span class="badge bg-secondary">' . htmlspecialchars($status) . '</span>';
        }
    }

    /**
     * Mendapatkan badge status pembayaran.
     */
    public static function paymentStatusBadge(string $status): string
    {
        if ($status === 'Lunas') {
            return '<span class="badge bg-success"><i class="fa-solid fa-check-double me-1"></i> Lunas</span>';
        }
        return '<span class="badge bg-warning text-dark"><i class="fa-solid fa-hourglass-half me-1"></i> Belum Bayar</span>';
    }

    /**
     * Mendapatkan badge status reservasi.
     */
    public static function reservationStatusBadge(string $status): string
    {
        switch ($status) {
            case 'Confirmed':
                return '<span class="badge bg-primary">Terkonfirmasi</span>';
            case 'Seated':
                return '<span class="badge bg-info text-dark">Duduk di Meja</span>';
            case 'Completed':
                return '<span class="badge bg-success">Selesai</span>';
            case 'Cancelled':
                return '<span class="badge bg-danger">Dibatalkan</span>';
            default:
                return '<span class="badge bg-secondary">' . htmlspecialchars($status) . '</span>';
        }
    }
}
