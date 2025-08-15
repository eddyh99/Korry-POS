<?php

namespace App\Controllers;

use App\Models\Auth\AuthModel;
use App\Models\Admin\AssignModel;

class Auth extends BaseApiController
{
    protected $authModel;
    protected $assignModel;

    // Gunakan initController, jangan __construct
    public function initController($request, $response, $logger)
    {
        // Panggil parent
        parent::initController($request, $response, $logger);

        // Inisialisasi model
        $this->authModel   = new AuthModel();
        $this->assignModel = new AssignModel();
    }

    public function getIndex()
    {
        $data = [
            'title'    => 'Login',
            'is_login' => false,
            'content'  => 'login/index',
        ];

        return view('layout/wrapper', $data);
    }

    // public function postAuth_login()
    // {
    //     $this->validation->setRules([
    //         'uname' => 'required',
    //         'pass'  => 'required',
    //     ]);

    //     if (!$this->validation->withRequest($this->request)->run()) {
    //         $this->session->setFlashdata('error', $this->validation->listErrors());
    //         return redirect()->to('/auth');
    //     }

    //     $uname = esc($this->request->getPost('uname'));
    //     $pass  = esc($this->request->getPost('pass'));

    //     $result = $this->authModel->verifyLogin($uname, $pass);

    //     if ($result) {
    //         $this->session->set('logged_status', [
    //             'username' => $result->username,
    //             'nama'     => $result->nama,
    //             'role'     => $result->role,
    //             'is_login' => true,
    //         ]);

    //         $role = $result->role;

    //         $this->session->setFlashdata('success', "Berhasil login sebagai {$role}");

    //         if ($role === "Staff") {
    //             $store = $this->assignModel->getStoreID($uname);
    //             if (!$store) {
    //                 $this->session->remove('logged_status');
    //                 $this->session->setFlashdata('error', "Staff belum di assign ke toko");
    //                 return redirect()->to('/auth');
    //             }
    //             $this->session->set('logged_status.storeid', $store->storeid);
    //             $this->session->set('logged_status.store', $store->store);
    //             return redirect()->to(base_url("staff/dashboard"));
    //         }

    //         if ($role !== "Owner") {
    //             $store = $this->assignModel->getStoreID($uname);
    //             if (!$store) {
    //                 $this->session->remove('logged_status');
    //                 $this->session->setFlashdata('error', "Staff belum di assign ke toko");
    //                 return redirect()->to('/auth');
    //             }
    //             $this->session->set('logged_status.storeid', $store->storeid);
    //             $this->session->set('logged_status.store', $store->store);
    //         }

    //         return redirect()->to(base_url("dashboard"));
    //     }

    //     $this->session->setFlashdata('error', "Username atau password salah, mohon periksa ulang");
    //     return redirect()->to('/auth');
    // }

    // public function postAuth_login()
    // {
    //     $this->validation->setRules([
    //         'uname' => 'required',
    //         'pass'  => 'required',
    //     ]);

    //     if (!$this->validation->withRequest($this->request)->run()) {
    //         $this->session->setFlashdata('error', $this->validation->listErrors());
    //         return redirect()->to('/auth');
    //     }

    //     $uname = esc($this->request->getPost('uname'));
    //     $pass  = esc($this->request->getPost('pass'));

    //     $result = $this->authModel->verifyLogin($uname, $pass);

    //     if (!$result) {
    //         $this->session->setFlashdata('error', "Username atau password salah, mohon periksa ulang");
    //         return redirect()->to('/auth');
    //     }

    //     if (empty($result->storeid)) {
    //         $this->session->setFlashdata('error', "{$result->role} belum di assign ke toko");
    //         return redirect()->to('/auth');
    //     }

    //     $this->session->set('logged_status', [
    //         'username' => $result->username,
    //         'nama'     => $result->nama,
    //         'role'     => $result->role,
    //         'storeid'  => $result->storeid,
    //         'is_login' => true,
    //     ]);

    //     $this->session->setFlashdata('success', "Berhasil login sebagai {$result->role}");

    //     return redirect()->to(base_url(
    //         in_array($result->role, ['Staff', 'Store Manager', 'Office Manager', 'Office Staff'])
    //             ? "staff/dashboard"
    //             : "dashboard"
    //     ));
    // }
    public function postAuth_login()
    {
        $this->validation->setRules([
            'uname' => 'required',
            'pass'  => 'required',
        ]);

        if (!$this->validation->withRequest($this->request)->run()) {
            $this->session->setFlashdata('error', $this->validation->listErrors());
            return redirect()->to('/auth');
        }

        $uname = esc($this->request->getPost('uname'));
        $pass  = esc($this->request->getPost('pass'));

        $result = $this->authModel->verifyLogin($uname, $pass);

        if (!$result) {
            $this->session->setFlashdata('error', "Username atau password salah, mohon periksa ulang");
            return redirect()->to('/auth');
        }

        if (empty($result->storeid) || empty($result->store)) {
            $this->session->setFlashdata('error', "{$result->role} belum di assign ke toko");
            return redirect()->to('/auth');
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
        return redirect()->to('/auth');
    }
}
