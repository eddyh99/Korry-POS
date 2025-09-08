<?php

namespace App\Controllers\Staff;

use App\Models\Staff\OpnameModel;

use App\Controllers\BaseApiController;

class Opname extends BaseApiController
{
    protected $opnameModel;

    public function __construct()
    {
        $this->opnameModel = new OpnameModel();
    }

    public function index()
    {
        $data = [
            'title'   => 'Korry - Point Of Sales',
            'content' => 'staff/opname/index',
            'extra'   => 'staff/opname/js/js_index',
            'mn_cash'  => 'active',
            'colset'   => 'collapse',
            'colmas'   => 'collapse',
            'colkonsi' => 'collapse',
            'colwho'   => 'collapse',
            'collap'   => 'collapse',
        ];

        return view('layout/wrapper', $data);
    }

    public function Listdata()
    {
        $result = $this->opnameModel->listOpname();
        return $this->response->setJSON($result);
    }
}
