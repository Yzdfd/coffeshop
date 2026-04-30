<?php

namespace App\Models;

use CodeIgniter\Model;

class MenuModel extends Model
{
    protected $table      = 'menus';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'category_id', 'name', 'description', 'price', 'hpp', 'status',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = '';

    public function getMenuWithKategori()
    {
        return $this->db->table('menus m')
            ->select('m.*, c.name as nama_kategori')
            ->join('categories c', 'c.id = m.category_id', 'left');
    }
}
