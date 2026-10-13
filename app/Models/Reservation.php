<?php
/**
 * File: app/Models/Reservation.php
 * Unit Kompetensi:
 * - J.620100.018.02 (OOP - Model Reservasi)
 * - J.620100.021.02 (Akses Basis Data dengan Relasi & Transaksi)
 */

namespace App\Models;

use App\Core\Model;
use PDO;

class Reservation extends Model
{
    protected string $table = 'reservations';

    /**
     * Mengambil seluruh data reservasi beserta informasi pelanggan dan meja.
     *
     * @param string|null $filterDate
     * @return array
     */
    public function getAllWithDetails(?string $filterDate = null): array
    {
        $sql = "SELECT r.*, 
                       c.name AS customer_name, c.phone AS customer_phone, c.email AS customer_email,
                       t.table_number, t.capacity AS table_capacity, t.location AS table_location
                FROM {$this->table} r
                JOIN customers c ON r.customer_id = c.id
                JOIN tables t ON r.table_id = t.id";

        if ($filterDate) {
            $sql .= " WHERE r.reservation_date = :fdate";
            $sql .= " ORDER BY r.reservation_date DESC, r.reservation_time ASC";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':fdate' => $filterDate]);
        } else {
            $sql .= " ORDER BY r.reservation_date DESC, r.reservation_time ASC";
            $stmt = $this->db->query($sql);
        }

        return $stmt->fetchAll();
    }

    /**
     * Membuat reservasi meja baru dan memperbarui status meja menjadi 'Dipesan'.
     * Menggunakan Database Transaction untuk integritas data.
     */
    public function createReservation(
        int $customerId,
        int $tableId,
        string $reservationDate,
        string $reservationTime,
        int $guestCount,
        ?string $notes = null
    ): string {
        $reservationCode = 'RSV-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -4));

        try {
            $this->db->beginTransaction();

            // 1. Simpan data reservasi
            $sql = "INSERT INTO {$this->table} 
                    (customer_id, table_id, reservation_code, reservation_date, reservation_time, guest_count, status, notes)
                    VALUES (:customer_id, :table_id, :code, :rdate, :rtime, :guest_count, 'Confirmed', :notes)";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':customer_id' => $customerId,
                ':table_id'    => $tableId,
                ':code'        => $reservationCode,
                ':rdate'       => $reservationDate,
                ':rtime'       => $reservationTime,
                ':guest_count' => $guestCount,
                ':notes'       => $notes
            ]);

            // 2. Ubah status meja menjadi 'Dipesan'
            $tableModel = new Table();
            $tableModel->updateStatus($tableId, 'Dipesan');

            $this->db->commit();
            return $reservationCode;
        } catch (\Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    /**
     * Memperbarui status reservasi (Confirmed, Seated, Completed, Cancelled).
     * Jika Seated -> meja menjadi 'Terisi'
     * Jika Completed atau Cancelled -> meja kembali 'Tersedia'
     */
    public function updateStatus(int $reservationId, string $status): bool
    {
        $res = $this->find($reservationId);
        if (!$res) {
            return false;
        }

        try {
            $this->db->beginTransaction();

            $sql = "UPDATE {$this->table} SET status = :status WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':status' => $status, ':id' => $reservationId]);

            $tableModel = new Table();
            if ($status === 'Seated') {
                $tableModel->updateStatus((int)$res['table_id'], 'Terisi');
            } elseif (in_array($status, ['Completed', 'Cancelled'])) {
                $tableModel->updateStatus((int)$res['table_id'], 'Tersedia');
            }

            $this->db->commit();
            return true;
        } catch (\Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }
}
