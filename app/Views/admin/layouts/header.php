<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Admin Panel' ?> - Café System</title>
    <link rel="stylesheet" href="<?= base_url('css/admin.css') ?>">
</head>
<body>
<div class="wrapper">

    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="sidebar-header">
            <span class="logo-icon">☕</span>
            <span class="logo-text">CaféAdmin</span>
        </div>
        <nav class="sidebar-nav">
            <p class="nav-label">Menu Utama</p>
            <a href="<?= base_url('admin/dashboard') ?>" class="nav-item <?= (current_url() == base_url('admin/dashboard')) ? 'active' : '' ?>">
                <span class="nav-icon">📊</span> Dashboard
            </a>

            <p class="nav-label">Manajemen</p>
            <a href="<?= base_url('admin/menu') ?>" class="nav-item <?= (strpos(current_url(), 'admin/menu') !== false) ? 'active' : '' ?>">
                <span class="nav-icon">🍽️</span> Menu
            </a>
            <a href="<?= base_url('admin/kategori') ?>" class="nav-item <?= (strpos(current_url(), 'admin/kategori') !== false) ? 'active' : '' ?>">
                <span class="nav-icon">🗂️</span> Kategori Menu
            </a>
            <a href="<?= base_url('admin/stok') ?>" class="nav-item <?= (strpos(current_url(), 'admin/stok') !== false) ? 'active' : '' ?>">
                <span class="nav-icon">📦</span> Stok Bahan
            </a>
            <a href="<?= base_url('admin/users') ?>" class="nav-item <?= (strpos(current_url(), 'admin/users') !== false) ? 'active' : '' ?>">
                <span class="nav-icon">👥</span> Kelola User
            </a>

            <p class="nav-label">Sistem</p>
            <a href="<?= base_url('admin/setting') ?>" class="nav-item <?= (strpos(current_url(), 'admin/setting') !== false) ? 'active' : '' ?>">
                <span class="nav-icon">⚙️</span> Setting Sistem
            </a>
            <a href="<?= base_url('logout') ?>" class="nav-item nav-logout">
                <span class="nav-icon">🚪</span> Logout
            </a>
        </nav>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
        <div class="topbar">
            <h1 class="page-title"><?= $title ?? 'Dashboard' ?></h1>
            <div class="topbar-right">
                <span class="admin-badge">👤 <?= esc(session("name") ?? "Admin") ?></span>
                <span style="font-size:.75rem;background:#6F4E37;color:#fff;padding:.2rem .6rem;border-radius:12px;font-weight:600;"><?= esc(ucfirst(session("role") ?? "admin")) ?></span>
                <a href="<?= base_url("logout") ?>" style="margin-left:.5rem;text-decoration:none;font-size:1.1rem;" title="Logout">🚪</a>
            </div>
        </div>
        <div class="content-area">
