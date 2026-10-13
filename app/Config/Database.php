<?php
/**
 * File: app/Config/Database.php
 * Unit Kompetensi: 
 * - J.620100.016.01 (Guidelines & Best Practices)
 * - J.620100.018.02 (Pemrograman Berorientasi Objek - Singleton Pattern)
 * - J.620100.021.02 (Menerapkan Akses Basis Data)
 */

namespace App\Config;

use PDO;
use PDOException;
use App\Core\ErrorHandler;

class Database
{
    /** @var Database|null Instance singleton tunggal */
    private static ?Database $instance = null;

    /** @var PDO Koneksi PDO ke basis data */
    private PDO $connection;

    /**
     * Konstruktor private untuk mencegah instansiasi langsung dari luar (Singleton).
     */
    private function __construct()
    {
        $host     = '127.0.0.1';
        $port     = '3306';
        $dbName   = 'db_restoran';
        $username = 'root';
        $password = '';

        $dsn = "mysql:host={$host};port={$port};dbname={$dbName};charset=utf8mb4";

        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        try {
            $this->connection = new PDO($dsn, $username, $password, $options);
        } catch (PDOException $e) {
            ErrorHandler::logError("Koneksi Database Gagal: " . $e->getMessage());
            die("Koneksi ke database gagal. Pastikan MySQL sudah aktif dan jalankan setup_db.php terlebih dahulu.");
        }
    }

    /**
     * Mengambil instance tunggal koneksi Database.
     *
     * @return Database
     */
    public static function getInstance(): Database
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Mendapatkan object PDO connection aktif.
     *
     * @return PDO
     */
    public function getConnection(): PDO
    {
        return $this->connection;
    }

    /**
     * Mencegah clone objek (Singleton protection).
     */
    private function __clone() {}

    /**
     * Mencegah unserialize objek (Singleton protection).
     */
    public function __wakeup()
    {
        throw new \Exception("Cannot unserialize singleton");
    }
}
