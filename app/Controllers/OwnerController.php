<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class OwnerController extends BaseController
{
    protected $db;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
    }

    public function index()
    {
        $today = date('Y-m-d');

        $totalRevenueRow = $this->db->table('transactions')
            ->selectSum('total')
            ->where('status', 'paid')
            ->get()->getRow();
        $totalRevenue = (float) ($totalRevenueRow->total ?? 0);

        $totalTransactions = $this->db->table('transactions')
            ->where('status', 'paid')
            ->countAllResults();

        $todaySalesRow = $this->db->table('transactions')
            ->selectSum('total')
            ->where('status', 'paid')
            ->where('DATE(paid_at)', $today)
            ->get()->getRow();
        $todaySales = (float) ($todaySalesRow->total ?? 0);

        // ─── CHART: HARIAN (14 hari terakhir) ───────────────────
        $startDaily = date('Y-m-d', strtotime('-13 days'));
        $dailyRows = $this->db->table('transactions')
            ->select("DATE(paid_at) as d, SUM(total) as total")
            ->where('status', 'paid')
            ->where('DATE(paid_at) >=', $startDaily)
            ->groupBy('DATE(paid_at)')
            ->orderBy('d', 'ASC')
            ->get()->getResultArray();

        $dailyMap = [];
        foreach ($dailyRows as $r) {
            $dailyMap[$r['d']] = (float) $r['total'];
        }
        $dailyLabels = [];
        $dailyTotals = [];
        for ($i = 13; $i >= 0; $i--) {
            $d = date('Y-m-d', strtotime("-{$i} days"));
            $dailyLabels[] = date('d M', strtotime($d));
            $dailyTotals[] = $dailyMap[$d] ?? 0;
        }

        // ─── CHART: BULANAN (12 bulan terakhir) ─────────────────
        $startMonth = date('Y-m-01', strtotime('-11 months'));
        $monthlyRows = $this->db->table('transactions')
            ->select("DATE_FORMAT(paid_at, '%Y-%m') as ym, SUM(total) as total")
            ->where('status', 'paid')
            ->where('paid_at >=', $startMonth)
            ->groupBy("DATE_FORMAT(paid_at, '%Y-%m')")
            ->orderBy('ym', 'ASC')
            ->get()->getResultArray();

        $monthlyMap = [];
        foreach ($monthlyRows as $r) {
            $monthlyMap[$r['ym']] = (float) $r['total'];
        }
        $monthlyLabels = [];
        $monthlyTotals = [];
        for ($i = 11; $i >= 0; $i--) {
            $ym = date('Y-m', strtotime("-{$i} months"));
            $monthlyLabels[] = date('M Y', strtotime($ym . '-01'));
            $monthlyTotals[] = $monthlyMap[$ym] ?? 0;
        }

        // ─── TOP 5 MENU TERLARIS ────────────────────────────────
        $topMenus = $this->db->table('order_items oi')
            ->select('m.name as menu_name, SUM(oi.qty) as total_qty')
            ->join('menus m', 'm.id = oi.menu_id', 'left')
            ->join('orders o', 'o.id = oi.order_id', 'left')
            ->where('o.status', 'paid')
            ->where('oi.status !=', 'cancelled')
            ->groupBy('oi.menu_id')
            ->orderBy('total_qty', 'DESC')
            ->limit(5)
            ->get()->getResultArray();

        // ─── PERF KARYAWAN (trx per user/kasir) ─────────────────
        $employeePerf = $this->db->table('transactions t')
            ->select('u.name as user_name, COUNT(t.id) as total_trx, SUM(t.total) as total_sales')
            ->join('users u', 'u.id = t.kasir_id', 'left')
            ->where('t.status', 'paid')
            ->groupBy('t.kasir_id')
            ->orderBy('total_trx', 'DESC')
            ->get()->getResultArray();

        // ─── PESANAN AKTIF SEKARANG ──────────────────────────────
        $activeOrders = $this->db->table('orders')
            ->where('status', 'open')
            ->countAllResults();

        // ─── AVG ORDER VALUE ─────────────────────────────────────
        $avgOrderRow = $this->db->table('transactions')
            ->selectAvg('total')
            ->where('status', 'paid')
            ->get()->getRow();
        $avgOrder = (float) ($avgOrderRow->total ?? 0);

        return view('owner/index', [
            'title'            => 'Dashboard Owner',
            'sidebarTitle'     => 'Owner',
            'sidebarSections'  => [
                [
                    'label' => 'Owner',
                    'items' => [
                        [
                            'url'    => base_url('owner'),
                            'active' => (strpos(current_url(), 'owner') !== false),
                            'icon'   => 'bi bi-bar-chart-line',
                            'text'   => 'Dashboard',
                        ],
                        [
                            'url'    => base_url('owner/stok-alert'),
                            'active' => (strpos(current_url(), 'stok-alert') !== false),
                            'icon'   => 'bi bi-exclamation-triangle',
                            'text'   => 'Alert Stok',
                        ],
                    ],
                ],
                [
                    'label' => 'Manajemen',
                    'items' => [
                        [
                            'url'    => base_url('owner/users'),
                            'active' => (strpos(current_url(), 'owner/users') !== false),
                            'icon'   => 'bi bi-people',
                            'text'   => 'Kelola User',
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
            ],
            'totalRevenue'     => $totalRevenue,
            'totalTransactions'=> $totalTransactions,
            'todaySales'       => $todaySales,
            'activeOrders'     => $activeOrders,
            'avgOrder'         => $avgOrder,
            'dailyLabels'      => $dailyLabels,
            'dailyTotals'      => $dailyTotals,
            'monthlyLabels'    => $monthlyLabels,
            'monthlyTotals'    => $monthlyTotals,
            'topMenus'         => $topMenus,
            'employeePerf'     => $employeePerf,
        ]);
    }

    public function stokAlert()
    {
        // Bahan di bawah stok minimum
        $bahan = $this->db->query("
            SELECT i.*, s.name as supplier_name 
            FROM ingredients i 
            LEFT JOIN suppliers s ON s.id = i.supplier_id
            WHERE i.stock_qty <= i.min_stock
            ORDER BY (i.stock_qty / NULLIF(i.min_stock, 0)) ASC
        ")->getResultArray();

        $allBahan = $this->db->table('ingredients i')
            ->select('i.*, s.name as supplier_name')
            ->join('suppliers s', 's.id = i.supplier_id', 'left')
            ->orderBy('i.name', 'ASC')
            ->get()->getResultArray();

        return view('owner/stok_alert', [
            'title'            => 'Alert Stok Bahan',
            'sidebarTitle'     => 'Owner',
            'sidebarSections'  => [
                [
                    'label' => 'Owner',
                    'items' => [
                        [
                            'url'    => base_url('owner'),
                            'active' => false,
                            'icon'   => 'bi bi-bar-chart-line',
                            'text'   => 'Dashboard',
                        ],
                        [
                            'url'    => base_url('owner/stok-alert'),
                            'active' => true,
                            'icon'   => 'bi bi-exclamation-triangle',
                            'text'   => 'Alert Stok',
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
            ],
            'bahanKritis' => $bahan,
            'allBahan'    => $allBahan,
        ]);
    }

    public function exportPenjualan()
    {
        $startDate = $this->request->getGet('start_date') ?: date('Y-m-01');
        $endDate   = $this->request->getGet('end_date') ?: date('Y-m-d');

        $rows = $this->db->table('transactions t')
            ->select('
                t.id as transaksi_id,
                t.order_id,
                t.payment_method,
                t.subtotal,
                t.tax_amount,
                t.service_amount,
                t.discount_amount,
                t.total,
                t.paid_at,
                u.name as kasir_name,
                o.table_id,
                tb.number as table_number
            ')
            ->join('users u', 'u.id = t.kasir_id', 'left')
            ->join('orders o', 'o.id = t.order_id', 'left')
            ->join('tables tb', 'tb.id = o.table_id', 'left')
            ->where('t.status', 'paid')
            ->where('DATE(t.paid_at) >=', $startDate)
            ->where('DATE(t.paid_at) <=', $endDate)
            ->orderBy('t.paid_at', 'DESC')
            ->get()->getResultArray();

        $filename = 'penjualan_' . $startDate . '_sd_' . $endDate . '.csv';

        $handle = fopen('php://temp', 'r+');
        fputcsv($handle, [
            'ID Transaksi',
            'ID Order',
            'Tanggal Bayar',
            'Kasir',
            'Meja',
            'Metode Bayar',
            'Subtotal',
            'Pajak',
            'Service',
            'Diskon',
            'Total',
        ]);

        foreach ($rows as $r) {
            fputcsv($handle, [
                (string) ($r['transaksi_id'] ?? ''),
                (string) ($r['order_id'] ?? ''),
                (string) ($r['paid_at'] ?? ''),
                (string) ($r['kasir_name'] ?? '-'),
                ! empty($r['table_number']) ? 'Meja ' . $r['table_number'] : 'Takeaway',
                (string) ($r['payment_method'] ?? '-'),
                (float) ($r['subtotal'] ?? 0),
                (float) ($r['tax_amount'] ?? 0),
                (float) ($r['service_amount'] ?? 0),
                (float) ($r['discount_amount'] ?? 0),
                (float) ($r['total'] ?? 0),
            ]);
        }

        rewind($handle);
        $csv = stream_get_contents($handle);
        fclose($handle);

        return $this->response
            ->setHeader('Content-Type', 'text/csv; charset=UTF-8')
            ->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"')
            ->setBody($csv ?: '');
    }
}

