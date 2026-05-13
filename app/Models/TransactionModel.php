<?php

namespace App\Models;

use CodeIgniter\Model;

class TransactionModel extends Model
{
    protected $table = 'transactions';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'order_id',
        'kasir_id',
        'subtotal',
        'tax_amount',
        'service_amount',
        'discount_amount',
        'total',
        'payment_method',
        'status',
        'paid_at',
    ];

    public function getPaidTransactionsWithItems(string $startDate, string $endDate): array
    {
        // Satu query untuk mendapatkan detail transaksi + item
        return $this->db->table('transactions t')
            ->select([
                't.id as transaksi_id',
                't.order_id',
                't.payment_method',
                't.paid_at',
                'u.name as kasir_name',
                'o.table_id',
                'tb.number as table_number',
                't.subtotal as trx_subtotal',
                't.tax_amount as trx_tax_amount',
                't.service_amount as trx_service_amount',
                't.discount_amount as trx_discount_amount',
                't.total as trx_total',

                'oi.id as order_item_id',
                'm.name as menu_name',
                'oi.qty as qty',
                'oi.unit_price as unit_price',
                '(oi.qty * oi.unit_price) as item_subtotal',
            ])
            ->join('users u', 'u.id = t.kasir_id', 'left')
            ->join('orders o', 'o.id = t.order_id', 'left')
            ->join('order_items oi', 'oi.order_id = o.id', 'left')
            ->join('menus m', 'm.id = oi.menu_id', 'left')
            ->join('tables tb', 'tb.id = o.table_id', 'left')
            ->where('t.status', 'paid')
            ->where('DATE(t.paid_at) >=', $startDate)
            ->where('DATE(t.paid_at) <=', $endDate)
            ->where('oi.status !=', 'cancelled')
            ->orderBy('t.paid_at', 'DESC')
            ->get()
            ->getResultArray();
    }
}

