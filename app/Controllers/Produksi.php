<?php

namespace App\Controllers;

use App\Models\ProduksiModel;
use App\Models\Admin\ProdukModel;
use App\Models\Admin\VendorproduksiModel;

use App\Controllers\BaseApiController;

class Produksi extends BaseApiController
{
    protected $produksiModel;
    protected $produkModel;
    protected $vendorproduksiModel;

    public function __construct()
    {
        $this->produksiModel        = new ProduksiModel();
        $this->produkModel          = new ProdukModel();
        $this->vendorproduksiModel  = new VendorproduksiModel();
    }

    public function getIndex()
    {
        if (!$this->session->get('logged_status')) {
            return redirect()->to(base_url());
        }

        $data = [
            'title'      => 'Data Stok Bahan Baku',
            'content'    => 'produksi/index',
            'extra'      => 'produksi/js/js_index', 
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
        $result = $this->produksiModel->listProduksi();
        return $this->response->setJSON($result);
    }

    public function getTambah()
    {
        if (!$this->session->get('logged_status')) {
            return redirect()->to(base_url());
        }

        $vendor     = $this->vendorproduksiModel->listVendor();
        $produk     = $this->produkModel->Listproduk();

        $data = [
            'title'      => 'Tambah Produksi',
            'content'    => 'produksi/tambah',
            'vendor'     => $vendor,
            'produk'     => $produk,
            'mn_master'  => 'active',
            'colmas'     => 'collapse',
            'colset'     => 'collapse in',
            'collap'     => 'collapse',
            'side11'     => 'active',
        ];
        return view('layout/wrapper', $data);
    }

    public function getHapus($nonota)
    {
        $userid  = session()->get('logged_status')['username'];
        $nonota  = base64_decode(esc($nonota));

        $data = [
            "status"     => 1,
            "user_id"     => $userid,
            "lastupdate" => date("Y-m-d H:i:s")
        ];

        $result = $this->produksiModel->hapusData($data, $nonota);

        if ($result["code"] == 0) {
            session()->setFlashdata('message', 'Data berhasil dihapus.');
        } else {
            session()->setFlashdata('message', 'Data gagal dihapus.');
        }
        return redirect()->to('/produksi');
    }

    // Handle Post Tambah

    public function postAddData()
    {
        $rules = [
            'idvendor' => [
                'label'  => 'Vendor',
                'rules'  => 'required|integer',
                'errors' => [
                    'required' => '{field} wajib dipilih.',
                    'integer'  => '{field} tidak valid.'
                ]
            ],
            'estimasi' => [
                'label'  => 'Estimasi',
                'rules'  => 'required|integer|greater_than_equal_to[0]',
                'errors' => [
                    'required' => '{field} wajib diisi.',
                    'integer'  => '{field} hanya angka.',
                    'greater_than_equal_to' => '{field} minimal 0.'
                ]
            ],
            'dp' => [
                'label'  => 'DP',
                'rules'  => 'required|integer|greater_than_equal_to[0]',
                'errors' => [
                    'required' => '{field} wajib diisi.',
                    'integer'  => '{field} hanya angka.'
                ]
            ],
            'total' => [
                'label'  => 'Total',
                'rules'  => 'required|integer|greater_than[0]',
                'errors' => [
                    'required' => '{field} wajib diisi.',
                    'integer'  => '{field} hanya angka.',
                    'greater_than' => '{field} harus lebih besar dari 0.'
                ]
            ],
            'barcode' => [
                'label'  => 'Barcode',
                'rules'  => 'required|exact_length[13]|numeric',
                'errors' => [
                    'required'     => '{field} wajib diisi.',
                    'exact_length' => '{field} harus 13 digit.',
                    'numeric'      => '{field} hanya angka.'
                ]
            ],
            'jumlah' => [
                'label'  => 'Jumlah Produksi',
                'rules'  => 'required|integer|greater_than[0]',
                'errors' => [
                    'required'     => '{field} wajib diisi.',
                    'integer'      => '{field} hanya angka.',
                    'greater_than' => '{field} harus lebih dari 0.'
                ]
            ]
        ];

        if (! $this->validate($rules)) {
            $this->session->setFlashdata('message', $this->validator->listErrors());
            return redirect()->to('produksi/tambah')->withInput();
        }

        $data = [
            "idvendor" => esc($this->request->getPost('idvendor')),
            "estimasi" => esc($this->request->getPost('estimasi')),
            "dp"       => esc($this->request->getPost('dp')),
            "total"    => esc($this->request->getPost('total')),
            "user_id"  => session()->get('logged_status')['username'],
            "barcode"  => esc($this->request->getPost('barcode')),
            "jumlah"   => esc($this->request->getPost('jumlah'))
        ];

        $result = $this->produksiModel->insertData($data);

        if ($result["code"] == 0) {
            $this->session->setFlashdata('message', 'Data berhasil disimpan.');
            return redirect()->to('/produksi');
        } else {
            $this->session->setFlashdata('message', 'Data gagal disimpan.');
            return redirect()->to('/produksi/tambah')->withInput();
        }
    }
}
