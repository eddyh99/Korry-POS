<?php

namespace App\Controllers\Admin;

use App\Models\Admin\SizeModel;

use App\Controllers\BaseApiController;

class Size extends BaseApiController
{
    protected $sizeModel;

    public function __construct()
    {
        $this->sizeModel = new SizeModel();
    }

    public function getIndex()
    {
        if (!session()->get('logged_status')) {
            return redirect()->to('/');
        }

        $data = [
            'title'      => 'Data Size',
            'content'    => 'admin/size/index',
            'extra'      => 'admin/size/js/js_index',
            'mn_setting' => 'active',
            'colmas'     => 'collapse',
            'colset'     => 'collapse in',
            'collap'     => 'collapse',
            'side5'      => 'active',
        ];

        return view('layout/wrapper', $data);
    }

    public function postListdata()
    {
        $result = $this->sizeModel->Listsize();
        return $this->response->setJSON($result);
    }

    public function getTambah()
    {
        if (!session()->get('logged_status')) {
            return redirect()->to('/');
        }

        $data = [
            'title'      => 'Tambah Data Size',
            'content'    => 'admin/size/tambah',
            'mn_setting' => 'active',
            'colmas'     => 'collapse',
            'colset'     => 'collapse in',
            'collap'     => 'collapse',
            'side5'      => 'active',
        ];

        return view('layout/wrapper', $data);
    }

    public function postAddData()
    {
        $rules = [
            'size' => 'trim|required',
        ];

        if (! $this->validate($rules)) {
            session()->setFlashdata('message', $this->validator->listErrors());
            return redirect()->to('/admin/size/tambah');
        }

        $size   = esc($this->request->getPost('size'));
        $userid = session()->get('logged_status')['username'];

        $data = [
            'nama'   => $size,
            'userid' => $userid
        ];

        $result = $this->sizeModel->insertData($data);

        if ($result['code'] == 0) {
            session()->setFlashdata('message', 'Data berhasil disimpan.');
            return redirect()->to('/admin/size');
        } else {
            session()->setFlashdata('message', 'Data gagal disimpan: ' . $result['message']);
            return redirect()->to('/admin/size/tambah');
        }
    }

    public function getUbah($size)
    {
        if (!session()->get('logged_status')) {
            return redirect()->to('/');
        }

        $size = base64_decode(esc($size));

        $result = $this->sizeModel->getSize($size);
        $data = [
            'title'      => 'Ubah Data Size',
            'content'    => 'admin/size/ubah',
            'detail'     => $result,
            'mn_setting' => 'active',
            'colset'     => 'collapse-in',
            'side5'      => 'active',
        ];

        return view('layout/wrapper', $data);
    }

    public function postUpdateData()
    {
        $rules = [
            'size' => 'trim|required',
        ];

        $oldsize = esc($this->request->getPost('oldsize'));

        if (! $this->validate($rules)) {
            session()->setFlashdata('message', $this->validator->listErrors());
            return redirect()->to('/admin/size/ubah/' . base64_encode($oldsize));
        }

        $size   = esc($this->request->getPost('size'));
        $userid = session()->get('logged_status')['username'];

        $data = [
            'nama'   => $size,
            'userid' => $userid
        ];

        $result = $this->sizeModel->updateData($data, $oldsize);

        if ($result['code'] == 0) {
            session()->setFlashdata('message', 'Data berhasil diubah.');
            return redirect()->to('/admin/size');
        } else {
            session()->setFlashdata('message', 'Data gagal diubah: ' . $result['message']);
            return redirect()->to('/admin/size/ubah/' . base64_encode($oldsize));
        }
    }

    public function getHapus($size)
    {
        $data = ['status' => 1];

        $size   = base64_decode(esc($size));
        $result = $this->sizeModel->hapusData($data, $size);

        if ($result['code'] == 0) {
            session()->setFlashdata('message', 'Data berhasil dihapus.');
        } else {
            session()->setFlashdata('message', 'Data gagal dihapus: ' . $result['message']);
        }

        return redirect()->to('/admin/size');
    }
}
