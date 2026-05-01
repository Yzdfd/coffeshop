<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table      = 'users';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'name', 'username', 'password_hash', 'role', 'shift', 'status',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = '';

    // Jangan expose password_hash di response biasa
    protected $hidden = ['password_hash'];

    /**
     * Cari user aktif berdasarkan username (untuk kebutuhan login).
     * Catatan: method ini me-return row DENGAN password_hash (unhide sementara).
     */
    public function findForLogin(string $username): ?array
    {
        return $this->select('id, name, username, password_hash, role, shift, status')
                    ->where('username', $username)
                    ->where('status', 'aktif')
                    ->first();
    }
}