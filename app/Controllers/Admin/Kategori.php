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

    /**
     * Encode icon & color ke dalam string description.
     * Format: [icon:☕][color:#6f4e37] deskripsi_asli
     */
    private function buildDescription(string $desc, string $icon, string $color): string
    {
        $meta = '';
        if ($icon)  $meta .= "[icon:{$icon}]";
        if ($color && $color !== '#6c757d') $meta .= "[color:{$color}]";
        $desc = trim($desc);
        return $meta ? $meta . ($desc ? " {$desc}" : '') : $desc;
    }

    public function index()
    {
        $data = [
            'title' => 'Kelola Kategori',
            'kategoris' => $this->kategoriModel->getKategoriWithCount(),
            'formAction' => base_url('admin/kategori/store'),
            'errors' => [],
        ];

        return view('admin/kategori/index', $data);
    }

    public function store()
    {
        $rules = [
            'name' => 'required|min_length[2]|max_length[100]|is_unique[categories.name]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $icon  = $this->request->getPost('icon_kategori') ?? '';
        $color = $this->request->getPost('color_kategori') ?? '';
        $desc  = $this->request->getPost('description') ?? '';

        $this->kategoriModel->insert([
            'name'        => $this->request->getPost('name'),
            'description' => $this->buildDescription($desc, $icon, $color),
            'sort_order'  => $this->request->getPost('sort_order') ?? 0,
        ]);

        return redirect()->to(base_url('admin/kategori'))
            ->with('success', 'Kategori berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $kategori = $this->kategoriModel->find($id);
        if (!$kategori) {
            return redirect()->to(base_url('admin/kategori'))->with('error', 'Kategori tidak ditemukan.');
        }

        $data = [
            'title' => 'Edit Kategori',
            'kategoris' => $this->kategoriModel->getKategoriWithCount(),
            'editKategori' => $kategori,
            'formAction' => base_url('admin/kategori/update/' . $id),
            'errors' => [],
        ];

        return view('admin/kategori/index', $data);
    }

    public function update($id)
    {
        $rules = [
            'name' => "required|min_length[2]|max_length[100]|is_unique[categories.name,id,{$id}]",
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $icon  = $this->request->getPost('icon_kategori') ?? '';
        $color = $this->request->getPost('color_kategori') ?? '';
        $desc  = $this->request->getPost('description') ?? '';

        $this->kategoriModel->update($id, [
            'name'        => $this->request->getPost('name'),
            'description' => $this->buildDescription($desc, $icon, $color),
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
                ->with('error', 'Kategori tidak bisa dihapus karena masih digunakan oleh ' . $jumlah . ' menu.');
        }

        try {
            $this->kategoriModel->delete($id);
        } catch (\Throwable $e) {
            return redirect()->to(base_url('admin/kategori'))
                ->with('error', 'Kategori tidak bisa dihapus karena masih terhubung dengan data lain.');
        }

        return redirect()->to(base_url('admin/kategori'))
            ->with('success', 'Kategori berhasil dihapus.');
    }
}