<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\SettingModel;

class Setting extends BaseController
{
    protected $settingModel;

    public function __construct()
    {
        $this->settingModel = new SettingModel();
    }

    public function index()
    {
        $data = [
            'title'   => 'Setting Sistem',
            'setting' => $this->settingModel->getSetting(),
        ];

        return view('admin/setting/index', $data);
    }

    public function save()
    {
        $fields = [
            'nama_cafe',
            'telepon',
            'alamat',
            'footer_struk',
            'pajak',
            'service_charge',
            'mata_uang',
            'manajemen_meja',
            'jumlah_meja',
        ];

        foreach ($fields as $field) {
            $this->settingModel->setSetting($field, $this->request->getPost($field) ?? '');
        }

        return redirect()->to(base_url('admin/setting'))
            ->with('success', 'Setting berhasil disimpan.');
    }
}
