<?php
/**
 * File: views/layouts/header.php
 * Template Header & Navigasi Utama RestoOrder Pro
 */
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pageTitle) ? htmlspecialchars($pageTitle) . ' - ' : '' ?>RestoOrder</title>
    
    <!-- Bootstrap 5.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- FontAwesome 6.4 Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    
    <!-- Google Fonts Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    
    <!-- Custom Application CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <!-- Navbar Navigasi Bersih & Elegan -->
    <nav class="navbar navbar-expand-xl navbar-custom sticky-top no-print">
        <div class="container-fluid px-lg-4 px-3">
            <!-- Brand Logo -->
            <a class="navbar-brand d-flex align-items-center gap-2 text-decoration-none" href="index.php?page=dashboard">
                <div class="brand-emblem">
                    <i class="fa-solid fa-utensils"></i>
                </div>
                <div class="brand-text-wrapper">
                    <span class="brand-title">RestoOrder</span>
                </div>
            </a>

            <!-- Mobile Toggle Button -->
            <button class="navbar-toggler border-0 shadow-none px-2" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu">
                <span class="navbar-toggler-icon"></span>
            </button>

            <!-- Navigation Links -->
            <div class="collapse navbar-collapse" id="navMenu">
                <ul class="navbar-nav me-auto mb-2 mb-xl-0 ms-xl-4 align-items-xl-center">
                    <li class="nav-item">
                        <a class="nav-link <?= ($currentPage === 'dashboard') ? 'active' : '' ?>" href="index.php?page=dashboard">
                            <i class="fa-solid fa-chart-pie"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= ($currentPage === 'tables') ? 'active' : '' ?>" href="index.php?page=tables">
                            <i class="fa-solid fa-chair"></i> Denah Meja
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= ($currentPage === 'reservations') ? 'active' : '' ?>" href="index.php?page=reservations">
                            <i class="fa-solid fa-calendar-check"></i> Reservasi
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= ($currentPage === 'orders') ? 'active' : '' ?>" href="index.php?page=orders">
                            <i class="fa-solid fa-cash-register"></i> Kasir & Order
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= ($currentPage === 'menu') ? 'active' : '' ?>" href="index.php?page=menu">
                            <i class="fa-solid fa-bowl-food"></i> Menu Resto
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= ($currentPage === 'reports') ? 'active' : '' ?>" href="index.php?page=reports">
                            <i class="fa-solid fa-file-invoice-dollar"></i> Laporan
                        </a>
                    </li>
                </ul>

                <!-- Right Action Bar -->
                <div class="d-flex flex-wrap align-items-center gap-2 mt-3 mt-xl-0">
                    <!-- Live Status Indicator -->
                    <div class="status-pill d-none d-md-inline-flex">
                        <span class="pulse-dot"></span>
                        <span><?= date('d M Y') ?></span>
                    </div>

                    <!-- Diagnostics & Monitoring -->
                    <a class="btn btn-nav-secondary btn-sm rounded-pill px-3 fw-semibold" href="index.php?page=logs">
                        <i class="fa-solid fa-terminal me-1 opacity-75"></i> Logs
                    </a>

                    <!-- Diagnostic Test Suite -->
                    <a class="btn btn-nav-action btn-sm rounded-pill px-3 fw-semibold" href="tests/run_tests.php" target="_blank">
                        <i class="fa-solid fa-vial-circle-check me-1"></i> Tests
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Container -->
    <main class="container-fluid px-lg-4 px-3 py-4 flex-grow-1">
        <!-- Flash Alert Messages -->
        <?php if (!empty($_SESSION['flash_success'])): ?>
            <div class="alert alert-success alert-dismissible fade show no-print border-0 shadow-sm rounded-4 d-flex align-items-center p-3 mb-4" role="alert">
                <div class="p-2 rounded-circle bg-success text-white me-3 d-flex align-items-center justify-content-center" style="width:32px; height:32px;">
                    <i class="fa-solid fa-check small"></i>
                </div>
                <div class="fw-semibold text-success-emphasis flex-grow-1"><?= htmlspecialchars($_SESSION['flash_success']) ?></div>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php unset($_SESSION['flash_success']); ?>
        <?php endif; ?>

        <?php if (!empty($_SESSION['flash_error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show no-print border-0 shadow-sm rounded-4 d-flex align-items-center p-3 mb-4" role="alert">
                <div class="p-2 rounded-circle bg-danger text-white me-3 d-flex align-items-center justify-content-center" style="width:32px; height:32px;">
                    <i class="fa-solid fa-triangle-exclamation small"></i>
                </div>
                <div class="fw-semibold text-danger-emphasis flex-grow-1"><?= htmlspecialchars($_SESSION['flash_error']) ?></div>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php unset($_SESSION['flash_error']); ?>
        <?php endif; ?>
