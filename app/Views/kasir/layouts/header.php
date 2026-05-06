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
            <span class="fw-bold ms-2"><?= esc($sidebarTitle ?? 'Kasir') ?></span>
        </div>
        <nav class="sidebar-nav">
            <?php
                $sidebarSections = $sidebarSections ?? [
                    [
                        'label' => 'Pesanan',
                        'items' => [
                            [
                                'url'    => base_url('kasir/dashboard'),
                                'active' => strpos(current_url(), 'kasir/dashboard') !== false,
                                'icon'   => 'bi bi-speedometer2',
                                'text'   => 'Dashboard',
                            ],
                            [
                                'url'    => base_url('kasir/pesanan/buat'),
                                'active' => strpos(current_url(), 'kasir/pesanan/buat') !== false,
                                'icon'   => 'bi bi-plus-circle',
                                'text'   => 'Buat Pesanan',
                            ],
                            [
                                'url'    => base_url('kasir/pesanan'),
                                'active' => (strpos(current_url(), 'kasir/pesanan') !== false && strpos(current_url(), 'buat') === false),
                                'icon'   => 'bi bi-list-check',
                                'text'   => 'Daftar Pesanan',
                            ],
                        ],
                    ],
                    [
                        'label' => 'Keuangan',
                        'items' => [
                            [
                                'url'    => base_url('kasir/pembayaran'),
                                'active' => strpos(current_url(), 'kasir/pembayaran') !== false,
                                'icon'   => 'bi bi-cash-coin',
                                'text'   => 'Pembayaran',
                            ],
                            [
                                'url'    => base_url('kasir/transaksi'),
                                'active' => strpos(current_url(), 'kasir/transaksi') !== false,
                                'icon'   => 'bi bi-receipt',
                                'text'   => 'Transaksi Harian',
                            ],
                        ],
                    ],
                    [
                        'label' => 'Akun',
                        'items' => [
                            [
                                'url'    => base_url('logout'),
                                'active' => false,
                                'icon'   => 'bi bi-box-arrow-left',
                                'text'   => 'Logout',
                                'class'  => 'nav-logout',
                            ],
                        ],
                    ],
                ];
            ?>

            <?php foreach ($sidebarSections as $section): ?>
                <p class="nav-label"><?= esc($section['label'] ?? '') ?></p>
                <?php foreach (($section['items'] ?? []) as $item): ?>
                    <a href="<?= esc($item['url'] ?? '#') ?>"
                       class="nav-item <?= !empty($item['class']) ? esc($item['class']) : '' ?> <?= !empty($item['active']) ? 'active' : '' ?>">
                        <i class="<?= esc($item['icon'] ?? '') ?> me-2"></i> <?= esc($item['text'] ?? '') ?>
                    </a>
                <?php endforeach; ?>
            <?php endforeach; ?>
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
