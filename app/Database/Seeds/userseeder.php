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
                'username'      => 'kasir1',
                'password_hash' => password_hash('kasir123', PASSWORD_DEFAULT),
                'role'          => 'kasir',
                'shift'         => 'pagi',
                'status'        => 'aktif',
                'created_at'    => date('Y-m-d H:i:s'),
            ],
            [
                'name'          => 'Kasir Dua',
                'username'      => 'kasir2',
                'password_hash' => password_hash('kasir123', PASSWORD_DEFAULT),
                'role'          => 'kasir',
                'shift'         => 'sore',
                'status'        => 'aktif',
                'created_at'    => date('Y-m-d H:i:s'),
            ],
            [
                'name'          => 'Waiter Satu',
                'username'      => 'waiter1',
                'password_hash' => password_hash('waiter123', PASSWORD_DEFAULT),
                'role'          => 'waiter',
                'shift'         => 'pagi',
                'status'        => 'aktif',
                'created_at'    => date('Y-m-d H:i:s'),
            ],
            [
                'name'          => 'Waiter Dua',
                'username'      => 'waiter2',
                'password_hash' => password_hash('waiter123', PASSWORD_DEFAULT),
                'role'          => 'waiter',
                'shift'         => 'sore',
                'status'        => 'aktif',
                'created_at'    => date('Y-m-d H:i:s'),
            ],
            [
                'name'          => 'Tim Dapur Satu',
                'username'      => 'dapur1',
                'password_hash' => password_hash('dapur123', PASSWORD_DEFAULT),
                'role'          => 'dapur',
                'shift'         => 'pagi',
                'status'        => 'aktif',
                'created_at'    => date('Y-m-d H:i:s'),
            ],
            [
                'name'          => 'Tim Dapur Dua',
                'username'      => 'dapur2',
                'password_hash' => password_hash('dapur123', PASSWORD_DEFAULT),
                'role'          => 'dapur',
                'shift'         => 'sore',
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