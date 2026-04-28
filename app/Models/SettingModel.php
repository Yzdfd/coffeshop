<?php

namespace App\Models;

use CodeIgniter\Model;

class SettingModel extends Model
{
    protected $table      = 'settings';
    protected $primaryKey = 'id';

    protected $allowedFields = ['key', 'value'];

    /**
     * Ambil semua setting sebagai array asosiatif [key => value].
     */
    public function getSetting(): array
    {
        $rows = $this->db->table('settings')->get()->getResultArray();

        $result = [];
        foreach ($rows as $row) {
            $result[$row['key']] = $row['value'];
        }

        return $result;
    }

    /**
     * Simpan atau update satu setting berdasarkan key.
     */
    public function setSetting(string $key, $value): void
    {
        $existing = $this->db->table('settings')->where('key', $key)->get()->getRowArray();

        if ($existing) {
            $this->db->table('settings')->where('key', $key)->update(['value' => $value]);
        } else {
            $this->db->table('settings')->insert(['key' => $key, 'value' => $value]);
        }
    }
}
