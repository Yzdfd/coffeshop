<?php

namespace App\Controllers\Kasir;

use App\Controllers\BaseController;
use App\Models\RecipeModel;
use App\Models\IngredientModel;
use App\Models\StockLogModel;

class Pembayaran extends BaseController
{
    protected $db;
    protected $recipeModel;
    protected $ingredientModel;
    protected $stockLogModel;

    public function __construct()
    {
        $this->db             = \Config\Database::connect();
        $this->recipeModel    = new RecipeModel();
        $this->ingredientModel = new IngredientModel();
        $this->stockLogModel  = new StockLogModel();
    }

    // ─── DAFTAR PESANAN BELUM DIBAYAR ────────────────────────
    public function index()
    {
        $orders = $this->db->table('orders o')
            ->select('o.*, t.number as table_number, u.name as kasir_name')
            ->join('tables t', 't.id = o.table_id',  'left')
            ->join('users u',  'u.id = o.waiter_id', 'left')
            ->where('o.status', 'open')
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

        // Pesanan yang bisa dibayar hanya yang statusnya open
        if ($order['status'] === 'paid') {
            return redirect()->to(base_url('kasir/pesanan'))->with('error', 'Pesanan sudah dibayar.');
        }

        if ($order['status'] === 'cancelled') {
            return redirect()->to(base_url('kasir/pesanan'))->with('error', 'Pesanan sudah dibatalkan.');
        }

        $items = $this->db->table('order_items oi')
            ->select('oi.*, m.name as menu_name')
            ->join('menus m', 'm.id = oi.menu_id', 'left')
            ->where('oi.order_id', $orderId)
            ->where('oi.status !=', 'cancelled')
            ->get()->getResultArray();

        $subtotal = array_sum(array_map(fn($i) => $i['unit_price'] * $i['qty'], $items));

        $setting = $this->db->table('settings')->get()->getResultArray();
        $settingArr = [];
        foreach ($setting as $s) { $settingArr[$s['key']] = $s['value']; }

        $taxRate       = ($settingArr['pajak'] ?? 0) / 100;
        $serviceRate   = ($settingArr['service_charge'] ?? 0) / 100;
        $taxAmount     = round($subtotal * $taxRate);
        $serviceAmount = round($subtotal * $serviceRate);
        $total         = $subtotal + $taxAmount + $serviceAmount;

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

        $subtotal      = (float) $this->request->getPost('subtotal');
        $taxAmount     = (float) $this->request->getPost('tax_amount');
        $serviceAmount = (float) $this->request->getPost('service_amount');
        $diskon        = (float) ($this->request->getPost('diskon') ?? 0);
        $total         = (float) $this->request->getPost('total');
        $paymentMethod = $this->request->getPost('payment_method');
        $uangDiterima  = (float) ($this->request->getPost('uang_diterima') ?? 0);
        $promoId       = $this->request->getPost('promo_id') ?: null;

        if ($paymentMethod === 'cash' && $uangDiterima < $total) {
            return redirect()->back()->with('error', 'Uang diterima kurang dari total pembayaran.');
        }

        // Mulai transaksi database untuk memastikan stok & transaksi konsisten
        $this->db->transBegin();

        // 1. Hitung kebutuhan stok ingredient berdasarkan resep & order_items
        $orderItems = $this->db->table('order_items oi')
            ->select('oi.menu_id, oi.qty')
            ->where('oi.order_id', $orderId)
            ->where('oi.status !=', 'cancelled')
            ->get()
            ->getResultArray();

        if (empty($orderItems)) {
            $this->db->transRollback();
            return redirect()->back()->with('error', 'Tidak ada item pada pesanan.');
        }

        // Map menu_id => total qty
        $menuQtyMap = [];
        foreach ($orderItems as $item) {
            $mid = (int) $item['menu_id'];
            $menuQtyMap[$mid] = ($menuQtyMap[$mid] ?? 0) + (int) $item['qty'];
        }

        if (! empty($menuQtyMap)) {
            // Ambil semua resep yang terkait dengan menu di pesanan
            $menuIds = array_keys($menuQtyMap);

            $recipes = $this->db->table('recipes r')
                ->select('r.menu_id, r.ingredient_id, r.qty_needed, i.stock_qty, i.name, i.unit, i.min_stock')
                ->join('ingredients i', 'i.id = r.ingredient_id', 'left')
                ->whereIn('r.menu_id', $menuIds)
                ->get()
                ->getResultArray();

            // Hitung total kebutuhan per ingredient
            $neededPerIngredient = []; // ingredient_id => qty_needed_total
            foreach ($recipes as $r) {
                $menuId       = (int) $r['menu_id'];
                $ingredientId = (int) $r['ingredient_id'];

                if (! isset($menuQtyMap[$menuId]) || ! $ingredientId) {
                    continue;
                }

                $orderQty = (int) $menuQtyMap[$menuId];
                $perCup   = (float) $r['qty_needed'];

                $neededPerIngredient[$ingredientId] = ($neededPerIngredient[$ingredientId] ?? 0.0) + ($orderQty * $perCup);
            }

            // Validasi stok cukup untuk semua ingredient yang dipakai
            if (! empty($neededPerIngredient)) {
                $ingredientIds = array_keys($neededPerIngredient);

                $ingredients = $this->db->table('ingredients')
                    ->whereIn('id', $ingredientIds)
                    ->get()
                    ->getResultArray();

                $ingredientMap = [];
                foreach ($ingredients as $ing) {
                    $ingredientMap[(int) $ing['id']] = $ing;
                }

                $kurangList = [];

                foreach ($neededPerIngredient as $ingredientId => $neededQty) {
                    $ing = $ingredientMap[$ingredientId] ?? null;
                    if (! $ing) {
                        continue;
                    }

                    $stokSaatIni = (float) $ing['stock_qty'];
                    if ($stokSaatIni < $neededQty) {
                        $kurangList[] = $ing['name'] . ' (butuh ' . $neededQty . ' ' . $ing['unit'] . ', stok ' . $stokSaatIni . ' ' . $ing['unit'] . ')';
                    }
                }

                if (! empty($kurangList)) {
                    $this->db->transRollback();
                    return redirect()->back()
                        ->with('error', 'Stok bahan tidak mencukupi: ' . implode('; ', $kurangList));
                }

                // 2. Kurangi stok ingredient & catat ke stock_logs
                foreach ($neededPerIngredient as $ingredientId => $neededQty) {
                    $this->db->table('ingredients')
                        ->where('id', $ingredientId)
                        ->set('stock_qty', 'stock_qty - ' . (float) $neededQty, false)
                        ->update();

                    // Simpan log pengurangan stok
                    $this->stockLogModel->logUsage(
                        (int) $ingredientId,
                        (int) $orderId,
                        0 - (float) $neededQty,
                        'Pengurangan stok otomatis dari pembayaran order #' . $orderId
                    );
                }
            }
        }

        // 3. Simpan transaksi pembayaran
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

        // 4. Update status order & meja
        $this->db->table('orders')->where('id', $orderId)->update(['status' => 'paid']);

        if ($order['table_id']) {
            $this->db->table('tables')->where('id', $order['table_id'])->update([
                'status'           => 'available',
                'current_order_id' => null,
            ]);
        }

        // 5. Commit transaksi database
        if ($this->db->transStatus() === false) {
            $this->db->transRollback();
            return redirect()->back()->with('error', 'Terjadi kesalahan saat memproses pembayaran.');
        }

        $this->db->transCommit();

        // 6. Simpan informasi uang cash (untuk struk)
        if ($paymentMethod === 'cash') {
            session()->setFlashdata('uang_diterima', $uangDiterima);
            session()->setFlashdata('kembalian', $uangDiterima - $total);
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
            return $this->response->setJSON([
                'valid'   => false,
                'message' => 'Kode promo tidak valid atau sudah kadaluarsa.',
            ]);
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
