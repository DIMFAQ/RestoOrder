<?php
/**
 * File: app/Models/MenuItem.php
 * Unit Kompetensi:
 * - J.620100.018.02 (OOP - Model Menu)
 * - J.620100.021.02 (Akses Basis Data)
 */

namespace App\Models;

use App\Core\Model;
use PDO;

class MenuItem extends Model
{
    protected string $table = 'menu_items';

    /**
     * Mengambil seluruh menu yang berstatus tersedia (is_available = 1).
     *
     * @param string|null $category Filter berdasarkan kategori jika ada
     * @return array
     */
    public function getActiveMenu(?string $category = null): array
    {
        if ($category) {
            $sql = "SELECT * FROM {$this->table} WHERE is_available = 1 AND category = :category ORDER BY name ASC";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':category' => $category]);
        } else {
            $sql = "SELECT * FROM {$this->table} WHERE is_available = 1 ORDER BY category ASC, name ASC";
            $stmt = $this->db->query($sql);
        }
        return $stmt->fetchAll();
    }

    /**
     * Menambahkan item menu baru.
     */
    public function create(string $name, string $category, float $price, ?string $description = null): int
    {
        $sql = "INSERT INTO {$this->table} (name, category, price, description, is_available) 
                VALUES (:name, :category, :price, :description, 1)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':name'        => $name,
            ':category'    => $category,
            ':price'       => $price,
            ':description' => $description
        ]);
        return (int)$this->db->lastInsertId();
    }

    /**
     * Memperbarui data item menu.
     */
    public function update(int $id, string $name, string $category, float $price, ?string $description = null, int $isAvailable = 1): bool
    {
        $sql = "UPDATE {$this->table} 
                SET name = :name, category = :category, price = :price, description = :description, is_available = :is_available
                WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':name'         => $name,
            ':category'     => $category,
            ':price'        => $price,
            ':description'  => $description,
            ':is_available' => $isAvailable,
            ':id'           => $id
        ]);
    }
}
