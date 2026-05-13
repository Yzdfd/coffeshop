<?php

namespace App\Models;

use CodeIgniter\Model;

class RecipeModel extends Model
{
    protected $table      = 'recipes';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'menu_id',
        'ingredient_id',
        'qty_needed',
        'unit',
    ];

    /**
     * Ambil daftar resep (bahan) untuk satu menu lengkap dengan informasi stok.
     */
    public function getByMenu(int $menuId): array
    {
        return $this->db->table('recipes r')
            ->select('r.*, i.name as ingredient_name, i.stock_qty, i.min_stock, i.unit as ingredient_unit')
            ->join('ingredients i', 'i.id = r.ingredient_id', 'left')
            ->where('r.menu_id', $menuId)
            ->get()
            ->getResultArray();
    }
}

