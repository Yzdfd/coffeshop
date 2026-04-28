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

    // ─── INDEX ───────────────────────────────────────────────────────────────
    public function index()
    {
        $search = $this->request->getGet('search');
        $filter = $this->request->getGet('filter');

        $builder = $this->stokModel->builder('stok_bahan');

        if ($search) {
            $builder->like('nama_bahan', $search);
        }
        if ($filter === 'ok') {
            $builder->where('stok > min_stok');
        } elseif ($filter === 'low') {
            $builder->where('stok > 0')->where('stok <=', 'min_stok', false);
        } elseif ($filter === 'empty') {
            $builder->where('stok', 0);
        }

        $data = [
            'title'  => 'Kelola Stok Bahan',
            'stoks'  => $builder->get()->getResultArray(),
            'search' => $search,
            'filter' => $filter,
        ];

        return view('admin/stok/index', $data);
    }

    // ─── CREATE ──────────────────────────────────────────────────────────────
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
            'nama_bahan' => 'required|min_length[2]|max_length[100]',
            'satuan'     => 'required',
            'stok'       => 'required|numeric|greater_than_equal_to[0]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $this->stokModel->insert([
            'nama_bahan'   => $this->request->getPost('nama_bahan'),
            'satuan'       => $this->request->getPost('satuan'),
            'stok'         => $this->request->getPost('stok'),
            'min_stok'     => $this->request->getPost('min_stok') ?? 5,
            'harga_satuan' => $this->request->getPost('harga_satuan') ?? 0,
            'keterangan'   => $this->request->getPost('keterangan'),
        ]);

        return redirect()->to(base_url('admin/stok'))
            ->with('success', 'Bahan berhasil ditambahkan.');
    }

    // ─── EDIT ────────────────────────────────────────────────────────────────
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
            'nama_bahan' => 'required|min_length[2]|max_length[100]',
            'satuan'     => 'required',
            'stok'       => 'required|numeric|greater_than_equal_to[0]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $this->stokModel->update($id, [
            'nama_bahan'   => $this->request->getPost('nama_bahan'),
            'satuan'       => $this->request->getPost('satuan'),
            'stok'         => $this->request->getPost('stok'),
            'min_stok'     => $this->request->getPost('min_stok'),
            'harga_satuan' => $this->request->getPost('harga_satuan'),
            'keterangan'   => $this->request->getPost('keterangan'),
        ]);

        return redirect()->to(base_url('admin/stok'))
            ->with('success', 'Data bahan berhasil diperbarui.');
    }

    // ─── TAMBAH STOK ─────────────────────────────────────────────────────────
    public function tambah($id)
    {
    $stok = $this->stokModel->find($id);
    if (! $stok) {
        return redirect()->to(base_url('admin/stok'))->with('error', 'Data tidak ditemukan.');
    }

    if ($this->request->getMethod() === 'post') {
        $jumlah = (int) $this->request->getPost('jumlah');
        if ($jumlah <= 0) {
            return redirect()->back()->with('error', 'Jumlah harus lebih dari 0.');
        }

        $stokBaru = (int)$stok['stok'] + $jumlah;

        $this->stokModel->update($id, ['stok' => $stokBaru]);

        return redirect()->to(base_url('admin/stok'))
            ->with('success', 'Stok berhasil ditambahkan sebanyak ' . $jumlah . '.');
    }

    return view('admin/stok/tambah', [
        'title' => 'Tambah Stok: ' . $stok['nama_bahan'],
        'stok'  => $stok,
    ]);
    }

    // ─── DELETE ─────────────────────────────────────────────────────────────
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
