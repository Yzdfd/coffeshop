<?php

namespace App\Controllers\Kasir;

use App\Controllers\BaseController;

class Pembayaran extends BaseController
{
    protected $db;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
    }

    // ─── DAFTAR PESANAN SIAP BAYAR ────────────────────────────
    public function index()
    {
        $orders = $this->db->table('orders o')
            ->select('o.*, t.number as table_number, u.name as kasir_name')
            ->join('tables t', 't.id = o.table_id',  'left')
            ->join('users u',  'u.id = o.waiter_id', 'left')
            ->where('o.status', 'ready')
            ->orderBy('o.ordered_at', 'DESC')
            ->get()->getResultArray();

        return view('kasir/pembayaran/daftar', [
            'title'  => 'Pembayaran',
            'orders' => $orders,
        ]);
    }

    // ─── FORM PEMBAYARAN ──────────────────────────────────────
    public function form($orderId)
    {
        $order = $this->db->table('orders o')
            ->select('o.*, t.number as table_number')
            ->join('tables t', 't.id = o.table_id', 'left')
            ->where('o.id', $orderId)
            ->get()->getRowArray();

        if (!$order) {
            return redirect()->to(base_url('kasir/pesanan'))->with('error', 'Pesanan tidak ditemukan.');
        }

        $items = $this->db->table('order_items oi')
            ->select('oi.*, m.name as menu_name')
            ->join('menus m', 'm.id = oi.menu_id', 'left')
            ->where('oi.order_id', $orderId)
            ->where('oi.status !=', 'cancelled')
            ->get()->getResultArray();

        // Hitung total
        $subtotal = array_sum(array_map(fn($i) => $i['unit_price'] * $i['qty'], $items));

        $setting = $this->db->table('settings')->get()->getResultArray();
        $settingArr = [];
        foreach ($setting as $s) { $settingArr[$s['key']] = $s['value']; }

        $taxRate     = ($settingArr['pajak'] ?? 0) / 100;
        $serviceRate = ($settingArr['service_charge'] ?? 0) / 100;
        $taxAmount   = round($subtotal * $taxRate);
        $serviceAmount = round($subtotal * $serviceRate);
        $total       = $subtotal + $taxAmount + $serviceAmount;

        return view('kasir/pembayaran/index', [
            'title'         => 'Pembayaran Pesanan #' . $orderId,
            'order'         => $order,
            'items'         => $items,
            'subtotal'      => $subtotal,
            'taxAmount'     => $taxAmount,
            'serviceAmount' => $serviceAmount,
            'total'         => $total,
            'setting'       => $settingArr,
        ]);
    }

    // ─── PROSES PEMBAYARAN ────────────────────────────────────
    public function proses($orderId)
    {
        $order = $this->db->table('orders')->where('id', $orderId)->get()->getRowArray();
        if (!$order) {
            return redirect()->back()->with('error', 'Pesanan tidak ditemukan.');
        }

        $subtotal      = $this->request->getPost('subtotal');
        $taxAmount     = $this->request->getPost('tax_amount');
        $serviceAmount = $this->request->getPost('service_amount');
        $diskon        = $this->request->getPost('diskon') ?? 0;
        $total         = $this->request->getPost('total');
        $paymentMethod = $this->request->getPost('payment_method');
        $uangDiterima  = $this->request->getPost('uang_diterima') ?? 0;
        $promoId       = $this->request->getPost('promo_id') ?: null;

        // Simpan transaksi
        $this->db->table('transactions')->insert([
            'order_id'        => $orderId,
            'kasir_id'        => session('user_id'),
            'subtotal'        => $subtotal,
            'tax_amount'      => $taxAmount,
            'service_amount'  => $serviceAmount,
            'discount_amount' => $diskon,
            'total'           => $total,
            'payment_method'  => $paymentMethod,
            'status'          => 'paid',
            'paid_at'         => date('Y-m-d H:i:s'),
        ]);
        $trxId = $this->db->insertID();

        // Update order
        $this->db->table('orders')->where('id', $orderId)->update(['status' => 'paid']);

        // Bebaskan meja
        if ($order['table_id']) {
            $this->db->table('tables')->where('id', $order['table_id'])->update([
                'status'           => 'available',
                'current_order_id' => null,
            ]);
        }

        return redirect()->to(base_url('kasir/transaksi/struk/' . $trxId))
            ->with('success', 'Pembayaran berhasil!');
    }

    // ─── CEK PROMO (AJAX) ─────────────────────────────────────
    public function cekPromo()
    {
        $kode     = $this->request->getGet('kode');
        $subtotal = (float) $this->request->getGet('subtotal');

        $promo = $this->db->table('promos')
            ->where('code', $kode)
            ->where('status', 'active')
            ->where('valid_from <=', date('Y-m-d H:i:s'))
            ->where('valid_until >=', date('Y-m-d H:i:s'))
            ->get()->getRowArray();

        if (!$promo) {
            return $this->response->setJSON(['valid' => false, 'message' => 'Kode promo tidak valid atau sudah kadaluarsa.']);
        }

        $diskon = 0;
        if ($promo['type'] === 'percent') {
            $diskon = round($subtotal * ($promo['value'] / 100));
            $msg = 'Promo ' . $promo['value'] . '% diterapkan. Diskon: Rp ' . number_format($diskon, 0, ',', '.');
        } else {
            $diskon = min($promo['value'], $subtotal);
            $msg = 'Promo Rp ' . number_format($diskon, 0, ',', '.') . ' diterapkan.';
        }

        return $this->response->setJSON([
            'valid'    => true,
            'promo_id' => $promo['id'],
            'diskon'   => $diskon,
            'message'  => $msg,
        ]);
    }
}
