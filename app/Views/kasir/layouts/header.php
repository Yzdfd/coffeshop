<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Kasir' ?> - Café System</title>
    <link href="<?= base_url('css/bootstrap.min.css') ?>" rel="stylesheet">
    <link href="<?= base_url('css/bootstrap-icons.min.css') ?>" rel="stylesheet">
    <link href="<?= base_url('css/kasir.css') ?>" rel="stylesheet">
</head>
<body>
<div class="d-flex" id="wrapper">

    <!-- SIDEBAR -->
    <div class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <span class="fs-4">☕</span>
            <span class="fw-bold ms-2">Kasir</span>
        </div>
        <nav class="sidebar-nav">
            <p class="nav-label">Pesanan</p>
            <a href="<?= base_url('kasir/dashboard') ?>"
               class="nav-item <?= strpos(current_url(), 'kasir/dashboard') !== false ? 'active' : '' ?>">
                <i class="bi bi-speedometer2 me-2"></i> Dashboard
            </a>
            <a href="<?= base_url('kasir/pesanan/buat') ?>"
               class="nav-item <?= strpos(current_url(), 'kasir/pesanan/buat') !== false ? 'active' : '' ?>">
                <i class="bi bi-plus-circle me-2"></i> Buat Pesanan
            </a>
            <a href="<?= base_url('kasir/pesanan') ?>"
               class="nav-item <?= (strpos(current_url(), 'kasir/pesanan') !== false && strpos(current_url(), 'buat') === false) ? 'active' : '' ?>">
                <i class="bi bi-list-check me-2"></i> Daftar Pesanan
            </a>

            <p class="nav-label">Keuangan</p>
            <a href="<?= base_url('kasir/pembayaran') ?>"
               class="nav-item <?= strpos(current_url(), 'kasir/pembayaran') !== false ? 'active' : '' ?>">
                <i class="bi bi-cash-coin me-2"></i> Pembayaran
            </a>
            <a href="<?= base_url('kasir/transaksi') ?>"
               class="nav-item <?= strpos(current_url(), 'kasir/transaksi') !== false ? 'active' : '' ?>">
                <i class="bi bi-receipt me-2"></i> Transaksi Harian
            </a>

            <p class="nav-label">Akun</p>
            <a href="<?= base_url('logout') ?>" class="nav-item nav-logout">
                <i class="bi bi-box-arrow-left me-2"></i> Logout
            </a>
        </nav>
    </div>

    <!-- MAIN -->
    <div class="flex-grow-1 d-flex flex-column" id="main-content">
        <nav class="navbar navbar-light bg-white border-bottom px-4 py-2 shadow-sm">
            <span class="navbar-brand fw-semibold text-dark mb-0 fs-6"><?= $title ?? 'Kasir' ?></span>
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-light text-dark border">
                    <i class="bi bi-person-circle me-1"></i> <?= session('name') ?? 'Kasir' ?>
                </span>
            </div>
        </nav>
        <div class="p-4">
