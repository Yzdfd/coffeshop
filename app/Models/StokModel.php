<?php

namespace App\Models;

use CodeIgniter\Model;

class StokModel extends Model
{
    protected $table      = 'inventory';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'supplier_id', 'name', 'unit', 'stock', 'min_stock', 'price', 'notes',
    ];

    protected $useTimestamps = true;
    protected $createdField  = '';
    protected $updatedField  = 'updated_at';

    public function getStokRendah(): array
    {
        return $this->db->table('inventory')
            ->where('stock <=', $this->db->protectIdentifiers('min_stock'), false)
            ->get()
            ->getResultArray();
    }
}
