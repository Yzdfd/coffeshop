<?php

namespace App\Controllers;

use App\Models\UserModel;

class Auth extends BaseController
{
    protected $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    /**
     * Tampilkan halaman login
     */
    public function login()
    {
        // Kalau sudah login, redirect ke dashboard sesuai role
        if (session()->get('isLoggedIn')) {
            return redirect()->to(base_url($this->getDashboardByRole(session()->get('role'))));
        }

        return view('auth/login', [
            'title' => 'Login - Café System',
        ]);
    }

    /**
     * Proses login form
     */
    public function loginProcess()
    {
        $rules = [
            'username' => 'required|min_length[3]',
            'password' => 'required|min_length[6]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $username = $this->request->getPost('username');
        $password = $this->request->getPost('password');

        // Cari user berdasarkan username
        // Gunakan db builder langsung karena UserModel punya $hidden = ['password_hash']
        $user = $this->userModel->db->table('users')
            ->where('username', $username)
            ->get()
            ->getRowArray();

        // Validasi: user tidak ditemukan
        if (! $user) {
            return redirect()->back()->withInput()
                ->with('error', 'Username atau password salah.');
        }

        // Validasi: akun nonaktif
        if ($user['status'] !== 'active') {
            return redirect()->back()->withInput()
                ->with('error', 'Akun Anda tidak aktif. Hubungi administrator.');
        }

        // Validasi: password salah
        if (! password_verify($password, $user['password_hash'])) {
            return redirect()->back()->withInput()
                ->with('error', 'Username atau password salah.');
        }

        // Login berhasil — simpan data ke session
        session()->set([
            'isLoggedIn' => true,
            'user_id'    => $user['id'],
            'name'       => $user['name'],
            'username'   => $user['username'],
            'role'       => $user['role'],
        ]);

        // Log aktivitas login (jika activity_logs tersedia)
        try {
            $this->userModel->db->table('activity_logs')->insert([
                'user_id'     => $user['id'],
                'action'      => 'login',
                'description' => 'User ' . $user['username'] . ' (' . $user['role'] . ') login',
                'ip_address'  => $this->request->getIPAddress(),
            ]);
        } catch (\Throwable $e) {
            // Abaikan error log, jangan sampai login gagal karena ini
        }

        // Redirect ke dashboard sesuai role
        $destination = $this->getDashboardByRole($user['role']);

        return redirect()->to(base_url($destination))
            ->with('success', 'Selamat datang, ' . $user['name'] . '! 👋');
    }

    /**
     * Proses logout
     */
    public function logout()
    {
        // Log aktivitas logout
        if (session()->get('isLoggedIn')) {
            try {
                $this->userModel->db->table('activity_logs')->insert([
                    'user_id'     => session()->get('user_id'),
                    'action'      => 'logout',
                    'description' => 'User ' . session()->get('username') . ' logout',
                    'ip_address'  => $this->request->getIPAddress(),
                ]);
            } catch (\Throwable $e) {
                // Abaikan
            }
        }

        session()->destroy();

        return redirect()->to(base_url('login'))
            ->with('success', 'Anda berhasil logout.');
    }

    /**
     * Tentukan dashboard tujuan berdasarkan role
     */
    private function getDashboardByRole(string $role): string
    {
        return match ($role) {
            'admin'  => 'admin/dashboard',
            'owner'  => 'owner/dashboard',
            'kasir'  => 'kasir/dashboard',
            'waiter' => 'waiter/dashboard',
            'dapur'  => 'dapur/dashboard',
            default  => 'login',
        };
    }
}
