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

    public function index()
    {
        $search         = $this->request->getGet('search');
        $filterKategori = $this->request->getGet('category_id');

        $builder = $this->menuModel->getMenuWithKategori();

        if ($search) {
            $builder->like('m.name', $search);
        }
        if ($filterKategori) {
            $builder->where('m.category_id', $filterKategori);
        }

        $data = [
            'title'          => 'Manajemen Menu',
            'menus'          => $builder->get()->getResultArray(),
            'kategoris'      => $this->kategoriModel->findAll(),
            'search'         => $search,
            'filterKategori' => $filterKategori,
        ];

        return view('admin/menu/index', $data);
    }

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
            'name'        => 'required|min_length[2]|max_length[100]',
            'category_id' => 'required',
            'price'       => 'required|numeric|greater_than_equal_to[0]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $this->menuModel->insert([
            'name'        => $this->request->getPost('name'),
            'category_id' => $this->request->getPost('category_id'),
            'price'       => $this->request->getPost('price'),
            'hpp'         => $this->request->getPost('hpp') ?? 0,
            'description' => $this->request->getPost('description'),
            'status'      => $this->request->getPost('status') ?? 'available',
        ]);

        return redirect()->to(base_url('admin/menu'))
            ->with('success', 'Menu berhasil ditambahkan.');
    }

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
            'name'        => 'required|min_length[2]|max_length[100]',
            'category_id' => 'required',
            'price'       => 'required|numeric|greater_than_equal_to[0]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $this->menuModel->update($id, [
            'name'        => $this->request->getPost('name'),
            'category_id' => $this->request->getPost('category_id'),
            'price'       => $this->request->getPost('price'),
            'hpp'         => $this->request->getPost('hpp') ?? 0,
            'description' => $this->request->getPost('description'),
            'status'      => $this->request->getPost('status'),
        ]);

        return redirect()->to(base_url('admin/menu'))
            ->with('success', 'Menu berhasil diperbarui.');
    }

    public function delete($id)
    {
        $menu = $this->menuModel->find($id);
        if (! $menu) {
            return redirect()->to(base_url('admin/menu'))->with('error', 'Menu tidak ditemukan.');
        }

        $this->menuModel->delete($id);

        return redirect()->to(base_url('admin/menu'))
            ->with('success', 'Menu berhasil dihapus.');
    }
}
