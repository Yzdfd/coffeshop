<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// ═══════════════════════════════════════════════════════════════
//  PUBLIC ROUTES — Tidak perlu login
// ═══════════════════════════════════════════════════════════════
$routes->get('/', 'Auth::login');                         // Root redirect ke login
$routes->get('login', 'Auth::login');                     // Halaman login
$routes->post('login/process', 'Auth::loginProcess');     // Proses form login
$routes->get('logout', 'Auth::logout');                   // Logout

// ═══════════════════════════════════════════════════════════════
//  ADMIN ROUTES — Hanya role: admin
// ═══════════════════════════════════════════════════════════════
$routes->group('admin', [
    'namespace' => 'App\Controllers\Admin',
    'filter'    => 'role:admin',
], function ($routes) {

    // ─── DASHBOARD ───────────────────────────────────────────
    $routes->get('dashboard', 'Dashboard::index');
    $routes->get('/', 'Dashboard::index');

    // ─── MENU ────────────────────────────────────────────────
    $routes->get('menu',                'Menu::index');
    $routes->get('menu/create',         'Menu::create');
    $routes->post('menu/store',         'Menu::store');
    $routes->get('menu/edit/(:num)',    'Menu::edit/$1');
    $routes->post('menu/update/(:num)', 'Menu::update/$1');
    $routes->get('menu/delete/(:num)',  'Menu::delete/$1');

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
    $routes->get('users/delete/(:num)',          'Users::delete/$1');

    // ─── SETTING SISTEM ──────────────────────────────────────
    $routes->get('setting',        'Setting::index');
    $routes->post('setting/save',  'Setting::save');
});

// ═══════════════════════════════════════════════════════════════
//  OWNER ROUTES — Hanya role: owner
// ═══════════════════════════════════════════════════════════════
$routes->group('owner', [
    'namespace' => 'App\Controllers',
    'filter'    => 'role:owner',
], function ($routes) {
    $routes->get('dashboard', 'Dashboard::owner');
    $routes->get('/',         'Dashboard::owner');
});

// ═══════════════════════════════════════════════════════════════
//  KASIR ROUTES — Hanya role: kasir
// ═══════════════════════════════════════════════════════════════
$routes->group('kasir', [
    'namespace' => 'App\Controllers',
    'filter'    => 'role:kasir',
], function ($routes) {
    $routes->get('dashboard', 'Dashboard::kasir');
    $routes->get('/',         'Dashboard::kasir');
});

// ═══════════════════════════════════════════════════════════════
//  WAITER ROUTES — Hanya role: waiter
// ═══════════════════════════════════════════════════════════════
$routes->group('waiter', [
    'namespace' => 'App\Controllers',
    'filter'    => 'role:waiter',
], function ($routes) {
    $routes->get('dashboard', 'Dashboard::waiter');
    $routes->get('/',         'Dashboard::waiter');
});

// ═══════════════════════════════════════════════════════════════
//  DAPUR ROUTES — Hanya role: dapur
// ═══════════════════════════════════════════════════════════════
$routes->group('dapur', [
    'namespace' => 'App\Controllers',
    'filter'    => 'role:dapur',
], function ($routes) {
    $routes->get('dashboard', 'Dashboard::dapur');
    $routes->get('/',         'Dashboard::dapur');
});
