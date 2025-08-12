<?php

namespace App\Controllers\Admin;

use App\Models\Admin\StoreModel;

use App\Controllers\BaseApiController;

class Store extends BaseApiController
{
    protected $storeModel;

    public function __construct()
    {
        $this->storeModel = new StoreModel();
    }

    public function getIndex()
    {
        if (!$this->session->get('logged_status')) {
            return redirect()->to(base_url());
        }

        $data = [
            'title'      => 'Data Store',
            'content'    => 'admin/store/index',
            'extra'      => 'admin/store/js/js_index',
            'mn_setting' => 'active',
            'colmas'     => 'collapse',
            'colset'     => 'collapse in',
            'collap'     => 'collapse',
            'side2'      => 'active',
        ];
        return view('layout/wrapper', $data);
    }

    public function postListdata()
    {
        $result = $this->storeModel->listStore();
        return $this->response->setJSON($result);
    }

    public function getTambah()
    {
        if (!$this->session->get('logged_status')) {
            return redirect()->to(base_url());
        }

        $data = [
            'title'      => 'Tambah Data Store',
            'content'    => 'admin/store/tambah',
            'mn_master'  => 'active',
            'colmas'     => 'collapse',
            'colset'     => 'collapse in',
            'collap'     => 'collapse',
            'side2'      => 'active',
        ];
        return view('layout/wrapper', $data);
    }

    public function postAddData()
    {
        $rules = [
            'store'      => 'required',
            'alamat'     => 'required',
            'kontak'     => 'required',
            'keterangan' => 'required'
        ];

        if (!$this->validate($rules)) {
            $this->session->setFlashdata(
                'message',
                $this->message->error_msg($this->validation->listErrors())
            );
            return redirect()->to(base_url("admin/store/tambah"));
        }

        $data = [
            "store"      => esc($this->request->getPost('store')),
            "alamat"     => esc($this->request->getPost('alamat')),
            "kontak"     => esc($this->request->getPost('kontak')),
            "keterangan" => esc($this->request->getPost('keterangan')),
            "userid"     => $this->session->get('logged_status')['username']
        ];

        $result = $this->storeModel->insertData($data);

        if ($result["code"] == 0) {
            $this->session->setFlashdata('message', 'Data berhasil disimpan.');
            return redirect()->to(base_url("admin/store"));
        } else {
            $this->session->setFlashdata('message', 'Data gagal disimpan.');
            return redirect()->to(base_url("admin/store/tambah"));
        }
    }

    public function getUbah($storeid)
    {
        if (!$this->session->get('logged_status')) {
            return redirect()->to(base_url());
        }

        $storeid = base64_decode(esc($storeid));
        $result  = $this->storeModel->getStore($storeid);

        $data = [
            'title'      => 'Ubah Data Store',
            'content'    => 'admin/store/ubah',
            'detail'     => $result,
            'mn_master'  => 'active',
            'colmas'     => 'collapse',
            'colset'     => 'collapse in',
            'collap'     => 'collapse',
            'side2'      => 'active',
        ];
        return view('layout/wrapper', $data);
    }

    public function postUpdateData()
    {
        $rules = [
            'store'      => 'required',
            'alamat'     => 'required',
            'kontak'     => 'required',
            'keterangan' => 'required'
        ];

        $storeid = esc($this->request->getPost('storeid'));

        if (!$this->validate($rules)) {
            $this->session->setFlashdata(
                'message',
                $this->message->error_msg($this->validation->listErrors())
            );
            return redirect()->to(base_url("admin/store/ubah/" . base64_encode($storeid)));
        }

        $data = [
            "store"      => esc($this->request->getPost('store')),
            "alamat"     => esc($this->request->getPost('alamat')),
            "kontak"     => esc($this->request->getPost('kontak')),
            "keterangan" => esc($this->request->getPost('keterangan')),
            "userid"     => $this->session->get('logged_status')['username']
        ];

        $result = $this->storeModel->updateData($data, $storeid);

        if ($result["code"] == 0) {
            $this->session->setFlashdata('message', 'Data berhasil diubah.');
            return redirect()->to(base_url("admin/store"));
        } else {
            $this->session->setFlashdata('message', 'Data gagal diubah.');
            return redirect()->to(base_url("admin/store/ubah/" . base64_encode($storeid)));
        }
    }

    public function getHapus($storeid)
    {
        $storeid = base64_decode(esc($storeid));

        $data = [
            "status" => 1,
            "userid" => $this->session->get('logged_status')['username']
        ];

        $result = $this->storeModel->hapusData($data, $storeid);

        if ($result["code"] == 0) {
            $this->session->setFlashdata('message', 'Data berhasil dihapus.');
        } else {
            $this->session->setFlashdata('message', 'Data gagal dihapus.');
        }
        return redirect()->to(base_url("admin/store"));
    }
}
