<?php
/**
 * File: app/Models/Customer.php
 * Unit Kompetensi:
 * - J.620100.018.02 (OOP - Model Pelanggan)
 * - J.620100.021.02 (Akses Basis Data)
 */

namespace App\Models;

use App\Core\Model;
use PDO;

class Customer extends Model
{
    protected string $table = 'customers';

    /**
     * Cari atau simpan pelanggan baru jika belum terdaftar berdasarkan nomor telepon.
     *
     * @param string $name
     * @param string $phone
     * @param string|null $email
     * @return int ID Pelanggan
     */
    public function findOrCreate(string $name, string $phone, ?string $email = null): int
    {
        // Cari apakah nomor telepon sudah ada
        $sql = "SELECT id FROM {$this->table} WHERE phone = :phone LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':phone' => $phone]);
        $existing = $stmt->fetch();

        if ($existing) {
            // Update nama jika ada perubahan
            $updateSql = "UPDATE {$this->table} SET name = :name, email = :email WHERE id = :id";
            $updateStmt = $this->db->prepare($updateSql);
            $updateStmt->execute([
                ':name'  => $name,
                ':email' => $email,
                ':id'    => $existing['id']
            ]);
            return (int)$existing['id'];
        }

        // Buat baru
        $insertSql = "INSERT INTO {$this->table} (name, phone, email) VALUES (:name, :phone, :email)";
        $insertStmt = $this->db->prepare($insertSql);
        $insertStmt->execute([
            ':name'  => $name,
            ':phone' => $phone,
            ':email' => $email
        ]);
        return (int)$this->db->lastInsertId();
    }
}
