<?php

namespace App\Controllers\Admin;

use App\Models\Admin\KategoriModel;

use App\Controllers\BaseApiController;

class Kategori extends BaseApiController
{
    protected $kategoriModel;

    public function __construct()
    {
        $this->kategoriModel = new KategoriModel();
    }

    public function getIndex()
    {
        $data = [
            'title'      => 'Data Kategori',
            'content'    => 'admin/kategori/index',
            'extra'      => 'admin/kategori/js/js_index',
            'mn_setting' => 'active',
            'colmas'     => 'collapse',
            'colset'     => 'collapse in',
            'collap'     => 'collapse',
            'side4'      => 'active',
        ];
        return view('layout/wrapper', $data);
    }

    public function postListdata()
    {
        $result = $this->kategoriModel->listKategori();
        return $this->response->setJSON($result);
    }

    public function getTambah()
    {
        $data = [
            'title'      => 'Tambah Data Kategori',
            'content'    => 'admin/kategori/tambah',
            'mn_setting' => 'active',
            'colmas'     => 'collapse',
            'colset'     => 'collapse in',
            'collap'     => 'collapse',
            'side4'      => 'active',
        ];
        return view('layout/wrapper', $data);
    }

    // public function postAddData()
    // {
    //     $rules = [
    //         'kategori' => 'trim|required'
    //     ];

    //     if (! $this->validate($rules)) {
    //         session()->setFlashdata('message', $this->message->error_msg($this->validator->listErrors()));
    //         return redirect()->to('/admin/kategori/tambah');
    //     }

    //     $kategori = ucfirst($this->request->getPost('kategori'));
    //     $userid   = session()->get('logged_status')['username'] ?? '';

    //     $data = [
    //         'namakategori' => $kategori,
    //         'userid'       => $userid
    //     ];

    //     $result = $this->kategoriModel->insertData($data);

    //     if ($result['code'] == 0) {
    //         $this->session->setFlashdata('message', 'Data berhasil disimpan.');
    //         return redirect()->to('/admin/kategori');
    //     } else {
    //         $this->session->setFlashdata('message', 'Data gagal disimpan.');
    //         return redirect()->to('/admin/kategori/tambah');
    //     }
    // }
    public function postAddData()
    {
        $rules = [
            'kategori' => [
                'label' => 'Kategori',
                'rules' => 'required|alpha_numeric_space|max_length[20]',
                'errors' => [
                    'required' => '{field} wajib diisi.',
                    'alpha_numeric_space' => '{field} hanya boleh huruf, angka, dan spasi.',
                    'max_length' => '{field} maksimal 20 karakter.'
                ]
            ]
        ];

        if (! $this->validate($rules)) {
            $this->session->setFlashdata('message', implode('<br>', $this->validator->getErrors()));
            return redirect()->to('/admin/kategori/tambah');
        }

        $kategori = ucfirst(trim($this->request->getPost('kategori')));
        $userid   = $this->session->get('logged_status')['username'] ?? '';

        $data = [
            'namakategori' => $kategori,
            'userid'       => $userid
        ];

        $result = $this->kategoriModel->insertData($data);

        $msg = ($result['code'] == 0) ? 'Data berhasil disimpan.' : 'Data gagal disimpan.';
        $this->session->setFlashdata('message', $msg);

        return redirect()->to('/admin/kategori');
    }

    public function getUbah($kategori)
    {
        $kategori = base64_decode($kategori);
        $result   = $this->kategoriModel->getKategori($kategori);

        $data = [
            'title'      => 'Ubah Data Kategori',
            'content'    => 'admin/kategori/ubah',
            'detail'     => $result,
            'mn_setting' => 'active',
            'colmas'     => 'collapse',
            'colset'     => 'collapse in',
            'collap'     => 'collapse',
            'side4'      => 'active',
        ];
        return view('layout/wrapper', $data);
    }

    // public function postUpdateData()
    // {
    //     $rules = [
    //         'kategori' => 'trim|required'
    //     ];

    //     $oldKategori = $this->request->getPost('oldkategori');

    //     if (! $this->validate($rules)) {
    //         session()->setFlashdata('message', $this->message->error_msg($this->validator->listErrors()));
    //         return redirect()->to('/admin/kategori/ubah/' . base64_encode($oldkategori));
    //     }

    //     $kategori = ucfirst($this->request->getPost('kategori'));
    //     $userid   = session()->get('logged_status')['username'] ?? '';

    //     $data = [
    //         'namakategori' => $kategori,
    //         'userid'       => $userid
    //     ];

    //     $result = $this->kategoriModel->updateData($data, $oldKategori);

    //     if ($result['code'] == 0) {
    //         $this->session->setFlashdata('message', 'Data berhasil diubah.');
    //         return redirect()->to('/admin/kategori');
    //     } else {
    //         $this->session->setFlashdata('message', 'Data gagal diubah.');
    //         return redirect()->to('/admin/kategori/ubah/' . base64_encode($oldKategori));
    //     }
    // }
    public function postUpdateData()
    {
        $rules = [
            'kategori' => [
                'label' => 'Kategori',
                'rules' => 'required|alpha_numeric_space|max_length[20]',
                'errors' => [
                    'required' => '{field} wajib diisi.',
                    'alpha_numeric_space' => '{field} hanya boleh huruf, angka, dan spasi.',
                    'max_length' => '{field} maksimal 20 karakter.'
                ]
            ]
        ];

        $oldKategori = $this->request->getPost('oldkategori');

        if (! $this->validate($rules)) {
            $this->session->setFlashdata('message', implode('<br>', $this->validator->getErrors()));
            return redirect()->to('/admin/kategori/ubah/' . base64_encode($oldKategori));
        }

        $kategori = ucfirst(trim($this->request->getPost('kategori')));
        $userid   = $this->session->get('logged_status')['username'] ?? '';

        $data = [
            'namakategori' => $kategori,
            'userid'       => $userid
        ];

        $result = $this->kategoriModel->updateData($data, $oldKategori);

        $msg = ($result['code'] == 0) ? 'Data berhasil diubah.' : 'Data gagal diubah.';
        $this->session->setFlashdata('message', $msg);

        return redirect()->to('/admin/kategori');
    }

    public function getHapus($kategori)
    {
        $kategori = base64_decode($kategori);

        $data = ['status' => 1];
        $result = $this->kategoriModel->hapusData($data, $kategori);

        if ($result['code'] == 0) {
            $this->session->setFlashdata('message', 'Data berhasil dihapus.');
        } else {
            $this->session->setFlashdata('message', 'Data gagal dihapus.');
        }

        return redirect()->to('/admin/kategori');
    }
}
