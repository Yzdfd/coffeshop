<?php

namespace App\Models;

use CodeIgniter\Model;

class KategoriModel extends Model
{
    protected $table      = 'kategoris';
    protected $primaryKey = 'id';

    protected $allowedFields = ['nama_kategori', 'deskripsi'];

    protected $useTimestamps = true;

    /**
     * Ambil semua kategori beserta jumlah menu di tiap kategori.
     */
    public function getKategoriWithCount()
    {
        return $this->db->table('kategoris k')
            ->select('k.*, COUNT(m.id) as jumlah_menu')
            ->join('menus m', 'm.kategori_id = k.id', 'left')
            ->groupBy('k.id')
            ->get()
            ->getResultArray();
    }

    /**
     * Hitung jumlah menu yang menggunakan kategori tertentu.
     */
    public function countMenuByKategori($kategoriId): int
    {
        return $this->db->table('menus')
            ->where('kategori_id', $kategoriId)
            ->countAllResults();
    }
}
