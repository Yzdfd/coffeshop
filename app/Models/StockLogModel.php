<?php

namespace App\Models;

use CodeIgniter\Model;

class StockLogModel extends Model
{
    protected $table      = 'stock_logs';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'ingredient_id',
        'order_id',
        'qty_change',
        'reason',
        'logged_at',
    ];

    public $useTimestamps = false;

    public function logUsage(int $ingredientId, int $orderId, float $qtyChange, string $reason): bool
    {
        return (bool) $this->insert([
            'ingredient_id' => $ingredientId,
            'order_id'      => $orderId,
            'qty_change'    => $qtyChange,
            'reason'        => $reason,
            'logged_at'     => date('Y-m-d H:i:s'),
        ]);
    }
}

