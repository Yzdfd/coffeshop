<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UserModel;

class Auth extends BaseController
{
    // ─── Halaman Login ────────────────────────────────────────────────────────
    public function loginPage()
    {
        // Kalau sudah login, redirect sesuai role
        if (session()->get('logged_in')) {
            return $this->redirectByRole(session()->get('role'));
        }

        return view('auth/login');
    }

    // ─── Proses Login ─────────────────────────────────────────────────────────
    public function loginProcess()
    {
        $rules = [
            'username' => 'required|min_length[3]',
            'password' => 'required|min_length[6]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Username dan password harus diisi dengan benar.');
        }

        $username = $this->request->getPost('username');
        $password = $this->request->getPost('password');

        $userModel = new UserModel();
        $user = $userModel->where('username', $username)
                          ->where('status', 'aktif')
                          ->first();

        if (! $user) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Username tidak ditemukan atau akun tidak aktif.');
        }

        if (! password_verify($password, $user['password_hash'])) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Password yang Anda masukkan salah.');
        }

        // Simpan data session
        session()->set([
            'logged_in' => true,
            'user_id'   => $user['id'],
            'name'      => $user['name'],
            'username'  => $user['username'],
            'role'      => $user['role'],
            'shift'     => $user['shift'],
        ]);

        // Catat activity log (opsional, kalau ada ActivityLogModel)
        // $this->logActivity($user['id'], 'login', 'User login ke sistem');

        return $this->redirectByRole($user['role']);
    }

    // ─── Logout ───────────────────────────────────────────────────────────────
    public function logout()
    {
        session()->destroy();
        return redirect()->to(base_url('login'))
            ->with('success', 'Anda berhasil keluar dari sistem.');
    }

    // ─── Helper: Redirect Berdasarkan Role ────────────────────────────────────
    private function redirectByRole(string $role)
    {
        return match ($role) {
            'admin'  => redirect()->to(base_url('admin/dashboard')),
            'kasir'  => redirect()->to(base_url('kasir/dashboard')),
            'waiter' => redirect()->to(base_url('waiter/dashboard')),
            'dapur'  => redirect()->to(base_url('dapur/dashboard')),
            'owner'  => redirect()->to(base_url('owner/dashboard')),
            default  => redirect()->to(base_url('login'))
                            ->with('error', 'Role tidak dikenali. Hubungi admin.'),
        };
    }
}