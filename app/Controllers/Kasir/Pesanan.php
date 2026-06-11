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

    // ─── HELPER: hitung stock per menu berdasarkan resep ────────
    private function hitungStockMenu(array $menus): array
    {
        // ambil semua resep sekaligus (batch)
        $menuIds = array_column($menus, 'id');
        if (empty($menuIds)) return $menus;

        $recipes = $this->db->table('recipes r')
            ->select('r.menu_id, r.qty_needed, i.stock_qty')
            ->join('ingredients i', 'i.id = r.ingredient_id', 'left')
            ->whereIn('r.menu_id', $menuIds)
            ->get()->getResultArray();

        // group resep by menu_id
        $recipeByMenu = [];
        foreach ($recipes as $r) {
            $recipeByMenu[$r['menu_id']][] = $r;
        }

        foreach ($menus as &$m) {
            $id = $m['id'];
            if (empty($recipeByMenu[$id])) {
                // tidak ada resep → stock tidak terbatas (null = tidak ada batasan)
                $m['stock_menu'] = null;
            } else {
                // stock menu = minimum dari semua bahan: floor(stock_bahan / qty_needed)
                $minStock = PHP_INT_MAX;
                foreach ($recipeByMenu[$id] as $r) {
                    $qty = (float)$r['qty_needed'];
                    if ($qty <= 0) continue;
                    $possible = (int)floor((float)$r['stock_qty'] / $qty);
                    $minStock = min($minStock, $possible);
                }
                $m['stock_menu'] = ($minStock === PHP_INT_MAX) ? null : $minStock;
            }
        }
        unset($m);

        return $menus;
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
            ->select('m.*, c.name as nama_kategori, c.description as kategori_desc')
            ->join('categories c', 'c.id = m.category_id', 'left')
            ->where('m.status', 'available');

        if ($filterKategori) {
            $menuBuilder->where('m.category_id', $filterKategori);
        }

        $menus = $menuBuilder->get()->getResultArray();
        $menus = $this->hitungStockMenu($menus);

        $kategoris = $this->db->table('categories')->orderBy('sort_order')->get()->getResultArray();

        return view('kasir/pesanan/buat', [
            'title'          => 'Buat Pesanan',
            'menus'          => $menus,
            'kategoris'      => $kategoris,
            'mejas'          => $this->db->table('tables')->orderBy('number')->get()->getResultArray(),
            'filterKategori' => $filterKategori,
        ]);
    }

    public function store()
    {
         $nomorMeja = $this->request->getPost('table_id');
         $notes     = $this->request->getPost('notes');

        if ($nomorMeja) {
        $notes = '[' . $nomorMeja . '] ' . $notes;
        }
        $tableId = null;
        $items   = json_decode($this->request->getPost('items'), true);

        if (empty($items)) {
            return redirect()->back()->with('error', 'Tidak ada item pesanan.');
        }

        $this->db->table('orders')->insert([
            'table_id'   => $tableId,
            'waiter_id'  => session('user_id'),
            'status'     => 'open',
            'notes'      => $notes,
            'ordered_at' => date('Y-m-d H:i:s'),
        ]);
        $orderId = $this->db->insertID();

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

        if ($tableId) {
            $this->db->table('tables')->where('id', $tableId)->update([
                'status'           => 'occupied',
                'current_order_id' => $orderId,
            ]);
        }

        return redirect()->to(base_url('kasir/pembayaran/' . $orderId))
            ->with('success', 'Pesanan #' . $orderId . ' berhasil dibuat, silakan proses pembayaran.');
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

    // ─── TAMBAH ITEM ──────────────────────────────────────────
    public function tambahItem($id)
    {
        $order = $this->db->table('orders')->where('id', $id)->get()->getRowArray();
        if (!$order || !in_array($order['status'], ['open'])) {
            return redirect()->to(base_url('kasir/pesanan'))->with('error', 'Pesanan tidak bisa diubah.');
        }

        if (strtolower($this->request->getMethod()) === 'post') {
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
            ->select('m.*, c.name as nama_kategori, c.description as kategori_desc')
            ->join('categories c', 'c.id = m.category_id', 'left')
            ->where('m.status', 'available');
        if ($filterKategori) { $menuBuilder->where('m.category_id', $filterKategori); }

        $menus = $menuBuilder->get()->getResultArray();
        $menus = $this->hitungStockMenu($menus);

        return view('kasir/pesanan/buat', [
            'title'          => 'Tambah Item ke Pesanan #' . $id,
            'menus'          => $menus,
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

        if ($order['status'] !== 'open') {
            return redirect()->to(base_url('kasir/pesanan'))
                ->with('error', 'Pesanan tidak bisa dibatalkan karena sudah ' . $order['status'] . '.');
        }

        $this->db->table('orders')->where('id', $id)->update(['status' => 'cancelled']);
        $this->db->table('order_items')->where('order_id', $id)->update(['status' => 'cancelled']);

        if ($order['table_id']) {
            $this->db->table('tables')->where('id', $order['table_id'])->update([
                'status'           => 'available',
                'current_order_id' => null,
            ]);
        }

        return redirect()->to(base_url('kasir/pesanan'))
            ->with('success', 'Pesanan #' . $id . ' berhasil dibatalkan.');
    }

    public function menus()
    {
        $katId = $this->request->getGet('kategori');
        $builder = $this->db->table('menus m')
            ->select('m.*, c.name as nama_kategori, c.description as kategori_desc')
            ->join('categories c', 'c.id = m.category_id', 'left')
            ->where('m.status', 'available');
        if ($katId) {
            $builder->where('m.category_id', $katId);
        }
        $menus = $builder->get()->getResultArray();
        $menus = $this->hitungStockMenu($menus);
        return $this->response->setJSON($menus);
    }
}