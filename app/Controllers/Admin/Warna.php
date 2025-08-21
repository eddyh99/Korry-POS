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
        if (!$this->session->get('logged_status')) {
            return redirect()->to(base_url());
        }

        $data = [
            'title'      => 'Data Warna',
            'content'    => 'admin/warna/index',
            'extra'      => 'admin/warna/js/js_index', 
            'mn_setting' => 'active',
            'colmas'     => 'collapse',
            'colset'     => 'collapse in',
            'collap'     => 'collapse',
            'side11'     => 'active',
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
        if (!$this->session->get('logged_status')) {
            return redirect()->to(base_url());
        }

        $data = [
            'title'      => 'Tambah Warna',
            'content'    => 'admin/warna/tambah',
            'mn_master'  => 'active',
            'colmas'     => 'collapse',
            'colset'     => 'collapse in',
            'collap'     => 'collapse',
            'side11'      => 'active',
        ];
        return view('layout/wrapper', $data);
    }

    public function getUbah($warnaid)
    {
        $warnaid = base64_decode(esc($warnaid));
        $result  = $this->warnaModel->getWarna($warnaid);

        $data = [
            'title'      => 'Ubah Warna',
            'content'    => 'admin/warna/ubah',
            'detail'     => $result,
            'mn_master'  => 'active',
            'colmas'     => 'collapse',
            'colset'     => 'collapse in',
            'collap'     => 'collapse',
            'side2'      => 'active',
        ];
        return view('layout/wrapper', $data);
    }

    public function getHapus($warnaid)
    {
        $warnaid = base64_decode(esc($warnaid));

        $data = [
            "status" => 1
        ];

        $result = $this->warnaModel->hapusData($data, $warnaid);

        if ($result["code"] == 0) {
            $this->session->setFlashdata('message', 'Data berhasil dihapus.');
        } else {
            $this->session->setFlashdata('message', 'Data gagal dihapus.');
        }
        return redirect()->to(base_url("admin/warna"));
    }

    // Handle Post Tambah & Ubah

    public function postAddData()
    {
        $rules = [
            'warna' => [
                'label' => 'Nama Warna',
                'rules' => 'required|trim|max_length[30]|alpha_numeric_space|is_unique[warna.nama]',
                'errors' => [
                    'required' => '{field} wajib diisi.',
                    'max_length' => '{field} maksimal 30 karakter.',
                    'alpha_numeric_space' => '{field} hanya boleh berisi huruf, angka, dan spasi.',
                    'is_unique'     => '{field} sudah digunakan.'
                ]
            ]
        ];

        if (!$this->validate($rules)) {
            $this->session->setFlashdata('message', implode('<br>', $this->validator->getErrors()));
            return redirect()->to(base_url("admin/warna/tambah"));
        }

        $data = [
            "nama"       => esc($this->request->getPost('warna'))
        ];

        $result = $this->warnaModel->insertData($data);

        if ($result["code"] == 0) {
            $this->session->setFlashdata('message', 'Data berhasil disimpan.');
            return redirect()->to(base_url("admin/warna"));
        } else {
            $this->session->setFlashdata('message', 'Data gagal disimpan.');
            return redirect()->to(base_url("admin/warna/tambah"));
        }
    }

    public function postUpdateData()
    {
        $rules = [
            'warna' => [
                'label' => 'Nama Warna',
                'rules' => 'required|trim|max_length[30]|alpha_numeric_space|is_unique[warna.nama]',
                'errors' => [
                    'required' => '{field} wajib diisi.',
                    'max_length' => '{field} maksimal 30 karakter.',
                    'alpha_numeric_space' => '{field} hanya boleh berisi huruf, angka, dan spasi.',
                    'is_unique'     => '{field} sudah digunakan.'
                ]
            ]
        ];

        $warnaid = esc($this->request->getPost('warnaid'));

        if (!$this->validate($rules)) {
            $this->session->setFlashdata('message', implode('<br>', $this->validator->getErrors()));
            return redirect()->to(base_url("admin/warna/ubah/" . base64_encode($warnaid)));
        }

        $data = [
            "nama"      => esc($this->request->getPost('warna'))
        ];

        $result = $this->warnaModel->updateData($data, $warnaid);

        if ($result["code"] == 0) {
            $this->session->setFlashdata('message', 'Data berhasil diubah.');
            return redirect()->to(base_url("admin/warna"));
        } else {
            $this->session->setFlashdata('message', 'Data gagal diubah.');
            return redirect()->to(base_url("admin/warna/ubah/" . base64_encode($warnaid)));
        }
    }
}
