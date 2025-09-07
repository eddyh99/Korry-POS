<?php

namespace App\Controllers;

use App\Models\Auth\AuthModel;
use App\Models\Admin\AssignModel;

class Auth extends BaseApiController
{
    protected $authModel;
    protected $assignModel;

    public function __construct()
    {
        $this->authModel   = new AuthModel();
        $this->assignModel = new AssignModel();
    }

    public function postAuth_login()
    {
        $this->validation->setRules([
            'uname' => 'required',
            'pass'  => 'required',
        ]);

        if (!$this->validation->withRequest($this->request)->run()) {
            $this->session->setFlashdata('error', $this->validation->listErrors());
            return redirect()->to('/');
        }

        $uname = esc($this->request->getPost('uname'));
        $pass  = esc($this->request->getPost('pass'));

        $result = $this->authModel->verifyLogin($uname, $pass);

        if (!$result) {
            $this->session->setFlashdata('error', "Username atau password salah, mohon periksa ulang");
            return redirect()->to('/');
        }

        if (empty($result->storeid) || empty($result->store)) {
            $this->session->setFlashdata('error', "{$result->role} belum di assign ke toko");
            return redirect()->to('/');
        }

        $this->session->set('logged_status', [
            'username' => $result->username,
            'nama'     => $result->nama,
            'role'     => $result->role,
            'storeid'  => $result->storeid,
            'store'    => $result->store,
            'is_login' => true,
        ]);

        $this->session->setFlashdata('success', "Berhasil login sebagai {$result->role}");

        return redirect()->to(base_url(
            in_array($result->role, ['Staff', 'Store Manager', 'Office Manager', 'Office Staff'])
                ? "staff/dashboard"
                : "dashboard"
        ));
    }
    
    public function getAuth_logout()
    {
        $this->session->destroy();
        return redirect()->to('/');
    }
}
