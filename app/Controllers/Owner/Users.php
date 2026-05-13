<?php

namespace App\Controllers\Owner;

use App\Controllers\BaseController;
use App\Models\UserModel;

class Users extends BaseController
{
    protected UserModel $userModel;

    private array $allowedRoles = ['admin', 'waiter', 'kasir', 'dapur', 'owner'];

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    private function sidebarSections(): array
    {
        return [
            [
                'label' => 'Owner',
                'items' => [
                    [
                        'url'    => base_url('owner'),
                        'active' => strpos(current_url(), 'owner') !== false && strpos(current_url(), 'owner/users') === false && strpos(current_url(), 'owner/stok-alert') === false,
                        'icon'   => 'bi bi-bar-chart-line',
                        'text'   => 'Dashboard',
                    ],
                    [
                        'url'    => base_url('owner/stok-alert'),
                        'active' => strpos(current_url(), 'owner/stok-alert') !== false,
                        'icon'   => 'bi bi-exclamation-triangle',
                        'text'   => 'Alert Stok',
                    ],
                ],
            ],
            [
                'label' => 'Manajemen',
                'items' => [
                    [
                        'url'    => base_url('owner/users'),
                        'active' => strpos(current_url(), 'owner/users') !== false,
                        'icon'   => 'bi bi-people',
                        'text'   => 'Kelola User',
                    ],
                ],
            ],
            [
                'label' => 'Akun',
                'items' => [
                    [
                        'url'    => base_url('logout'),
                        'active' => false,
                        'icon'   => 'bi bi-box-arrow-left',
                        'text'   => 'Logout',
                        'class'  => 'nav-logout',
                    ],
                ],
            ],
        ];
    }

    public function index()
    {
        $search     = $this->request->getGet('search');
        $filterRole = $this->request->getGet('role');

        $builder = $this->userModel->db->table('users')
            ->select('id, name, username, role, shift, status, created_at');

        if ($search) {
            $builder->groupStart()
                ->like('name', $search)
                ->orLike('username', $search)
                ->groupEnd();
        }

        if ($filterRole && in_array($filterRole, $this->allowedRoles, true)) {
            $builder->where('role', $filterRole);
        }

        $users = $builder->orderBy('created_at', 'DESC')->get()->getResultArray();

        return view('owner/users/index', [
            'title'          => 'Kelola User',
            'sidebarTitle'   => 'Owner',
            'sidebarSections'=> $this->sidebarSections(),
            'users'          => $users,
            'search'         => $search,
            'filterRole'     => $filterRole,
        ]);
    }

    public function create()
    {
        return view('owner/users/form', [
            'title'           => 'Tambah User',
            'sidebarTitle'    => 'Owner',
            'sidebarSections' => $this->sidebarSections(),
            'formAction'      => base_url('owner/users/store'),
            'errors'          => [],
            'user'            => null,
            'roles'           => $this->allowedRoles,
        ]);
    }

    public function store()
    {
        $rules = [
            'name'             => 'required|min_length[2]|max_length[100]',
            'username'         => 'required|min_length[3]|max_length[50]|is_unique[users.username]',
            'role'             => 'required|in_list[admin,waiter,kasir,dapur,owner]',
            'password'         => 'required|min_length[6]',
            'password_confirm' => 'required|matches[password]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $this->userModel->insert([
            'name'          => $this->request->getPost('name'),
            'username'      => $this->request->getPost('username'),
            'role'          => $this->request->getPost('role'),
            'shift'         => $this->request->getPost('shift'),
            'status'        => $this->request->getPost('status') ?? 'aktif',
            'password_hash' => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
        ]);

        return redirect()->to(base_url('owner/users'))
            ->with('success', 'User berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $user = $this->userModel->find($id);
        if (! $user) {
            return redirect()->to(base_url('owner/users'))->with('error', 'User tidak ditemukan.');
        }

        if (! in_array($user['role'], $this->allowedRoles, true)) {
            return redirect()->to(base_url('owner/users'))->with('error', 'Role user tidak valid.');
        }

        return view('owner/users/form', [
            'title'           => 'Edit User',
            'sidebarTitle'    => 'Owner',
            'sidebarSections' => $this->sidebarSections(),
            'formAction'      => base_url('owner/users/update/' . $id),
            'errors'          => [],
            'user'            => $user,
            'roles'           => $this->allowedRoles,
        ]);
    }

    public function update($id)
    {
        $user = $this->userModel->find($id);
        if (! $user) {
            return redirect()->to(base_url('owner/users'))->with('error', 'User tidak ditemukan.');
        }

        $rules = [
            'name'     => 'required|min_length[2]|max_length[100]',
            'username' => "required|min_length[3]|max_length[50]|is_unique[users.username,id,{$id}]",
            'role'     => 'required|in_list[admin,waiter,kasir,dapur,owner]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->userModel->update($id, [
            'name'   => $this->request->getPost('name'),
            'username' => $this->request->getPost('username'),
            'role'   => $this->request->getPost('role'),
            'shift'  => $this->request->getPost('shift'),
            'status' => $this->request->getPost('status'),
        ]);

        return redirect()->to(base_url('owner/users'))
            ->with('success', 'User berhasil diperbarui.');
    }

    public function resetPassword($id)
    {
        $user = $this->userModel->find($id);
        if (! $user) {
            return redirect()->to(base_url('owner/users'))->with('error', 'User tidak ditemukan.');
        }

        $defaultPassword = 'password123';
        $this->userModel->update($id, [
            'password_hash' => password_hash($defaultPassword, PASSWORD_DEFAULT),
        ]);

        return redirect()->to(base_url('owner/users'))
            ->with('success', 'Password user "' . $user['username'] . '" direset ke: ' . $defaultPassword);
    }

    public function delete($id)
    {
        if ((int) $id === (int) session('user_id')) {
            return redirect()->to(base_url('owner/users'))
                ->with('error', 'Anda tidak bisa menghapus akun sendiri.');
        }

        $user = $this->userModel->find($id);
        if (! $user) {
            return redirect()->to(base_url('owner/users'))->with('error', 'User tidak ditemukan.');
        }

        if (! $this->userModel->delete($id)) {
            return redirect()->to(base_url('owner/users'))->with('error', 'Gagal menghapus user.');
        }

        return redirect()->to(base_url('owner/users'))->with('success', 'User berhasil dihapus.');
    }

    public function toggle($id)
    {
        $user = $this->userModel->find($id);
        if (! $user) {
            return redirect()->to(base_url('owner/users'))->with('error', 'User tidak ditemukan.');
        }

        if (! in_array($user['role'], $this->allowedRoles, true)) {
            return redirect()->to(base_url('owner/users'))->with('error', 'Role user tidak valid.');
        }

        $newStatus = $user['status'] === 'aktif' ? 'nonaktif' : 'aktif';
        $this->userModel->update($id, ['status' => $newStatus]);

        $label = $newStatus === 'aktif' ? 'diaktifkan' : 'dinonaktifkan';
        return redirect()->to(base_url('owner/users'))
            ->with('success', 'User "' . $user['username'] . '" berhasil ' . $label . '.');
    }
}

