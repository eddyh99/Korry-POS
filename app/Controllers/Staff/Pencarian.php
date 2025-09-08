<?php

namespace App\Controllers\Staff;

use App\Controllers\BaseApiController;

class Pencarian extends BaseApiController
{
    public function getIndex()
    {
        $data = [
            'title'     => 'Pencarian',
            'content'   => 'staff/cashier/cari',
            'extra'     => 'staff/cashier/js/js_cash',
            'mn_cari'   => 'active',
            'colset'    => 'collapse',
            'colmas'    => 'collapse',
            'collap'    => 'collapse',
            'colkonsi'  => 'collapse',
            'colwho'    => 'collapse',
        ];

        return view('layout/wrapper', $data);
    }
}
