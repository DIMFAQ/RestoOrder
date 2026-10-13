<?php
/**
 * File: app/Core/Model.php
 * Unit Kompetensi:
 * - J.620100.018.02 (Pemrograman Berorientasi Objek - Abstract Base Class & Inheritance)
 * - J.620100.021.02 (Menerapkan Akses Basis Data - Prepared Statements & PDO)
 */

namespace App\Core;

use App\Config\Database;
use PDO;

abstract class Model
{
    /** @var PDO Koneksi basis data */
    protected PDO $db;

    /** @var string Nama tabel basis data */
    protected string $table = '';

    /** @var string Primary key tabel */
    protected string $primaryKey = 'id';

    /**
     * Konstruktor menginisialisasi koneksi basis data.
     */
    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Mengambil semua data dari tabel.
     *
     * @param string $orderBy Kolom pengurutan
     * @param string $direction Urutan (ASC / DESC)
     * @return array
     */
    public function all(string $orderBy = 'id', string $direction = 'DESC'): array
    {
        $sql = "SELECT * FROM {$this->table} ORDER BY {$orderBy} {$direction}";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }

    /**
     * Mengambil satu baris data berdasarkan Primary Key.
     *
     * @param int $id
     * @return array|null
     */
    public function find(int $id): ?array
    {
        $sql = "SELECT * FROM {$this->table} WHERE {$this->primaryKey} = :id LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch();
        return $result ?: null;
    }

    /**
     * Menghapus baris data berdasarkan Primary Key.
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        $sql = "DELETE FROM {$this->table} WHERE {$this->primaryKey} = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    /**
     * Menghitung total jumlah baris data pada tabel.
     *
     * @return int
     */
    public function count(): int
    {
        $sql = "SELECT COUNT(*) as total FROM {$this->table}";
        $stmt = $this->db->query($sql);
        $res = $stmt->fetch();
        return (int)($res['total'] ?? 0);
    }
}
