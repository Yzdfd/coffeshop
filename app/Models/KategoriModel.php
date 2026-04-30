<?php

namespace App\Models;

use CodeIgniter\Model;

class KategoriModel extends Model
{
    protected $table      = 'categories';
    protected $primaryKey = 'id';

    protected $allowedFields = ['name', 'description', 'sort_order'];

    protected $useTimestamps = false;

    public function getKategoriWithCount()
    {
        return $this->db->table('categories c')
            ->select('c.*, COUNT(m.id) as jumlah_menu')
            ->join('menus m', 'm.category_id = c.id', 'left')
            ->groupBy('c.id')
            ->orderBy('c.sort_order', 'ASC')
            ->get()
            ->getResultArray();
    }

    public function countMenuByKategori($kategoriId): int
    {
        return $this->db->table('menus')
            ->where('category_id', $kategoriId)
            ->countAllResults();
    }
}
