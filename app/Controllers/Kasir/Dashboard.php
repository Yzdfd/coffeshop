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
            ->whereIn('o.status', ['open', 'process', 'ready'])
            ->where('DATE(o.ordered_at)', $today)   // ← ganti whereDate
            ->orderBy('o.ordered_at', 'DESC')
            ->get()->getResultArray();

        $totalDiproses = $db->table('orders')
            ->where('status', 'process')
            ->where('DATE(ordered_at)', $today)      // ← ganti whereDate
            ->countAllResults();

        $totalTransaksiHari = $db->table('transactions')
            ->where('status', 'paid')
            ->where('DATE(paid_at)', $today)         // ← ganti whereDate
            ->countAllResults();

        $pendapatanHari = $db->table('transactions')
            ->selectSum('total')
            ->where('status', 'paid')
            ->where('DATE(paid_at)', $today)         // ← ganti whereDate
            ->get()->getRow()->total ?? 0;

        return view('kasir/dashboard', [
            'title'              => 'Dashboard Kasir',
            'pesananAktif'       => $pesananAktif,
            'totalPesananAktif'  => count($pesananAktif),
            'totalDiproses'      => $totalDiproses,
            'totalTransaksiHari' => $totalTransaksiHari,
            'pendapatanHari'     => $pendapatanHari,
        ]);
    }
}