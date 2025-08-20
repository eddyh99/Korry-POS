<?php

namespace App\Controllers\Admin;

use App\Models\Admin\StokbahanbakuModel;
use App\Models\Admin\BahanbakuModel;
use App\Models\Admin\ProdukModel;

use App\Controllers\BaseApiController;

class Stokbahanbaku extends BaseApiController
{
    protected $stokbahanbakuModel;

    public function __construct()
    {
        $this->stokbahanbakuModel   = new StokbahanbakuModel();
        $this->bahanbakuModel       = new BahanbakuModel();
        $this->produkModel          = new ProdukModel();
    }

    public function getIndex()
    {
        if (!$this->session->get('logged_status')) {
            return redirect()->to(base_url());
        }

        $data = [
            'title'      => 'Data Stok Bahan Baku',
            'content'    => 'admin/stokbahanbaku/index',
            'extra'      => 'admin/stokbahanbaku/js/js_index', 
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
        $result = $this->stokbahanbakuModel->listStokbahanbaku();
        return $this->response->setJSON($result);
    }

    public function getTambah()
    {
        if (!$this->session->get('logged_status')) {
            return redirect()->to(base_url());
        }

        $bahanbaku  = $this->bahanbakuModel->Listbahanbaku();
        $produk     = $this->produkModel->Listproduk();

        $data = [
            'title'      => 'Tambah Stok Bahan Baku',
            'content'    => 'admin/stokbahanbaku/tambah',
            'bahanbaku'  => $bahanbaku,
            'produk'     => $produk,
            'mn_master'  => 'active',
            'colmas'     => 'collapse',
            'colset'     => 'collapse in',
            'collap'     => 'collapse',
            'side11'     => 'active',
        ];
        return view('layout/wrapper', $data);
    }

    // Handle Post Tambah

    public function postAddData()
    {
        $rules = [
            'barcode' => [
                'label'  => 'Barcode',
                'rules'  => 'required|trim|exact_length[13]|numeric',
                'errors' => [
                    'required'     => '{field} wajib diisi.',
                    'exact_length' => '{field} harus terdiri dari 13 digit.',
                    'numeric'      => '{field} hanya boleh berisi angka.'
                ]
            ],
            'idbahan' => [
                'label' => 'Bahan Baku',
                'rules' => 'required|integer',
                'errors' => [
                    'required' => '{field} wajib dipilih.',
                    'integer' => '{field} tidak valid.'
                ]
            ],
            'jumlah' => [
                'label' => 'Jumlah',
                'rules' => 'required|integer|greater_than[0]',
                'errors' => [
                    'required' => '{field} wajib diisi.',
                    'integer' => '{field} harus berupa angka.',
                    'greater_than' => '{field} harus lebih dari 0.'
                ]
            ],
        ];

        if (!$this->validate($rules)) {
            $this->session->setFlashdata('message', implode('<br>', $this->validator->getErrors()));
            return redirect()->to(base_url("admin/stokbahanbaku/tambah"))->withInput();
        }

        $data = [
            "barcode" => esc($this->request->getPost('barcode')),
            "idbahan" => (int) esc($this->request->getPost('idbahan')),
            "jumlah"  => (int) esc($this->request->getPost('jumlah'))
        ];

        $result = $this->stokbahanbakuModel->insertData($data);

        if ($result["code"] == 0) {
            $this->session->setFlashdata('message', 'Data berhasil disimpan.');
            return redirect()->to(base_url("admin/stokbahanbaku"));
        } else {
            $this->session->setFlashdata('message', 'Data gagal disimpan.');
            return redirect()->to(base_url("admin/stokbahanbaku/tambah"));
        }
    }
}
