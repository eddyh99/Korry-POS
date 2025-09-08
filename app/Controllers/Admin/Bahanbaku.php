<?php

namespace App\Controllers\Admin;

use App\Models\Admin\BahanbakuModel;

use App\Controllers\BaseApiController;

class Bahanbaku extends BaseApiController
{
    protected $bahanbakuModel;

    public function __construct()
    {
        $this->bahanbakuModel = new BahanbakuModel();
    }

    public function getIndex()
    {
        $data = [
            'title'      => 'Data Bahan Baku', // ✅ ganti judul
            'content'    => 'admin/bahanbaku/index',     // ✅ ganti view
            'extra'      => 'admin/bahanbaku/js/js_index', 
            'mn_setting' => 'active',
            'colset'     => 'collapse in',
            'colmas'     => 'collapse',
            'colkonsi'   => 'collapse',
            'colwho'     => 'collapse',
            'collap'     => 'collapse',
            'side6'     => 'active',
        ];
        return view('layout/wrapper', $data);
    }

    // public function postListdata()
    // {
    //     $result = $this->bahanbakuModel->listBahanbaku();
    //     return $this->response->setJSON($result);
    // }
    public function postListdata()
    {
        $result = $this->bahanbakuModel->listBahanbakuWithStok();
        return $this->response->setJSON($result);
    }

    public function getTambah()
    {
        if (!$this->session->get('logged_status')) {
            return redirect()->to(base_url());
        }

        $data = [
            'title'      => 'Tambah Bahan Baku',
            'content'    => 'admin/bahanbaku/tambah',
            'mn_master'  => 'active',
            'colset'     => 'collapse in',
            'colmas'     => 'collapse',
            'colkonsi'   => 'collapse',
            'colwho'     => 'collapse',
            'collap'     => 'collapse',
            'side6'      => 'active',
        ];
        return view('layout/wrapper', $data);
    }

    public function getUbah($bbakuid)
    {
        $bbakuid = base64_decode(esc($bbakuid));
        $result  = $this->bahanbakuModel->getBahanbaku($bbakuid);

        $data = [
            'title'      => 'Ubah Bahan Baku',
            'content'    => 'admin/bahanbaku/ubah',     
            'detail'     => $result,
            'mn_master'  => 'active',
            'colset'     => 'collapse in',
            'colmas'     => 'collapse',
            'colkonsi'   => 'collapse',
            'colwho'     => 'collapse',
            'collap'     => 'collapse',
            'side2'      => 'active',
        ];
        return view('layout/wrapper', $data);
    }

    public function getHapus($bbakuid)
    {
        $bbakuid = base64_decode(esc($bbakuid));

        $data = [
            "is_delete" => 1
        ];

        $result = $this->bahanbakuModel->hapusData($data, $bbakuid);

        if ($result["code"] == 0) {
            $this->session->setFlashdata('message', 'Data berhasil dihapus.');
        } else {
            $this->session->setFlashdata('message', 'Data gagal dihapus.');
        }
        return redirect()->to(base_url("admin/bahanbaku")); 
    }

    // Handle Post Tambah & Ubah

    public function postAddData()
    {
        $rules = [
            'bahanbaku' => [
                'label' => 'Nama Bahan Baku',
                'rules' => 'required|trim|max_length[30]|alpha_numeric_space',
                'errors' => [
                    'required' => '{field} wajib diisi.',
                    'alpha_numeric_space' => '{field} hanya boleh berisi huruf, angka, dan spasi.',
                    'max_length' => '{field} maksimal 30 karakter.'
                ]
            ],
            'stok_min' => [ 
                'label' => 'Stok Minimal',
                'rules' => 'required|is_natural_no_zero|max_length[11]',
                'errors' => [
                    'required' => '{field} wajib diisi.',
                    'is_natural_no_zero' => '{field} harus bilangan bulat positif dan tidak boleh nol.',
                    'max_length' => '{field} maksimal 11 digit.'
                ]
            ]
        ];

        if (!$this->validate($rules)) {
            $this->session->setFlashdata('message', implode('<br>', $this->validator->getErrors()));
            return redirect()->to(base_url("admin/bahanbaku/tambah"))->withInput();
        }

        $data = [
            "namabahan" => esc($this->request->getPost('bahanbaku')),
            "min"       => esc($this->request->getPost('stok_min')),
            "user_id"   => $this->session->get('logged_status')['username']
        ];

        $result = $this->bahanbakuModel->insertData($data);

        if ($result["code"] == 0) {
            $this->session->setFlashdata('message', 'Data berhasil disimpan.');
            return redirect()->to(base_url("admin/bahanbaku"));
        } else {
            $this->session->setFlashdata('message', 'Data gagal disimpan.');
            return redirect()->to(base_url("admin/bahanbaku/tambah"))->withInput();
        }
    }
    
    public function postUpdateData()
    {
        $rules = [
            'bahanbaku' => [
                'label' => 'Nama Bahan Baku',
                'rules' => 'required|trim|max_length[30]|alpha_numeric_space',
                'errors' => [
                    'required' => '{field} wajib diisi.',
                    'alpha_numeric_space' => '{field} hanya boleh berisi huruf, angka, dan spasi.',
                    'max_length' => '{field} maksimal 30 karakter.'
                ]
            ],
            'stok_min' => [
                'label' => 'Stok Minimal',
                'rules' => 'required|is_natural_no_zero|max_length[11]',
                'errors' => [
                    'required' => '{field} wajib diisi.',
                    'is_natural_no_zero' => '{field} harus bilangan bulat positif dan tidak boleh nol.',
                    'max_length' => '{field} maksimal 11 digit.'
                ]
            ]
        ];

        $bbakuid = esc($this->request->getPost('bbakuid'));

        if (!$this->validate($rules)) {
            $this->session->setFlashdata('message', implode('<br>', $this->validator->getErrors()));
            return redirect()->to(base_url("admin/bahanbaku/ubah/" . base64_encode($bbakuid)));
        }

        $data = [
            "namabahan" => esc($this->request->getPost('bahanbaku')),
            "min"       => esc($this->request->getPost('stok_min')),
            "user_id"   => $this->session->get('logged_status')['username']
        ];

        $result = $this->bahanbakuModel->updateData($data, $bbakuid);

        if ($result["code"] == 0) {
            $this->session->setFlashdata('message', 'Data berhasil diubah.');
            return redirect()->to(base_url("admin/bahanbaku"));
        } else {
            $this->session->setFlashdata('message', 'Data gagal diubah.');
            return redirect()->to(base_url("admin/bahanbaku/ubah/" . base64_encode($bbakuid)));
        }
    }
}
