<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * RoleFilter
 * Dipakai dengan argument role yang diizinkan.
 * Contoh penggunaan di Routes: ['filter' => 'role:admin,owner']
 */
class RoleFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        // Kalau belum login sama sekali, redirect ke login dulu
        if (! session()->get('isLoggedIn')) {
            return redirect()->to(base_url('login'))
                ->with('error', 'Silakan login terlebih dahulu.');
        }

        // Cek role user yang sedang login
        $userRole = session()->get('role');

        // Kalau $arguments kosong berarti semua role boleh akses (cukup login)
        if (empty($arguments)) {
            return;
        }

        // Cek apakah role user ada di daftar yang diizinkan
        if (! in_array($userRole, $arguments)) {
            // Catat di session bahwa ada percobaan akses tidak diizinkan
            session()->setFlashdata('error', 'Akses ditolak. Anda tidak memiliki hak untuk halaman ini.');

            // Redirect ke dashboard sesuai role masing-masing
            return redirect()->to(base_url($this->getHomeByRole($userRole)));
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Tidak perlu tindakan after
    }

    /**
     * Tentukan halaman home berdasarkan role
     */
    private function getHomeByRole(string $role): string
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
