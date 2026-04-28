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

    // ─── INDEX + FORM TAMBAH ─────────────────────────────────────────────────
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
            'nama_kategori' => 'required|min_length[2]|max_length[80]|is_unique[kategoris.nama_kategori]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $this->kategoriModel->insert([
            'nama_kategori' => $this->request->getPost('nama_kategori'),
            'deskripsi'     => $this->request->getPost('deskripsi'),
        ]);

        return redirect()->to(base_url('admin/kategori'))
            ->with('success', 'Kategori berhasil ditambahkan.');
    }

    // ─── EDIT ────────────────────────────────────────────────────────────────
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
        $kategori = $this->kategoriModel->find($id);
        if (! $kategori) {
            return redirect()->to(base_url('admin/kategori'))->with('error', 'Kategori tidak ditemukan.');
        }

        $rules = [
            'nama_kategori' => "required|min_length[2]|max_length[80]|is_unique[kategoris.nama_kategori,id,{$id}]",
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $this->kategoriModel->update($id, [
            'nama_kategori' => $this->request->getPost('nama_kategori'),
            'deskripsi'     => $this->request->getPost('deskripsi'),
        ]);

        return redirect()->to(base_url('admin/kategori'))
            ->with('success', 'Kategori berhasil diperbarui.');
    }

    // ─── DELETE ─────────────────────────────────────────────────────────────
    public function delete($id)
    {
        $kategori = $this->kategoriModel->find($id);
        if (! $kategori) {
            return redirect()->to(base_url('admin/kategori'))->with('error', 'Kategori tidak ditemukan.');
        }

        // Cek apakah ada menu yang menggunakan kategori ini
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
