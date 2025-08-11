<?php

namespace App\Controllers\Staff;

use App\Controllers\BaseController;
use App\Models\Admin\DashboardModel;

class Dashboard extends BaseController
{
    protected $dashboard;
    protected $session;

    public function __construct()
    {
        $this->session = session();

        // Cek apakah sudah login
        if (!$this->session->has('logged_status')) {
            return redirect()->to('/')->send();
        }

        // Load model
        $this->dashboard = new DashboardModel();
    }

    public function index()
    {
        $data = [
            'title'     => 'Dashboard Area',
            'content'   => 'staff/dashboard/index',
            'extra'     => 'staff/dashboard/js/js_index',
            'extracss'  => 'staff/dashboard/css/dash_css',
            'mn_dash'   => 'active',
            'collap'    => 'collapse',
        ];

        return view('layout/wrapper', $data);
    }

    public function penjualan()
    {
        $month = date('m');

        $result = $this->dashboard->getPenjualan($month);

        $data = (count($result) === 0) ? 0 : $result;

        return $this->response->setJSON($data);
    }

    public function jualbrand()
    {
        $month = date('m');

        $result = $this->dashboard->getBrand($month);

        $data = (count($result) === 0) ? 0 : $result;

        return $this->response->setJSON($data);
    }

    public function brandstore()
    {
        $month = date('m');

        $result = $this->dashboard->getBrandstore($month);

        $data = (count($result) === 0) ? 0 : $result;

        return $this->response->setJSON($data);
    }
}
