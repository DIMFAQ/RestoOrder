<?php
/**
 * File: app/Models/Report.php
 * Unit Kompetensi:
 * - J.620100.018.02 (OOP - Model Pelaporan)
 * - J.620100.021.02 (Akses Basis Data Agregasi & Grouping)
 */

namespace App\Models;

use App\Core\Model;
use PDO;

class Report extends Model
{
    /**
     * Mengambil ringkasan statistik KPI Restoran untuk Dashboard.
     */
    public function getSummaryStats(): array
    {
        // 1. Total Pendapatan Lunas
        $sqlRev = "SELECT COALESCE(SUM(grand_total), 0) AS total_revenue FROM orders WHERE payment_status = 'Lunas'";
        $revenue = (float)$this->db->query($sqlRev)->fetch()['total_revenue'];

        // 2. Total Pesanan
        $sqlOrd = "SELECT COUNT(*) AS total_orders FROM orders";
        $totalOrders = (int)$this->db->query($sqlOrd)->fetch()['total_orders'];

        // 3. Total Reservasi Aktif
        $sqlRes = "SELECT COUNT(*) AS total_res FROM reservations WHERE status IN ('Confirmed', 'Seated')";
        $activeRes = (int)$this->db->query($sqlRes)->fetch()['total_res'];

        // 4. Meja Tersedia
        $sqlTbl = "SELECT COUNT(*) AS available_tables FROM tables WHERE status = 'Tersedia'";
        $availableTables = (int)$this->db->query($sqlTbl)->fetch()['available_tables'];

        return [
            'total_revenue'    => $revenue,
            'total_orders'     => $totalOrders,
            'active_reservations' => $activeRes,
            'available_tables' => $availableTables,
        ];
    }

    /**
     * Mengambil data laporan pesanan berdasarkan rentang tanggal.
     */
    public function getOrderReport(string $startDate, string $endDate): array
    {
        $sql = "SELECT o.*, c.name AS customer_name, c.phone AS customer_phone, t.table_number
                FROM orders o
                JOIN customers c ON o.customer_id = c.id
                JOIN tables t ON o.table_id = t.id
                WHERE DATE(o.order_date) BETWEEN :start_date AND :end_date
                ORDER BY o.order_date DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':start_date' => $startDate,
            ':end_date'   => $endDate
        ]);
        return $stmt->fetchAll();
    }

    /**
     * Mengambil menu terlaris (Top Selling Items) untuk visualisasi Chart.js.
     */
    public function getTopSellingMenu(int $limit = 5): array
    {
        $sql = "SELECT m.name, SUM(oi.quantity) AS total_sold, SUM(oi.subtotal) AS total_sales
                FROM order_items oi
                JOIN menu_items m ON oi.menu_item_id = m.id
                JOIN orders o ON oi.order_id = o.id
                WHERE o.payment_status = 'Lunas'
                GROUP BY m.id, m.name
                ORDER BY total_sold DESC
                LIMIT :limit";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Mengambil tren penjualan harian selama 7 hari terakhir.
     */
    public function getDailySalesTrend(): array
    {
        $sql = "SELECT DATE(order_date) AS date, COUNT(*) AS order_count, SUM(grand_total) AS total_revenue
                FROM orders
                WHERE payment_status = 'Lunas' AND order_date >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
                GROUP BY DATE(order_date)
                ORDER BY DATE(order_date) ASC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }
}
