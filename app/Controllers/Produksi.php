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
            'title'      => 'Data Produksi',
            'content'    => 'produksi/index',
            'extra'      => 'produksi/js/js_index', 
            'mn_produksi' => 'active',
            'collap'     => 'collapse',
            'colmas'     => 'collapse',
            'colset'     => 'collapse',
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
        $produk     = $this->produkModel->ListProdukProduksi();

        $data = [
            'title'      => 'Tambah Produksi',
            'content'    => 'produksi/tambah',
            'extra'      => 'produksi/js/js_tambah', 
            'extracss'   => 'produksi/css/css_tambah',
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
        // Rules validasi
        $rules = [
            "idvendor" => [
                "label"  => "Vendor",
                "rules"  => "required|integer",
                "errors" => [
                    "required" => "{field} wajib dipilih",
                    "integer"  => "{field} tidak valid"
                ]
            ],
            "estimasi" => [
                "label"  => "Estimasi",
                "rules"  => "required|integer|greater_than_equal_to[0]",
                "errors" => [
                    "required" => "{field} wajib diisi",
                    "integer"  => "{field} hanya angka",
                    "greater_than_equal_to" => "{field} minimal 0"
                ]
            ],
            "dp" => [
                "label"  => "DP",
                "rules"  => "permit_empty|integer|greater_than_equal_to[0]", // bisa kosong → default di model
                "errors" => [
                    "integer"  => "{field} hanya angka",
                    "greater_than_equal_to" => "{field} minimal 0"
                ]
            ],
            "barcode.*" => [
                "label"  => "Produk",
                "rules"  => "required|exact_length[13]|numeric",
                "errors" => [
                    "required"     => "{field} wajib diisi",
                    "exact_length" => "{field} harus 13 digit",
                    "numeric"      => "{field} hanya angka"
                ]
            ],
            "jumlah.*" => [
                "label"  => "Jumlah Produksi",
                "rules"  => "required|integer|greater_than[0]",
                "errors" => [
                    "required"     => "{field} wajib diisi",
                    "integer"      => "{field} hanya angka",
                    "greater_than" => "{field} harus lebih dari 0"
                ]
            ]
        ];

        // Jalankan rules validasi
        if (! $this->validate($rules)) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    "status"  => false,
                    "message" => implode("\n", $this->validator->getErrors()),
                    "errors"  => $this->validator->getErrors()
                ]);
            } else {
                $this->session->setFlashdata('message', $this->validator->listErrors());
                return redirect()->to('/produksi/tambah')->withInput();
            }
        }

        // Ambil data setelah validasi
        $barcodes = $this->request->getPost("barcode");
        $jumlahs  = $this->request->getPost("jumlah");
        $hargas   = $this->request->getPost("harga");
        $sizes   = $this->request->getPost("size");

        $data = [
            "idvendor" => esc($this->request->getPost("idvendor")),
            "estimasi" => (int) $this->request->getPost("estimasi"),
            "dp"       => (int) $this->request->getPost("dp") ?: 0,
            "total"    => (int) $this->request->getPost("totalproduksi") ?: 0,
            "user_id"  => session()->get("logged_status")["username"],
            "detail"   => []
        ];

        foreach ($barcodes as $i => $barcode) {
            $data["detail"][] = [
                "barcode" => esc($barcode),
                "jumlah"  => (int) $jumlahs[$i],
                'size'    => esc($sizes[$i]),
                'harga'   => $this->produkModel->getProduk($barcode)->total_biaya_bahan ?? 0,
                "biaya"   => (int) $hargas[$i],
            ];
        }

        $result = $this->produksiModel->insertData($data);

        return $this->response->setJSON($result);
    }
    
    public function getListdatadeadline(){
        $result = $this->produksiModel->listdeadline();
        return $this->response->setJSON($result);
    }

    public function getComplete($nonota=null){
        $result = $this->produksiModel->complete_produksi($nonota);
        return $this->response->setJSON($result);
    }
}
