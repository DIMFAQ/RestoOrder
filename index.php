<?php
/**
 * File: index.php
 * RestoOrder Pro - Sistem Manajemen Pemesanan Restoran Modern
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 1. PSR-4 Compatible Autoloader Murni (Tanpa Composer)
spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    $baseDir = __DIR__ . '/app/';

    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }

    $relativeClass = substr($class, $len);
    $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';

    if (file_exists($file)) {
        require_once $file;
    }
});

// 2. Inisialisasi Error Handler & Activity Logger Global
\App\Core\ErrorHandler::register();

// Catat log aktivitas request HTTP ke logs/app.log
$reqUri = $_SERVER['REQUEST_URI'] ?? '/index.php';
$reqMethod = $_SERVER['REQUEST_METHOD'] ?? 'GET';
\App\Core\ErrorHandler::logInfo("HTTP {$reqMethod} - {$reqUri}");

// Inisialisasi Model
$tableModel       = new \App\Models\Table();
$customerModel    = new \App\Models\Customer();
$menuModel        = new \App\Models\MenuItem();
$reservationModel = new \App\Models\Reservation();
$orderModel       = new \App\Models\Order();
$reportModel      = new \App\Models\Report();

// Router Sederhana
$currentPage = $_GET['page'] ?? 'dashboard';
$action      = $_GET['action'] ?? null;

// Routing Request
try {
    switch ($currentPage) {
        case 'dashboard':
            $stats               = $reportModel->getSummaryStats();
            $salesTrend          = $reportModel->getDailySalesTrend();
            $topMenu             = $reportModel->getTopSellingMenu(5);
            $recentReservations  = array_slice($reservationModel->getAllWithDetails(), 0, 5);
            $tables              = $tableModel->all('table_number', 'ASC');
            require __DIR__ . '/views/dashboard.php';
            break;

        case 'tables':
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'create') {
                $num = \App\Core\Validator::sanitize($_POST['table_number'] ?? '');
                $cap = (int)($_POST['capacity'] ?? 4);
                $loc = \App\Core\Validator::sanitize($_POST['location'] ?? 'Indoor');

                $validator = new \App\Core\Validator();
                $validator->required('Nomor Meja', $num)->minNumeric('Kapasitas', $cap, 1);

                if ($validator->isValid()) {
                    $tableModel->create($num, $cap, $loc);
                    \App\Core\ErrorHandler::logInfo("Meja baru ditambahkan: {$num} (Kapasitas: {$cap}, Lokasi: {$loc})");
                    $_SESSION['flash_success'] = "Meja {$num} berhasil ditambahkan!";
                } else {
                    $_SESSION['flash_error'] = $validator->getFirstError();
                }
                header('Location: index.php?page=tables');
                exit;
            }

            if ($action === 'update_status' && isset($_GET['id'], $_GET['status'])) {
                $tableModel->updateStatus((int)$_GET['id'], $_GET['status']);
                \App\Core\ErrorHandler::logInfo("Status meja ID {$_GET['id']} diubah menjadi '{$_GET['status']}'");
                $_SESSION['flash_success'] = "Status meja berhasil diperbarui!";
                header('Location: index.php?page=tables');
                exit;
            }

            if ($action === 'delete' && isset($_GET['id'])) {
                $tableModel->delete((int)$_GET['id']);
                \App\Core\ErrorHandler::logInfo("Meja ID {$_GET['id']} dihapus dari sistem.");
                $_SESSION['flash_success'] = "Meja berhasil dihapus!";
                header('Location: index.php?page=tables');
                exit;
            }

            $tables = $tableModel->all('table_number', 'ASC');
            require __DIR__ . '/views/tables.php';
            break;

        case 'reservations':
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'create') {
                $custName  = \App\Core\Validator::sanitize($_POST['customer_name'] ?? '');
                $custPhone = \App\Core\Validator::sanitize($_POST['customer_phone'] ?? '');
                $custEmail = \App\Core\Validator::sanitize($_POST['customer_email'] ?? '');
                $tableId   = (int)($_POST['table_id'] ?? 0);
                $guestCnt  = (int)($_POST['guest_count'] ?? 1);
                $resDate   = $_POST['reservation_date'] ?? date('Y-m-d');
                $resTime   = $_POST['reservation_time'] ?? '19:00';
                $notes     = \App\Core\Validator::sanitize($_POST['notes'] ?? '');

                $validator = new \App\Core\Validator();
                $validator->required('Nama Pelanggan', $custName)
                          ->required('No. HP', $custPhone)
                          ->phone('No. HP', $custPhone)
                          ->required('Meja', $tableId)
                          ->date('Tanggal', $resDate);

                if ($validator->isValid()) {
                    $customerId = $customerModel->findOrCreate($custName, $custPhone, $custEmail ?: null);
                    $code = $reservationModel->createReservation($customerId, $tableId, $resDate, $resTime, $guestCnt, $notes);
                    \App\Core\ErrorHandler::logInfo("Reservasi baru dibuat: {$code} untuk pelanggan {$custName}");
                    $_SESSION['flash_success'] = "Reservasi berhasil dibuat dengan kode: {$code}";
                } else {
                    $_SESSION['flash_error'] = $validator->getFirstError();
                }
                header('Location: index.php?page=reservations');
                exit;
            }

            if ($action === 'update_status' && isset($_GET['id'], $_GET['status'])) {
                $reservationModel->updateStatus((int)$_GET['id'], $_GET['status']);
                \App\Core\ErrorHandler::logInfo("Status reservasi ID {$_GET['id']} diubah menjadi '{$_GET['status']}'");
                $_SESSION['flash_success'] = "Status reservasi berhasil diperbarui!";
                header('Location: index.php?page=reservations');
                exit;
            }

            $filterDate = $_GET['filter_date'] ?? null;
            $reservations = $reservationModel->getAllWithDetails($filterDate);
            $availableTables = $tableModel->getAvailableTables();
            require __DIR__ . '/views/reservations.php';
            break;

        case 'orders':
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($action === 'create' || $action === 'save')) {
                $custName  = \App\Core\Validator::sanitize($_POST['customer_name'] ?? '');
                $custPhone = \App\Core\Validator::sanitize($_POST['customer_phone'] ?? '');
                $tableId   = (int)($_POST['table_id'] ?? 0);
                $resId     = !empty($_POST['reservation_id']) ? (int)$_POST['reservation_id'] : null;
                $payStatus = $_POST['payment_status'] ?? 'Belum Bayar';
                $payMethod = $_POST['payment_method'] ?? 'Tunai';
                $discount  = (float)($_POST['discount_amount'] ?? 0);
                $cashReceived = (float)($_POST['cash_received'] ?? 0);
                $changeAmount = (float)($_POST['change_amount'] ?? 0);

                $menuItemIds = $_POST['menu_items'] ?? [];
                $quantities  = $_POST['quantities'] ?? [];

                $validator = new \App\Core\Validator();
                $validator->required('Nama Pelanggan', $custName)
                          ->required('No. Telepon', $custPhone)
                          ->required('Meja', $tableId);

                if (empty($menuItemIds) || count($menuItemIds) === 0) {
                    $validator->required('Menu Pesanan', '', 'Minimal pilih satu menu makanan/minuman.');
                }

                if ($validator->isValid()) {
                    $customerId = $customerModel->findOrCreate($custName, $custPhone);

                    $order = new \App\Models\Order();
                    $order->setDiscount($discount);

                    // Tambahkan setiap item ke objek Order
                    for ($i = 0; $i < count($menuItemIds); $i++) {
                        $mId = (int)$menuItemIds[$i];
                        $qty = (int)($quantities[$i] ?? 1);
                        if ($mId > 0 && $qty > 0) {
                            $itemData = $menuModel->find($mId);
                            if ($itemData) {
                                $order->addItem($mId, $itemData['name'], $qty, (float)$itemData['price']);
                            }
                        }
                    }

                    // Hitung kembalian jika pembayaran tunai
                    if ($payMethod === 'Tunai' && $cashReceived > 0) {
                        $order->setCashPayment($cashReceived);
                        $changeAmount = $order->getChangeAmount();
                    }

                    $orderCode = $order->saveOrder($customerId, $tableId, $resId, $payStatus, $payMethod, $cashReceived, $changeAmount);
                    $grandTotal = $order->calculateGrandTotal();

                    $logMsg = "Transaksi pesanan baru dicatat: {$orderCode} | Total: Rp " . number_format($grandTotal);
                    if ($payMethod === 'Tunai' && $cashReceived > 0) {
                        $logMsg .= " | Bayar: Rp " . number_format($cashReceived) . " | Kembali: Rp " . number_format($changeAmount);
                    }
                    \App\Core\ErrorHandler::logInfo($logMsg);

                    $flashMsg = "Pesanan berhasil disimpan dengan No. Order: {$orderCode}.";
                    if ($payMethod === 'Tunai' && $cashReceived > 0) {
                        $flashMsg .= " Uang: Rp " . number_format($cashReceived, 0, ',', '.') . " | Kembalian: Rp " . number_format($changeAmount, 0, ',', '.');
                    }
                    $_SESSION['flash_success'] = $flashMsg;
                } else {
                    $_SESSION['flash_error'] = $validator->getFirstError();
                }
                header('Location: index.php?page=orders');
                exit;
            }

            if ($action === 'pay' && isset($_GET['id'])) {
                $orderModel->markAsPaid((int)$_GET['id'], 'Tunai');
                \App\Core\ErrorHandler::logInfo("Pembayaran dilunasi untuk Order ID {$_GET['id']}");
                $_SESSION['flash_success'] = "Pesanan berhasil dilunasi!";
                header('Location: index.php?page=orders');
                exit;
            }

            if ($action === 'update_status' && isset($_GET['id'], $_GET['status'])) {
                $targetStatus = $_GET['status'];
                if (in_array($targetStatus, ['Lunas', 'Belum Bayar'])) {
                    $orderModel->updatePaymentStatus((int)$_GET['id'], $targetStatus);
                    \App\Core\ErrorHandler::logInfo("Status bayar Order ID {$_GET['id']} diubah menjadi '{$targetStatus}'");
                    $_SESSION['flash_success'] = "Status pembayaran berhasil diubah menjadi: {$targetStatus}";
                }
                header('Location: index.php?page=orders');
                exit;
            }

            if ($action === 'delete' && isset($_GET['id'])) {
                $orderModel->delete((int)$_GET['id']);
                \App\Core\ErrorHandler::logInfo("Pesanan ID {$_GET['id']} dihapus/dibatalkan.");
                $_SESSION['flash_success'] = "Data pesanan berhasil dihapus.";
                header('Location: index.php?page=orders');
                exit;
            }

            $tables       = $tableModel->all('table_number', 'ASC');
            $reservations = $reservationModel->getAllWithDetails();
            $menuItems    = $menuModel->getActiveMenu();

            // Jika membuka form pembuatan pesanan baru (Layar Penuh POS, Bukan Modal Pop-up)
            if ($action === 'new' || $action === 'create') {
                require __DIR__ . '/views/order_create.php';
                break;
            }

            $orders = $orderModel->getAllOrders();
            require __DIR__ . '/views/orders.php';
            break;

        case 'receipt':
            $orderId = (int)($_GET['id'] ?? 0);
            $order = $orderModel->getOrderDetails($orderId);
            if (!$order) {
                $_SESSION['flash_error'] = "Data pesanan tidak ditemukan.";
                header('Location: index.php?page=orders');
                exit;
            }
            require __DIR__ . '/views/receipt.php';
            break;

        case 'menu':
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'create') {
                $name     = \App\Core\Validator::sanitize($_POST['name'] ?? '');
                $category = $_POST['category'] ?? 'Makanan';
                $price    = (float)($_POST['price'] ?? 0);
                $desc     = \App\Core\Validator::sanitize($_POST['description'] ?? '');

                $validator = new \App\Core\Validator();
                $validator->required('Nama Menu', $name)->minNumeric('Harga', $price, 500);

                if ($validator->isValid()) {
                    $menuModel->create($name, $category, $price, $desc);
                    \App\Core\ErrorHandler::logInfo("Menu baru ditambahkan: {$name} ({$category}) - Rp " . number_format($price));
                    $_SESSION['flash_success'] = "Menu {$name} berhasil ditambahkan!";
                } else {
                    $_SESSION['flash_error'] = $validator->getFirstError();
                }
                header('Location: index.php?page=menu');
                exit;
            }

            if ($action === 'delete' && isset($_GET['id'])) {
                $menuModel->delete((int)$_GET['id']);
                \App\Core\ErrorHandler::logInfo("Menu ID {$_GET['id']} dihapus.");
                $_SESSION['flash_success'] = "Menu berhasil dihapus!";
                header('Location: index.php?page=menu');
                exit;
            }

            $selectedCategory = $_GET['category'] ?? null;
            $menuItems = $menuModel->getActiveMenu($selectedCategory);
            require __DIR__ . '/views/menu.php';
            break;

        case 'reports':
            $startDate = $_GET['start_date'] ?? date('Y-m-01');
            $endDate   = $_GET['end_date'] ?? date('Y-m-d');
            $reportData = $reportModel->getOrderReport($startDate, $endDate);

            // Ringkasan data laporan terstruktur
            $totalRev = 0.0;
            foreach ($reportData as $row) {
                if ($row['payment_status'] === 'Lunas') {
                    $totalRev += (float)$row['grand_total'];
                }
            }
            $count = count($reportData);
            $avg   = $count > 0 ? ($totalRev / $count) : 0;

            $reportSummary = [
                'total'   => $totalRev,
                'count'   => $count,
                'average' => $avg,
            ];

            require __DIR__ . '/views/reports.php';
            break;

        case 'logs':
            if ($action === 'test_error') {
                \App\Core\ErrorHandler::logWarning("Tes simulasi warning: Verifikasi sistem monitoring pada " . date('Y-m-d H:i:s'));
                $_SESSION['flash_success'] = "Simulasi log peringatan berhasil dicatat ke logs/app.log!";
                header('Location: index.php?page=logs');
                exit;
            }

            if ($action === 'clear') {
                @file_put_contents(__DIR__ . '/logs/app.log', '');
                \App\Core\ErrorHandler::logInfo("File log sistem telah dibersihkan oleh pengguna.");
                $_SESSION['flash_success'] = "File log berhasil dibersihkan!";
                header('Location: index.php?page=logs');
                exit;
            }

            $logs = \App\Core\ErrorHandler::getRecentLogs(60);
            require __DIR__ . '/views/logs.php';
            break;

        default:
            header('Location: index.php?page=dashboard');
            exit;
    }
} catch (\Throwable $e) {
    \App\Core\ErrorHandler::handleException($e);
}
