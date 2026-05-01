<?php

namespace App\Models;

use CodeIgniter\Model;

class StokModel extends Model
{
    protected $table      = 'ingredients';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'supplier_id', 'name', 'unit', 'stock_qty', 'min_stock', 'price', 'notes',
    ];

    protected $useTimestamps = true;
    protected $createdField  = '';
    protected $updatedField  = 'updated_at';

    public function getStokRendah(): array
    {
        return $this->db->table('ingredients')
            ->where('stock_qty <=', $this->db->protectIdentifiers('min_stock'), false)
            ->get()
            ->getResultArray();
    }
}