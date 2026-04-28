<?php

namespace App\Models;

use CodeIgniter\Model;

class StokModel extends Model
{
    protected $table      = 'stok_bahan';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'nama_bahan', 'satuan', 'stok', 'min_stok', 'harga_satuan', 'keterangan',
    ];

    protected $useTimestamps = true;

    /**
     * Ambil semua bahan yang stoknya <= min_stok (termasuk habis).
     */
    public function getStokRendah(): array
    {
        return $this->db->table('stok_bahan')
            ->where('stok <=', $this->db->protectIdentifiers('min_stok'), false)
            ->get()
            ->getResultArray();
    }
}
