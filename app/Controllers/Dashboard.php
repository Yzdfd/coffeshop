<?php

namespace App\Controllers;

/**
 * Dashboard Controller
 * Menangani halaman dashboard untuk role yang panelnya belum dibangun.
 * Role admin sudah punya controller sendiri di Controllers/Admin/Dashboard.php
 */
class Dashboard extends BaseController
{
    public function owner()
    {
        return view('auth/coming_soon', [
            'title'   => 'Panel Owner',
            'icon'    => '📊',
            'message' => 'Panel Owner (laporan penjualan, grafik analitik, monitoring karyawan) sedang dalam pengembangan.',
        ]);
    }

    public function kasir()
    {
        return view('auth/coming_soon', [
            'title'   => 'Panel Kasir',
            'icon'    => '🧾',
            'message' => 'Panel Kasir (pembayaran, cetak struk, split bill, diskon/promo) sedang dalam pengembangan.',
        ]);
    }

    public function waiter()
    {
        return view('auth/coming_soon', [
            'title'   => 'Panel Waiter',
            'icon'    => '🍽️',
            'message' => 'Panel Waiter (input pesanan, pilih meja, kirim ke dapur) sedang dalam pengembangan.',
        ]);
    }

    public function dapur()
    {
        return view('auth/coming_soon', [
            'title'   => 'Panel Dapur',
            'icon'    => '👨‍🍳',
            'message' => 'Panel Dapur (pesanan masuk real-time, update status masak) sedang dalam pengembangan.',
        ]);
    }
}
