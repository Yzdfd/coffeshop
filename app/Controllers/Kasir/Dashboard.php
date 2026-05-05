<?php

namespace App\Controllers\Kasir;

use App\Controllers\BaseController;

class Dashboard extends BaseController
{
    public function index()
    {
        $db = \Config\Database::connect();
        $today = date('Y-m-d');

        $pesananAktif = $db->table('orders o')
            ->select('o.*, t.number as table_number, u.name as kasir_name')
            ->join('tables t', 't.id = o.table_id', 'left')
            ->join('users u', 'u.id = o.waiter_id', 'left')
            ->where('o.status', 'open')
            ->orderBy('o.ordered_at', 'DESC')
            ->get()->getResultArray();

        $totalTransaksiHari = $db->table('transactions')
            ->where('status', 'paid')
            ->where('DATE(paid_at)', $today)
            ->countAllResults();

        $pendapatanRow = $db->table('transactions')
            ->selectSum('total')
            ->where('status', 'paid')
            ->where('DATE(paid_at)', $today)
            ->get()->getRow();

        $pendapatanHari = $pendapatanRow->total ?? 0;

        $totalDibatalkan = $db->table('orders')
            ->where('status', 'cancelled')
            ->where('DATE(ordered_at)', $today)
            ->countAllResults();

        return view('kasir/dashboard', [
            'title'              => 'Dashboard Kasir',
            'pesananAktif'       => $pesananAktif,
            'totalPesananAktif'  => count($pesananAktif),
            'totalDibatalkan'    => $totalDibatalkan,
            'totalTransaksiHari' => $totalTransaksiHari,
            'pendapatanHari'     => $pendapatanHari,
        ]);
    }
}
