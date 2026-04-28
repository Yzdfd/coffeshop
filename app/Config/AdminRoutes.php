<?php

// ============================================================
// TAMBAHKAN KODE INI KE DALAM FILE: app/Config/Routes.php
// Di dalam bagian $routes->group() atau langsung di bawah
// $routes->setDefaultNamespace(...)
// ============================================================

// ─── Admin Routes ───────────────────────────────────────────
$routes->group('admin', ['namespace' => 'App\Controllers\Admin'], function ($routes) {

    // Dashboard
    $routes->get('dashboard',          'Dashboard::index');
    $routes->get('/',                  'Dashboard::index');

    // Menu
    $routes->get('menu',               'Menu::index');
    $routes->get('menu/create',        'Menu::create');
    $routes->post('menu/store',        'Menu::store');
    $routes->get('menu/edit/(:num)',   'Menu::edit/$1');
    $routes->post('menu/update/(:num)','Menu::update/$1');
    $routes->get('menu/delete/(:num)', 'Menu::delete/$1');

    // Kategori
    $routes->get('kategori',               'Kategori::index');
    $routes->post('kategori/store',        'Kategori::store');
    $routes->get('kategori/edit/(:num)',   'Kategori::edit/$1');
    $routes->post('kategori/update/(:num)','Kategori::update/$1');
    $routes->get('kategori/delete/(:num)', 'Kategori::delete/$1');

    // Stok Bahan
    $routes->get('stok',                'Stok::index');
    $routes->get('stok/create',         'Stok::create');
    $routes->post('stok/store',         'Stok::store');
    $routes->get('stok/edit/(:num)',    'Stok::edit/$1');
    $routes->post('stok/update/(:num)', 'Stok::update/$1');
    $routes->match(['get','post'], 'stok/tambah/(:num)', 'Stok::tambah/$1');
    $routes->get('stok/delete/(:num)',  'Stok::delete/$1');

    // Users
    $routes->get('users',                       'Users::index');
    $routes->get('users/create',                'Users::create');
    $routes->post('users/store',                'Users::store');
    $routes->get('users/edit/(:num)',           'Users::edit/$1');
    $routes->post('users/update/(:num)',        'Users::update/$1');
    $routes->get('users/reset-password/(:num)', 'Users::resetPassword/$1');
    $routes->get('users/delete/(:num)',         'Users::delete/$1');

    // Setting
    $routes->get('setting',       'Setting::index');
    $routes->post('setting/save', 'Setting::save');
});
