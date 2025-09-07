<?php

namespace App\Controllers\Admin;

use App\Models\Admin\ProduksizeModel;
use App\Models\Admin\SizeModel;
use App\Models\Admin\ProdukModel;

use App\Controllers\BaseApiController;

class Produksize extends BaseApiController
{
    protected $produksizeModel;
    protected $sizeModel;
    protected $produkModel;

    public function __construct()
    {
        $this->produksizeModel = new ProduksizeModel();
        $this->sizeModel       = new SizeModel();
        $this->produkModel     = new ProdukModel();
    }

    public function getIndex()
    {
        $data = [
            'title'      => 'Data Produk - Size',
            'content'    => 'admin/produksize/index',
            'extra'      => 'admin/produksize/js/js_index',
            'mn_master'  => 'active',
            'colmas'     => 'collapse in',
            'colset'     => 'collapse',
            'collap'     => 'collapse',
            'side14'      => 'active',
        ];
        return view('layout/wrapper', $data);
    }

    public function postListdata()
    {
        $result = $this->produksizeModel->listSize();
        return $this->response->setJSON($result);
    }

    public function getTambah()
    {
        $size   = $this->sizeModel->listSize();
        $produk = $this->produkModel->listProduk();

        $data = [
            'title'      => 'Tambah Data Produk - Size',
            'content'    => 'admin/produksize/tambah',
            'extra'      => 'admin/produksize/js/js_tambah',
            'size'       => $size,
            'produk'     => $produk,
            'mn_master'  => 'active',
            'colmas'     => 'collapse in',
            'colset'     => 'collapse',
            'collap'     => 'collapse',
            'side14'      => 'active',
        ];

        return view('layout/wrapper', $data);
    }

  
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
            'size' => [
                'label' => 'Size',
                'rules' => 'required|regex_match[/^[A-Za-z0-9\s\-\/]{1,20}$/]',
                'errors' => [
                    'required'    => '{field} wajib diisi.',
                    'regex_match' => '{field} hanya boleh berisi huruf, angka, spasi, tanda minus (-), atau slash (/).',
                ]
            ]
        ];

        if (! $this->validate($rules)) {
            session()->setFlashdata('message', $this->validator->listErrors());
            return redirect()->to('/admin/produksize/tambah')->withInput();
        }

        $data = [
            'barcode' => esc($this->request->getPost('barcode')),
            'size'    => esc($this->request->getPost('size')),
            'userid'  => session()->get('logged_status')['username'],
        ];


        $result = $this->produksizeModel->insertData($data);

        if (isset($result['code']) && $result['code'] === 0) {
            $this->session->setFlashdata('message', 'Data berhasil disimpan.');
            return redirect()->to('/admin/produksize');
        } else {
            $this->session->setFlashdata('message', 'Data gagal disimpan.');
            return redirect()->to('/admin/produksize/tambah')->withInput();
        }
    }

    public function getHapus($barcode, $size)
    {
        $userid  = session()->get('logged_status')['username'];
        $barcode = base64_decode(esc($barcode));
        $size    = base64_decode(esc($size));

        $data = [
            'status' => 1,
            'userid' => $userid,
        ];

        $where = [
            'barcode' => $barcode,
            'size'    => $size,
        ];

        $result = $this->produksizeModel->hapusData($data, $where);

        if (isset($result['code']) && $result['code'] === 0) {
            $this->session->setFlashdata('message', 'Data berhasil dihapus.');
        } else {
            $this->session->setFlashdata('message', 'Data gagal dihapus.');
        }

        return redirect()->to('/admin/produksize');
    }

    // Batas Insert, Update, Delete

    public function postGetprodukname()
    {
        $barcode = esc($this->request->getPost('barcode'));
        $produk  = $this->produkModel->getProduk($barcode);

        // Jika produk ditemukan, kembalikan namaproduk, jika tidak kosong
        $result = $produk ? $produk->namaproduk : '';

        return $this->response->setBody($result);
    }

    // Fungsi filter array (tidak wajib di class, bisa dijadikan private method)
    private function myFilter($var)
    {
        return ($var !== null && $var !== false && $var !== "");
    }

    // Fungsi import file produk size dari excel (xlsx)
    public function postImport()
    {
        $userid = session()->get('logged_status')['username'];

        $file = $this->request->getFile('produksize');

        if (!$file || !$file->isValid()) {
            session()->setFlashdata('message', 'File tidak valid.');
            return redirect()->to('/admin/produksize/tambah');
        }

        $fileMime = $file->getMimeType();
        $allowedMimes = [
            'application/vnd.ms-excel',
            'application/excel',
            'application/vnd.msexcel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ];

        if (!in_array($fileMime, $allowedMimes)) {
            session()->setFlashdata('message', 'Tipe file tidak diperbolehkan.');
            return redirect()->to('/admin/produksize/tambah');
        }

        // Pakai PhpSpreadsheet untuk baca file
        $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
        $spreadsheet = $reader->load($file->getTempName());
        $sheetData = $spreadsheet->getActiveSheet()->toArray();

        // Filter data kosong dan unik
        $filtered = array_filter($sheetData, fn($row) => $this->myFilter(array_filter($row)));
        $unique = array_map("unserialize", array_unique(array_map("serialize", $filtered)));

        $data = [];
        foreach ($unique as $dt) {
            $data[] = [
                'barcode' => $dt[0],
                'size'    => $dt[1],
                'userid'  => $userid,
                // 'status' default sudah 0, tidak perlu set
            ];
        }

        $result = $this->produksizeModel->insertBatchData($data);

        if (isset($result['code']) && $result['code'] === 0) {
            session()->setFlashdata('message', 'Data berhasil disimpan.');
            return redirect()->to('/admin/produksize');
        } else {
            session()->setFlashdata('message', 'Ada barcode yang tidak terdaftar.');
            return redirect()->to('/admin/produksize/tambah');
        }
    }
}



