<?php

namespace App\Models;

use CodeIgniter\Model;

class MenuModel extends Model
{
    protected $table = 'menus';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'category_id',
        'name',
        'description',
        'price',
        'hpp',
        'status',
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = '';

    public function getMenuWithKategori()
    {
        return $this->db->table('menus m')
            ->select('m.*, c.name as nama_kategori')
            ->join('categories c', 'c.id = m.category_id', 'left');
    }

    public function countRecipesByMenu(int $menuId): int
    {
        return $this->db->table('recipes')
            ->where('menu_id', $menuId)
            ->countAllResults();
    }

    public function countOrderItemsByMenu(int $menuId): int
    {
        return $this->db->table('order_items')
            ->where('menu_id', $menuId)
            ->countAllResults();
    }

    public function isUsed(int $menuId): bool
    {
        if ($this->countRecipesByMenu($menuId) > 0) {
            return true;
        }

        if ($this->countOrderItemsByMenu($menuId) > 0) {
            return true;
        }

        if ($this->db->tableExists('transaction_items')) {
            $count = $this->db->table('transaction_items')
                ->where('menu_id', $menuId)
                ->countAllResults();

            if ($count > 0) {
                return true;
            }
        }

        return false;
    }
}
