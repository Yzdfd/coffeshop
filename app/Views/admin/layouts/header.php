<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Admin Panel' ?> - Café System</title>
    <!-- Bootstrap 5 -->
    <link href="<?= base_url('css/bootstrap.min.css') ?>" rel="stylesheet">
    <link href="<?= base_url('css/bootstrap-icons.min.css') ?>" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('css/admin.css') ?>">
</head>
<body>

<div class="d-flex" id="wrapper">

    <!-- SIDEBAR -->
    <div class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <span class="fs-4">☕</span>
            <span class="fw-bold ms-2">CaféAdmin</span>
        </div>
        <nav class="sidebar-nav">
            <p class="nav-label">Menu Utama</p>
            <a href="<?= base_url('admin/dashboard') ?>"
               class="nav-item <?= (strpos(current_url(), 'admin/dashboard') !== false) ? 'active' : '' ?>">
                <i class="bi bi-speedometer2 me-2"></i> Dashboard
            </a>

            <p class="nav-label">Manajemen</p>
            <a href="<?= base_url('admin/menu') ?>"
               class="nav-item <?= (strpos(current_url(), 'admin/menu') !== false) ? 'active' : '' ?>">
                <i class="bi bi-egg-fried me-2"></i> Menu
            </a>
            <a href="<?= base_url('admin/kategori') ?>"
               class="nav-item <?= (strpos(current_url(), 'admin/kategori') !== false) ? 'active' : '' ?>">
                <i class="bi bi-tags me-2"></i> Kategori Menu
            </a>
            <a href="<?= base_url('admin/stok') ?>"
               class="nav-item <?= (strpos(current_url(), 'admin/stok') !== false) ? 'active' : '' ?>">
                <i class="bi bi-box-seam me-2"></i> Stok Bahan
            </a>
            <a href="<?= base_url('admin/users') ?>"
               class="nav-item <?= (strpos(current_url(), 'admin/users') !== false) ? 'active' : '' ?>">
                <i class="bi bi-people me-2"></i> Kelola User
            </a>

            <p class="nav-label">Sistem</p>
            <a href="<?= base_url('admin/setting') ?>"
               class="nav-item <?= (strpos(current_url(), 'admin/setting') !== false) ? 'active' : '' ?>">
                <i class="bi bi-gear me-2"></i> Setting Sistem
            </a>
            <a href="<?= base_url('logout') ?>" class="nav-item nav-logout">
                <i class="bi bi-box-arrow-left me-2"></i> Logout
            </a>
        </nav>
    </div>

    <!-- MAIN CONTENT -->
    <div class="flex-grow-1 d-flex flex-column" id="main-content">
        <!-- TOPBAR -->
        <nav class="navbar navbar-light bg-white border-bottom px-4 py-2 shadow-sm">
            <span class="navbar-brand fw-semibold text-dark mb-0 fs-6"><?= $title ?? 'Dashboard' ?></span>
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-light text-dark border">
                    <i class="bi bi-person-circle me-1"></i> Admin
                </span>
            </div>
        </nav>

        <!-- PAGE CONTENT -->
        <div class="p-4">
