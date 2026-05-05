<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class Promo extends BaseController
{
    protected $db;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
    }

    // ─── INDEX ───────────────────────────────────────────────
    public function index()
    {
        $filterStatus = $this->request->getGet('status');

        $builder = $this->db->table('promos')->orderBy('id', 'DESC');

        if ($filterStatus) {
            $builder->where('status', $filterStatus);
        }

        return view('admin/promo/index', [
            'title'        => 'Kelola Promo',
            'promos'       => $builder->get()->getResultArray(),
            'filterStatus' => $filterStatus,
        ]);
    }

    // ─── CREATE ──────────────────────────────────────────────
    public function create()
    {
        return view('admin/promo/form', [
            'title'      => 'Tambah Promo',
            'formAction' => base_url('admin/promo/store'),
            'promo'      => [],
            'errors'     => [],
        ]);
    }

    public function store()
    {
        $rules = [
            'code'        => 'required|min_length[2]|max_length[50]|is_unique[promos.code]',
            'type'        => 'required|in_list[percent,fixed]',
            'value'       => 'required|numeric|greater_than[0]',
            'valid_from'  => 'required',
            'valid_until' => 'required',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $this->db->table('promos')->insert([
            'code'        => strtoupper($this->request->getPost('code')),
            'type'        => $this->request->getPost('type'),
            'value'       => $this->request->getPost('value'),
            'valid_from'  => $this->request->getPost('valid_from'),
            'valid_until' => $this->request->getPost('valid_until'),
            'status'      => $this->request->getPost('status') ?? 'active',
        ]);

        return redirect()->to(base_url('admin/promo'))
            ->with('success', 'Promo berhasil ditambahkan.');
    }

    // ─── EDIT ────────────────────────────────────────────────
    public function edit($id)
    {
        $promo = $this->db->table('promos')->where('id', $id)->get()->getRowArray();
        if (!$promo) {
            return redirect()->to(base_url('admin/promo'))->with('error', 'Promo tidak ditemukan.');
        }

        return view('admin/promo/form', [
            'title'      => 'Edit Promo',
            'formAction' => base_url('admin/promo/update/' . $id),
            'promo'      => $promo,
            'errors'     => [],
        ]);
    }

    public function update($id)
    {
        $promo = $this->db->table('promos')->where('id', $id)->get()->getRowArray();
        if (!$promo) {
            return redirect()->to(base_url('admin/promo'))->with('error', 'Promo tidak ditemukan.');
        }

        $rules = [
            'code'        => "required|min_length[2]|max_length[50]|is_unique[promos.code,id,{$id}]",
            'type'        => 'required|in_list[percent,fixed]',
            'value'       => 'required|numeric|greater_than[0]',
            'valid_from'  => 'required',
            'valid_until' => 'required',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $this->db->table('promos')->where('id', $id)->update([
            'code'        => strtoupper($this->request->getPost('code')),
            'type'        => $this->request->getPost('type'),
            'value'       => $this->request->getPost('value'),
            'valid_from'  => $this->request->getPost('valid_from'),
            'valid_until' => $this->request->getPost('valid_until'),
            'status'      => $this->request->getPost('status'),
        ]);

        return redirect()->to(base_url('admin/promo'))
            ->with('success', 'Promo berhasil diperbarui.');
    }

    // ─── TOGGLE STATUS ────────────────────────────────────────
    public function toggle($id)
    {
        $promo = $this->db->table('promos')->where('id', $id)->get()->getRowArray();
        if (!$promo) {
            return redirect()->to(base_url('admin/promo'))->with('error', 'Promo tidak ditemukan.');
        }

        $newStatus = $promo['status'] == 'active' ? 'inactive' : 'active';
        $this->db->table('promos')->where('id', $id)->update(['status' => $newStatus]);

        $label = $newStatus == 'active' ? 'diaktifkan' : 'dinonaktifkan';
        return redirect()->to(base_url('admin/promo'))
            ->with('success', 'Promo "' . $promo['code'] . '" berhasil ' . $label . '.');
    }

    // ─── DELETE ──────────────────────────────────────────────
    public function delete($id)
    {
        $promo = $this->db->table('promos')->where('id', $id)->get()->getRowArray();
        if (!$promo) {
            return redirect()->to(base_url('admin/promo'))->with('error', 'Promo tidak ditemukan.');
        }

        $this->db->table('promos')->where('id', $id)->delete();

        return redirect()->to(base_url('admin/promo'))
            ->with('success', 'Promo "' . $promo['code'] . '" berhasil dihapus.');
    }
}
