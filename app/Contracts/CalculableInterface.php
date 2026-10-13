<?php
/**
 * File: app/Contracts/CalculableInterface.php
 * Unit Kompetensi:
 * - J.620100.018.02 (Mengimplementasikan Pemrograman Berorientasi Objek - Interface & Polymorphism)
 */

namespace App\Contracts;

interface CalculableInterface
{
    /**
     * Menghitung subtotal pesanan sebelum pajak dan diskon.
     *
     * @return float
     */
    public function calculateSubtotal(): float;

    /**
     * Menghitung nilai pajak (misal 10% PPN).
     *
     * @param float $taxRate Persentase pajak (0 - 100)
     * @return float
     */
    public function calculateTax(float $taxRate = 10.0): float;

    /**
     * Menghitung total keseluruhan setelah potongan dan pajak.
     *
     * @return float
     */
    public function calculateGrandTotal(): float;
}
