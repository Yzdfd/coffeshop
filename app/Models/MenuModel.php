<?php

namespace App\Models;

use CodeIgniter\Model;

class MenuModel extends Model
{
    protected $table      = 'menus';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'nama_menu', 'kategori_id', 'harga', 'varian',
        'deskripsi', 'status', 'gambar',
    ];

    protected $useTimestamps = true;

    /**
     * Ambil semua menu beserta nama kategori-nya.
     */
    public function getMenuWithKategori()
    {
        return $this->db->table('menus m')
            ->select('m.*, k.nama_kategori')
            ->join('kategoris k', 'k.id = m.kategori_id', 'left');
    }
}
