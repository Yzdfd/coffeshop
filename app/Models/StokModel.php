<?php

namespace App\Models;

use CodeIgniter\Model;

class StokModel extends Model
{
    protected $table      = 'ingredients';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'supplier_id', 'name', 'unit', 'stock_qty', 'min_stock'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at'; // FIX: jangan kosong
    protected $updatedField  = 'updated_at';

    public function getStokRendah(): array
    {
        return $this->db->table($this->table)
            ->where('stock_qty <= min_stock') // FIX utama
            ->get()
            ->getResultArray();
    }
}