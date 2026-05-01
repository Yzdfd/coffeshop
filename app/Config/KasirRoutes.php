<?php

// ============================================================
// KASIR ROUTES
// Tambahkan ke app/Config/Routes.php
// ============================================================

$routes->group('kasir', ['namespace' => 'App\Controllers\Kasir'], function ($routes) {

    // ─── DASHBOARD ───────────────────────────────────────────
    // URL: /kasir/dashboard
    $routes->get('/',         'Dashboard::index');
    $routes->get('dashboard', 'Dashboard::index');

    // ─── PESANAN ─────────────────────────────────────────────
    // URL: /kasir/pesanan
    $routes->get('pesanan',                       'Pesanan::index');        // Daftar pesanan
    $routes->get('pesanan/buat',                  'Pesanan::buat');         // Form buat pesanan
    $routes->post('pesanan/store',                'Pesanan::store');        // Simpan pesanan baru
    $routes->get('pesanan/detail/(:num)',         'Pesanan::detail/$1');    // Detail pesanan
    $routes->match(['get','post'], 'pesanan/tambah-item/(:num)', 'Pesanan::tambahItem/$1'); // Tambah item
    $routes->get('pesanan/cancel/(:num)',         'Pesanan::cancel/$1');    // Batalkan pesanan

    // ─── PEMBAYARAN ───────────────────────────────────────────
    // URL: /kasir/pembayaran
    $routes->get('pembayaran',                    'Pembayaran::index');     // Daftar siap bayar
    $routes->get('pembayaran/(:num)',             'Pembayaran::form/$1');   // Form pembayaran
    $routes->post('pembayaran/proses/(:num)',     'Pembayaran::proses/$1'); // Proses bayar
    $routes->get('pembayaran/cek-promo',          'Pembayaran::cekPromo'); // Cek promo (AJAX)

    // ─── TRANSAKSI ────────────────────────────────────────────
    // URL: /kasir/transaksi
    $routes->get('transaksi',                     'Transaksi::index');      // Riwayat transaksi harian
    $routes->get('transaksi/struk/(:num)',        'Transaksi::struk/$1');   // Cetak struk
    $routes->get('transaksi/void/(:num)',         'Transaksi::void/$1');    // Void / refund
});
