<?php

namespace App\Controllers\Admin;

use App\Models\Admin\BrandModel;

use App\Controllers\BaseApiController;

class Brand extends BaseApiController
{
    protected $brandModel;

    public function __construct()
    {
        $this->brandModel = new BrandModel();
    }

    public function getIndex()
    {
        $data = [
            'title'      => 'Data Brand',
            'content'    => 'admin/brand/index',
            'extra'      => 'admin/brand/js/js_index',
            'mn_setting' => 'active',
            'colmas'     => 'collapse',
            'colset'     => 'collapse in',
            'collap'     => 'collapse',
            'side3'      => 'active',
        ];
        return view('layout/wrapper', $data);
    }

    public function postListdata()
    {
        $result = $this->brandModel->listBrand();
        return $this->response->setJSON($result);
    }

    public function getTambah()
    {
        $data = [
            'title'      => 'Tambah Data Brand',
            'content'    => 'admin/brand/tambah',
            'mn_setting' => 'active',
            'colmas'     => 'collapse',
            'colset'     => 'collapse in',
            'collap'     => 'collapse',
            'side3'      => 'active',
        ];
        return view('layout/wrapper', $data);
    }

    public function postAddData()
    {
        $rules = [
            'brand' => 'trim|required',
        ];

        if (! $this->validate($rules)) {
            session()->setFlashdata('message', $this->message->error_msg($this->validator->listErrors()));
            return redirect()->to('/admin/brand/tambah');
        }

        $brand      = esc($this->request->getPost('brand'));
        $keterangan = esc($this->request->getPost('keterangan'));
        $userid     = session()->get('logged_status')['username'];

        $data = [
            'namabrand'  => ucfirst($brand),
            'keterangan' => $keterangan,
            'userid'     => $userid
        ];

        $result = $this->brandModel->insertData($data);

        if ($result['code'] == 0) {
            $this->session->setFlashdata('message', 'Data berhasil disimpan.');
            return redirect()->to('/admin/brand');
        } else {
            $this->session->setFlashdata('message', 'Data gagal disimpan.');
            return redirect()->to('/admin/brand/tambah');
        }
    }

    public function getUbah($brand)
    {
        $brand = base64_decode(esc($brand));
        $result = $this->brandModel->getBrand($brand);

        $data = [
            'title'      => 'Ubah Data Brand',
            'content'    => 'admin/brand/ubah',
            'detail'     => $result,
            'mn_setting' => 'active',
            'colmas'     => 'collapse',
            'colset'     => 'collapse in',
            'collap'     => 'collapse',
            'side3'      => 'active',
        ];
        return view('layout/wrapper', $data);
    }

    public function postUpdateData()
    {
        $rules = [
            'brand' => 'trim|required',
        ];

        $oldbrand = esc($this->request->getPost('oldbrand'));

        if (! $this->validate($rules)) {
            session()->setFlashdata('message', $this->message->error_msg($this->validator->listErrors()));
            return redirect()->to('/admin/brand/ubah/' . base64_encode($oldbrand));
        }

        $brand      = esc($this->request->getPost('brand'));
        $keterangan = esc($this->request->getPost('keterangan'));
        $userid     = session()->get('logged_status')['username'];

        $data = [
            'namabrand'  => ucfirst($brand),
            'keterangan' => $keterangan,
            'userid'     => $userid
        ];

        $result = $this->brandModel->updateData($data, $oldbrand);

        if ($result['code'] == 0) {
            $this->session->setFlashdata('message', 'Data berhasil diubah.');
            return redirect()->to('/admin/brand');
        } else {
            $this->session->setFlashdata('message', 'Data gagal diubah.');
            return redirect()->to('/admin/brand/ubah/' . base64_encode($oldbrand));
        }
    }

    public function getHapus($brand)
    {
        $userid = session()->get('logged_status')['username'];
        $brand = base64_decode(esc($brand));

        $data = [
            'status' => 1,
            'userid' => $userid
        ];

        $result = $this->brandModel->hapusData($data, $brand);

        if ($result['code'] == 0) {
            $this->session->setFlashdata('message', 'Data berhasil dihapus.');
        } else {
            $this->session->setFlashdata('message', 'Data gagal dihapus.');
        }

        return redirect()->to('/admin/brand');
    }
}
