<?php

namespace App\Models;

use CodeIgniter\Model;

class StokModel extends Model
{
    protected $table = 'ingredients';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'supplier_id',
        'name',
        'stock_qty',
        'min_stock',
        'unit',
    ];

    protected $useTimestamps = false; // DB handles timestamps natively

    public function getStokRendah(): array
    {
        return $this->db->table('ingredients')
            ->where('stock_qty <= min_stock', null, false)
            ->get()
            ->getResultArray();
    }

    public function isUsed(int $ingredientId): bool
    {
        $recipeCount = $this->db->table('recipes')
            ->where('ingredient_id', $ingredientId)
            ->countAllResults();

        if ($recipeCount > 0) {
            return true;
        }

        $stockLogCount = $this->db->table('stock_logs')
            ->where('ingredient_id', $ingredientId)
            ->countAllResults();

        return $stockLogCount > 0;
    }
}
