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
        fputcsv($handle, [
            'TRX',
            'Tanggal Bayar',
            'Kasir',
            'Meja',
            'Metode Bayar',
            'Menu Dibeli',
            'Qty',
            'Subtotal Item',
            'Total Transaksi',
        ]);

        foreach ($rows as $r) {
            $trxCode = 'TRX' . str_pad((string) ((int) ($r['transaksi_id'] ?? 0)), 3, '0', STR_PAD_LEFT);

            $tableName = ! empty($r['table_number'])
                ? 'Meja ' . $r['table_number']
                : 'Takeaway';

            fputcsv($handle, [
                $trxCode,
                (string) ($r['paid_at'] ?? ''),
                (string) ($r['kasir_name'] ?? '-'),
                (string) $tableName,
                (string) ($r['payment_method'] ?? '-'),
                (string) ($r['menu_name'] ?? '-'),
                (int) ($r['qty'] ?? 0),
                (float) ($r['item_subtotal'] ?? 0),
                (float) ($r['trx_total'] ?? 0),
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

