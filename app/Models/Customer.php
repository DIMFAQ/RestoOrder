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
     * Cari atau simpan pelanggan baru jika belum terdaftar.
     * Nomor telepon bersifat opsional (jika diisi, dicocokkan berdasarkan telepon; jika kosong, dicocokkan berdasarkan nama).
     *
     * @param string $name
     * @param string|null $phone
     * @param string|null $email
     * @return int ID Pelanggan
     */
    public function findOrCreate(string $name, ?string $phone = null, ?string $email = null): int
    {
        $cleanPhone = (!empty($phone) && trim($phone) !== '-') ? trim($phone) : null;

        // 1. Jika nomor telepon diisi, cari berdasarkan nomor telepon
        if ($cleanPhone !== null) {
            $sql = "SELECT id FROM {$this->table} WHERE phone = :phone LIMIT 1";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':phone' => $cleanPhone]);
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
        } else {
            // 2. Jika nomor telepon tidak diisi, cari berdasarkan nama yang nomor HP-nya juga belum ada/strip
            $sql = "SELECT id FROM {$this->table} WHERE name = :name AND (phone IS NULL OR phone = '' OR phone = '-') LIMIT 1";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':name' => $name]);
            $existing = $stmt->fetch();
            if ($existing) {
                return (int)$existing['id'];
            }
        }

        // 3. Buat pelanggan baru
        $insertSql = "INSERT INTO {$this->table} (name, phone, email) VALUES (:name, :phone, :email)";
        $insertStmt = $this->db->prepare($insertSql);
        $insertStmt->execute([
            ':name'  => $name,
            ':phone' => $cleanPhone ?: '-',
            ':email' => $email
        ]);
        return (int)$this->db->lastInsertId();
    }
}
