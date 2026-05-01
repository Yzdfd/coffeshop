<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\StokModel;

class Stok extends BaseController
{
    protected $stokModel;

    public function __construct()
    {
        $this->stokModel = new StokModel();
    }

    public function index()
    {
        $search = $this->request->getGet('search');
        $filter = $this->request->getGet('filter');

        $builder = $this->stokModel->db->table('ingredients');

        if ($search) {
            $builder->like('name', $search);
        }
        if ($filter === 'ok') {
            $builder->where('stock_qty > min_stock');
        } elseif ($filter === 'low') {
            $builder->where('stock_qty > 0')->where('stock_qty <= min_stock');
        } elseif ($filter === 'empty') {
            $builder->where('stock_qty', 0);
        }

        $data = [
            'title'  => 'Kelola Stok Bahan (Ingredients)',
            'stoks'  => $builder->get()->getResultArray(),
            'search' => $search,
            'filter' => $filter,
        ];

        return view('admin/stok/index', $data);
    }

    public function create()
    {
        $data = [
            'title'      => 'Tambah Bahan',
            'formAction' => base_url('admin/stok/store'),
            'errors'     => [],
            'stok'       => [],
        ];

        return view('admin/stok/form', $data);
    }

    public function store()
    {
        $rules = [
            'name'  => 'required|min_length[2]|max_length[100]',
            'unit'  => 'required',
            'stock' => 'required|numeric|greater_than_equal_to[0]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $this->stokModel->insert([
            'name'      => $this->request->getPost('name'),
            'unit'      => $this->request->getPost('unit'),
            'stock_qty' => $this->request->getPost('stock_qty'),
            'min_stock' => $this->request->getPost('min_stock') ?? 5,
        ]);

        return redirect()->to(base_url('admin/stok'))
            ->with('success', 'Bahan berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $stok = $this->stokModel->find($id);
        if (! $stok) {
            return redirect()->to(base_url('admin/stok'))->with('error', 'Data tidak ditemukan.');
        }

        $data = [
            'title'      => 'Edit Bahan',
            'formAction' => base_url('admin/stok/update/' . $id),
            'errors'     => [],
            'stok'       => $stok,
        ];

        return view('admin/stok/form', $data);
    }

    public function update($id)
    {
        $stok = $this->stokModel->find($id);
        if (! $stok) {
            return redirect()->to(base_url('admin/stok'))->with('error', 'Data tidak ditemukan.');
        }

        $rules = [
            'name'  => 'required|min_length[2]|max_length[100]',
            'unit'  => 'required',
            'stock' => 'required|numeric|greater_than_equal_to[0]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $this->stokModel->update($id, [
            'name'      => $this->request->getPost('name'),
            'unit'      => $this->request->getPost('unit'),
            'stock_qty' => $this->request->getPost('stock_qty'),
            'min_stock' => $this->request->getPost('min_stock'),
        ]);

        return redirect()->to(base_url('admin/stok'))
            ->with('success', 'Data bahan berhasil diperbarui.');
    }

    public function tambah($id)
    {
        $stok = $this->stokModel->find($id);
        if (! $stok) {
            return redirect()->to(base_url('admin/stok'))->with('error', 'Data tidak ditemukan.');
        }

        if ($this->request->getMethod() === 'post') {
            $jumlah = (float) $this->request->getPost('jumlah');
            if ($jumlah <= 0) {
                return redirect()->back()->with('error', 'Jumlah harus lebih dari 0.');
            }

            $stokBaru = (float)$stok['stock_qty'] + $jumlah;
            $this->stokModel->update($id, ['stock_qty' => $stokBaru]);

            return redirect()->to(base_url('admin/stok'))
                ->with('success', 'Stok berhasil ditambahkan sebanyak ' . $jumlah . ' ' . $stok['unit'] . '.');
        }

        return view('admin/stok/tambah', [
            'title' => 'Tambah Stok: ' . $stok['name'],
            'stok'  => $stok,
        ]);
    }

    public function delete($id)
    {
        $stok = $this->stokModel->find($id);
        if (! $stok) {
            return redirect()->to(base_url('admin/stok'))->with('error', 'Data tidak ditemukan.');
        }

        $this->stokModel->delete($id);

        return redirect()->to(base_url('admin/stok'))
            ->with('success', 'Bahan berhasil dihapus.');
    }
}