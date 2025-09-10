<?php

namespace App\Controllers\Admin;

use App\Models\Admin\ProdukModel;
use App\Models\Admin\BrandModel;
use App\Models\Admin\KategoriModel;
use App\Models\Admin\BahanbakuModel;

use App\Models\Admin\FabricModel;
use App\Models\Admin\WarnaModel;

use App\Models\Admin\BiayaproduksiModel;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;

use App\Controllers\BaseApiController;

class Produk extends BaseApiController
{
    protected $produkModel;
    protected $brandModel;
    protected $kategoriModel;

    public function __construct()
    {
        $this->produkModel      = new ProdukModel();
        $this->brandModel       = new BrandModel();
        $this->kategoriModel    = new KategoriModel();
        $this->bahanbakuModel   = new BahanbakuModel();

        $this->fabricModel      = new FabricModel();
        $this->warnaModel       = new WarnaModel();

        $this->biayaproduksiModel   = new BiayaproduksiModel();
    }

    public function getIndex()
    {
        $data = [
            'title'      => 'Data Produk',
            'content'    => 'admin/produk/index',
            'extra'      => 'admin/produk/js/js_index',
            'mn_master'  => 'active',
            'colset'     => 'collapse',
            'colmas'     => 'collapse in',
            'colkonsi'   => 'collapse',
            'colwho'     => 'collapse',
            'collap'     => 'collapse',
            'side13'     => 'active',
        ];
        return view('layout/wrapper', $data);
    }

    
    public function postListdata()
    {
        $columns = ['barcode', 'namaproduk', 'namabrand', 'namakategori', 'harga', 'harga_konsinyasi', 'harga_wholesale'];

        $start  = $this->request->getPost('start');
        $limit  = $this->request->getPost('length');
        $order  = $columns[$this->request->getPost('order')[0]['column']];
        $dir    = $this->request->getPost('order')[0]['dir'];

        $totalData = $this->produkModel->allposts_count();
        $totalFiltered = $totalData;

        if (empty($this->request->getPost('search')['value'])) {
            $result = $this->produkModel->allposts($limit, $start, $order, $dir);
        } else {
            $search = $this->request->getPost('search')['value'];
            $result = $this->produkModel->posts_search($limit, $start, $search, $order, $dir);
            $totalFiltered = $this->produkModel->posts_search_count($search);
        }

        return $this->response->setJSON([
            "recordsTotal"    => $totalData,
            "recordsFiltered" => $totalFiltered,
            "produk"          => $result
        ]);
    }

    public function getTambah()
    {
        $brand      = $this->brandModel->Listbrand();
        $kategori   = $this->kategoriModel->Listkategori();
        $bahanbaku  = $this->bahanbakuModel->Listbahanbaku();

        $fabric = $this->fabricModel->listFabric();
        $warna  = $this->warnaModel->listWarna();

        $biayaproduksi  = $this->biayaproduksiModel->listBiayaproduksi();

        $data = [
            'title'    => 'Tambah Data Produk',
            'content'  => 'admin/produk/tambah',
            'extra'      => 'admin/produk/js/js_tambah',
            'brand'    => $brand,
            'kategori' => $kategori,
            'fabric'    => $fabric,
            'warna'     => $warna,
            'bahanbaku' => $bahanbaku,
            'biayaproduksi' => $biayaproduksi,
            'mn_master' => 'active',
            'colset'    => 'collapse',
            'colmas'    => 'collapse in',
            'colkonsi'  => 'collapse',
            'colwho'    => 'collapse',
            'collap'    => 'collapse',
            'side13'    => 'active',
        ];
        return view('layout/wrapper', $data);
    }

    public function getUbah($barcode)
    {
        $barcode    = base64_decode(esc($barcode));
        $produk     = $this->produkModel->getProduk($barcode);

        $brand      = $this->brandModel->Listbrand();
        $kategori   = $this->kategoriModel->Listkategori();

        $fabric = $this->fabricModel->listFabric();
        $warna  = $this->warnaModel->listWarna();

        $bahanbaku  = $this->bahanbakuModel->Listbahanbaku();

        $produkBahan = $this->produkModel->getProdukBahan($barcode);
        
        $biayaproduksi  = $this->biayaproduksiModel->listBiayaproduksi();

        $produkBiaya = $this->produkModel->getProdukBiaya($barcode);

        $data = [
            'title'    => 'Ubah Data Produk',
            'content'  => 'admin/produk/ubah',
            'extra'      => 'admin/produk/js/js_ubah',
            'produk'   => $produk,
            'barcode'  => $barcode,
            'brand'    => $brand,
            'kategori' => $kategori,
            'fabric'    => $fabric,
            'warna'     => $warna,
            'bahanbaku' => $bahanbaku,
            'produkBahan'=> $produkBahan,
            'biayaproduksi' => $biayaproduksi,
            'produkBiaya'=> $produkBiaya,
            'mn_master' => 'active',
            'colset'    => 'collapse',
            'colmas'    => 'collapse in',
            'colkonsi'  => 'collapse',
            'colwho'    => 'collapse',
            'collap'    => 'collapse',
            'side13'    => 'active',
        ];
        return view('layout/wrapper', $data);
    }

    public function getHapus($barcode)
    {
        $userid  = session()->get('logged_status')['username'];
        $barcode = base64_decode(esc($barcode));

        $data = [
            "status" => 1,
            "userid" => $userid
        ];

        $result = $this->produkModel->hapusData($data, $barcode);

        if ($result["code"] == 0) {
            $this->session->setFlashdata('message', 'Data berhasil dihapus.');
        } else {
            $this->session->setFlashdata('message', 'Data gagal dihapus.');
        }
        return redirect()->to('/admin/produk');
    }

// Handle Post Add & Update Data

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
            'produk' => [
                'label'  => 'Nama Produk',
                'rules'  => 'required|trim|max_length[50]|alpha_numeric_space',
                'errors' => [
                    'required'            => '{field} wajib diisi.',
                    'max_length'          => '{field} maksimal 50 karakter.',
                    'alpha_numeric_space' => '{field} hanya boleh berisi huruf, angka, dan spasi.'
                ]
            ],
            'fabric' => [
                'label' => 'Nama Fabric',
                'rules' => 'required|trim|max_length[30]|alpha_numeric_space',
                'errors' => [
                    'required' => '{field} wajib diisi.',
                    'max_length' => '{field} maksimal 30 karakter.',
                    'alpha_numeric_space' => '{field} hanya boleh berisi huruf, angka, dan spasi.'
                ]
            ],
            'warna' => [
                'label' => 'Nama Warna',
                'rules' => 'required|trim|max_length[30]|alpha_numeric_space',
                'errors' => [
                    'required' => '{field} wajib diisi.',
                    'max_length' => '{field} maksimal 30 karakter.',
                    'alpha_numeric_space' => '{field} hanya boleh berisi huruf, angka, dan spasi.'
                ]
            ],
            'brand' => [
                'label'  => 'Nama Brand',
                'rules'  => 'required|trim|max_length[50]|alpha_numeric_space',
                'errors' => [
                    'required'            => '{field} wajib diisi.',
                    'max_length'          => '{field} maksimal 50 karakter.',
                    'alpha_numeric_space' => '{field} hanya boleh berisi huruf, angka, dan spasi.'
                ]
            ],
            'kategori' => [
                'label'  => 'Kategori',
                'rules'  => 'required|trim|max_length[50]|alpha_numeric_space',
                'errors' => [
                    'required'            => '{field} wajib diisi.',
                    'max_length'          => '{field} maksimal 50 karakter.',
                    'alpha_numeric_space' => '{field} hanya boleh berisi huruf, angka, dan spasi.'
                ]
            ],
            'harga' => [
                'label'  => 'Harga Retail',
                'rules'  => 'required|trim|numeric|max_length[10]',
                'errors' => [
                    'required'   => '{field} wajib diisi.',
                    'numeric'    => '{field} hanya boleh berisi angka.',
                    'max_length' => '{field} maksimal 10 digit.'
                ]
            ],
            'hargakonsinyasi' => [
                'label'  => 'Harga Konsinyasi',
                'rules'  => 'required|trim|numeric|max_length[10]',
                'errors' => [
                    'required'   => '{field} wajib diisi.',
                    'numeric'    => '{field} hanya boleh berisi angka.',
                    'max_length' => '{field} maksimal 10 digit.'
                ]
            ],
            'hargawholesale' => [
                'label'  => 'Harga Wholesale',
                'rules'  => 'required|trim|numeric|max_length[10]',
                'errors' => [
                    'required'   => '{field} wajib diisi.',
                    'numeric'    => '{field} hanya boleh berisi angka.',
                    'max_length' => '{field} maksimal 10 digit.'
                ]
            ],
            'diskon' => [
                'label'  => 'Diskon',
                'rules'  => 'permit_empty|trim|numeric|max_length[10]',
                'errors' => [
                    'numeric'    => '{field} hanya boleh berisi angka.',
                    'max_length' => '{field} maksimal 10 digit.'
                ]
            ],
            'sku' => [
                'label'  => 'SKU',
                'rules'  => 'required|trim|alpha_dash|max_length[10]',
                'errors' => [
                    'required'    => '{field} wajib diisi.',
                    'alpha_numeric' => '{field} hanya boleh huruf, dash, underscore dan angka.',
                    'min_length'  => '{field} harus 10 karakter.',
                    'max_length'  => '{field} harus 10 karakter.'
                ]
            ],
            'bahanbaku.*' => [
                'label'  => 'Bahan Baku',
                'rules'  => 'required|trim|integer',
                'errors' => [
                    'required' => '{field} wajib dipilih.',
                    'integer'  => '{field} harus berupa ID yang valid.'
                ]
            ],
            'jumlah.*' => [
                'label'  => 'Jumlah Bahan',
                'rules'  => 'required|trim|numeric|greater_than[0]',
                'errors' => [
                    'required'     => '{field} wajib diisi.',
                    'numeric'      => '{field} hanya boleh berisi angka.',
                    'greater_than' => '{field} harus lebih dari 0.'
                ]
            ],
            'biayaproduksi.*' => [
                'label'  => 'Jenis Biaya Produksi',
                'rules'  => 'required|trim',
                'errors' => [
                    'required' => '{field} wajib dipilih.'
                ]
            ],
            'hargaproduksi.*' => [
                'label'  => 'Nominal Biaya Produksi',
                'rules'  => 'required|trim|numeric|greater_than[0]',
                'errors' => [
                    'required'     => '{field} wajib diisi.',
                    'numeric'      => '{field} hanya boleh berisi angka.',
                    'greater_than' => '{field} harus lebih dari 0.'
                ]
            ]
        ];

        if (! $this->validate($rules)) {
            $this->session->setFlashdata('message', $this->validator->listErrors());
            return redirect()->to('/admin/produk/tambah')->withInput();
        }

        // Ambil inputan dulu
        $bahanbaku = $this->request->getPost('bahanbaku'); // bisa single atau array
        $jumlah    = $this->request->getPost('jumlah');
        $biayaproduksi = $this->request->getPost('biayaproduksi');
        $hargaproduksi = $this->request->getPost('hargaproduksi');

        $data = [
            "barcode"      => esc($this->request->getPost('barcode')),
            "namaproduk"   => esc($this->request->getPost('produk')),
            "fabric"       => esc($this->request->getPost('fabric')),
            "warna"        => esc($this->request->getPost('warna')),
            "namabrand"    => esc($this->request->getPost('brand')),
            "namakategori" => esc($this->request->getPost('kategori')),
            "harga"        => esc($this->request->getPost('harga')),
            "hargakonsinyasi" => esc($this->request->getPost('hargakonsinyasi')),
            "hargawholesale"  => esc($this->request->getPost('hargawholesale')),
            "diskon"       => esc($this->request->getPost('diskon')),
            "userid"       => session()->get('logged_status')['username'],
            "sku"          => esc($this->request->getPost('sku')),
        ];

        // Format ulang bahanbaku
        $data['bahanbaku'] = [];
        if (!empty($bahanbaku) && is_array($bahanbaku)) {
            foreach ($bahanbaku as $i => $idbahan) {
                $data['bahanbaku'][] = [
                    "barcode" => $data['barcode'],
                    "idbahan" => esc($idbahan),
                    "jumlah"  => isset($jumlah[$i]) ? esc($jumlah[$i]) : 0,
                ];
            }
        }

        // Format ulang biaya produksi
        $data['biayaproduksi'] = [];
        if (!empty($biayaproduksi) && is_array($biayaproduksi)) {
            foreach ($biayaproduksi as $i => $namabiaya) {
                $data['biayaproduksi'][] = [
                    "barcode" => $data['barcode'],
                    "namabiaya"     => esc($namabiaya),
                    "nominal" => isset($hargaproduksi[$i]) ? esc($hargaproduksi[$i]) : 0,
                ];
            }
        }

        $result = $this->produkModel->insertData($data);

        if ($result["code"] == 0) {
            $this->session->setFlashdata('message', 'Data berhasil disimpan.');
            return redirect()->to('/admin/produk');
        } else {
            $this->session->setFlashdata('message', 'Data gagal disimpan.');
            return redirect()->to('/admin/produk/tambah')->withInput();
        }
    }

    public function postUpdateData()
    {
        $rules = [
            'produk' => [
                'label'  => 'Nama Produk',
                'rules'  => 'required|trim|max_length[50]|alpha_numeric_space',
                'errors' => [
                    'required'            => '{field} wajib diisi.',
                    'max_length'          => '{field} maksimal 50 karakter.',
                    'alpha_numeric_space' => '{field} hanya boleh berisi huruf, angka, dan spasi.'
                ]
            ],
            'fabric' => [
                'label' => 'Nama Fabric',
                'rules' => 'required|trim|max_length[30]|alpha_numeric_space',
                'errors' => [
                    'required' => '{field} wajib diisi.',
                    'max_length' => '{field} maksimal 30 karakter.',
                    'alpha_numeric_space' => '{field} hanya boleh berisi huruf, angka, dan spasi.'
                ]
            ],
            'warna' => [
                'label' => 'Nama Warna',
                'rules' => 'required|trim|max_length[30]|alpha_numeric_space',
                'errors' => [
                    'required' => '{field} wajib diisi.',
                    'max_length' => '{field} maksimal 30 karakter.',
                    'alpha_numeric_space' => '{field} hanya boleh berisi huruf, angka, dan spasi.'
                ]
            ],
            'brand' => [
                'label'  => 'Nama Brand',
                'rules'  => 'required|trim|max_length[50]|alpha_numeric_space',
                'errors' => [
                    'required'            => '{field} wajib diisi.',
                    'max_length'          => '{field} maksimal 50 karakter.',
                    'alpha_numeric_space' => '{field} hanya boleh berisi huruf, angka, dan spasi.'
                ]
            ],
            'kategori' => [
                'label'  => 'Kategori',
                'rules'  => 'required|trim|max_length[50]|alpha_numeric_space',
                'errors' => [
                    'required'            => '{field} wajib diisi.',
                    'max_length'          => '{field} maksimal 50 karakter.',
                    'alpha_numeric_space' => '{field} hanya boleh berisi huruf, angka, dan spasi.'
                ]
            ],
            'harga' => [
                'label'  => 'Harga Retail',
                'rules'  => 'required|trim|numeric|max_length[10]',
                'errors' => [
                    'required'   => '{field} wajib diisi.',
                    'numeric'    => '{field} hanya boleh berisi angka.',
                    'max_length' => '{field} maksimal 10 digit.'
                ]
            ],
            'hargakonsinyasi' => [
                'label'  => 'Harga Konsinyasi',
                'rules'  => 'required|trim|numeric|max_length[10]',
                'errors' => [
                    'required'   => '{field} wajib diisi.',
                    'numeric'    => '{field} hanya boleh berisi angka.',
                    'max_length' => '{field} maksimal 10 digit.'
                ]
            ],
            'hargawholesale' => [
                'label'  => 'Harga Wholesale',
                'rules'  => 'required|trim|numeric|max_length[10]',
                'errors' => [
                    'required'   => '{field} wajib diisi.',
                    'numeric'    => '{field} hanya boleh berisi angka.',
                    'max_length' => '{field} maksimal 10 digit.'
                ]
            ],
            'diskon' => [
                'label'  => 'Diskon',
                'rules'  => 'permit_empty|trim|numeric|max_length[10]',
                'errors' => [
                    'numeric'    => '{field} hanya boleh berisi angka.',
                    'max_length' => '{field} maksimal 10 digit.'
                ]
            ],
            'sku' => [
                'label'  => 'SKU',
                'rules'  => 'required|trim|alpha_dash|max_length[10]',
                'errors' => [
                    'required'      => '{field} wajib diisi.',
                    'alpha_dash'    => '{field} hanya boleh huruf, dash, underscore dan angka.',
                    'min_length'    => '{field} harus 10 karakter.',
                    'max_length'    => '{field} harus 10 karakter.'
                ]
            ],
            'bahanbaku.*' => [
                'label'  => 'Bahan Baku',
                'rules'  => 'required|trim|integer',
                'errors' => [
                    'required' => '{field} wajib dipilih.',
                    'integer'  => '{field} harus berupa ID yang valid.'
                ]
            ],
            'jumlah.*' => [
                'label'  => 'Jumlah Bahan',
                'rules'  => 'required|trim|numeric|greater_than[0]',
                'errors' => [
                    'required'     => '{field} wajib diisi.',
                    'numeric'      => '{field} hanya boleh berisi angka.',
                    'greater_than' => '{field} harus lebih dari 0.'
                ]
            ],
            'biayaproduksi.*' => [
                'label'  => 'Jenis Biaya Produksi',
                'rules'  => 'required|trim',
                'errors' => [
                    'required' => '{field} wajib dipilih.'
                ]
            ],
            'hargaproduksi.*' => [
                'label'  => 'Nominal Biaya Produksi',
                'rules'  => 'required|trim|numeric|greater_than[0]',
                'errors' => [
                    'required'     => '{field} wajib diisi.',
                    'numeric'      => '{field} hanya boleh berisi angka.',
                    'greater_than' => '{field} harus lebih dari 0.'
                ]
            ]
        ];

        if (! $this->validate($rules)) {
            $this->session->setFlashdata('message', $this->validator->listErrors());
            return redirect()->back()->withInput();
        }

        $barcode = esc($this->request->getPost('barcode'));
        $bahanbaku = $this->request->getPost('bahanbaku'); // bisa single atau array
        $jumlah    = $this->request->getPost('jumlah');
        $biayaproduksi = $this->request->getPost('biayaproduksi');
        $hargaproduksi = $this->request->getPost('hargaproduksi');

        $data = [
            "namaproduk"   => esc($this->request->getPost('produk')),
            "namabrand"    => esc($this->request->getPost('brand')),
            "namakategori" => esc($this->request->getPost('kategori')),
            // Tambahan : Fabric & Warna
            "fabric" => esc($this->request->getPost('fabric')),
            "warna"  => esc($this->request->getPost('warna')),
            // Tambahan : Biaya Produksi
            "biayaproduksi" => esc($this->request->getPost('biayaproduksi')),

            "harga"        => esc($this->request->getPost('harga')),
            // Tambahan : Harga Konsinyasi & Harga Wholesale
            "hargakonsinyasi" => esc($this->request->getPost('hargakonsinyasi')),
            "hargawholesale"  => esc($this->request->getPost('hargawholesale')),
            // Tambahan : Harga Produksi
            "hargaproduksi"  => esc($this->request->getPost('hargaproduksi')),

            "diskon"       => esc($this->request->getPost('diskon')),
            "userid"       => session()->get('logged_status')['username'],
            "sku"          => esc($this->request->getPost('sku')),
        ];

        // Format ulang bahanbaku
        $data['bahanbaku'] = [];
        if (!empty($bahanbaku) && is_array($bahanbaku)) {
            foreach ($bahanbaku as $i => $idbahan) {
                $data['bahanbaku'][] = [
                    "barcode" => $barcode,
                    "idbahan" => esc($idbahan),
                    "jumlah"  => isset($jumlah[$i]) ? esc($jumlah[$i]) : 0,
                ];
            }
        }

        // Format ulang biaya produksi
        $data['biayaproduksi'] = [];
        if (!empty($biayaproduksi) && is_array($biayaproduksi)) {
            foreach ($biayaproduksi as $i => $namabiaya) {
                $data['biayaproduksi'][] = [
                    "barcode" => $barcode,
                    "namabiaya"     => esc($namabiaya),
                    "nominal" => isset($hargaproduksi[$i]) ? esc($hargaproduksi[$i]) : 0,
                ];
            }
        }

        $result = $this->produkModel->setData($data, $barcode);

        if ($result["code"] == 0) {
            // update relasi produk_bahan
            $this->produkModel->setProdukBahan($barcode, $bahanbaku, $jumlah);
            // update relasi produk_biaya
            $this->produkModel->setProdukBiaya($barcode, $biayaproduksi, $hargaproduksi);

            $this->session->setFlashdata('message', 'Data berhasil diubah.');
            return redirect()->to('/admin/produk');
        } else {
            $this->session->setFlashdata('message', 'Data gagal diubah.');
            return redirect()->to('/admin/produk/ubah/' . base64_encode($barcode));
        }
    }

// Handle import

    public function postImport()
    {
        $userid = session()->get('logged_status')['username'];

        $fileMimes = [
            'application/vnd.ms-excel',
            'application/excel',
            'application/vnd.msexcel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
        ];

        $file = $this->request->getFile('produk');

        if ($file && in_array($file->getMimeType(), $fileMimes)) {
            log_message('debug', 'File diterima dengan MIME: ' . $file->getMimeType());

            $reader = new Xlsx();
            $spreadsheet = $reader->load($file->getTempName());
            $sheetData = $spreadsheet->getActiveSheet()->toArray();

            log_message('debug', 'Data sheet di-load, total baris: ' . count($sheetData));

            $array = array_map('array_filter', $sheetData);
            $array = array_filter($array);
            $input = array_map("unserialize", array_unique(array_map("serialize", $array)));

            log_message('debug', 'Data uniq setelah filter, total baris: ' . count($input));

            $data = [];
            foreach ($input as $index => $dt) {
                if (count($dt) < 5) {
                    log_message('error', "Baris ke-$index invalid, kurang kolom: " . json_encode($dt));
                    continue;
                }

                $temp["barcode"]      = $dt[0];
                $temp["namaproduk"]   = $dt[1];
                $temp["namabrand"]    = $dt[2];
                $temp["namakategori"] = $dt[3];
                $temp["harga"]        = $dt[4];
                $temp["userid"]       = $userid;
                $data[]               = $temp;
            }

            log_message('debug', 'Data siap insert, total record valid: ' . count($data));

            $result = $this->produkModel->insertbatchData($data);

            if ($result["code"] == 0) {
                $this->session->setFlashdata('message', 'Data Berhasil Di-import');
                return redirect()->to('/admin/produk');
            } else {
                log_message('error', 'Gagal insert batch data: ' . $result["message"]);
                $this->session->setFlashdata('message', 'Data Gagal Di-import');
                return redirect()->to('/admin/produk/tambah');
            }
        }

        log_message('error', 'File tidak valid atau tidak ditemukan saat import');
        $this->session->setFlashdata('message', 'File tidak Valid');
        return redirect()->to('/admin/produk/tambah');
    }


    public function getPanggil($barcode)
    {
        $userid  = session()->get('logged_status')['username'];
        $barcode = base64_decode(esc($barcode));

        $data = [
            "status" => 0,
            "userid" => $userid
        ];

        $result = $this->produkModel->hapusData($data, $barcode);

        if ($result["code"] == 0) {
            $this->session->setFlashdata('message', 'Data berhasil dipanggil kembali.');
        } else {
            $this->session->setFlashdata('message', 'Data gagal dipanggil kembali.');
        }

        return redirect()->to('/admin/produk');
    }
}



