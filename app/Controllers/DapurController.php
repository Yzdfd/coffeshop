<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class DapurController extends BaseController
{
    protected $db;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
    }

    public function index()
    {
        // Ambil order yang masih berjalan + item yang perlu diproses dapur
        // Ambil semua order yang masih ada item 'cooking' (termasuk yang sudah paid)
        // atau order 'open' yang punya item 'pending'
        $rows = $this->db->query("
            SELECT
                oi.id as order_item_id,
                oi.order_id,
                oi.menu_id,
                oi.qty,
                oi.notes as item_notes,
                oi.status as item_status,
                o.status as order_status,
                o.notes as order_notes,
                o.ordered_at,
                t.number as table_number,
                m.name as menu_name
            FROM order_items oi
            LEFT JOIN orders o ON o.id = oi.order_id
            LEFT JOIN tables t ON t.id = o.table_id
            LEFT JOIN menus m ON m.id = oi.menu_id
            WHERE o.status IN ('open', 'paid')
              AND oi.status IN ('pending', 'cooking')
            ORDER BY o.ordered_at ASC, oi.id ASC
        ")->getResultArray();

        $orders = [];
        foreach ($rows as $r) {
            $oid = $r['order_id'];
            if (! isset($orders[$oid])) {
                $orders[$oid] = [
                    'order_id'     => $oid,
                    'table_number' => $r['table_number'],
                    'ordered_at'   => $r['ordered_at'],
                    'order_notes'  => $r['order_notes'],
                    'order_status' => $r['order_status'],
                    'items'        => [],
                ];
            }

            $orders[$oid]['items'][] = [
                'order_item_id' => $r['order_item_id'],
                'menu_id'       => $r['menu_id'],
                'menu_name'     => $r['menu_name'],
                'qty'           => $r['qty'],
                'notes'         => $r['item_notes'],
                'status'        => $r['item_status'],
            ];
        }

        return view('dapur/index', [
            'title'           => 'Dashboard Dapur',
            'sidebarTitle'    => 'Dapur',
            'sidebarSections' => $this->sidebarSections('orders'),
            'orders'          => array_values($orders),
        ]);
    }

    /**
     * Helper: sidebar sections dapur (reusable)
     */
    private function sidebarSections(string $activePage = 'orders'): array
    {
        return [
            [
                'label' => 'Dapur',
                'items' => [
                    [
                        'url'    => base_url('dapur'),
                        'active' => $activePage === 'orders',
                        'icon'   => 'bi bi-speedometer2',
                        'text'   => 'Incoming Orders',
                    ],
                    [
                        'url'    => base_url('dapur/stok'),
                        'active' => $activePage === 'stok',
                        'icon'   => 'bi bi-boxes',
                        'text'   => 'Stok Bahan',
                    ],
                    [
                        'url'    => base_url('dapur/resep'),
                        'active' => $activePage === 'resep',
                        'icon'   => 'bi bi-journal-text',
                        'text'   => 'Resep Menu',
                    ],
                ],
            ],
            [
                'label' => 'Akun',
                'items' => [
                    [
                        'url'    => base_url('logout'),
                        'active' => false,
                        'icon'   => 'bi bi-box-arrow-left',
                        'text'   => 'Logout',
                        'class'  => 'nav-logout',
                    ],
                ],
            ],
        ];
    }

    /**
     * Halaman Stok Bahan untuk dapur (read-only)
     */
    public function stok()
    {
        $search = $this->request->getGet('search');
        $filter = $this->request->getGet('filter');

        $builder = $this->db->table('ingredients');

        if ($search) {
            $builder->like('name', $search);
        }
        if ($filter === 'ok') {
            $builder->where('stock_qty > min_stock');
        } elseif ($filter === 'low') {
            $builder->where('stock_qty > 0')->where('stock_qty <= min_stock');
        } elseif ($filter === 'empty') {
            $builder->where('stock_qty', 0);
        }

        $stoks = $builder->orderBy('name', 'ASC')->get()->getResultArray();

        return view('dapur/stok', [
            'title'           => 'Stok Bahan',
            'sidebarTitle'    => 'Dapur',
            'sidebarSections' => $this->sidebarSections('stok'),
            'stoks'           => $stoks,
            'search'          => $search,
            'filter'          => $filter,
        ]);
    }

    /**
     * Halaman daftar semua menu beserta resepnya
     */
    public function resepList()
    {
        $menus = $this->db->table('menus')
            ->where('status', 'active')
            ->orderBy('name', 'ASC')
            ->get()->getResultArray();

        return view('dapur/resep_list', [
            'title'           => 'Resep Menu',
            'sidebarTitle'    => 'Dapur',
            'sidebarSections' => $this->sidebarSections('resep'),
            'menus'           => $menus,
        ]);
    }


    public function resep(int $menuId)
    {
        $menu = $this->db->table('menus')->where('id', $menuId)->get()->getRowArray();
        if (! $menu) {
            return $this->response->setStatusCode(404)->setBody('Menu tidak ditemukan.');
        }

        $bahan = $this->db->table('recipes r')
            ->select('i.name as ingredient_name, r.qty_needed, r.unit, i.stock_qty, i.min_stock')
            ->join('ingredients i', 'i.id = r.ingredient_id', 'left')
            ->where('r.menu_id', $menuId)
            ->get()->getResultArray();

        // Ambil varian jika ada
        $varian = $this->db->table('menu_variants')
            ->where('menu_id', $menuId)
            ->get()->getResultArray();

        return view('dapur/resep', [
            'title'           => 'Resep: ' . $menu['name'],
            'sidebarTitle'    => 'Dapur',
            'sidebarSections' => $this->sidebarSections('resep'),
            'menu'            => $menu,
            'bahan'           => $bahan,
            'varian'          => $varian,
        ]);
    }

    public function updateStatus()
    {
        if (strtolower($this->request->getMethod()) !== 'post') {
            return $this->response->setStatusCode(405)->setJSON([
                'success' => false,
                'message' => 'Method not allowed.',
            ]);
        }

        $action = $this->request->getPost('action');
        $orderId = (int) ($this->request->getPost('order_id') ?? 0);
        $orderItemId = (int) ($this->request->getPost('order_item_id') ?? 0);
        $status = $this->request->getPost('status');

        // ─── CANCEL ORDER ───────────────────────────────────────
        if ($action === 'cancel_order' && $orderId > 0) {
            $order = $this->db->table('orders')->where('id', $orderId)->get()->getRowArray();
            if (! $order) {
                return $this->response->setJSON(['success' => false, 'message' => 'Order tidak ditemukan.']);
            }
            if (! in_array($order['status'], ['open', 'process'], true)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Order tidak bisa dibatalkan karena status sudah ' . $order['status'] . '.',
                ]);
            }

            $this->db->table('orders')->where('id', $orderId)->update(['status' => 'cancelled']);
            $this->db->table('order_items')->where('order_id', $orderId)->update(['status' => 'cancelled']);

            if (! empty($order['table_id'])) {
                $this->db->table('tables')->where('id', $order['table_id'])->update([
                    'status'           => 'available',
                    'current_order_id' => null,
                ]);
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Order #' . $orderId . ' dibatalkan.',
                'data'    => ['order_id' => $orderId, 'order_status' => 'cancelled'],
            ]);
        }

        // ─── UPDATE ITEM STATUS ─────────────────────────────────
        if ($orderItemId <= 0 || ! in_array($status, ['pending', 'cooking', 'ready', 'cancelled'], true)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Data tidak valid.']);
        }

        $item = $this->db->table('order_items')->where('id', $orderItemId)->get()->getRowArray();
        if (! $item) {
            return $this->response->setJSON(['success' => false, 'message' => 'Item tidak ditemukan.']);
        }

        // Validasi transisi status sederhana agar alur tetap rapi
        $current = $item['status'];
        $allowedNext = match ($current) {
            'pending' => ['pending', 'cooking', 'cancelled'],
            'cooking' => ['cooking', 'ready', 'cancelled'],
            'ready'   => ['ready'],
            default   => [$current],
        };
        if (! in_array($status, $allowedNext, true)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Transisi status tidak diizinkan.',
            ]);
        }

        $this->db->table('order_items')->where('id', $orderItemId)->update(['status' => $status]);

        // Sinkronkan status order berdasarkan kondisi item terkini
        $newOrderStatus = $this->recalculateOrderStatus($item['order_id']);

        return $this->response->setJSON([
            'success'      => true,
            'message'      => 'Status berhasil diupdate.',
            'data'         => [
                'order_item_id' => $orderItemId,
                'status'        => $status,
                'order_status'  => $newOrderStatus,
            ],
        ]);
    }

    /**
     * Hitung ulang status order berdasarkan kondisi semua item-nya.
     *
     * Aturan:
     *   - Semua item cancelled          → order = cancelled
     *   - Semua item (non-cancel) ready → order = ready
     *   - Ada item cooking              → order = process
     *   - Semua item pending            → order = open
     *
     * Order yang sudah 'paid' tidak disentuh.
     */
    private function recalculateOrderStatus(int $orderId): string
    {
        $order = $this->db->table('orders')->where('id', $orderId)->get()->getRowArray();
        if (! $order || $order['status'] === 'paid') {
            return $order['status'] ?? 'paid';
        }

        $items = $this->db->table('order_items')
            ->where('order_id', $orderId)
            ->get()->getResultArray();

        if (empty($items)) {
            return $order['status'];
        }

        $statuses   = array_column($items, 'status');
        $nonCancelled = array_filter($statuses, fn($s) => $s !== 'cancelled');

        if (empty($nonCancelled)) {
            // Semua item dibatalkan
            $newStatus = 'cancelled';
            if (! empty($order['table_id'])) {
                $this->db->table('tables')->where('id', $order['table_id'])->update([
                    'status'           => 'available',
                    'current_order_id' => null,
                ]);
            }
        } elseif (count(array_filter($nonCancelled, fn($s) => $s === 'ready')) === count($nonCancelled)) {
            // Semua item (yg aktif) sudah ready
            $newStatus = 'ready';
        } elseif (in_array('cooking', $nonCancelled, true)) {
            // Ada yg sedang dimasak
            $newStatus = 'process';
        } else {
            // Semua masih pending
            $newStatus = 'open';
        }

        if ($order['status'] !== $newStatus) {
            $this->db->table('orders')->where('id', $orderId)->update(['status' => $newStatus]);
        }

        return $newStatus;
    }

}