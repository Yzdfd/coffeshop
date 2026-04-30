<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\MenuModel;
use App\Models\KategoriModel;
use App\Models\StokModel;
use App\Models\UserModel;

class Dashboard extends BaseController
{
    public function index()
    {
        $menuModel     = new MenuModel();
        $kategoriModel = new KategoriModel();
        $stokModel     = new StokModel();
        $userModel     = new UserModel();

        $stokPeringatan = $stokModel->getStokRendah();

        $data = [
            'title'          => 'Dashboard',
            'totalMenu'      => $menuModel->countAll(),
            'totalKategori'  => $kategoriModel->countAll(),
            'totalUser'      => $userModel->countAll(),
            'stokRendah'     => count($stokPeringatan),
            'stokPeringatan' => $stokPeringatan,
        ];

        return view('admin/dashboard', $data);
    }
}
