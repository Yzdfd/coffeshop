<?php

namespace App\Controllers\Kasir;

use App\Controllers\BaseController;

class Transaksi extends BaseController
{
    protected $db;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
    }

    // ─── TRANSAKSI HARIAN ─────────────────────────────────────
    public function index()
    {
        $tanggal = $this->request->getGet('tanggal') ?? date('Y-m-d');

        $transaksis = $this->db->table('transactions t')
            ->select('t.*, u.name as kasir_name')
            ->join('users u', 'u.id = t.kasir_id', 'left')
            ->where('t.status', 'paid')
            ->where('DATE(t.paid_at)', $tanggal)
            ->orderBy('t.paid_at', 'DESC')
            ->get()->getResultArray();

        $totalPendapatan = array_sum(array_column($transaksis, 'total'));
        $totalCash = array_sum(array_map(
            fn($t) => $t['payment_method'] === 'cash' ? $t['total'] : 0, $transaksis
        ));
        $totalNonCash = $totalPendapatan - $totalCash;

        return view('kasir/transaksi/index', [
            'title'           => 'Transaksi Harian',
            'transaksis'      => $transaksis,
            'totalTrx'        => count($transaksis),
            'totalPendapatan' => $totalPendapatan,
            'totalCash'       => $totalCash,
            'totalNonCash'    => $totalNonCash,
            'tanggal'         => $tanggal,
        ]);
    }

    // ─── CETAK STRUK ──────────────────────────────────────────
    public function struk($id)
    {
        $transaksi = $this->db->table('transactions t')
            ->select('t.*, u.name as kasir_name')
            ->join('users u', 'u.id = t.kasir_id', 'left')
            ->where('t.id', $id)
            ->get()->getRowArray();

        if (!$transaksi) {
            return redirect()->to(base_url('kasir/transaksi'))->with('error', 'Transaksi tidak ditemukan.');
        }

        $order = $this->db->table('orders o')
            ->select('o.*, t.number as table_number')
            ->join('tables t', 't.id = o.table_id', 'left')
            ->where('o.id', $transaksi['order_id'])
            ->get()->getRowArray();

        $items = $this->db->table('order_items oi')
            ->select('oi.*, m.name as menu_name')
            ->join('menus m', 'm.id = oi.menu_id', 'left')
            ->where('oi.order_id', $transaksi['order_id'])
            ->where('oi.status !=', 'cancelled')
            ->get()->getResultArray();

        $setting = $this->db->table('settings')->get()->getResultArray();
        $settingArr = [];
        foreach ($setting as $s) { $settingArr[$s['key']] = $s['value']; }

        return view('kasir/pembayaran/struk', [
            'title'     => 'Struk #' . $id,
            'transaksi' => $transaksi,
            'order'     => $order,
            'items'     => $items,
            'setting'   => $settingArr,
        ]);
    }

    // ─── VOID / REFUND ────────────────────────────────────────
    public function void($id)
    {
        $transaksi = $this->db->table('transactions')->where('id', $id)->get()->getRowArray();
        if (!$transaksi) {
            return redirect()->to(base_url('kasir/transaksi'))->with('error', 'Transaksi tidak ditemukan.');
        }

        // Update status transaksi
        $this->db->table('transactions')->where('id', $id)->update(['status' => 'void']);

        // Kembalikan status order
        $this->db->table('orders')->where('id', $transaksi['order_id'])->update(['status' => 'cancelled']);

        return redirect()->to(base_url('kasir/transaksi'))
            ->with('success', 'Transaksi #' . $id . ' berhasil di-void.');
    }
}
