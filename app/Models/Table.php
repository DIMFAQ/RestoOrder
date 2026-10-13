<?php
/**
 * File: app/Models/Table.php
 * Unit Kompetensi:
 * - J.620100.018.02 (OOP - Class, Property, Encapsulation & Inheritance)
 * - J.620100.021.02 (Akses Basis Data)
 */

namespace App\Models;

use App\Core\Model;
use PDO;

class Table extends Model
{
    protected string $table = 'tables';

    /**
     * Mengambil daftar meja yang sedang tersedia (Tersedia).
     *
     * @return array
     */
    public function getAvailableTables(): array
    {
        $sql = "SELECT * FROM {$this->table} WHERE status = 'Tersedia' ORDER BY table_number ASC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }

    /**
     * Memperbarui status meja (Tersedia / Dipesan / Terisi).
     *
     * @param int $tableId
     * @param string $newStatus
     * @return bool
     */
    public function updateStatus(int $tableId, string $newStatus): bool
    {
        $allowedStatuses = ['Tersedia', 'Dipesan', 'Terisi'];
        if (!in_array($newStatus, $allowedStatuses)) {
            throw new \InvalidArgumentException("Status meja tidak valid: {$newStatus}");
        }

        $sql = "UPDATE {$this->table} SET status = :status WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':status' => $newStatus,
            ':id'     => $tableId
        ]);
    }

    /**
     * Menambahkan meja baru.
     *
     * @param string $tableNumber
     * @param int $capacity
     * @param string $location
     * @return int ID meja yang baru dibuat
     */
    public function create(string $tableNumber, int $capacity, string $location = 'Indoor'): int
    {
        $sql = "INSERT INTO {$this->table} (table_number, capacity, location, status) 
                VALUES (:table_number, :capacity, :location, 'Tersedia')";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':table_number' => $tableNumber,
            ':capacity'     => $capacity,
            ':location'     => $location
        ]);
        return (int)$this->db->lastInsertId();
    }
}
