<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\UserModel;

class Users extends BaseController
{
    protected $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    // ─── INDEX ───────────────────────────────────────────────────────────────
    public function index()
    {
        $search     = $this->request->getGet('search');
        $filterRole = $this->request->getGet('role');

        $builder = $this->userModel->builder();
        $builder->select('id, nama_lengkap, username, role, status, created_at');

        if ($search) {
            $builder->groupStart()
                ->like('nama_lengkap', $search)
                ->orLike('username', $search)
                ->groupEnd();
        }
        if ($filterRole) {
            $builder->where('role', $filterRole);
        }

        $data = [
            'title'      => 'Kelola User',
            'users'      => $builder->get()->getResultArray(),
            'search'     => $search,
            'filterRole' => $filterRole,
        ];

        return view('admin/users/index', $data);
    }

    // ─── CREATE ──────────────────────────────────────────────────────────────
    public function create()
    {
        $data = [
            'title'      => 'Tambah User',
            'formAction' => base_url('admin/users/store'),
            'errors'     => [],
            'user'       => null,
        ];

        return view('admin/users/form', $data);
    }

    public function store()
    {
        $rules = [
            'nama_lengkap'    => 'required|min_length[2]|max_length[100]',
            'username'        => 'required|min_length[3]|max_length[50]|is_unique[users.username]',
            'role'            => 'required|in_list[admin,waiter,kasir,dapur,owner]',
            'password'        => 'required|min_length[6]',
            'password_confirm'=> 'required|matches[password]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $this->userModel->insert([
            'nama_lengkap' => $this->request->getPost('nama_lengkap'),
            'username'     => $this->request->getPost('username'),
            'role'         => $this->request->getPost('role'),
            'status'       => $this->request->getPost('status') ?? 'aktif',
            'password'     => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
        ]);

        return redirect()->to(base_url('admin/users'))
            ->with('success', 'User berhasil ditambahkan.');
    }

    // ─── EDIT ────────────────────────────────────────────────────────────────
    public function edit($id)
    {
        $user = $this->userModel->find($id);
        if (! $user) {
            return redirect()->to(base_url('admin/users'))->with('error', 'User tidak ditemukan.');
        }

        $data = [
            'title'      => 'Edit User',
            'formAction' => base_url('admin/users/update/' . $id),
            'errors'     => [],
            'user'       => $user,
        ];

        return view('admin/users/form', $data);
    }

    public function update($id)
    {
        $user = $this->userModel->find($id);
        if (! $user) {
            return redirect()->to(base_url('admin/users'))->with('error', 'User tidak ditemukan.');
        }

        $rules = [
            'nama_lengkap' => 'required|min_length[2]|max_length[100]',
            'username'     => "required|min_length[3]|max_length[50]|is_unique[users.username,id,{$id}]",
            'role'         => 'required|in_list[admin,waiter,kasir,dapur,owner]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $this->userModel->update($id, [
            'nama_lengkap' => $this->request->getPost('nama_lengkap'),
            'username'     => $this->request->getPost('username'),
            'role'         => $this->request->getPost('role'),
            'status'       => $this->request->getPost('status'),
        ]);

        return redirect()->to(base_url('admin/users'))
            ->with('success', 'User berhasil diperbarui.');
    }

    // ─── RESET PASSWORD ──────────────────────────────────────────────────────
    public function resetPassword($id)
    {
        $user = $this->userModel->find($id);
        if (! $user) {
            return redirect()->to(base_url('admin/users'))->with('error', 'User tidak ditemukan.');
        }

        // Reset ke password default: "password123"
        $defaultPassword = 'password123';
        $this->userModel->update($id, [
            'password' => password_hash($defaultPassword, PASSWORD_DEFAULT),
        ]);

        return redirect()->to(base_url('admin/users'))
            ->with('success', 'Password user "' . $user['username'] . '" direset ke: ' . $defaultPassword);
    }

    // ─── DELETE ─────────────────────────────────────────────────────────────
    public function delete($id)
    {
        // Tidak boleh hapus diri sendiri
        if ($id == session('user_id')) {
            return redirect()->to(base_url('admin/users'))
                ->with('error', 'Anda tidak bisa menghapus akun sendiri.');
        }

        $user = $this->userModel->find($id);
        if (! $user) {
            return redirect()->to(base_url('admin/users'))->with('error', 'User tidak ditemukan.');
        }

        $this->userModel->delete($id);

        return redirect()->to(base_url('admin/users'))
            ->with('success', 'User berhasil dihapus.');
    }
}
