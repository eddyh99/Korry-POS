<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class Loginfilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $user = session()->get('logged_status');
        if (!$user) {
            header("Location:" . base_url());
            exit();
        }        
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do something here
    }
}