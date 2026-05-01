<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

/**
 * AuthFilter
 *
 * Protects routes by checking if the user is logged in.
 * Usage di Routes.php:
 *   $routes->group('admin', ['filter' => 'auth'], function ($routes) { ... });
 *
 * Atau di Filters.php (globals / filters):
 *   'auth' => ['before' => ['admin/*', 'kasir/*', 'waiter/*', 'dapur/*', 'owner/*']]
 */
class AuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        if (! session()->get('logged_in')) {
            return redirect()->to(base_url('login'))
                ->with('error', 'Silakan login terlebih dahulu.');
        }

        // Cek role jika ada argumen (contoh: ['filter' => 'auth:admin,kasir'])
        if ($arguments) {
            $role = session()->get('role');
            if (! in_array($role, $arguments)) {
                return redirect()->back()
                    ->with('error', 'Anda tidak memiliki akses ke halaman ini.');
            }
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // tidak diperlukan
    }
}