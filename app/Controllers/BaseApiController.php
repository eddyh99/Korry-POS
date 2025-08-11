<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

class BaseApiController extends ResourceController
{
    protected $session;
    protected $validation;
    protected $request;

    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        // Jangan lupa panggil parent initController
        parent::initController($request, $response, $logger);

        // Set timezone
        date_default_timezone_set('Asia/Singapore');

        // Inisialisasi service yang sering dipakai
        $this->session   = session();
        $this->validation = service('validation');
        $this->request   = $request;
    }
}
