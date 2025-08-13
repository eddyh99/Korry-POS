<?php

namespace App\Controllers\Admin;

use App\Models\Admin\ProdukModel;
use App\Models\Admin\BrandModel;
use App\Models\Admin\KategoriModel;

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
        $this->produkModel   = new ProdukModel();
        $this->brandModel    = new BrandModel();
        $this->kategoriModel = new KategoriModel();
    }

    public function getIndex()
    {
        $data = [
            'title'      => 'Data Produk',
            'content'    => 'admin/produk/index',
            'extra'      => 'admin/produk/js/js_index',
            'mn_master'  => 'active',
            'colmas'     => 'collapse in',
            'colset'     => 'collapse',
            'collap'     => 'collapse',
            'side7'      => 'active',
        ];
        return view('layout/wrapper', $data);
    }

    public function postListdata()
    {
        $columns = ['barcode', 'namaproduk', 'namabrand', 'namakategori', 'harga'];

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
        $brand    = $this->brandModel->Listbrand();
        $kategori = $this->kategoriModel->Listkategori();

        $data = [
            'title'    => 'Tambah Data Produk',
            'content'  => 'admin/produk/tambah',
            'brand'    => $brand,
            'kategori' => $kategori,
            'mn_master' => 'active',
            'colmas'   => 'collapse in',
            'colset'   => 'collapse',
            'collap'   => 'collapse',
            'side7'    => 'active',
        ];
        return view('layout/wrapper', $data);
    }

    // public function postAddData()
    // {
    //     $rules = [
    //         'barcode'  => 'trim|required',
    //         'produk'   => 'trim|required',
    //         'brand'    => 'trim|required',
    //         'kategori' => 'trim|required',
    //         'harga'    => 'trim|required',
    //         'diskon'   => 'trim|required',
    //     ];

    //     if (! $this->validate($rules)) {
    //         $this->session->setFlashdata('message', $this->validator->listErrors());
    //         return redirect()->to('/admin/produk/tambah');
    //     }

    //     $data = [
    //         "barcode"      => esc($this->request->getPost('barcode')),
    //         "namaproduk"   => esc($this->request->getPost('produk')),
    //         "namabrand"    => esc($this->request->getPost('brand')),
    //         "namakategori" => esc($this->request->getPost('kategori')),
    //         "harga"        => esc($this->request->getPost('harga')),
    //         "diskon"       => esc($this->request->getPost('diskon')),
    //         "userid"       => session()->get('logged_status')['username']
    //     ];

    //     $result = $this->produkModel->insertData($data);

    //     if ($result["code"] == 0) {
    //         $this->session->setFlashdata('message', 'Data berhasil disimpan.');
    //         return redirect()->to('/admin/produk');
    //     } else {
    //         $this->session->setFlashdata('message', 'Data gagal disimpan.');
    //         return redirect()->to('/admin/produk/tambah');
    //     }
    // }
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
                'label'  => 'Harga',
                'rules'  => 'required|trim|numeric|max_length[10]',
                'errors' => [
                    'required'   => '{field} wajib diisi.',
                    'numeric'    => '{field} hanya boleh berisi angka.',
                    'max_length' => '{field} maksimal 10 digit.'
                ]
            ],
            'diskon' => [
                'label'  => 'Diskon',
                'rules'  => 'required|trim|numeric|max_length[10]',
                'errors' => [
                    'required'   => '{field} wajib diisi.',
                    'numeric'    => '{field} hanya boleh berisi angka.',
                    'max_length' => '{field} maksimal 10 digit.'
                ]
            ]
        ];

        if (! $this->validate($rules)) {
            $this->session->setFlashdata('message', $this->validator->listErrors());
            return redirect()->to('/admin/produk/tambah')->withInput();
        }

        $data = [
            "barcode"      => esc($this->request->getPost('barcode')),
            "namaproduk"   => esc($this->request->getPost('produk')),
            "namabrand"    => esc($this->request->getPost('brand')),
            "namakategori" => esc($this->request->getPost('kategori')),
            "harga"        => esc($this->request->getPost('harga')),
            "diskon"       => esc($this->request->getPost('diskon')),
            "userid"       => session()->get('logged_status')['username']
        ];

        $result = $this->produkModel->insertData($data);

        if ($result["code"] == 0) {
            $this->session->setFlashdata('message', 'Data berhasil disimpan.');
            return redirect()->to('/admin/produk');
        } else {
            $this->session->setFlashdata('message', 'Data gagal disimpan.');
            return redirect()->to('/admin/produk/tambah')->withInput();
        }
    }

    public function getUbah($barcode)
    {
        $barcode    = base64_decode(esc($barcode));
        $produk     = $this->produkModel->getProduk($barcode);
        $brand      = $this->brandModel->Listbrand();
        $kategori   = $this->kategoriModel->Listkategori();

        $data = [
            'title'    => 'Ubah Data Produk',
            'content'  => 'admin/produk/ubah',
            'brand'    => $brand,
            'kategori' => $kategori,
            'barcode'  => $barcode,
            'produk'   => $produk,
            'mn_master' => 'active',
            'colmas'   => 'collapse in',
            'colset'   => 'collapse',
            'collap'   => 'collapse',
            'side7'    => 'active',
        ];
        return view('layout/wrapper', $data);
    }

    // public function postUpdateData()
    // {
    //     $rules = [
    //         'barcode'  => 'trim|required',
    //         'produk'   => 'trim|required',
    //         'brand'    => 'trim|required',
    //         'kategori' => 'trim|required',
    //         'harga'    => 'trim|required',
    //         'diskon'   => 'trim|required',
    //     ];

    //     if (! $this->validate($rules)) {
    //         $this->session->setFlashdata('message', $this->validator->listErrors());
    //         return redirect()->to('/admin/produk/ubah');
    //     }

    //     $barcode = esc($this->request->getPost('barcode'));
    //     $data = [
    //         "namaproduk"   => esc($this->request->getPost('produk')),
    //         "namabrand"    => esc($this->request->getPost('brand')),
    //         "namakategori" => esc($this->request->getPost('kategori')),
    //         "harga"        => esc($this->request->getPost('harga')),
    //         "diskon"       => esc($this->request->getPost('diskon')),
    //         "userid"       => session()->get('logged_status')['username']
    //     ];

    //     $result = $this->produkModel->setData($data, $barcode);

    //     if ($result["code"] == 0) {
    //         $this->session->setFlashdata('message', 'Data berhasil diubah.');
    //         return redirect()->to('/admin/produk');
    //     } else {
    //         $this->session->setFlashdata('message', 'Data gagal diubah.');
    //         return redirect()->to('/admin/produk/ubah/' . base64_encode($barcode));
    //     }
    // }
    public function postUpdateData()
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
                'label'  => 'Harga',
                'rules'  => 'required|trim|numeric|max_length[10]',
                'errors' => [
                    'required'   => '{field} wajib diisi.',
                    'numeric'    => '{field} hanya boleh berisi angka.',
                    'max_length' => '{field} maksimal 10 digit.'
                ]
            ],
            'diskon' => [
                'label'  => 'Diskon',
                'rules'  => 'required|trim|numeric|max_length[10]',
                'errors' => [
                    'required'   => '{field} wajib diisi.',
                    'numeric'    => '{field} hanya boleh berisi angka.',
                    'max_length' => '{field} maksimal 10 digit.'
                ]
            ]
        ];

        if (! $this->validate($rules)) {
            $this->session->setFlashdata('message', $this->validator->listErrors());
            return redirect()->back()->withInput();
        }

        $barcode = esc($this->request->getPost('barcode'));
        $data = [
            "namaproduk"   => esc($this->request->getPost('produk')),
            "namabrand"    => esc($this->request->getPost('brand')),
            "namakategori" => esc($this->request->getPost('kategori')),
            "harga"        => esc($this->request->getPost('harga')),
            "diskon"       => esc($this->request->getPost('diskon')),
            "userid"       => session()->get('logged_status')['username']
        ];

        $result = $this->produkModel->setData($data, $barcode);

        if ($result["code"] == 0) {
            $this->session->setFlashdata('message', 'Data berhasil diubah.');
            return redirect()->to('/admin/produk');
        } else {
            $this->session->setFlashdata('message', 'Data gagal diubah.');
            return redirect()->to('/admin/produk/ubah/' . base64_encode($barcode));
        }
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

    // Batas Insert, Update, Delete

    // public function postImport()
    // {
    //     $userid = session()->get('logged_status')['username'];

    //     $fileMimes = [
    //         'application/vnd.ms-excel',
    //         'application/excel',
    //         'application/vnd.msexcel',
    //         'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
    //     ];

    //     $file = $this->request->getFile('produk');

    //     if ($file && in_array($file->getMimeType(), $fileMimes)) {
    //         $reader = new Xlsx();
    //         $spreadsheet = $reader->load($file->getTempName());
    //         $sheetData = $spreadsheet->getActiveSheet()->toArray();

    //         $array = array_map('array_filter', $sheetData);
    //         $array = array_filter($array);
    //         $input = array_map("unserialize", array_unique(array_map("serialize", $array)));

    //         $data = [];
    //         foreach ($input as $dt) {
    //             $temp["barcode"]      = $dt[0];
    //             $temp["namaproduk"]   = $dt[1];
    //             $temp["namabrand"]    = $dt[2];
    //             $temp["namakategori"] = $dt[3];
    //             $temp["harga"]        = $dt[4];
    //             $temp["userid"]       = $userid;
    //             $data[]               = $temp;
    //         }

    //         $result = $this->produkModel->insertbatchData($data);
    //         if ($result["code"] == 0) {
    //             session()->setFlashdata('message', $this->message->success_msg());
    //             return redirect()->to('/admin/produk');
    //         } else {
    //             session()->setFlashdata('message', $this->message->error_msg($result["message"]));
    //             return redirect()->to('/admin/produk/tambah');
    //         }
    //     }

    //     session()->setFlashdata('message', $this->message->error_msg("File tidak valid"));
    //     return redirect()->to('/admin/produk/tambah');
    // }
// public function postImport()
// {
//     $userid = session()->get('logged_status')['username'];

//     $fileMimes = [
//         'application/vnd.ms-excel',
//         'application/excel',
//         'application/vnd.msexcel',
//         'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
//     ];

//     $file = $this->request->getFile('produk');

//     if ($file && in_array($file->getMimeType(), $fileMimes)) {
//         $reader = new Xlsx();
//         $spreadsheet = $reader->load($file->getTempName());
//         $sheetData = $spreadsheet->getActiveSheet()->toArray();

//         $array = array_map('array_filter', $sheetData);
//         $array = array_filter($array);
//         $input = array_map("unserialize", array_unique(array_map("serialize", $array)));

//         $data = [];
//         foreach ($input as $dt) {
//             $temp["barcode"]      = $dt[0];
//             $temp["namaproduk"]   = $dt[1];
//             $temp["namabrand"]    = $dt[2];
//             $temp["namakategori"] = $dt[3];
//             $temp["harga"]        = $dt[4];
//             $temp["userid"]       = $userid;
//             $data[]               = $temp;
//         }

//         $result = $this->produkModel->insertbatchData($data);
//         if ($result["code"] == 0) {
//             $this->session->setFlashdata('message', 'Data Berhasil Di-import');
//             return redirect()->to('/admin/produk');
//         } else {
//             $this->session->setFlashdata('message', 'Data Gagal Di-import');
//             return redirect()->to('/admin/produk/tambah');
//         }
//     }

//     session()->setFlashdata('message', $this->message->error_msg("File tidak valid"));
//     return redirect()->to('/admin/produk/tambah');
// }
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



