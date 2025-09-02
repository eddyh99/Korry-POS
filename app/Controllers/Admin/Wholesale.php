<?php

namespace App\Controllers\Admin;

use App\Models\Admin\WholesaleModel;
use App\Models\Admin\WholesalerModel;

use App\Models\Admin\ProdukModel;

use App\Controllers\BaseApiController;

class Wholesale extends BaseApiController
{
    protected $wholesaleModel;
    protected $wholesalerModel;

    public function __construct()
    {
        $this->wholesaleModel   = new WholesaleModel();
        $this->wholesalerModel  = new WholesalerModel();

        $this->produkModel     = new ProdukModel();
    }

    // === Order Wholesale : Index ===

    public function getOrder()
    {
        if (!$this->session->get('logged_status')) {
            return redirect()->to(base_url());
        }

        $data = [
            'title'      => 'Data Order Wholesale',
            'content'    => 'admin/wholesale/order/index',
            'extra'      => 'admin/wholesale/order/js/js_index', 
            'mn_setting' => 'active',
            'colmas'     => 'collapse',
            'colset'     => 'collapse in',
            'collap'     => 'collapse',
            'side11'     => 'active',
        ];
        return view('layout/wrapper', $data);
    }

    public function postOrderlistdata()
    {
        $result = $this->wholesaleModel->listOrderWholesale();
        return $this->response->setJSON($result);
    }

    // === Order Wholesale : Tambah ===

    public function getOrdertambah()
    {
        $wholesaler = $this->wholesalerModel->listWholesaler();
        $produk = $this->produkModel->listProdukOrderWholesale();

        $data = [
            'title'    => 'Tambah Data',
            'content'  => 'admin/wholesale/order/tambah',
            'extra'    => 'admin/wholesale/order/js/js_tambah',
            'extracss' => 'admin/wholesale/order/css/css_tambah',
            'wholesaler'    => $wholesaler,
            'produk'        => $produk,
            'mn_req'   => 'active',
            'colmas'   => 'collapse',
            'colset'   => 'collapse',
            'collap'   => 'collapse',
        ];

        return view('layout/wrapper', $data);
    }

    // === Order Wholesale : Handle POST Tambah ===

    public function postAddDataOrder()
    {
        // Rules validasi
        $rules = [
            // "nonota" => [
            //     "label"  => "No Nota",
            //     "rules"  => "required|exact_length[6]|numeric|is_unique[wholesale_order.notaorder]",
            //     "errors" => [
            //         "required"     => "{field} wajib diisi",
            //         "exact_length" => "{field} harus 6 digit",
            //         "numeric"      => "{field} hanya boleh angka",
            //         "is_unique"    => "{field} sudah digunakan."
            //     ]
            // ],
            "wholesaler" => [
                "label"  => "Wholesaler",
                "rules"  => "required|integer",
                "errors" => [
                    "required" => "{field} wajib dipilih",
                    "integer"  => "{field} tidak valid"
                ]
            ],
            "lama" => [
                "label"  => "Lama Tempo",
                "rules"  => "permit_empty|integer|greater_than_equal_to[0]",
                "errors" => [
                    "integer"                 => "{field} harus berupa angka",
                    "greater_than_equal_to"   => "{field} minimal 0 hari"
                ]
            ],
            "diskon" => [
                "label"  => "Diskon",
                "rules"  => "permit_empty|integer|greater_than_equal_to[0]|less_than_equal_to[100]",
                "errors" => [
                    "integer"               => "{field} harus berupa angka",
                    "greater_than_equal_to" => "{field} minimal 0",
                    "less_than_equal_to"    => "{field} maksimal 100"
                ]
            ],
            "ppn" => [
                "label"  => "PPN",
                "rules"  => "permit_empty|decimal|greater_than_equal_to[0]",
                "errors" => [
                    "decimal"                => "{field} harus berupa angka desimal",
                    "greater_than_equal_to"  => "{field} minimal 0"
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
                return redirect()->to('/admin/wholesale/ordertambah')->withInput();
            }
        }

        // Ambil data setelah validasi
        // $nonota   = esc($this->request->getPost("nonota"));
        $wholesaler  = esc($this->request->getPost("wholesaler"));
        $lama     = $this->request->getPost("lama");
        $diskon   = $this->request->getPost("diskon");
        $ppn      = $this->request->getPost("ppn");
        $barcodes = $this->request->getPost("barcode");
        $jumlahs  = $this->request->getPost("jumlah");
        $potongans  = $this->request->getPost("potongan");

        $data = [
            // "notaorder" => $nonota,
            "id_wholesaler" => $wholesaler,
            "lama"     => ($lama   !== null && $lama   !== "") ? (int) $lama   : 0,
            "diskon"   => ($diskon !== null && $diskon !== "") ? (int) $diskon : 0,
            "ppn"      => ($ppn    !== null && $ppn    !== "") ? (float) $ppn  : 0,
            "userid"   => session()->get("logged_status")["username"],
            "detail"   => []
        ];

        foreach ($barcodes as $i => $barcode) {
            $data["detail"][] = [
                "barcode"  => esc($barcode),
                "jumlah"   => (int) $jumlahs[$i],
                "potongan" => (int) $potongans[$i]
            ];
        }

        $result = $this->wholesaleModel->insertWholesaleOrder($data);

        return $this->response->setJSON($result);
    }
    
    // === Cicilan Wholesale : Index ===

    public function getCicilan()
    {
        if (!$this->session->get('logged_status')) {
            return redirect()->to(base_url());
        }

        $data = [
            'title'      => 'Data Cicilan Wholesale',
            'content'    => 'admin/wholesale/cicilan/index',
            'extra'      => 'admin/wholesale/cicilan/js/js_index', 
            'mn_setting' => 'active',
            'colmas'     => 'collapse',
            'colset'     => 'collapse in',
            'collap'     => 'collapse',
            'side11'     => 'active',
        ];
        return view('layout/wrapper', $data);
    }

    public function postCicilanlistdata()
    {
        $result = $this->wholesaleModel->listCicilanWholesale();
        return $this->response->setJSON($result);
    }

    // === Cicilan Wholesale : Tambah ===

    public function getCicilantambah()
    {
        $wholesale_order = $this->wholesaleModel->listOrderWholesale();

        $data = [
            'title'    => 'Tambah Data',
            'content'  => 'admin/wholesale/cicilan/tambah',
            'wholesale_order' => $wholesale_order,
            'mn_req'   => 'active',
            'colmas'   => 'collapse',
            'colset'   => 'collapse',
            'collap'   => 'collapse',
        ];

        return view('layout/wrapper', $data);
    }

    // === Cicilan Wholesale : Handle POST Tambah ===

    public function postAddDataCicilan()
    {
        $rules = [
            'nonota' => [
                'label'  => 'Nomor Cicilan',
                'rules'  => 'required|max_length[6]|is_unique[wholesale_cicilan.nonota]',
                'errors' => [
                    'required'   => '{field} wajib diisi',
                    'max_length' => '{field} maksimal 6 karakter',
                    'is_unique'  => '{field} sudah digunakan.'
                ]
            ],
            'notaorder' => [
                'label'  => 'Nota Order',
                'rules'  => 'required',
                'errors' => [
                    'required' => '{field} wajib dipilih'
                ]
            ],
            'bayar' => [
                'label'  => 'Nominal Bayar',
                'rules'  => 'required|decimal',
                'errors' => [
                    'required' => '{field} wajib diisi',
                    'decimal'  => '{field} harus berupa angka desimal'
                ]
            ]
        ];

        if (!$this->validate($rules)) {
            $this->session->setFlashdata('message', $this->validator->listErrors());
            return redirect()->to(base_url("admin/wholesale/cicilantambah"));
        }

        $data = [
            'nonota'    => $this->request->getPost('nonota'),
            'notaorder' => $this->request->getPost('notaorder'),
            'bayar'     => $this->request->getPost('bayar'),
            'userid'    => session()->get("logged_status")["username"]
        ];

        $result = $this->wholesaleModel->insertWholesaleCicilan($data);

        if ($result["code"] == 0) {
            $this->session->setFlashdata('message', 'Data cicilan berhasil disimpan.');
            return redirect()->to(base_url("admin/wholesale/cicilan"));
        } else {
            $this->session->setFlashdata('message', 'Gagal menyimpan data cicilan: ' . $result["message"]);
            return redirect()->to(base_url("admin/wholesale/cicilantambah"));
        }
    }

    // === Order Wholesale : Hapus ===

    public function getOrderhapus($notaorder)
    {
        $notaorder = base64_decode(esc($notaorder));

        $data = [
            "is_void" => 1
        ];

        $result = $this->wholesaleModel->hapusOrderWholesale($data, $notaorder);

        if ($result["code"] == 0) {
            $this->session->setFlashdata('message', 'Data berhasil dihapus.');
        } else {
            $this->session->setFlashdata('message', 'Data gagal dihapus.');
        }
        return redirect()->to(base_url("admin/wholesale/order"));
    }    

    // === Order Wholesale : Hapus ===

    public function getCicilanhapus($nonota)
    {
        $nonota = base64_decode(esc($nonota));

        $data = [
            "status" => 'void'
        ];

        $result = $this->wholesaleModel->hapusCicilanWholesale($data, $nonota);

        if ($result["code"] == 0) {
            $this->session->setFlashdata('message', 'Data berhasil dihapus.');
        } else {
            $this->session->setFlashdata('message', 'Data gagal dihapus.');
        }
        return redirect()->to(base_url("admin/wholesale/cicilan"));
    }
}
