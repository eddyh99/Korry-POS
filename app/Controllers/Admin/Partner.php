<?php

namespace App\Controllers\Admin;

use App\Models\Admin\PartnerModel;

use App\Controllers\BaseApiController;

class Partner extends BaseApiController
{
    protected $partnerModel;

    public function __construct()
    {
        $this->partnerModel = new PartnerModel();
    }

    public function getIndex()
    {
        if (!$this->session->get('logged_status')) {
            return redirect()->to(base_url());
        }

        $data = [
            'title'      => 'Data Partner Konsinyasi', // ✅ ganti judul
            'content'    => 'admin/partner/index',     // ✅ ganti view
            'extra'      => 'admin/partner/js/js_index', 
            'mn_master' => 'active',
            'colmas'     => 'collapse',
            'colset'     => 'collapse in',
            'collap'     => 'collapse',
            'side18'     => 'active',
        ];
        return view('layout/wrapper', $data);
    }

    public function postListdata()
    {
        $result = $this->partnerModel->listPartner();
        return $this->response->setJSON($result);
    }

    public function getTambah()
    {
        if (!$this->session->get('logged_status')) {
            return redirect()->to(base_url());
        }

        $data = [
            'title'      => 'Tambah Partner Konsinyasi',
            'content'    => 'admin/partner/tambah',
            'mn_master'  => 'active',
            'colmas'     => 'collapse',
            'colset'     => 'collapse in',
            'collap'     => 'collapse',
            'side18'      => 'active',
        ];
        return view('layout/wrapper', $data);
    }

    public function getUbah($partnerid)
    {
        $partnerid = base64_decode(esc($partnerid));
        $result  = $this->partnerModel->getPartner($partnerid);

        $data = [
            'title'      => 'Ubah Partner Konsinyasi',
            'content'    => 'admin/partner/ubah',
            'detail'     => $result,
            'mn_master'  => 'active',
            'colmas'     => 'collapse',
            'colset'     => 'collapse in',
            'collap'     => 'collapse',
            'side18'      => 'active',
        ];
        return view('layout/wrapper', $data);
    }

    public function getHapus($partnerid)
    {
        $partnerid = base64_decode(esc($partnerid));

        $data = [
            "status" => 1
        ];

        $result = $this->partnerModel->hapusData($data, $partnerid);

        if ($result["code"] == 0) {
            $this->session->setFlashdata('message', 'Data berhasil dihapus.');
        } else {
            $this->session->setFlashdata('message', 'Data gagal dihapus.');
        }
        return redirect()->to(base_url("admin/partner"));
    }

    // Handle Post Tambah & Ubah

    public function postAddData()
    {
        $rules = [
            'partner' => [
                'label' => 'Nama Partner',
                'rules' => 'required|trim|max_length[30]|alpha_numeric_space|is_unique[partner_konsinyasi.nama]',
                'errors' => [
                    'required' => '{field} wajib diisi.',
                    'alpha_numeric_space' => '{field} hanya boleh berisi huruf, angka, dan spasi.',
                    'max_length' => '{field} maksimal 30 karakter.',
                    'is_unique'     => '{field} sudah digunakan.'
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
                'rules' => 'required|regex_match[/^((\+62|62|0)8[1-9][0-9]{6,9}|0[2-9][0-9]{1,3}[0-9]{5,8})$/]',
                'errors' => [
                    'required' => '{field} wajib diisi.',
                    'regex_match' => '{field} tidak valid. Gunakan format +62..., 08..., atau 0361....'
                ]
            ]
        ];

        if (!$this->validate($rules)) {
            $this->session->setFlashdata('message', implode('<br>', $this->validator->getErrors()));
            return redirect()->to(base_url("admin/partner/tambah"));
        }

        $data = [
            "nama"       => esc($this->request->getPost('partner')),
            "alamat"     => esc($this->request->getPost('alamat')),
            "kontak"     => esc($this->request->getPost('kontak'))
        ];

        $result = $this->partnerModel->insertData($data);

        if ($result["code"] == 0) {
            $this->session->setFlashdata('message', 'Data berhasil disimpan.');
            return redirect()->to(base_url("admin/partner"));
        } else {
            $this->session->setFlashdata('message', 'Data gagal disimpan.');
            return redirect()->to(base_url("admin/partner/tambah"));
        }
    }

    public function postUpdateData()
    {
        $rules = [
            'partner' => [
                'label' => 'Nama Partner',
                'rules' => 'required|trim|max_length[30]|alpha_numeric_space|is_unique[partner_konsinyasi.nama]',
                'errors' => [
                    'required' => '{field} wajib diisi.',
                    'max_length' => '{field} maksimal 30 karakter.',
                    'alpha_numeric_space' => '{field} hanya boleh berisi huruf, angka, dan spasi.',
                    'is_unique'     => '{field} sudah digunakan.'
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
                'rules' => 'required|regex_match[/^((\+62|62|0)8[1-9][0-9]{6,9}|0[2-9][0-9]{1,3}[0-9]{5,8})$/]',
                'errors' => [
                    'required' => '{field} wajib diisi.',
                    'regex_match' => '{field} tidak valid. Masukkan nomor HP atau telepon rumah yang benar.',
                ]
            ]
        ];

        $partnerid = esc($this->request->getPost('partnerid'));

        if (!$this->validate($rules)) {
            $this->session->setFlashdata('message', implode('<br>', $this->validator->getErrors()));
            return redirect()->to(base_url("admin/partner/ubah/" . base64_encode($partnerid)));
        }

        $data = [
            "nama"      => esc($this->request->getPost('partner')),
            "alamat"     => esc($this->request->getPost('alamat')),
            "kontak"     => esc($this->request->getPost('kontak'))
        ];

        $result = $this->partnerModel->updateData($data, $partnerid);

        if ($result["code"] == 0) {
            $this->session->setFlashdata('message', 'Data berhasil diubah.');
            return redirect()->to(base_url("admin/partner"));
        } else {
            $this->session->setFlashdata('message', 'Data gagal diubah.');
            return redirect()->to(base_url("admin/partner/ubah/" . base64_encode($partnerid)));
        }
    }
}
