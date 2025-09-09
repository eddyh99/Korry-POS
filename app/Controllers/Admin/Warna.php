<?php

namespace App\Controllers\Admin;

use App\Models\Admin\WarnaModel;

use App\Controllers\BaseApiController;

class Warna extends BaseApiController
{
    protected $warnaModel;

    public function __construct()
    {
        $this->warnaModel = new WarnaModel();
    }

    public function getIndex()
    {
        $data = [
            'title'      => 'Data Warna',
            'content'    => 'admin/warna/index',
            'extra'      => 'admin/warna/js/js_index',
            'mn_setting' => 'active',
            'colset'     => 'collapse in',
            'colmas'     => 'collapse',
            'colkonsi'   => 'collapse',
            'colwho'     => 'collapse',
            'collap'     => 'collapse',
            'side8'      => 'active',
        ];
        return view('layout/wrapper', $data);
    }

    public function postListdata()
    {
        $result = $this->warnaModel->listWarna();
        return $this->response->setJSON($result);
    }

    public function getTambah()
    {
        $data = [
            'title'      => 'Tambah Data Warna',
            'content'    => 'admin/warna/tambah',
            'mn_setting' => 'active',
            'colset'     => 'collapse in',
            'colmas'     => 'collapse',
            'colkonsi'   => 'collapse',
            'colwho'     => 'collapse',
            'collap'     => 'collapse',
            'side8'      => 'active',
        ];
        return view('layout/wrapper', $data);
    }

    public function getUbah($warna)
    {
        $warna = base64_decode($warna);
        $result   = $this->warnaModel->getWarna($warna);

        $data = [
            'title'      => 'Ubah Data Warna',
            'content'    => 'admin/warna/ubah',
            'detail'     => $result,
            'mn_setting' => 'active',
            'colset'     => 'collapse in',
            'colmas'     => 'collapse',
            'colkonsi'   => 'collapse',
            'colwho'     => 'collapse',
            'collap'     => 'collapse',
            'side8'      => 'active',
        ];
        return view('layout/wrapper', $data);
    }

    public function getHapus($warna)
    {
        $warna = base64_decode($warna);

        $data = ['status' => 1];
        $result = $this->warnaModel->hapusData($data, $warna);

        if ($result['code'] == 0) {
            $this->session->setFlashdata('message', 'Data berhasil dihapus.');
        } else {
            $this->session->setFlashdata('message', 'Data gagal dihapus.');
        }

        return redirect()->to('/admin/warna');
    }

    // Handle Post Tambah & Ubah    

    public function postAddData()
    {
        $rules = [
            'warna' => [
                'label' => 'Warna',
                'rules' => 'required|alpha_numeric_space|max_length[50]|is_unique[warna.namawarna]',
                'errors' => [
                    'required' => '{field} wajib diisi.',
                    'alpha_numeric_space' => '{field} hanya boleh huruf, angka, dan spasi.',
                    'max_length' => '{field} maksimal 50 karakter.',
                    'is_unique' => '{field} sudah digunakan.'
                ]
            ]
        ];

        if (! $this->validate($rules)) {
            $this->session->setFlashdata('message', implode('<br>', $this->validator->getErrors()));
            return redirect()->to('/admin/warna/tambah');
        }

        $warna = ucfirst(trim($this->request->getPost('warna')));
        $userid   = $this->session->get('logged_status')['username'] ?? '';

        $data = [
            'namawarna' => $warna,
            'userid'       => $userid
        ];

        $result = $this->warnaModel->insertData($data);

        $msg = ($result['code'] == 0) ? 'Data berhasil disimpan.' : 'Data gagal disimpan.';
        $this->session->setFlashdata('message', $msg);

        return redirect()->to('/admin/warna');
    }

    public function postUpdateData()
    {
        $rules = [
            'warna' => [
                'label' => 'Warna',
                'rules' => 'required|alpha_numeric_space|max_length[50]|is_unique[warna.namawarna]',
                'errors' => [
                    'required' => '{field} wajib diisi.',
                    'alpha_numeric_space' => '{field} hanya boleh huruf, angka, dan spasi.',
                    'max_length' => '{field} maksimal 50 karakter.',
                    'is_unique' => '{field} sudah digunakan.'
                ]
            ]
        ];

        $oldWarna = $this->request->getPost('oldwarna');

        if (! $this->validate($rules)) {
            $this->session->setFlashdata('message', implode('<br>', $this->validator->getErrors()));
            return redirect()->to('/admin/warna/ubah/' . base64_encode($oldWarna));
        }

        $warna = ucfirst(trim($this->request->getPost('warna')));
        $userid   = $this->session->get('logged_status')['username'] ?? '';

        $data = [
            'namawarna' => $warna,
            'userid'       => $userid
        ];

        $result = $this->warnaModel->updateData($data, $oldWarna);

        $msg = ($result['code'] == 0) ? 'Data berhasil diubah.' : 'Data gagal diubah.';
        $this->session->setFlashdata('message', $msg);

        return redirect()->to('/admin/warna');
    }
}
