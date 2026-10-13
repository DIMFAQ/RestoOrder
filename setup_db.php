<?php
/**
 * Script Inisialisasi Database Otomatis
 * Unit Kompetensi: J.620100.021.02 (Menerapkan Akses Basis Data)
 */

$host = '127.0.0.1';
$user = 'root';
$pass = '';
$dbName = 'db_restoran';

header('Content-Type: text/plain; charset=utf-8');

try {
    echo "========================================================\n";
    echo "  INISIALISASI BASIS DATA SISTEM PEMESANAN RESTORAN\n";
    echo "========================================================\n";

    // 1. Koneksi awal ke server MySQL (tanpa memilih database terlebih dahulu)
    $pdo = new PDO("mysql:host={$host};charset=utf8mb4", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
    echo "[✓] Berhasil terhubung ke server MySQL ({$host})\n";

    // 2. Eksekusi skema (CREATE DATABASE & TABLES)
    $schemaFile = __DIR__ . '/database/schema.sql';
    if (!file_exists($schemaFile)) {
        throw new Exception("File schema.sql tidak ditemukan pada: {$schemaFile}");
    }

    $schemaSql = file_get_contents($schemaFile);
    $pdo->exec($schemaSql);
    echo "[✓] Skema database dan tabel-tabel berhasil dibuat/diperbarui!\n";

    // 3. Eksekusi data seeder
    $seederFile = __DIR__ . '/database/seeder.sql';
    if (file_exists($seederFile)) {
        $seederSql = file_get_contents($seederFile);
        $pdo->exec($seederSql);
        echo "[✓] Data awal (Dummy Seeder) berhasil dimasukkan!\n";
    }

    echo "========================================================\n";
    echo "STATUS: SUKSES! Basis data '{$dbName}' siap digunakan.\n";
    echo "Buka aplikasi pada browser: http://localhost:8000\n";
    echo "========================================================\n";

} catch (PDOException $e) {
    echo "[ERROR PDO]: " . $e->getMessage() . "\n";
    exit(1);
} catch (Exception $e) {
    echo "[ERROR UMUM]: " . $e->getMessage() . "\n";
    exit(1);
}
