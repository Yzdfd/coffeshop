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

    public function index()
    {
        $search     = $this->request->getGet('search');
        $filterRole = $this->request->getGet('role');

        $builder = $this->userModel->db->table('users');
        $builder->select('id, name, username, role, shift, status, created_at');

        if ($search) {
            $builder->groupStart()
                ->like('name', $search)
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
            'name'            => 'required|min_length[2]|max_length[100]',
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
            'name'          => $this->request->getPost('name'),
            'username'      => $this->request->getPost('username'),
            'role'          => $this->request->getPost('role'),
            'shift'         => $this->request->getPost('shift'),
            'status'        => $this->request->getPost('status') ?? 'active',
            'password_hash' => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
        ]);

        return redirect()->to(base_url('admin/users'))
            ->with('success', 'User berhasil ditambahkan.');
    }

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
        $rules = [
            'name'     => 'required|min_length[2]|max_length[100]',
            'username' => "required|min_length[3]|max_length[50]|is_unique[users.username,id,{$id}]",
            'role'     => 'required|in_list[admin,waiter,kasir,dapur,owner]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $this->userModel->update($id, [
            'name'     => $this->request->getPost('name'),
            'username' => $this->request->getPost('username'),
            'role'     => $this->request->getPost('role'),
            'shift'    => $this->request->getPost('shift'),
            'status'   => $this->request->getPost('status'),
        ]);

        return redirect()->to(base_url('admin/users'))
            ->with('success', 'User berhasil diperbarui.');
    }

    public function resetPassword($id)
    {
        $user = $this->userModel->find($id);
        if (! $user) {
            return redirect()->to(base_url('admin/users'))->with('error', 'User tidak ditemukan.');
        }

        $defaultPassword = 'password123';
        $this->userModel->update($id, [
            'password_hash' => password_hash($defaultPassword, PASSWORD_DEFAULT),
        ]);

        return redirect()->to(base_url('admin/users'))
            ->with('success', 'Password user "' . $user['username'] . '" direset ke: ' . $defaultPassword);
    }

    public function delete($id)
    {
        if ($id == session('user_id')) {
            return redirect()->to(base_url('admin/users'))
                ->with('error', 'Anda tidak bisa menghapus akun sendiri.');
        }

        $this->userModel->delete($id);

        return redirect()->to(base_url('admin/users'))
            ->with('success', 'User berhasil dihapus.');
    }
}
