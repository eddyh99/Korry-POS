<?php

namespace App\Controllers\Staff;

use App\Controllers\BaseController;
use App\Models\Admin\DashboardModel;

class Dashboard extends BaseController
{
    protected $dashboard;

    public function __construct()
    {
        $this->dashboard = new DashboardModel();
    }

    public function getIndex()
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

    public function getPenjualan()
    {
        $month = date('m');
        $year  = date('Y');

        $result = $this->dashboard->getPenjualan($month, $year);
        $data   = (count($result) === 0) ? [] : $result;

        return $this->response->setJSON($data);
    }

    public function getJualbrand()
    {
        $month = date('m');
        $year  = date('Y');

        $result = $this->dashboard->getBrand($month, $year);
        $data   = (count($result) === 0) ? [] : $result;

        return $this->response->setJSON($data);
    }

    public function getBrandstore()
    {
        $month = date('m');
        $year  = date('Y');

        $result = $this->dashboard->getBrandstore($month, $year);
        $data   = (count($result) === 0) ? [] : $result;

        return $this->response->setJSON($data);
    }

    public function getToptenpenjualan()
    {
        $result = $this->dashboard->toptenpenjualan();
        $data   = (count($result) === 0) ? [] : $result;

        return $this->response->setJSON($data);
    }
}
