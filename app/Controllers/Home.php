<?php

namespace App\Controllers;

class Home extends BaseController
{
    public function getIndex()
    {
        $data = [
            'title'    => 'Login',
            'is_login' => false,
            'content'  => 'login/index',
        ];

        return view('layout/wrapper', $data);
    }
}
