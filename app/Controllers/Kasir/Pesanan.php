<?php

namespace App\Controllers\Kasir;

use App\Controllers\BaseController;

class Pesanan extends BaseController
{
    protected $db;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
    }

    // ─── DAFTAR PESANAN ──────────────────────────────────────
    public function index()
    {
        $filterStatus = $this->request->getGet('status');

        $builder = $this->db->table('orders o')
            ->select('o.*, t.number as table_number, u.name as kasir_name,
                      COUNT(oi.id) as jumlah_item')
            ->join('tables t',      't.id = o.table_id',   'left')
            ->join('users u',       'u.id = o.waiter_id',  'left')
            ->join('order_items oi','oi.order_id = o.id',  'left')
            ->groupBy('o.id')
            ->orderBy('o.ordered_at', 'DESC');

        if ($filterStatus) {
            $builder->where('o.status', $filterStatus);
        }

        return view('kasir/pesanan/index', [
            'title'        => 'Daftar Pesanan',
            'orders'       => $builder->get()->getResultArray(),
            'filterStatus' => $filterStatus,
        ]);
    }

    // ─── BUAT PESANAN ─────────────────────────────────────────
    public function buat()
    {
        $filterKategori = $this->request->getGet('kategori');

        $menuBuilder = $this->db->table('menus m')
            ->select('m.*, c.name as nama_kategori')
            ->join('categories c', 'c.id = m.category_id', 'left');

        if ($filterKategori) {
            $menuBuilder->where('m.category_id', $filterKategori);
        }

        return view('kasir/pesanan/buat', [
            'title'          => 'Buat Pesanan',
            'menus'          => $menuBuilder->get()->getResultArray(),
            'kategoris'      => $this->db->table('categories')->orderBy('sort_order')->get()->getResultArray(),
            'mejas'          => $this->db->table('tables')->orderBy('number')->get()->getResultArray(),
            'filterKategori' => $filterKategori,
        ]);
    }

    public function store()
    {
        $tableId = $this->request->getPost('table_id') ?: null;
        $notes   = $this->request->getPost('notes');
        $items   = json_decode($this->request->getPost('items'), true);

        if (empty($items)) {
            return redirect()->back()->with('error', 'Tidak ada item pesanan.');
        }

        // Buat order
        $this->db->table('orders')->insert([
            'table_id'   => $tableId,
            'waiter_id'  => session('user_id'),
            'status'     => 'open',
            'notes'      => $notes,
            'ordered_at' => date('Y-m-d H:i:s'),
        ]);
        $orderId = $this->db->insertID();

        // Insert order items
        foreach ($items as $item) {
            $menu = $this->db->table('menus')->where('id', $item['id'])->get()->getRow();
            if (!$menu) continue;

            $this->db->table('order_items')->insert([
                'order_id'   => $orderId,
                'menu_id'    => $item['id'],
                'qty'        => $item['qty'],
                'unit_price' => $menu->price,
                'notes'      => $item['catatan'] ?? '',
                'status'     => 'pending',
            ]);
        }

        // Update status meja jika ada
        if ($tableId) {
            $this->db->table('tables')->where('id', $tableId)->update([
                'status'           => 'occupied',
                'current_order_id' => $orderId,
            ]);
        }

        return redirect()->to(base_url('kasir/pesanan/detail/' . $orderId))
            ->with('success', 'Pesanan #' . $orderId . ' berhasil dibuat dan dikirim ke dapur!');
    }

    // ─── DETAIL PESANAN ───────────────────────────────────────
    public function detail($id)
    {
        $order = $this->db->table('orders o')
            ->select('o.*, t.number as table_number, u.name as kasir_name')
            ->join('tables t', 't.id = o.table_id',  'left')
            ->join('users u',  'u.id = o.waiter_id', 'left')
            ->where('o.id', $id)
            ->get()->getRowArray();

        if (!$order) {
            return redirect()->to(base_url('kasir/pesanan'))->with('error', 'Pesanan tidak ditemukan.');
        }

        $items = $this->db->table('order_items oi')
            ->select('oi.*, m.name as menu_name')
            ->join('menus m', 'm.id = oi.menu_id', 'left')
            ->where('oi.order_id', $id)
            ->get()->getResultArray();

        $setting = $this->db->table('settings')->get()->getResultArray();
        $settingArr = [];
        foreach ($setting as $s) { $settingArr[$s['key']] = $s['value']; }

        return view('kasir/pesanan/detail', [
            'title'   => 'Detail Pesanan #' . $id,
            'order'   => $order,
            'items'   => $items,
            'setting' => $settingArr,
        ]);
    }

    // ─── TAMBAH ITEM ─────────────────────────────────────────
    public function tambahItem($id)
    {
        $order = $this->db->table('orders')->where('id', $id)->get()->getRowArray();
        if (!$order || !in_array($order['status'], ['open', 'process'])) {
            return redirect()->to(base_url('kasir/pesanan'))->with('error', 'Pesanan tidak bisa diubah.');
        }

        if ($this->request->getMethod() === 'post') {
            $items = json_decode($this->request->getPost('items'), true);
            if (!empty($items)) {
                foreach ($items as $item) {
                    $menu = $this->db->table('menus')->where('id', $item['id'])->get()->getRow();
                    if (!$menu) continue;
                    $this->db->table('order_items')->insert([
                        'order_id'   => $id,
                        'menu_id'    => $item['id'],
                        'qty'        => $item['qty'],
                        'unit_price' => $menu->price,
                        'notes'      => $item['catatan'] ?? '',
                        'status'     => 'pending',
                    ]);
                }
            }
            return redirect()->to(base_url('kasir/pesanan/detail/' . $id))
                ->with('success', 'Item berhasil ditambahkan.');
        }

        $filterKategori = $this->request->getGet('kategori');
        $menuBuilder = $this->db->table('menus m')
            ->select('m.*, c.name as nama_kategori')
            ->join('categories c', 'c.id = m.category_id', 'left')
            ->where('m.status', 'available');
        if ($filterKategori) { $menuBuilder->where('m.category_id', $filterKategori); }

        return view('kasir/pesanan/buat', [
            'title'          => 'Tambah Item ke Pesanan #' . $id,
            'menus'          => $menuBuilder->get()->getResultArray(),
            'kategoris'      => $this->db->table('categories')->get()->getResultArray(),
            'mejas'          => [],
            'filterKategori' => $filterKategori,
            'addToOrder'     => $id,
        ]);
    }

    // ─── CANCEL PESANAN ───────────────────────────────────────
    public function cancel($id)
    {
        $order = $this->db->table('orders')->where('id', $id)->get()->getRowArray();
        if (!$order) {
            return redirect()->to(base_url('kasir/pesanan'))->with('error', 'Pesanan tidak ditemukan.');
        }

        if (!in_array($order['status'], ['open', 'process'])) {
            return redirect()->to(base_url('kasir/pesanan'))->with('error', 'Pesanan tidak bisa dibatalkan.');
        }

        $this->db->table('orders')->where('id', $id)->update(['status' => 'cancelled']);

        // Bebaskan meja
        if ($order['table_id']) {
            $this->db->table('tables')->where('id', $order['table_id'])->update([
                'status'           => 'available',
                'current_order_id' => null,
            ]);
        }

        return redirect()->to(base_url('kasir/pesanan'))
            ->with('success', 'Pesanan #' . $id . ' berhasil dibatalkan.');
    }
}
