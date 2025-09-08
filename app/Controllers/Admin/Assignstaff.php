<?php

namespace App\Controllers\Admin;

use App\Models\Admin\AssignModel;
use App\Models\Admin\StoreModel;
use App\Models\Admin\PenggunaModel;

use App\Controllers\BaseApiController;

class Assignstaff extends BaseApiController
{
    protected $assignModel;
    protected $storeModel;
    protected $penggunaModel;

    public function __construct()
    {
        $this->assignModel   = new AssignModel();
        $this->storeModel    = new StoreModel();
        $this->penggunaModel = new PenggunaModel();
    }

    public function getIndex()
    {
        $data = [
            'title'     => 'Data Assign Staff',
            'content'   => 'admin/assignstaff/index',
            'extra'     => 'admin/assignstaff/js/js_index',
            'mn_master' => 'active',
            'colset'     => 'collapse',
            'colmas'     => 'collapse in',
            'colkonsi'   => 'collapse',
            'colwho'     => 'collapse',
            'collap'     => 'collapse',
            'side12'     => 'active',
        ];

        return view('layout/wrapper', $data);
    }

    public function postListdata()
    {
        $result = $this->assignModel->ListStaff();
        return $this->response->setJSON($result);
    }

    public function getTambah()
    {
        $data = [
            'title'     => 'Tambah Data Staff',
            'content'   => 'admin/assignstaff/tambah',
            'extra'     => 'admin/assignstaff/js/js_tambah',
            'store'     => $this->storeModel->Liststore(),
            'staff'     => $this->penggunaModel->getNonAdmin(),
            'mn_master' => 'active',
            'colset'     => 'collapse',
            'colmas'     => 'collapse in',
            'colkonsi'   => 'collapse',
            'colwho'     => 'collapse',
            'collap'     => 'collapse',
            'side12'    => 'active',
        ];

        return view('layout/wrapper', $data);
    }

    // public function postAddData()
    // {
    //     $rules = [
    //         'username' => 'trim|required',
    //         'storeid'  => 'trim|required',
    //     ];

    //     if (! $this->validate($rules)) {
    //         session()->setFlashdata('message', $this->validator->listErrors());
    //         return redirect()->to('/admin/assignstaff/tambah');
    //     }

    //     $data = [
    //         'username' => esc($this->request->getPost('username')),
    //         'storeid'  => esc($this->request->getPost('storeid')),
    //         'userid'   => session('logged_status')['username'] ?? null
    //     ];

    //     $result = $this->assignModel->insertData($data);

    //     if ($result['code'] == 0) {
    //         $this->session->setFlashdata('message', 'Data berhasil disimpan.');
    //         return redirect()->to('/admin/assignstaff');
    //     }

    //     $this->session->setFlashdata('message', 'Data gagal disimpan.');
    //     return redirect()->to('/admin/assignstaff/tambah');
    // }
    public function postAddData()
    {
        $rules = [
            'username' => [
                'label'  => 'Username',
                'rules'  => 'required|alpha_numeric',
                'errors' => [
                    'required'      => '{field} wajib diisi.',
                    'alpha_numeric' => '{field} hanya boleh berisi huruf dan angka tanpa spasi.'
                ]
            ],
            'storeid' => [
                'label'  => 'Store ID',
                'rules'  => 'required|is_natural_no_zero',
                'errors' => [
                    'required'           => '{field} wajib diisi.',
                    'is_natural_no_zero' => '{field} harus berupa angka dan tidak boleh nol.'
                ]
            ]
        ];

        if (! $this->validate($rules)) {
            session()->setFlashdata('message', implode('<br>', $this->validator->getErrors()));
            return redirect()->to('/admin/assignstaff/tambah')->withInput();
        }

        $data = [
            'username' => esc($this->request->getPost('username')),
            'storeid'  => esc($this->request->getPost('storeid')),
            'userid'   => session('logged_status')['username'] ?? null
        ];

        $result = $this->assignModel->insertData($data);

        if ($result['code'] == 0) {
            session()->setFlashdata('message', 'Data berhasil disimpan.');
            return redirect()->to('/admin/assignstaff');
        }

        session()->setFlashdata('message', 'Data gagal disimpan: ' . ($result['message'] ?? ''));
        return redirect()->to('/admin/assignstaff/tambah')->withInput();
    }

    public function getHapus($uname, $storeid)
    {
        $userid  = session('logged_status')['username'] ?? null;
        $uname   = base64_decode(esc($uname));
        $storeid = base64_decode(esc($storeid));

        $data  = [
            'status' => 1,
            'userid' => $userid
        ];

        $where = [
            'username' => $uname,
            'storeid'  => $storeid
        ];

        $result = $this->assignModel->hapusData($data, $where);

        if ($result['code'] == 0) {
            session()->setFlashdata('message', 'Data berhasil dihapus.');
        } else {
            session()->setFlashdata('message', 'Data gagal dihapus');
        }

        return redirect()->to('/admin/assignstaff');
    }
}
