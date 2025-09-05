<?php

namespace App\Controllers\Admin;

use App\Models\Admin\VendorproduksiModel;

use App\Controllers\BaseApiController;

class Vendorproduksi extends BaseApiController
{
    protected $vendorModel;

    public function __construct()
    {
        $this->vendorModel = new VendorproduksiModel();
    }

    public function getIndex()
    {
        if (!$this->session->get('logged_status')) {
            return redirect()->to(base_url());
        }

        $data = [
            'title'      => 'Data Vendor Produksi',
            'content'    => 'admin/vendorproduksi/index',
            'extra'      => 'admin/vendorproduksi/js/js_index', 
            'mn_master' => 'active',
            'colmas'     => 'collapse',
            'colset'     => 'collapse in',
            'collap'     => 'collapse',
            'side17'     => 'active',
        ];
        return view('layout/wrapper', $data);
    }

    public function postListdata()
    {
        $result = $this->vendorModel->listVendor();
        return $this->response->setJSON($result);
    }

    public function getTambah()
    {
        if (!$this->session->get('logged_status')) {
            return redirect()->to(base_url());
        }

        $data = [
            'title'      => 'Tambah Vendor Produksi',
            'content'    => 'admin/vendorproduksi/tambah',
            'mn_master'  => 'active',
            'colmas'     => 'collapse',
            'colset'     => 'collapse in',
            'collap'     => 'collapse',
            'side17'      => 'active',
        ];
        return view('layout/wrapper', $data);
    }

    public function getUbah($vendorid)
    {
        $vendorid = base64_decode(esc($vendorid));
        $result  = $this->vendorModel->getVendor($vendorid);

        $data = [
            'title'      => 'Ubah Partner Konsinyasi',
            'content'    => 'admin/vendorproduksi/ubah',
            'detail'     => $result,
            'mn_master'  => 'active',
            'colmas'     => 'collapse',
            'colset'     => 'collapse in',
            'collap'     => 'collapse',
            'side17'      => 'active',
        ];
        return view('layout/wrapper', $data);
    }

    public function getHapus($vendorid)
    {
        $vendorid = base64_decode(esc($vendorid));

        $data = [
            "status" => 1
        ];

        $result = $this->vendorModel->hapusData($data, $vendorid);

        if ($result["code"] == 0) {
            $this->session->setFlashdata('message', 'Data berhasil dihapus.');
        } else {
            $this->session->setFlashdata('message', 'Data gagal dihapus.');
        }
        return redirect()->to(base_url("admin/vendorproduksi"));
    }

    // Handle Post Tambah & Ubah

    public function postAddData()
    {
        $rules = [
            'vendorproduksi' => [
                'label' => 'Nama Vendor',
                'rules' => 'required|trim|max_length[30]|alpha_numeric_space',
                'errors' => [
                    'required' => '{field} wajib diisi.',
                    'alpha_numeric_space' => '{field} hanya boleh berisi huruf, angka, dan spasi.',
                    'max_length' => '{field} maksimal 30 karakter.'
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
            return redirect()->to(base_url("admin/vendorproduksi/tambah"));
        }

        $data = [
            "nama"       => esc($this->request->getPost('vendorproduksi')),
            "kontak"     => esc($this->request->getPost('kontak')),
            "tipe"       => esc($this->request->getPost('tipe'))
        ];

        $result = $this->vendorModel->insertData($data);

        if ($result["code"] == 0) {
            $this->session->setFlashdata('message', 'Data berhasil disimpan.');
            return redirect()->to(base_url("admin/vendorproduksi"));
        } else {
            $this->session->setFlashdata('message', 'Data gagal disimpan.');
            return redirect()->to(base_url("admin/vendorproduksi/tambah"));
        }
    }

    public function postUpdateData()
    {
        $rules = [
            'vendorproduksi' => [
                'label' => 'Nama Vendor',
                'rules' => 'required|trim|max_length[30]|alpha_numeric_space',
                'errors' => [
                    'required' => '{field} wajib diisi.',
                    'max_length' => '{field} maksimal 30 karakter.',
                    'alpha_numeric_space' => '{field} hanya boleh berisi huruf, angka, dan spasi.',
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

        $vendorid = esc($this->request->getPost('vendorid'));

        if (!$this->validate($rules)) {
            $this->session->setFlashdata('message', implode('<br>', $this->validator->getErrors()));
            return redirect()->to(base_url("admin/vendorproduksi/ubah/" . base64_encode($vendorid)));
        }

        $data = [
            "nama"       => esc($this->request->getPost('vendorproduksi')),
            "kontak"     => esc($this->request->getPost('kontak')),
            "tipe"       => esc($this->request->getPost('tipe'))
        ];

        $result = $this->vendorModel->updateData($data, $vendorid);

        if ($result["code"] == 0) {
            $this->session->setFlashdata('message', 'Data berhasil diubah.');
            return redirect()->to(base_url("admin/vendorproduksi"));
        } else {
            $this->session->setFlashdata('message', 'Data gagal diubah.');
            return redirect()->to(base_url("admin/vendorproduksi/ubah/" . base64_encode($vendorid)));
        }
    }
}
