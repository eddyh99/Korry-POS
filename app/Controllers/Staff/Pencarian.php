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
            'collap'    => 'collapse',
            'mn_cari'   => 'active',
        ];

        return view('layout/wrapper', $data);
    }
}
