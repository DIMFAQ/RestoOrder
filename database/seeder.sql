-- Data Awal (Dummy / Seed Data) Sistem Restoran
USE `db_restoran`;

-- Data Meja
INSERT INTO `tables` (`table_number`, `capacity`, `location`, `status`) VALUES
('T-01', 2, 'Indoor - VIP', 'Tersedia'),
('T-02', 4, 'Indoor - Regular', 'Dipesan'),
('T-03', 4, 'Indoor - Regular', 'Tersedia'),
('T-04', 6, 'Outdoor - Garden', 'Terisi'),
('T-05', 8, 'Indoor - Family', 'Tersedia'),
('T-06', 2, 'Outdoor - Terrace', 'Tersedia'),
('T-07', 4, 'Indoor - Regular', 'Tersedia'),
('T-08', 10, 'Indoor - Meeting Room', 'Tersedia')
ON DUPLICATE KEY UPDATE `status` = VALUES(`status`);

-- Data Menu Makanan & Minuman
INSERT INTO `menu_items` (`name`, `category`, `price`, `description`, `is_available`) VALUES
('Nasi Goreng Spesial Resto', 'Makanan', 35000.00, 'Nasi goreng dengan udang, telur mata sapi, dan sate ayam', 1),
('Ayam Bakar Madu', 'Makanan', 42000.00, 'Ayam bakar dengan bumbu rempah madu istimewa dan sambal terasi', 1),
('Bebek Goreng Crispy', 'Makanan', 48000.00, 'Bebek renyah gurih disajikan dengan lalapan dan sambal korek', 1),
('Mie Goreng Seafood', 'Makanan', 38000.00, 'Mie kenyal dengan cumi, udang, dan sayuran segar', 1),
('Sup Buntut Sapi', 'Makanan', 65000.00, 'Sup buntut sapi empuk berkuah gurih kaya rempah nusantara', 1),
('Es Teh Manis Melati', 'Minuman', 8000.00, 'Teh melati wangi dengan es segar', 1),
('Es Jeruk Peras Segar', 'Minuman', 15000.00, 'Jeruk murni peras dengan gula alami', 1),
('Jus Alpukat Kocok', 'Minuman', 22000.00, 'Jus alpukat lembut dengan susu kental manis cokelat', 1),
('Kopi Susu Gula Aren', 'Minuman', 20000.00, 'Espresso robusta & arabica dengan susu segar dan aren', 1),
('Pisang Bakar Keju Cokelat', 'Dessert', 25000.00, 'Pisang raja bakar topping parutan keju edam dan meses', 1),
('Pancake Es Krim Vanilla', 'Dessert', 28000.00, 'Pancake fluffy disajikan dengan sirup mapel dan gelato vanilla', 1),
('Paket Hemat Keluarga (4 Orang)', 'Paket', 165000.00, '1 Sup Buntut, 2 Ayam Bakar, 1 Mie Goreng, 4 Nasi, 4 Es Teh', 1);

-- Data Pelanggan Contoh
INSERT INTO `customers` (`id`, `name`, `phone`, `email`) VALUES
(1, 'Dimas Arya', '081234567890', 'dimas@example.com'),
(2, 'Siti Nurhaliza', '085711223344', 'siti@example.com'),
(3, 'Budi Santoso', '081988776655', 'budi@example.com'),
(4, 'Anita Rahma', '082155667788', 'anita@example.com')
ON DUPLICATE KEY UPDATE `name` = VALUES(`name`);

-- Data Reservasi Contoh
INSERT INTO `reservations` (`id`, `customer_id`, `table_id`, `reservation_code`, `reservation_date`, `reservation_time`, `guest_count`, `status`, `notes`) VALUES
(1, 1, 2, 'RSV-20261012-001', '2026-10-12', '19:00:00', 4, 'Confirmed', 'Ulang tahun, mohon meja dekat jendela'),
(2, 2, 4, 'RSV-20261012-002', '2026-10-12', '13:00:00', 5, 'Seated', 'Makan siang kantor')
ON DUPLICATE KEY UPDATE `reservation_code` = VALUES(`reservation_code`);

-- Data Pesanan Contoh
INSERT INTO `orders` (`id`, `order_code`, `reservation_id`, `customer_id`, `table_id`, `total_amount`, `tax_amount`, `discount_amount`, `grand_total`, `payment_status`, `payment_method`) VALUES
(1, 'ORD-20261012-001', 2, 2, 4, 182000.00, 18200.00, 0.00, 200200.00, 'Lunas', 'QRIS')
ON DUPLICATE KEY UPDATE `order_code` = VALUES(`order_code`);

-- Detail Item Pesanan Contoh
INSERT INTO `order_items` (`order_id`, `menu_item_id`, `quantity`, `unit_price`, `subtotal`) VALUES
(1, 1, 2, 35000.00, 70000.00),
(1, 2, 2, 42000.00, 84000.00),
(1, 6, 2, 8000.00, 16000.00),
(1, 7, 1, 12000.00, 12000.00);
