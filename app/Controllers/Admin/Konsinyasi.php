<?php

namespace App\Controllers\Admin;

use App\Models\Admin\KonsinyasiModel;

use App\Models\Admin\StoreModel;
use App\Models\Admin\ProdukModel;

use App\Models\Admin\PartnerModel;

use App\Controllers\BaseApiController;

class Konsinyasi extends BaseApiController
{
    protected $konsinyasiModel;

    protected $storeModel;
    protected $produkModel;

    public function __construct()
    {
        $this->konsinyasiModel = new KonsinyasiModel();

        $this->storeModel       = new StoreModel();
        $this->produkModel      = new ProdukModel();

        $this->partnerModel      = new partnerModel();
    }

    public function getDo()
    {
        if (!$this->session->get('logged_status')) {
            return redirect()->to(base_url());
        }

        $data = [
            'title'      => 'Data Do Konsinyasi',
            'content'    => 'admin/konsinyasi/do/index',
            'extra'      => 'admin/konsinyasi/do/js/js_index', 
            'mn_setting' => 'active',
            'colmas'     => 'collapse',
            'colset'     => 'collapse in',
            'collap'     => 'collapse',
            'side11'     => 'active',
        ];
        return view('layout/wrapper', $data);
    }

    public function postDolistdata()
    {
        $result = $this->konsinyasiModel->listDoKonsinyasi();
        return $this->response->setJSON($result);
    }

    public function getDotambah()
    {
        $store = $this->storeModel->Liststore();
        $produk = $this->produkModel->ListProdukDoKonsinyasi();

        $partner = $this->partnerModel->listPartner();

        $data = [
            'title'    => 'Tambah Data',
            'content'  => 'admin/konsinyasi/do/tambah',
            'extra'    => 'admin/konsinyasi/do/js/js_tambah',
            'extracss' => 'admin/konsinyasi/do/css/css_tambah',
            'store'    => $store,
            'produk'   => $produk,
            'partner'   => $partner,
            'mn_req'   => 'active',
            'colmas'   => 'collapse',
            'colset'   => 'collapse',
            'collap'   => 'collapse',
        ];

        return view('layout/wrapper', $data);
    }

    public function getDohapus($nonota_do)
    {
        $nonota_do = base64_decode(esc($nonota_do));

        $data = [
            "is_void" => 1
        ];

        $result = $this->konsinyasiModel->hapusDoKonsinyasi($data, $nonota_do);

        if ($result["code"] == 0) {
            $this->session->setFlashdata('message', 'Data berhasil dihapus.');
        } else {
            $this->session->setFlashdata('message', 'Data gagal dihapus.');
        }
        return redirect()->to(base_url("admin/konsinyasi/do"));
    }

    // Handle Post Add/Insert Data

    // public function postAddData()
    // {
    //     $data = [
    //         "nonota"       => esc($this->request->getPost('...')),
    //         "..."          => esc($this->request->getPost('...')),
    //         "userid"       => session()->get('logged_status')['username'],
    //         "barcode"      => esc($this->request->getPost('...')),
    //         "jumlah"       => $this->request->getPost('jumlah')
    //     ];

    //     $result = $this->produkModel->insertDoKonsinyasi($data);

    //     if ($result["code"] == 0) {
    //         $this->session->setFlashdata('message', 'Data berhasil disimpan.');
    //         return redirect()->to('/admin/produk');
    //     } else {
    //         $this->session->setFlashdata('message', 'Data gagal disimpan.');
    //         return redirect()->to('/admin/produk/tambah')->withInput();
    //     }
    // }

    public function postAddData()
    {
        // Rules validasi
        $rules = [
            "nonota" => [
                "label"  => "No Nota",
                "rules"  => "required|exact_length[6]|numeric|is_unique[do_konsinyasi.nonota]",
                "errors" => [
                    "required"     => "{field} wajib diisi",
                    "exact_length" => "{field} harus 6 digit",
                    "numeric"      => "{field} hanya boleh angka",
                    "is_unique"    => "{field} sudah digunakan."
                ]
            ],
            "partner" => [
                "label"  => "Partner Konsinyasi",
                "rules"  => "required|integer",
                "errors" => [
                    "required" => "{field} wajib dipilih",
                    "integer"  => "{field} tidak valid"
                ]
            ],
            "barcode" => [
                "label"  => "Produk",
                "rules"  => "required",
                "errors" => [
                    "required" => "{field} wajib dipilih"
                ]
            ],
            "jumlah.*" => [ // validasi tiap elemen array jumlah
                "label"  => "Jumlah Produk",
                "rules"  => "required|integer|greater_than[0]",
                "errors" => [
                    "required"     => "{field} wajib diisi",
                    "integer"      => "{field} harus berupa angka",
                    "greater_than" => "{field} harus lebih dari 0"
                ]
            ],
        ];

        // Jalankan validasi

        // if (! $this->validate($rules)) {
        //     return $this->response->setJSON([
        //         "status"  => false,
        //         "message" => $this->validator->listErrors()
        //     ]);
        // }

        // if (! $this->validate($rules)) {
        //     $this->session->setFlashdata('message', $this->validator->listErrors());
        //     return redirect()->to('/admin/konsinyasi/dotambah')->withInput();
        // }

        if (! $this->validate($rules)) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    "status"  => false,
                    "message" => implode("\n", $this->validator->getErrors()),
                    "errors"  => $this->validator->getErrors()
                ]);
            } else {
                $this->session->setFlashdata('message', $this->validator->listErrors());
                return redirect()->to('/admin/konsinyasi/dotambah')->withInput();
            }
        }

        // Ambil data setelah validasi
        $nonota   = esc($this->request->getPost("nonota"));
        $partner  = esc($this->request->getPost("partner"));
        $barcodes = $this->request->getPost("barcode");
        $jumlahs  = $this->request->getPost("jumlah");

        $data = [
            "nonota"  => $nonota,
            "partner" => $partner,
            "userid"  => session()->get("logged_status")["username"],
            "detail"  => []
        ];

        foreach ($barcodes as $i => $barcode) {
            $data["detail"][] = [
                "barcode" => esc($barcode),
                "jumlah"  => (int) $jumlahs[$i]
            ];
        }

        $result = $this->konsinyasiModel->insertDoKonsinyasi($data);

        return $this->response->setJSON($result);
    }   
}
