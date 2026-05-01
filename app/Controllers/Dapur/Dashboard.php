<?php

namespace App\Controllers\Dapur;

use App\Controllers\BaseController;

class Dashboard extends BaseController
{
    public function index()
    {
        $data = [
            'title' => 'Dashboard Dapur'
        ];
        return view('dapur/dashboard', $data);
    }
}