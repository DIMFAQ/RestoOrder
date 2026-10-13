-- Skema Database Sistem Pemesanan Restoran
-- Standar Uji Kompetensi BNSP / LSP
-- Unit: J.620100.021.02 (Menerapkan Akses Basis Data)

CREATE DATABASE IF NOT EXISTS `db_restoran` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `db_restoran`;

SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS `order_items`;
DROP TABLE IF EXISTS `orders`;
DROP TABLE IF EXISTS `reservations`;
DROP TABLE IF EXISTS `menu_items`;
DROP TABLE IF EXISTS `customers`;
DROP TABLE IF EXISTS `tables`;
SET FOREIGN_KEY_CHECKS = 1;

-- 1. Tabel Meja Restoran (Tables)
CREATE TABLE IF NOT EXISTS `tables` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `table_number` VARCHAR(10) NOT NULL UNIQUE,
    `capacity` INT NOT NULL DEFAULT 4,
    `location` VARCHAR(50) DEFAULT 'Indoor',
    `status` ENUM('Tersedia', 'Dipesan', 'Terisi') NOT NULL DEFAULT 'Tersedia',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- 2. Tabel Pelanggan (Customers)
CREATE TABLE IF NOT EXISTS `customers` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(100) NOT NULL,
    `phone` VARCHAR(20) NULL DEFAULT '-',
    `email` VARCHAR(100) NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- 3. Tabel Kategori Menu & Menu Restoran (Menu Items)
CREATE TABLE IF NOT EXISTS `menu_items` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(100) NOT NULL,
    `category` ENUM('Makanan', 'Minuman', 'Dessert', 'Paket') NOT NULL,
    `price` DECIMAL(12,2) NOT NULL,
    `description` TEXT NULL,
    `is_available` TINYINT(1) NOT NULL DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- 4. Tabel Reservasi Meja (Reservations)
CREATE TABLE IF NOT EXISTS `reservations` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `customer_id` INT NOT NULL,
    `table_id` INT NOT NULL,
    `reservation_code` VARCHAR(20) NOT NULL UNIQUE,
    `reservation_date` DATE NOT NULL,
    `reservation_time` TIME NOT NULL,
    `guest_count` INT NOT NULL DEFAULT 1,
    `status` ENUM('Pending', 'Confirmed', 'Seated', 'Completed', 'Cancelled') NOT NULL DEFAULT 'Confirmed',
    `notes` TEXT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `fk_res_customer` FOREIGN KEY (`customer_id`) REFERENCES `customers`(`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_res_table` FOREIGN KEY (`table_id`) REFERENCES `tables`(`id`) ON DELETE RESTRICT
) ENGINE=InnoDB;

-- 5. Tabel Pesanan Pelanggan (Orders)
CREATE TABLE IF NOT EXISTS `orders` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `order_code` VARCHAR(20) NOT NULL UNIQUE,
    `reservation_id` INT NULL,
    `customer_id` INT NOT NULL,
    `table_id` INT NOT NULL,
    `total_amount` DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    `tax_amount` DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    `discount_amount` DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    `grand_total` DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    `cash_received` DECIMAL(12,2) NULL DEFAULT 0.00,
    `change_amount` DECIMAL(12,2) NULL DEFAULT 0.00,
    `payment_status` ENUM('Belum Bayar', 'Lunas') NOT NULL DEFAULT 'Belum Bayar',
    `payment_method` ENUM('Tunai', 'Transfer', 'QRIS', 'Kartu') NULL,
    `order_date` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `fk_order_res` FOREIGN KEY (`reservation_id`) REFERENCES `reservations`(`id`) ON DELETE SET NULL,
    CONSTRAINT `fk_order_customer` FOREIGN KEY (`customer_id`) REFERENCES `customers`(`id`) ON DELETE RESTRICT,
    CONSTRAINT `fk_order_table` FOREIGN KEY (`table_id`) REFERENCES `tables`(`id`) ON DELETE RESTRICT
) ENGINE=InnoDB;

-- 6. Tabel Detail Item Pesanan (Order Items)
CREATE TABLE IF NOT EXISTS `order_items` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `order_id` INT NOT NULL,
    `menu_item_id` INT NOT NULL,
    `quantity` INT NOT NULL DEFAULT 1,
    `unit_price` DECIMAL(12,2) NOT NULL,
    `subtotal` DECIMAL(12,2) NOT NULL,
    CONSTRAINT `fk_item_order` FOREIGN KEY (`order_id`) REFERENCES `orders`(`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_item_menu` FOREIGN KEY (`menu_item_id`) REFERENCES `menu_items`(`id`) ON DELETE RESTRICT
) ENGINE=InnoDB;
