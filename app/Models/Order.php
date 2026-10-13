<?php
/**
 * File: app/Models/Order.php
 * Unit Kompetensi:
 * - J.620100.018.02 (OOP - Interface Implementation & Polymorphism)
 * - J.620100.021.02 (Akses Basis Data dengan Transaksi)
 * - J.620100.016.01 (Guidelines & Best Practices)
 */

namespace App\Models;

use App\Core\Model;
use App\Contracts\CalculableInterface;
use PDO;

class Order extends Model implements CalculableInterface
{
    protected string $table = 'orders';

    /** @var array Item-item yang dipesan [ ['menu_id'=>1, 'name'=>'Nasi', 'qty'=>2, 'price'=>10000], ... ] */
    private array $items = [];

    /** @var float Persentase pajak */
    private float $taxRate = 10.0;

    /** @var float Potongan harga */
    private float $discountAmount = 0.0;

    /** @var float Uang tunai yang diserahkan pelanggan */
    private float $cashReceived = 0.0;

    /** @var float Kembalian uang pelanggan */
    private float $changeAmount = 0.0;

    /**
     * Menambahkan item pesanan ke dalam objek memori (Encapsulation).
     */
    public function addItem(int $menuId, string $name, int $qty, float $price): self
    {
        $this->items[] = [
            'menu_id'  => $menuId,
            'name'     => $name,
            'qty'      => $qty,
            'price'    => $price,
            'subtotal' => $qty * $price,
        ];
        return $this;
    }

    /**
     * Set diskon potongan harga.
     */
    public function setDiscount(float $discount): self
    {
        $this->discountAmount = max(0, $discount);
        return $this;
    }

    /**
     * Set nominal uang tunai yang diserahkan pelanggan dan hitung kembalian otomatis.
     */
    public function setCashPayment(float $cash): self
    {
        $this->cashReceived = max(0, $cash);
        $grandTotal = $this->calculateGrandTotal();
        $this->changeAmount = max(0, $this->cashReceived - $grandTotal);
        return $this;
    }

    public function getCashReceived(): float
    {
        return $this->cashReceived;
    }

    public function getChangeAmount(): float
    {
        return $this->changeAmount;
    }

    /**
     * Implementasi CalculableInterface: Hitung Subtotal.
     */
    public function calculateSubtotal(): float
    {
        $subtotal = 0.0;
        foreach ($this->items as $item) {
            $subtotal += (float)$item['subtotal'];
        }
        return $subtotal;
    }

    /**
     * Implementasi CalculableInterface: Hitung Pajak (default 10%).
     */
    public function calculateTax(float $taxRate = 10.0): float
    {
        $this->taxRate = $taxRate;
        $subtotal = $this->calculateSubtotal();
        // Pajak dihitung dari subtotal setelah diskon
        $taxable = max(0, $subtotal - $this->discountAmount);
        return round($taxable * ($this->taxRate / 100), 2);
    }

    /**
     * Implementasi CalculableInterface: Hitung Grand Total.
     */
    public function calculateGrandTotal(): float
    {
        $subtotal = $this->calculateSubtotal();
        $tax = $this->calculateTax($this->taxRate);
        $total = ($subtotal - $this->discountAmount) + $tax;
        return max(0, round($total, 2));
    }

    /**
     * Menyimpan transaksi pesanan beserta seluruh item ke database secara atomik.
     *
     * @param int $customerId
     * @param int $tableId
     * @param int|null $reservationId
     * @param string $paymentStatus 'Belum Bayar' atau 'Lunas'
     * @param string|null $paymentMethod
     * @param float $cashReceived
     * @param float $changeAmount
     * @return string Order Code
     */
    public function saveOrder(
        int $customerId,
        int $tableId,
        ?int $reservationId = null,
        string $paymentStatus = 'Belum Bayar',
        ?string $paymentMethod = 'Tunai',
        float $cashReceived = 0.0,
        float $changeAmount = 0.0
    ): string {
        if (empty($this->items)) {
            throw new \InvalidArgumentException("Pesanan tidak boleh kosong! Tambahkan minimal satu item menu.");
        }

        $subtotal = $this->calculateSubtotal();
        $tax = $this->calculateTax($this->taxRate);
        $grandTotal = $this->calculateGrandTotal();
        $orderCode = 'ORD-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -4));

        // Jika cashReceived dipass, gunakan; jika tidak gunakan nilai property
        $actualCash = $cashReceived > 0 ? $cashReceived : $this->cashReceived;
        $actualChange = $changeAmount > 0 ? $changeAmount : max(0, $actualCash - $grandTotal);

        try {
            $this->db->beginTransaction();

            // 1. Insert ke tabel orders
            $sqlOrder = "INSERT INTO {$this->table} 
                         (order_code, reservation_id, customer_id, table_id, total_amount, tax_amount, discount_amount, grand_total, cash_received, change_amount, payment_status, payment_method)
                         VALUES (:code, :res_id, :cust_id, :table_id, :total, :tax, :discount, :grand, :cash_rec, :change_amt, :pay_status, :pay_method)";
            $stmt = $this->db->prepare($sqlOrder);
            $stmt->execute([
                ':code'       => $orderCode,
                ':res_id'     => $reservationId,
                ':cust_id'    => $customerId,
                ':table_id'   => $tableId,
                ':total'      => $subtotal,
                ':tax'        => $tax,
                ':discount'   => $this->discountAmount,
                ':grand'      => $grandTotal,
                ':cash_rec'   => $actualCash,
                ':change_amt' => $actualChange,
                ':pay_status' => $paymentStatus,
                ':pay_method' => $paymentMethod
            ]);
            $orderId = (int)$this->db->lastInsertId();

            // 2. Insert ke tabel order_items
            $sqlItem = "INSERT INTO order_items (order_id, menu_item_id, quantity, unit_price, subtotal)
                        VALUES (:order_id, :menu_id, :qty, :price, :subtotal)";
            $stmtItem = $this->db->prepare($sqlItem);

            foreach ($this->items as $item) {
                $stmtItem->execute([
                    ':order_id' => $orderId,
                    ':menu_id'  => $item['menu_id'],
                    ':qty'      => $item['qty'],
                    ':price'    => $item['price'],
                    ':subtotal' => $item['subtotal']
                ]);
            }

            // 3. Update meja jadi Terisi jika belum
            $tableModel = new Table();
            $tableModel->updateStatus($tableId, 'Terisi');

            $this->db->commit();
            return $orderCode;
        } catch (\Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    /**
     * Mengambil daftar pesanan lengkap beserta relasi.
     */
    public function getAllOrders(): array
    {
        $sql = "SELECT o.*, c.name AS customer_name, c.phone AS customer_phone, t.table_number
                FROM {$this->table} o
                JOIN customers c ON o.customer_id = c.id
                JOIN tables t ON o.table_id = t.id
                ORDER BY o.order_date DESC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }

    /**
     * Mengambil detail pesanan dan rincian item menunya.
     */
    public function getOrderDetails(int $orderId): ?array
    {
        $sql = "SELECT o.*, c.name AS customer_name, c.phone AS customer_phone, c.email AS customer_email,
                       t.table_number, t.location AS table_location
                FROM {$this->table} o
                JOIN customers c ON o.customer_id = c.id
                JOIN tables t ON o.table_id = t.id
                WHERE o.id = :id LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $orderId]);
        $order = $stmt->fetch();

        if (!$order) {
            return null;
        }

        // Ambil item
        $sqlItems = "SELECT oi.*, m.name AS menu_name, m.category AS menu_category
                     FROM order_items oi
                     JOIN menu_items m ON oi.menu_item_id = m.id
                     WHERE oi.order_id = :oid";
        $stmtItems = $this->db->prepare($sqlItems);
        $stmtItems->execute([':oid' => $orderId]);
        $order['items'] = $stmtItems->fetchAll();

        return $order;
    }

    /**
     * Mengupdate status pembayaran menjadi Lunas atau Belum Bayar.
     */
    public function markAsPaid(int $orderId, string $paymentMethod = 'Tunai'): bool
    {
        return $this->updatePaymentStatus($orderId, 'Lunas', $paymentMethod);
    }

    /**
     * Update status pembayaran secara fleksibel (Lunas / Belum Bayar).
     */
    public function updatePaymentStatus(int $orderId, string $status, ?string $paymentMethod = null): bool
    {
        if ($paymentMethod) {
            $sql = "UPDATE {$this->table} SET payment_status = :status, payment_method = :method WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                ':status' => $status,
                ':method' => $paymentMethod,
                ':id'     => $orderId
            ]);
        } else {
            $sql = "UPDATE {$this->table} SET payment_status = :status WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                ':status' => $status,
                ':id'     => $orderId
            ]);
        }
    }
}
