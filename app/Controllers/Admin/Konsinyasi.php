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
        $this->storeModel      = new StoreModel();
        $this->produkModel     = new ProdukModel();
        $this->partnerModel    = new partnerModel();
    }


    // === DO Konsinyasi : Index ===

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

    // === DO Konsinyasi : Tambah ===

    public function getDotambah()
    {
        $partner = $this->partnerModel->listPartner();
        $produk  = $this->produkModel->ListProdukDoKonsinyasi();

        $data = [
            'title'    => 'Tambah Data',
            'content'  => 'admin/konsinyasi/do/tambah',
            'extra'    => 'admin/konsinyasi/do/js/js_tambah',
            'extracss' => 'admin/konsinyasi/do/css/css_tambah',
            'partner'  => $partner,
            'produk'   => $produk,
            'mn_req'   => 'active',
            'colmas'   => 'collapse',
            'colset'   => 'collapse',
            'collap'   => 'collapse',
        ];

        return view('layout/wrapper', $data);
    }

    // === DO Konsinyasi : Hapus ===

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

    // === Nota Konsinyasi : Index ===

    public function getNota()
    {
        if (!$this->session->get('logged_status')) {
            return redirect()->to(base_url());
        }

        $data = [
            'title'      => 'Data Nota Konsinyasi',
            'content'    => 'admin/konsinyasi/nota/index',
            'extra'      => 'admin/konsinyasi/nota/js/js_index', 
            'mn_setting' => 'active',
            'colmas'     => 'collapse',
            'colset'     => 'collapse in',
            'collap'     => 'collapse',
            'side11'     => 'active',
        ];
        return view('layout/wrapper', $data);
    }

    public function postNotalistdata()
    {
        $result = $this->konsinyasiModel->listNotaKonsinyasi();
        return $this->response->setJSON($result);
    }

    // === Nota Konsinyasi : Tambah ===

    public function getNotatambah()
    {
        $do_konsinyasi = $this->konsinyasiModel->listDoKonsinyasi();

        $data = [
            'title'    => 'Tambah Data',
            'content'  => 'admin/konsinyasi/nota/tambah',
            'extra'    => 'admin/konsinyasi/nota/js/js_tambah',
            'extracss' => 'admin/konsinyasi/nota/css/css_tambah',
            'do_konsinyasi'   => $do_konsinyasi,
            'mn_req'   => 'active',
            'colmas'   => 'collapse',
            'colset'   => 'collapse',
            'collap'   => 'collapse',
        ];

        return view('layout/wrapper', $data);
    }

    // === Nota Konsinyasi : Hapus ===

    public function getNotahapus($notajual)
    {
        $notajual = base64_decode(esc($notajual));

        $data = [
            "status" => 'void'
        ];

        $result = $this->konsinyasiModel->hapusNotaKonsinyasi($data, $notajual);

        if ($result["code"] == 0) {
            $this->session->setFlashdata('message', 'Data berhasil dihapus.');
        } else {
            $this->session->setFlashdata('message', 'Data gagal dihapus.');
        }
        return redirect()->to(base_url("admin/konsinyasi/nota"));
    }

    // === Retur Konsinyasi : Index ===

    public function getRetur()
    {
        if (!$this->session->get('logged_status')) {
            return redirect()->to(base_url());
        }

        $data = [
            'title'      => 'Data Retur Konsinyasi',
            'content'    => 'admin/konsinyasi/retur/index',
            'extra'      => 'admin/konsinyasi/retur/js/js_index', 
            'mn_setting' => 'active',
            'colmas'     => 'collapse',
            'colset'     => 'collapse in',
            'collap'     => 'collapse',
            'side11'     => 'active',
        ];
        return view('layout/wrapper', $data);
    }

    public function postReturlistdata()
    {
        $result = $this->konsinyasiModel->listReturKonsinyasi();
        return $this->response->setJSON($result);
    }

    // === Retur Konsinyasi : Tambah ===

    public function getReturtambah()
    {
        $do_konsinyasi = $this->konsinyasiModel->listDoKonsinyasi();

        $data = [
            'title'    => 'Tambah Data',
            'content'  => 'admin/konsinyasi/retur/tambah',
            'extra'    => 'admin/konsinyasi/retur/js/js_tambah',
            'extracss' => 'admin/konsinyasi/retur/css/css_tambah',
            'do_konsinyasi'   => $do_konsinyasi,
            'mn_req'   => 'active',
            'colmas'   => 'collapse',
            'colset'   => 'collapse',
            'collap'   => 'collapse',
        ];

        return view('layout/wrapper', $data);
    }
    
    // === Retur Konsinyasi : Hapus ===

    public function getReturhapus($noretur)
    {
        $noretur = base64_decode(esc($noretur));

        $data = [
            "is_void" => 1
        ];

        $result = $this->konsinyasiModel->hapusReturKonsinyasi($data, $noretur);

        if ($result["code"] == 0) {
            $this->session->setFlashdata('message', 'Data berhasil dihapus.');
        } else {
            $this->session->setFlashdata('message', 'Data gagal dihapus.');
        }
        return redirect()->to(base_url("admin/konsinyasi/retur"));
    }

    // === Handle POST Add Data (insert) ===

    public function postAddDataDo()
    {
        // Rules validasi
        $rules = [
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
                return redirect()->to('/admin/konsinyasi/dotambah')->withInput();
            }
        }

        // Ambil data setelah validasi
        $partner  = esc($this->request->getPost("partner"));
        $barcodes = $this->request->getPost("barcode");
        $jumlahs  = $this->request->getPost("jumlah");

        $data = [
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
    // public function postAddDataDo()
    // {
    //     // Rules validasi
    //     $rules = [
    //         "nonota" => [
    //             "label"  => "No Nota",
    //             "rules"  => "required|exact_length[6]|numeric|is_unique[do_konsinyasi.nonota]",
    //             "errors" => [
    //                 "required"     => "{field} wajib diisi",
    //                 "exact_length" => "{field} harus 6 digit",
    //                 "numeric"      => "{field} hanya boleh angka",
    //                 "is_unique"    => "{field} sudah digunakan."
    //             ]
    //         ],
    //         "partner" => [
    //             "label"  => "Partner Konsinyasi",
    //             "rules"  => "required|integer",
    //             "errors" => [
    //                 "required" => "{field} wajib dipilih",
    //                 "integer"  => "{field} tidak valid"
    //             ]
    //         ],
    //         "barcode" => [
    //             "label"  => "Produk",
    //             "rules"  => "required",
    //             "errors" => [
    //                 "required" => "{field} wajib dipilih"
    //             ]
    //         ],
    //         "jumlah.*" => [ // validasi tiap elemen array jumlah
    //             "label"  => "Jumlah Produk",
    //             "rules"  => "required|integer|greater_than[0]",
    //             "errors" => [
    //                 "required"     => "{field} wajib diisi",
    //                 "integer"      => "{field} harus berupa angka",
    //                 "greater_than" => "{field} harus lebih dari 0"
    //             ]
    //         ],
    //     ];

    //     // Jalankan rules validasi
    //     if (! $this->validate($rules)) {
    //         if ($this->request->isAJAX()) {
    //             return $this->response->setJSON([
    //                 "status"  => false,
    //                 "message" => implode("\n", $this->validator->getErrors()),
    //                 "errors"  => $this->validator->getErrors()
    //             ]);
    //         } else {
    //             $this->session->setFlashdata('message', $this->validator->listErrors());
    //             return redirect()->to('/admin/konsinyasi/dotambah')->withInput();
    //         }
    //     }

    //     // Ambil data setelah validasi
    //     $nonota   = esc($this->request->getPost("nonota"));
    //     $partner  = esc($this->request->getPost("partner"));
    //     $barcodes = $this->request->getPost("barcode");
    //     $jumlahs  = $this->request->getPost("jumlah");

    //     $data = [
    //         "nonota"  => $nonota,
    //         "partner" => $partner,
    //         "userid"  => session()->get("logged_status")["username"],
    //         "detail"  => []
    //     ];

    //     foreach ($barcodes as $i => $barcode) {
    //         $data["detail"][] = [
    //             "barcode" => esc($barcode),
    //             "jumlah"  => (int) $jumlahs[$i]
    //         ];
    //     }

    //     $result = $this->konsinyasiModel->insertDoKonsinyasi($data);

    //     return $this->response->setJSON($result);
    // }

    public function postAddDataNota()
    {
        // Rules validasi
        $rules = [
            "diskon" => [
                "label"  => "Diskon",
                "rules"  => "permit_empty|numeric|max_length[6]",
                "errors" => [
                    "numeric"    => "{field} harus berupa angka",
                    "max_length" => "{field} maksimal 6 digit"
                ]
            ],
            "ppn" => [
                "label"  => "PPN",
                "rules"  => "permit_empty|numeric|max_length[6]",
                "errors" => [
                    "numeric"    => "{field} harus berupa angka",
                    "max_length" => "{field} maksimal 6 digit"
                ]
            ],
            // minimal 1 DO harus ada
            "do_konsinyasi" => [
                "label"  => "No. DO Konsinyasi",
                "rules"  => "required",
                "errors" => [
                    "required" => "{field} wajib dipilih minimal 1"
                ]
            ],
            // minimal 1 produk harus ada
            "barcode" => [
                "label"  => "Produk",
                "rules"  => "required",
                "errors" => [
                    "required" => "{field} wajib dipilih minimal 1"
                ]
            ],
            "jumlah.*" => [
                "label"  => "Jumlah",
                "rules"  => "required|numeric|greater_than_equal_to[1]",
                "errors" => [
                    "required"              => "{field} wajib diisi",
                    "numeric"               => "{field} harus berupa angka",
                    "greater_than_equal_to" => "{field} minimal 1"
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
                return redirect()->to('/admin/konsinyasi/notatambah')->withInput();
            }
        }

        // Ambil input utama
        $diskon        = (float) $this->request->getPost("diskon");
        $ppn           = (float) $this->request->getPost("ppn");
        $do_konsinyasi = $this->request->getPost("do_konsinyasi");  // single value
        $barcodes      = $this->request->getPost("barcode");        // array
        $jumlahs       = $this->request->getPost("jumlah");         // array

        if (empty($do_konsinyasi) || empty($barcodes) || empty($jumlahs)) {
            return $this->response->setJSON([
                "status"  => false,
                "message" => "Data tidak lengkap"
            ]);
        }

        // Susun data header + detail
        $data = [
            "diskon"   => $diskon,
            "ppn"      => $ppn,
            "userid"   => session()->get("logged_status")["username"],
            "detail"   => []
        ];

        foreach ($barcodes as $i => $barcode) {
            $data["detail"][] = [
                "notakonsinyasi" => esc($do_konsinyasi),
                "barcode"        => esc($barcode),
                "jumlah"         => (int) $jumlahs[$i],
            ];
        }

        // Simpan data via Model
        $result = $this->konsinyasiModel->insertNotaKonsinyasi($data);

        return $this->response->setJSON($result);
    }
    // public function postAddDataNota()
    // {
    //     // Rules validasi
    //     $rules = [
    //         "notajual" => [
    //             "label"  => "Nota Jual",
    //             "rules"  => "required|numeric|max_length[6]|is_unique[nota_konsinyasi.notajual]",
    //             "errors" => [
    //                 "required"   => "{field} wajib diisi",
    //                 "numeric"    => "{field} harus berupa angka",
    //                 "max_length" => "{field} maksimal 6 digit",
    //                 "is_unique"  => "{field} sudah digunakan."
    //             ]
    //         ],
    //         "diskon" => [
    //             "label"  => "Diskon",
    //             "rules"  => "permit_empty|numeric|max_length[6]",
    //             "errors" => [
    //                 "numeric"    => "{field} harus berupa angka",
    //                 "max_length" => "{field} maksimal 6 digit"
    //             ]
    //         ],
    //         "ppn" => [
    //             "label"  => "PPN",
    //             "rules"  => "permit_empty|numeric|max_length[6]",
    //             "errors" => [
    //                 "numeric"    => "{field} harus berupa angka",
    //                 "max_length" => "{field} maksimal 6 digit"
    //             ]
    //         ],
    //         // minimal 1 detail harus dikirim (array)
    //         "do_id" => [
    //             "label"  => "No. DO Konsinyasi",
    //             "rules"  => "required",
    //             "errors" => [
    //                 "required" => "{field} wajib dipilih minimal 1"
    //             ]
    //         ],
    //         "barcode" => [
    //             "label"  => "Produk",
    //             "rules"  => "required",
    //             "errors" => [
    //                 "required" => "{field} wajib dipilih minimal 1"
    //             ]
    //         ],
    //         "jumlah" => [
    //             "label"  => "Jumlah",
    //             "rules"  => "required|numeric|greater_than_equal_to[1]",
    //             "errors" => [
    //                 "required"                => "{field} wajib diisi",
    //                 "numeric"                 => "{field} harus berupa angka",
    //                 "greater_than_equal_to"   => "{field} minimal 1"
    //             ]
    //         ],
    //     ];

    //     // Jalankan rules validasi
    //     if (! $this->validate($rules)) {
    //         if ($this->request->isAJAX()) {
    //             return $this->response->setJSON([
    //                 "status"  => false,
    //                 "message" => implode("\n", $this->validator->getErrors()),
    //                 "errors"  => $this->validator->getErrors()
    //             ]);
    //         } else {
    //             $this->session->setFlashdata('message', $this->validator->listErrors());
    //             return redirect()->to('/admin/konsinyasi/notatambah')->withInput();
    //         }
    //     }

    //     // Ambil input utama
    //     $notajual = esc($this->request->getPost("notajual"));
    //     $diskon   = (float) $this->request->getPost("diskon");
    //     $ppn      = (float) $this->request->getPost("ppn");

    //     // Array detail
    //     $do_ids   = $this->request->getPost("do_id");     // array notakonsinyasi
    //     $barcodes = $this->request->getPost("barcode");   // array barcode
    //     $jumlahs  = $this->request->getPost("jumlah");    // array jumlah

    //     if (!$notajual || empty($do_ids) || empty($barcodes) || empty($jumlahs)) {
    //         return $this->response->setJSON([
    //             "status"  => false,
    //             "message" => "Data tidak lengkap"
    //         ]);
    //     }

    //     $data = [
    //         "notajual" => $notajual,
    //         "diskon"   => $diskon,
    //         "ppn"      => $ppn,
    //         "userid"   => session()->get("logged_status")["username"],
    //         "detail"   => []
    //     ];

    //     foreach ($barcodes as $i => $barcode) {
    //         $data["detail"][] = [
    //             "notakonsinyasi" => esc($do_ids[$i]),
    //             "barcode"        => esc($barcode),
    //             "jumlah"         => (int) $jumlahs[$i],
    //         ];
    //     }

    //     $result = $this->konsinyasiModel->insertNotaKonsinyasi($data);

    //     return $this->response->setJSON($result);
    // }

    public function postAddDataRetur()
    {
        // Rules validasi
        $rules = [
            "do_konsinyasi" => [
                "label"  => "No. DO Konsinyasi",
                "rules"  => "required",
                "errors" => [
                    "required" => "{field} wajib dipilih"
                ]
            ],
            "noretur" => [
                "label"  => "No. Retur",
                "rules"  => "required|numeric|max_length[6]|is_unique[retur_konsinyasi.noretur]",
                "errors" => [
                    "required"   => "{field} wajib diisi",
                    "numeric"    => "{field} harus berupa angka",
                    "max_length" => "{field} maksimal 6 digit",
                    "is_unique"  => "{field} sudah digunakan."
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
                return redirect()->to('/admin/konsinyasi/returtambah')->withInput();
            }
        }

        // Ambil input
        $noretur      = esc($this->request->getPost("noretur"));
        $nokonsinyasi = esc($this->request->getPost("do_konsinyasi"));
        $details      = $this->request->getPost("details"); // array dari JS

        // Validasi tambahan untuk detail produk
        if (empty($details) || !is_array($details)) {
            return $this->response->setJSON([
                "status"  => false,
                "message" => "Detail retur belum diisi"
            ]);
        }

        foreach ($details as $row) {
            if (empty($row["barcode"]) || (int)$row["jumlah"] <= 0) {
                return $this->response->setJSON([
                    "status"  => false,
                    "message" => "Detail retur tidak valid. Pastikan produk, jumlah, dan alasan diisi."
                ]);
            }
        }

        // Siapkan data untuk disimpan
        $data = [
            "noretur"      => $noretur,
            "nokonsinyasi" => $nokonsinyasi,
            "userid"       => session()->get("logged_status")["username"],
            "detail"       => []
        ];

        foreach ($details as $row) {
            $data["detail"][] = [
                "barcode" => esc($row["barcode"]),
                "jumlah"  => (int) $row["jumlah"],
                "alasan"  => esc($row["alasan"])
            ];
        }

        // Simpan ke model
        $result = $this->konsinyasiModel->insertReturKonsinyasi($data);

        return $this->response->setJSON($result);
    }

    // === Nota & Retur Konsinyasi : Fungsi Pendukung ===
    public function getListdo()
    {
        $doList = $this->konsinyasiModel->getAvailableDO();
        return $this->response->setJSON($doList);
    }

    public function postListprodukbydo()
    {
        $do_id = $this->request->getPost("do_id");

        if (!$do_id) {
            return $this->response->setJSON([]);
        }

        $produk = $this->konsinyasiModel->getProdukByDo($do_id);

        return $this->response->setJSON($produk);
    }
}
