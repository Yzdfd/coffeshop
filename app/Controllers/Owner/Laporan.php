<?php

namespace App\Controllers\Owner;

use App\Controllers\BaseController;
use App\Models\TransactionModel;

class Laporan extends BaseController
{
    protected TransactionModel $transactionModel;

    public function __construct()
    {
        $this->transactionModel = new TransactionModel();
    }

    /**
     * Export laporan penjualan owner.
     * Format: CSV (bisa dibuka langsung di Excel).
     */
    public function exportPenjualan()
    {
        $startDate = $this->request->getGet('start_date') ?: date('Y-m-01');
        $endDate   = $this->request->getGet('end_date') ?: date('Y-m-d');

        $rows = $this->transactionModel->getPaidTransactionsWithItems($startDate, $endDate);

        $filename = 'laporan_penjualan_' . $startDate . '_sd_' . $endDate . '.csv';

        $handle = fopen('php://temp', 'r+');

        // HEADER CSV
        fputcsv($handle, [
            'TRX',
            'Tanggal Bayar',
            'Kasir',
            'Meja',
            'Metode Bayar',
            'Menu Dibeli',
            'Total Qty',
            'Total Transaksi',
        ]);

        // GROUPING TRANSAKSI
        $grouped = [];

        foreach ($rows as $r) {

            $trxId = $r['transaksi_id'];

            // kalau transaksi belum ada
            if (!isset($grouped[$trxId])) {

                $grouped[$trxId] = [
                    'trx'            => 'TRX' . str_pad((string)$trxId, 3, '0', STR_PAD_LEFT),
                    'paid_at'        => $r['paid_at'] ?? '',
                    'kasir_name'     => $r['kasir_name'] ?? '-',
                    'table_name'     => !empty($r['table_number'])
                        ? 'Meja ' . $r['table_number']
                        : 'Takeaway',
                    'payment_method' => $r['payment_method'] ?? '-',
                    'menus'          => [],
                    'total_qty'      => 0,
                    'trx_total'      => $r['trx_total'] ?? 0,
                ];
            }

            // gabung menu
            $grouped[$trxId]['menus'][] =
                ($r['menu_name'] ?? '-') . ' x' . ($r['qty'] ?? 0);

            // total qty
            $grouped[$trxId]['total_qty'] += (int)($r['qty'] ?? 0);
        }

        // EXPORT KE CSV
        foreach ($grouped as $g) {

            fputcsv($handle, [
                $g['trx'],
                $g['paid_at'],
                $g['kasir_name'],
                $g['table_name'],
                $g['payment_method'],
                implode(' | ', $g['menus']),
                $g['total_qty'],
                $g['trx_total'],
            ]);
        }

        rewind($handle);

        $csv = stream_get_contents($handle);

        fclose($handle);

        return $this->response
            ->setHeader('Content-Type', 'text/csv; charset=UTF-8')
            ->setHeader(
                'Content-Disposition',
                'attachment; filename="' . $filename . '"'
            )
            ->setBody($csv ?: '');
    }
}