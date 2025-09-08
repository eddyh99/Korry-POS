<?php

namespace App\Controllers\Admin;

use App\Models\Admin\WholesalerModel;

use App\Controllers\BaseApiController;

class Wholesaler extends BaseApiController
{
    protected $wholesalerModel;

    public function __construct()
    {
        $this->wholesalerModel = new WholesalerModel();
    }

    public function getIndex()
    {

        $data = [
            'title'      => 'Data Whole Saler',
            'content'    => 'admin/wholesaler/index',
            'extra'      => 'admin/wholesaler/js/js_index', 
            'mn_master'  => 'active',
            'colset'     => 'collapse',
            'colmas'     => 'collapse in',
            'colkonsi'   => 'collapse',
            'colwho'     => 'collapse',
            'collap'     => 'collapse',
            'side19'     => 'active',
        ];
        return view('layout/wrapper', $data);
    }

    public function postListdata()
    {
        $result = $this->wholesalerModel->listWholesaler();
        return $this->response->setJSON($result);
    }

    public function getTambah()
    {

        $data = [
            'title'      => 'Tambah Whole Saler',
            'content'    => 'admin/wholesaler/tambah',
            'mn_master'  => 'active',
            'colset'     => 'collapse',
            'colmas'     => 'collapse in',
            'colkonsi'   => 'collapse',
            'colwho'     => 'collapse',
            'collap'     => 'collapse',
            'side19'     => 'active',
        ];
        return view('layout/wrapper', $data);
    }

    public function getUbah($wsalerid)
    {
        $wsalerid = base64_decode(esc($wsalerid));
        $result  = $this->wholesalerModel->getWholesaler($wsalerid);

        $data = [
            'title'      => 'Ubah Whole Saler',
            'content'    => 'admin/wholesaler/ubah',
            'detail'     => $result,
            'mn_master'  => 'active',
            'colset'     => 'collapse',
            'colmas'     => 'collapse in',
            'colkonsi'   => 'collapse',
            'colwho'     => 'collapse',
            'collap'     => 'collapse',
            'side19'     => 'active',
        ];
        return view('layout/wrapper', $data);
    }

    public function getHapus($wsalerid)
    {
        $wsalerid = base64_decode(esc($wsalerid));

        $data = [
            "status" => 1
        ];

        $result = $this->wholesalerModel->hapusData($data, $wsalerid);

        if ($result["code"] == 0) {
            $this->session->setFlashdata('message', 'Data berhasil dihapus.');
        } else {
            $this->session->setFlashdata('message', 'Data gagal dihapus.');
        }
        return redirect()->to(base_url("admin/wholesaler"));
    }

    // Handle Post Tambah & Ubah

    public function postAddData()
    {
        $rules = [
            'wholesaler' => [
                'label' => 'Nama Wholesaler',
                'rules' => 'required|trim|max_length[30]|alpha_numeric_space|is_unique[wholesaler.nama]',
                'errors' => [
                    'required' => '{field} wajib diisi.',
                    'alpha_numeric_space' => '{field} hanya boleh berisi huruf, angka, dan spasi.',
                    'max_length' => '{field} maksimal 30 karakter.',
                    'is_unique' => '{field} sudah digunakan.'
                ]
            ],
            'alamat' => [
                'label' => 'Alamat',
                'rules' => 'required|trim|max_length[50]|alpha_numeric_punct',
                'errors' => [
                    'required' => '{field} wajib diisi.',
                    'alpha_numeric_punct' => '{field} hanya boleh berisi huruf, angka, spasi, dan tanda baca tertentu.',
                    'max_length' => '{field} maksimal 50 karakter.'
                ]
            ],
            'kontak' => [
                'label' => 'Nomor Telepon',
                'rules' => 'permit_empty|regex_match[/^((\+62|62|0)8[1-9][0-9]{6,9}|0[2-9][0-9]{1,3}[0-9]{5,8})$/]',
                'errors' => [
                    'regex_match' => '{field} tidak valid. Gunakan format +62..., 08..., atau 0361....'
                ]
            ]
        ];

        if (!$this->validate($rules)) {
            $this->session->setFlashdata('message', implode('<br>', $this->validator->getErrors()));
            return redirect()->to(base_url("admin/wholesaler/tambah"));
        }

        $data = [
            "nama"       => esc($this->request->getPost('wholesaler')),
            "alamat"     => esc($this->request->getPost('alamat')),
            "kontak"     => esc($this->request->getPost('kontak'))
        ];

        $result = $this->wholesalerModel->insertData($data);

        if ($result["code"] == 0) {
            $this->session->setFlashdata('message', 'Data berhasil disimpan.');
            return redirect()->to(base_url("admin/wholesaler"));
        } else {
            $this->session->setFlashdata('message', 'Data gagal disimpan.');
            return redirect()->to(base_url("admin/wholesaler/tambah"));
        }
    }

    public function postUpdateData()
    {
        $rules = [
            'wholesaler' => [
                'label' => 'Nama Wholesaler',
                'rules' => 'required|trim|max_length[30]|alpha_numeric_space',
                'errors' => [
                    'required' => '{field} wajib diisi.',
                    'max_length' => '{field} maksimal 30 karakter.',
                    'alpha_numeric_space' => '{field} hanya boleh berisi huruf, angka, dan spasi.',
                ]
            ],
            'alamat' => [
                'label' => 'Alamat',
                'rules' => 'required|trim|max_length[50]|alpha_numeric_space',
                'errors' => [
                    'required' => '{field} wajib diisi.',
                    'max_length' => '{field} maksimal 50 karakter.',
                    'alpha_numeric_space' => '{field} hanya boleh berisi huruf, angka, dan spasi.'
                ]
            ],
            'kontak' => [
                'label' => 'Nomor Telepon',
                'rules' => 'permit_empty|regex_match[/^((\+62|62|0)8[1-9][0-9]{6,9}|0[2-9][0-9]{1,3}[0-9]{5,8})$/]',
                'errors' => [
                    'regex_match' => '{field} tidak valid. Masukkan nomor HP atau telepon rumah yang benar.'
                ]
            ]
        ];

        $wsalerid = esc($this->request->getPost('wsalerid'));

        if (!$this->validate($rules)) {
            $this->session->setFlashdata('message', implode('<br>', $this->validator->getErrors()));
            return redirect()->to(base_url("admin/wholesaler/ubah/" . base64_encode($wsalerid)));
        }

        $data = [
            "nama"      => esc($this->request->getPost('wholesaler')),
            "alamat"     => esc($this->request->getPost('alamat')),
            "kontak"     => esc($this->request->getPost('kontak'))
        ];

        $result = $this->wholesalerModel->updateData($data, $wsalerid);

        if ($result["code"] == 0) {
            $this->session->setFlashdata('message', 'Data berhasil diubah.');
            return redirect()->to(base_url("admin/wholesaler"));
        } else {
            $this->session->setFlashdata('message', 'Data gagal diubah.');
            return redirect()->to(base_url("admin/wholesaler/ubah/" . base64_encode($wsalerid)));
        }
    }
}
