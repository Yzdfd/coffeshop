<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\KategoriModel;

class Kategori extends BaseController
{
    protected $kategoriModel;

    public function __construct()
    {
        $this->kategoriModel = new KategoriModel();
    }

    public function index()
    {
        $data = [
            'title'      => 'Kelola Kategori',
            'kategoris'  => $this->kategoriModel->getKategoriWithCount(),
            'formAction' => base_url('admin/kategori/store'),
            'errors'     => [],
        ];

        return view('admin/kategori/index', $data);
    }

    public function store()
    {
        $rules = [
            'name' => 'required|min_length[2]|max_length[100]|is_unique[categories.name]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $this->kategoriModel->insert([
            'name'        => $this->request->getPost('name'),
            'description' => $this->request->getPost('description'),
            'sort_order'  => $this->request->getPost('sort_order') ?? 0,
        ]);

        return redirect()->to(base_url('admin/kategori'))
            ->with('success', 'Kategori berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $kategori = $this->kategoriModel->find($id);
        if (! $kategori) {
            return redirect()->to(base_url('admin/kategori'))->with('error', 'Kategori tidak ditemukan.');
        }

        $data = [
            'title'        => 'Edit Kategori',
            'kategoris'    => $this->kategoriModel->getKategoriWithCount(),
            'editKategori' => $kategori,
            'formAction'   => base_url('admin/kategori/update/' . $id),
            'errors'       => [],
        ];

        return view('admin/kategori/index', $data);
    }

    public function update($id)
    {
        $rules = [
            'name' => "required|min_length[2]|max_length[100]|is_unique[categories.name,id,{$id}]",
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $this->kategoriModel->update($id, [
            'name'        => $this->request->getPost('name'),
            'description' => $this->request->getPost('description'),
            'sort_order'  => $this->request->getPost('sort_order') ?? 0,
        ]);

        return redirect()->to(base_url('admin/kategori'))
            ->with('success', 'Kategori berhasil diperbarui.');
    }

    public function delete($id)
    {
        $jumlah = $this->kategoriModel->countMenuByKategori($id);
        if ($jumlah > 0) {
            return redirect()->to(base_url('admin/kategori'))
                ->with('error', 'Kategori tidak bisa dihapus, masih digunakan oleh ' . $jumlah . ' menu.');
        }

        $this->kategoriModel->delete($id);

        return redirect()->to(base_url('admin/kategori'))
            ->with('success', 'Kategori berhasil dihapus.');
    }
}
