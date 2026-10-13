<?php
/**
 * File: tests/OrderTest.php
 * Unit Kompetensi:
 * - J.620100.033.02 (Melaksanakan Pengujian Unit Program - Pengujian Kalkulasi Order)
 */

namespace Tests;

require_once __DIR__ . '/TestCase.php';
require_once __DIR__ . '/../app/Contracts/CalculableInterface.php';
require_once __DIR__ . '/../app/Config/Database.php';
require_once __DIR__ . '/../app/Core/ErrorHandler.php';
require_once __DIR__ . '/../app/Core/Model.php';
require_once __DIR__ . '/../app/Models/Order.php';

use App\Models\Order;

class OrderTest extends TestCase
{
    /**
     * Menguji perhitungan subtotal item pesanan.
     */
    public function testSubtotalCalculation(): void
    {
        $order = new Order();
        $order->addItem(1, 'Nasi Goreng', 2, 35000.00); // 70.000
        $order->addItem(2, 'Es Teh Manis', 3, 8000.00);  // 24.000

        $subtotal = $order->calculateSubtotal();
        $this->assertEquals(94000.00, $subtotal, "Subtotal pesanan harus berjumlah 94.000");
    }

    /**
     * Menguji perhitungan pajak 10% (PPN).
     */
    public function testTaxCalculation(): void
    {
        $order = new Order();
        $order->addItem(1, 'Ayam Bakar', 2, 40000.00); // 80.000
        $tax = $order->calculateTax(10.0); // 10% dari 80.000 = 8.000

        $this->assertEquals(8000.00, $tax, "Pajak 10% dari 80.000 harus bernilai 8.000");
    }

    /**
     * Menguji perhitungan grand total dengan potongan diskon dan pajak.
     */
    public function testGrandTotalWithDiscountAndTax(): void
    {
        $order = new Order();
        $order->addItem(1, 'Steak Wagyu', 1, 100000.00); // Subtotal 100.000
        $order->setDiscount(10000.00); // Diskon 10.000 -> Sisa 90.000
        
        // Pajak 10% dari 90.000 = 9.000
        // Grand total = 90.000 + 9.000 = 99.000
        $grandTotal = $order->calculateGrandTotal();
        $this->assertEquals(99000.00, $grandTotal, "Grand total setelah diskon dan pajak harus bernilai 99.000");
    }

    /**
     * Menguji perhitungan uang tunai diterima dan uang kembalian pelanggan.
     */
    public function testCashReceivedAndChangeCalculation(): void
    {
        $order = new Order();
        $order->addItem(1, 'Paket Makan', 1, 90000.00);
        // Subtotal 90.000, Pajak 10% = 9.000, Grand total = 99.000
        $grandTotal = $order->calculateGrandTotal();
        $this->assertEquals(99000.00, $grandTotal);

        // Pelanggan membayar uang tunai Rp 100.000
        $order->setCashPayment(100000.00);
        $this->assertEquals(100000.00, $order->getCashReceived(), "Uang diterima harus 100.000");
        $this->assertEquals(1000.00, $order->getChangeAmount(), "Kembalian harus Rp 1.000");
    }
}
