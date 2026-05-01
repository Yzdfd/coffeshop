<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');
$routes->group('admin', ['namespace' => 'App\Controllers\Admin'], function ($routes) {

    // ─── DASHBOARD ───────────────────────────────────────────
    // URL: /admin/dashboard
    // Menampilkan ringkasan sistem (total menu, stok rendah, dll)
    $routes->get('dashboard', 'Dashboard::index');
    $routes->get('/', 'Dashboard::index');

    // ─── MENU ────────────────────────────────────────────────
    // URL: /admin/menu
    // CRUD menu café (nama, harga, kategori, gambar, varian)
    $routes->get('menu',                'Menu::index');         // Daftar semua menu
    $routes->get('menu/create',         'Menu::create');        // Form tambah menu
    $routes->post('menu/store',         'Menu::store');         // Simpan menu baru
    $routes->get('menu/edit/(:num)',    'Menu::edit/$1');       // Form edit menu
    $routes->post('menu/update/(:num)', 'Menu::update/$1');     // Simpan perubahan menu
    $routes->get('menu/delete/(:num)',  'Menu::delete/$1');     // Hapus menu

    // ─── KATEGORI ────────────────────────────────────────────
    // URL: /admin/kategori
    // Kelola kategori menu (Minuman Panas, Makanan Berat, dll)
    $routes->get('kategori',                'Kategori::index');         // Daftar + form tambah
    $routes->post('kategori/store',         'Kategori::store');         // Simpan kategori baru
    $routes->get('kategori/edit/(:num)',    'Kategori::edit/$1');       // Form edit kategori
    $routes->post('kategori/update/(:num)', 'Kategori::update/$1');     // Simpan perubahan
    $routes->get('kategori/delete/(:num)',  'Kategori::delete/$1');     // Hapus kategori

    // ─── STOK BAHAN ──────────────────────────────────────────
    // URL: /admin/stok
    // Kelola stok bahan baku (kopi, susu, gula, dll)
    // Notifikasi otomatis kalau stok di bawah minimum
    $routes->get('stok',                'Stok::index');         // Daftar semua bahan
    $routes->get('stok/create',         'Stok::create');        // Form tambah bahan baru
    $routes->post('stok/store',         'Stok::store');         // Simpan bahan baru
    $routes->get('stok/edit/(:num)',    'Stok::edit/$1');       // Form edit bahan
    $routes->post('stok/update/(:num)', 'Stok::update/$1');     // Simpan perubahan bahan
    $routes->match(['get','post'], 'stok/tambah/(:num)', 'Stok::tambah/$1'); // Tambah jumlah stok
    $routes->get('stok/delete/(:num)',  'Stok::delete/$1');     // Hapus bahan

    // ─── KELOLA USER ─────────────────────────────────────────
    // URL: /admin/users
    // Buat dan kelola akun waiter, kasir, dapur, owner
    $routes->get('users',                        'Users::index');            // Daftar semua user
    $routes->get('users/create',                 'Users::create');           // Form tambah user
    $routes->post('users/store',                 'Users::store');            // Simpan user baru
    $routes->get('users/edit/(:num)',            'Users::edit/$1');          // Form edit user
    $routes->post('users/update/(:num)',         'Users::update/$1');        // Simpan perubahan user
    $routes->get('users/reset-password/(:num)',  'Users::resetPassword/$1'); // Reset password ke default
    $routes->get('users/delete/(:num)',          'Users::delete/$1');        // Hapus user

    // ─── SETTING SISTEM ──────────────────────────────────────
    // URL: /admin/setting
    // Atur nama café, pajak, service charge, jumlah meja, dll
    $routes->get('setting',        'Setting::index'); // Halaman setting
    $routes->post('setting/save',  'Setting::save');  // Simpan setting
});

// ─── KASIR ROUTES ────────────────────────────────────────────
$routes->group('kasir', ['namespace' => 'App\Controllers\Kasir'], function ($routes) {

    // Dashboard
    $routes->get('/',         'Dashboard::index');
    $routes->get('dashboard', 'Dashboard::index');

    // Pesanan
    $routes->get('pesanan',                       'Pesanan::index');
    $routes->get('pesanan/buat',                  'Pesanan::buat');
    $routes->post('pesanan/store',                'Pesanan::store');
    $routes->get('pesanan/detail/(:num)',         'Pesanan::detail/$1');
    $routes->match(['get','post'], 'pesanan/tambah-item/(:num)', 'Pesanan::tambahItem/$1');
    $routes->get('pesanan/cancel/(:num)',         'Pesanan::cancel/$1');

    // Pembayaran
    $routes->get('pembayaran',                    'Pembayaran::index');
    $routes->get('pembayaran/(:num)',             'Pembayaran::form/$1');
    $routes->post('pembayaran/proses/(:num)',     'Pembayaran::proses/$1');
    $routes->get('pembayaran/cek-promo',          'Pembayaran::cekPromo');

    // Transaksi
    $routes->get('transaksi',                     'Transaksi::index');
    $routes->get('transaksi/struk/(:num)',        'Transaksi::struk/$1');
    $routes->get('transaksi/void/(:num)',         'Transaksi::void/$1');
});