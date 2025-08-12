<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\admin\DashboardModel;

class Dashboard extends BaseController
{
    protected $dashboard;
    protected $session;

    public function __construct()
    {
        $this->session = session();

        // Cek apakah sudah login
        if (!$this->session->has('logged_status')) {
            // Redirect ke halaman login
            header('Location: ' . base_url('/'));
            exit;
        }

        // Load model
        $this->dashboard = new DashboardModel();
    }

    public function getIndex()
    {
        $data = [
            'title'   => 'Dashboard Area',
            'content' => 'admin/dashboard/index',
            'extra'   => 'admin/dashboard/js/js_index',
            'mn_dash' => 'active',
            'collap'  => 'collapse',
            'colmas'  => 'collapse',
            'colset'  => 'collapse',
        ];

        return view('layout/wrapper', $data);
    }

    public function penjualan()
    {
        $month = date("m");
        $year  = date("Y");

        $result = $this->dashboard->getPenjualan($month, $year);

        $data = (count($result) == 0) ? 0 : $result;

        return $this->response->setJSON($data);
    }

    public function jualbrand()
    {
        $month = date("m");
        $year  = date("Y");

        $result = $this->dashboard->getBrand($month, $year);

        $data = (count($result) == 0) ? 0 : $result;

        return $this->response->setJSON($data);
    }

    public function brandstore()
    {
        $month = date("m");
        $year  = date("Y");

        $result = $this->dashboard->getBrandstore($month, $year);

        $data = (count($result) == 0) ? 0 : $result;

        return $this->response->setJSON($data);
    }
}
