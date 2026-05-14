<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run()
    {
        $users = [
            [
                'name'          => 'Administrator',
                'username'      => 'admin',
                'password_hash' => password_hash('admin123', PASSWORD_DEFAULT),
                'role'          => 'admin',
                'shift'         => 'pagi',
                'status'        => 'aktif',
                'created_at'    => date('Y-m-d H:i:s'),
            ],
            [
                'name'          => 'Owner Utama',
                'username'      => 'owner',
                'password_hash' => password_hash('owner123', PASSWORD_DEFAULT),
                'role'          => 'owner',
                'shift'         => 'pagi',
                'status'        => 'aktif',
                'created_at'    => date('Y-m-d H:i:s'),
            ],
            [
                'name'          => 'Kasir Satu',
                'username'      => 'kasir',
                'password_hash' => password_hash('kasir123', PASSWORD_DEFAULT),
                'role'          => 'kasir',
                'shift'         => 'pagi',
                'status'        => 'aktif',
                'created_at'    => date('Y-m-d H:i:s'),
            ],

            [
                'name'          => 'Tim Dapur Satu',
                'username'      => 'dapur',
                'password_hash' => password_hash('dapur123', PASSWORD_DEFAULT),
                'role'          => 'dapur',
                'shift'         => 'pagi',
                'status'        => 'aktif',
                'created_at'    => date('Y-m-d H:i:s'),
            ],

        ];

        // Hindari duplikasi jika seeder dijalankan ulang
        foreach ($users as $user) {
            $exists = $this->db->table('users')
                               ->where('username', $user['username'])
                               ->countAllResults();

            if ($exists === 0) {
                $this->db->table('users')->insert($user);
            }
        }
    }
}