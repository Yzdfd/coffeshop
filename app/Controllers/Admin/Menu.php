<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\MenuModel;
use App\Models\KategoriModel;

class Menu extends BaseController
{
    protected $menuModel;
    protected $kategoriModel;

    public function __construct()
    {
        $this->menuModel     = new MenuModel();
        $this->kategoriModel = new KategoriModel();
    }

    // ─── INDEX ──────────────────────────────────────────────────────────────
    public function index()
    {
        $search       = $this->request->getGet('search');
        $filterKategori = $this->request->getGet('kategori_id');

        $builder = $this->menuModel->getMenuWithKategori();

        if ($search) {
            $builder->like('m.nama_menu', $search);
        }
        if ($filterKategori) {
            $builder->where('m.kategori_id', $filterKategori);
        }

        $data = [
            'title'         => 'Manajemen Menu',
            'menus'         => $builder->get()->getResultArray(),
            'kategoris'     => $this->kategoriModel->findAll(),
            'search'        => $search,
            'filterKategori'=> $filterKategori,
        ];

        return view('admin/menu/index', $data);
    }

    // ─── CREATE ─────────────────────────────────────────────────────────────
    public function create()
    {
        $data = [
            'title'      => 'Tambah Menu',
            'kategoris'  => $this->kategoriModel->findAll(),
            'formAction' => base_url('admin/menu/store'),
            'errors'     => [],
            'menu'       => [],
        ];

        return view('admin/menu/form', $data);
    }

    public function store()
    {
        $rules = [
            'nama_menu'   => 'required|min_length[2]|max_length[100]',
            'kategori_id' => 'required|is_not_unique[kategoris.id]',
            'harga'       => 'required|numeric|greater_than_equal_to[0]',
            'gambar'      => 'is_image[gambar]|max_size[gambar,2048]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $gambar = $this->request->getFile('gambar');
        $namaGambar = null;
        if ($gambar && $gambar->isValid() && ! $gambar->hasMoved()) {
            $namaGambar = $gambar->getRandomName();
            $gambar->move(ROOTPATH . 'public/uploads/menu', $namaGambar);
        }

        $this->menuModel->insert([
            'nama_menu'   => $this->request->getPost('nama_menu'),
            'kategori_id' => $this->request->getPost('kategori_id'),
            'harga'       => $this->request->getPost('harga'),
            'varian'      => $this->request->getPost('varian'),
            'deskripsi'   => $this->request->getPost('deskripsi'),
            'status'      => $this->request->getPost('status') ?? 'tersedia',
            'gambar'      => $namaGambar,
        ]);

        return redirect()->to(base_url('admin/menu'))
            ->with('success', 'Menu berhasil ditambahkan.');
    }

    // ─── EDIT ────────────────────────────────────────────────────────────────
    public function edit($id)
    {
        $menu = $this->menuModel->find($id);
        if (! $menu) {
            return redirect()->to(base_url('admin/menu'))->with('error', 'Menu tidak ditemukan.');
        }

        $data = [
            'title'      => 'Edit Menu',
            'kategoris'  => $this->kategoriModel->findAll(),
            'formAction' => base_url('admin/menu/update/' . $id),
            'errors'     => [],
            'menu'       => $menu,
        ];

        return view('admin/menu/form', $data);
    }

    public function update($id)
    {
        $menu = $this->menuModel->find($id);
        if (! $menu) {
            return redirect()->to(base_url('admin/menu'))->with('error', 'Menu tidak ditemukan.');
        }

        $rules = [
            'nama_menu'   => 'required|min_length[2]|max_length[100]',
            'kategori_id' => 'required|is_not_unique[kategoris.id]',
            'harga'       => 'required|numeric|greater_than_equal_to[0]',
            'gambar'      => 'is_image[gambar]|max_size[gambar,2048]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $gambar = $this->request->getFile('gambar');
        $namaGambar = $menu['gambar'];

        if ($gambar && $gambar->isValid() && ! $gambar->hasMoved()) {
            // Hapus gambar lama
            if ($namaGambar && file_exists(ROOTPATH . 'public/uploads/menu/' . $namaGambar)) {
                unlink(ROOTPATH . 'public/uploads/menu/' . $namaGambar);
            }
            $namaGambar = $gambar->getRandomName();
            $gambar->move(ROOTPATH . 'public/uploads/menu', $namaGambar);
        }

        $this->menuModel->update($id, [
            'nama_menu'   => $this->request->getPost('nama_menu'),
            'kategori_id' => $this->request->getPost('kategori_id'),
            'harga'       => $this->request->getPost('harga'),
            'varian'      => $this->request->getPost('varian'),
            'deskripsi'   => $this->request->getPost('deskripsi'),
            'status'      => $this->request->getPost('status'),
            'gambar'      => $namaGambar,
        ]);

        return redirect()->to(base_url('admin/menu'))
            ->with('success', 'Menu berhasil diperbarui.');
    }

    // ─── DELETE ─────────────────────────────────────────────────────────────
    public function delete($id)
    {
        $menu = $this->menuModel->find($id);
        if (! $menu) {
            return redirect()->to(base_url('admin/menu'))->with('error', 'Menu tidak ditemukan.');
        }

        if ($menu['gambar'] && file_exists(ROOTPATH . 'public/uploads/menu/' . $menu['gambar'])) {
            unlink(ROOTPATH . 'public/uploads/menu/' . $menu['gambar']);
        }

        $this->menuModel->delete($id);

        return redirect()->to(base_url('admin/menu'))
            ->with('success', 'Menu berhasil dihapus.');
    }
}
