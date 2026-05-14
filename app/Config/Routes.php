<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// ─── AUTH ─────────────────────────────────────────────────────────────────────
$routes->get('/',        'Auth::loginPage');    // Root redirect ke login
$routes->get('login',   'Auth::loginPage');
$routes->post('login',  'Auth::loginProcess');
$routes->get('logout',  'Auth::logout');

// ─── ADMIN ────────────────────────────────────────────────────────────────────
// Gunakan filter 'auth:admin' agar hanya role admin yang bisa akses
$routes->group('admin', ['namespace' => 'App\Controllers\Admin', 'filter' => 'auth:admin'], function ($routes) {

    // ─── DASHBOARD ───────────────────────────────────────────
    $routes->get('dashboard', 'Dashboard::index');
    $routes->get('/',         'Dashboard::index');

    // ─── MENU ────────────────────────────────────────────────
    $routes->get('menu',                'Menu::index');
    $routes->get('menu/create',         'Menu::create');
    $routes->post('menu/store',         'Menu::store');
    $routes->get('menu/edit/(:num)',    'Menu::edit/$1');
    $routes->post('menu/update/(:num)', 'Menu::update/$1');
    $routes->get('menu/delete/(:num)',  'Menu::delete/$1');

    // ─── RESEP MENU ─────────────────────────────────────────
    $routes->get('resep',                       'Recipe::index');
    $routes->get('resep/menu/(:num)',         'Recipe::manage/$1');
    $routes->post('resep/store/(:num)',       'Recipe::store/$1');
    $routes->post('resep/delete/(:num)',      'Recipe::delete/$1');

    // ─── KATEGORI ────────────────────────────────────────────
    $routes->get('kategori',                'Kategori::index');
    $routes->post('kategori/store',         'Kategori::store');
    $routes->get('kategori/edit/(:num)',    'Kategori::edit/$1');
    $routes->post('kategori/update/(:num)', 'Kategori::update/$1');
    $routes->get('kategori/delete/(:num)',  'Kategori::delete/$1');

    // ─── STOK BAHAN ──────────────────────────────────────────
    $routes->get('stok',                'Stok::index');
    $routes->get('stok/create',         'Stok::create');
    $routes->post('stok/store',         'Stok::store');
    $routes->get('stok/edit/(:num)',    'Stok::edit/$1');
    $routes->post('stok/update/(:num)', 'Stok::update/$1');
    $routes->match(['get','post'], 'stok/tambah/(:num)', 'Stok::tambah/$1');
    $routes->get('stok/delete/(:num)',  'Stok::delete/$1');

    // ─── KELOLA USER ─────────────────────────────────────────
    $routes->get('users',                        'Users::index');
    $routes->get('users/create',                 'Users::create');
    $routes->post('users/store',                 'Users::store');
    $routes->get('users/edit/(:num)',            'Users::edit/$1');
    $routes->post('users/update/(:num)',         'Users::update/$1');
    $routes->get('users/reset-password/(:num)',  'Users::resetPassword/$1');
    $routes->get('users/toggle/(:num)', 'Users::toggle/$1');

    // Promo─────────────────────────────────────────
    $routes->get('promo',                'Promo::index');
    $routes->get('promo/create',         'Promo::create');
    $routes->post('promo/store',         'Promo::store');
    $routes->get('promo/edit/(:num)',    'Promo::edit/$1');
    $routes->post('promo/update/(:num)', 'Promo::update/$1');
    $routes->get('promo/toggle/(:num)',  'Promo::toggle/$1');
    $routes->get('promo/delete/(:num)',  'Promo::delete/$1');

    // ─── SETTING SISTEM ──────────────────────────────────────
    $routes->get('setting',       'Setting::index');
    $routes->post('setting/save', 'Setting::save');


  
});

// ─── KASIR ───────────────────────────────────────────────────────────────────
$routes->group('kasir', ['namespace' => 'App\\Controllers\\Kasir', 'filter' => 'auth:kasir'], function ($routes) {
    $routes->get('/',         'Dashboard::index');
    $routes->get('dashboard', 'Dashboard::index');

    // ─── PESANAN ─────────────────────────────────────────────
    $routes->get('pesanan',                       'Pesanan::index');
    $routes->get('pesanan/buat',                  'Pesanan::buat');
    $routes->post('pesanan/store',                'Pesanan::store');
    $routes->get('pesanan/detail/(:num)',         'Pesanan::detail/$1');
    $routes->match(['get','post'], 'pesanan/tambah-item/(:num)', 'Pesanan::tambahItem/$1');
    $routes->get('pesanan/cancel/(:num)',         'Pesanan::cancel/$1');

    // ─── PEMBAYARAN ──────────────────────────────────────────
    $routes->get('pembayaran',                    'Pembayaran::index');
    $routes->get('pembayaran/(:num)',             'Pembayaran::form/$1');
    $routes->post('pembayaran/proses/(:num)',     'Pembayaran::proses/$1');
    $routes->get('pembayaran/cek-promo',          'Pembayaran::cekPromo');

    // ─── TRANSAKSI ───────────────────────────────────────────
    $routes->get('transaksi',                     'Transaksi::index');
    $routes->get('transaksi/struk/(:num)',        'Transaksi::struk/$1');
    $routes->get('transaksi/print/(:num)', 'Transaksi::printStruk/$1');
    $routes->get('transaksi/void/(:num)',         'Transaksi::void/$1');
});

// ─── WAITER ──────────────────────────────────────────────────────────────────
$routes->group('waiter', ['namespace' => 'App\Controllers\Waiter', 'filter' => 'auth:waiter'], function ($routes) {
    $routes->get('dashboard', 'Dashboard::index');
    // ... tambah routes waiter di sini
});

// ─── DAPUR (KDS) ──────────────────────────────────────────────────────────────
// Sesuai requirement: /dapur -> DapurController::index + AJAX update status
$routes->get('dapur', 'DapurController::index', ['filter' => 'auth:dapur']);
$routes->get('dapur/stok', 'DapurController::stok', ['filter' => 'auth:dapur']);
$routes->get('dapur/resep', 'DapurController::resepList', ['filter' => 'auth:dapur']);
$routes->post('dapur/updateStatus', 'DapurController::updateStatus', ['filter' => 'auth:dapur']);
$routes->get('dapur/resep/(:num)', 'DapurController::resep/$1', ['filter' => 'auth:dapur']);

// ─── DAPUR ───────────────────────────────────────────────────────────────────
$routes->group('dapur', ['namespace' => 'App\Controllers', 'filter' => 'auth:dapur'], function ($routes) {
    $routes->get('dashboard', 'DapurController::index');
});

// ─── OWNER ────────────────────────────────────────────────────────────────────
// Sesuai requirement: /owner -> OwnerController::index
$routes->get('owner', 'OwnerController::index', ['filter' => 'auth:owner']);

// ─── OWNER ───────────────────────────────────────────────────────────────────
$routes->group('owner', ['namespace' => 'App\Controllers', 'filter' => 'auth:owner'], function ($routes) {
    $routes->get('dashboard', 'OwnerController::index');
    $routes->get('export-penjualan', 'Owner\Laporan::exportPenjualan');
    $routes->get('stok-alert', 'OwnerController::stokAlert');

    // Kelola User (ROLE OWNER dapat semua role)
    $routes->get('users', 'Owner\Users::index');
    $routes->get('users/create', 'Owner\Users::create');
    $routes->post('users/store', 'Owner\Users::store');
    $routes->get('users/edit/(:num)', 'Owner\Users::edit/$1');
    $routes->post('users/update/(:num)', 'Owner\Users::update/$1');
    $routes->get('users/reset-password/(:num)', 'Owner\Users::resetPassword/$1');
    $routes->get('users/delete/(:num)', 'Owner\Users::delete/$1');
    $routes->get('users/toggle/(:num)', 'Owner\Users::toggle/$1');
});

$routes->get('/generate-hash', 'Auth::generateHash');