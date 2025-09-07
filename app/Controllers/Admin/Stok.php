<?php

namespace App\Controllers\Admin;

use App\Models\Admin\StokModel;
use App\Models\Admin\StoreModel;
use App\Models\Admin\ProdukModel;
use App\Models\Admin\BrandModel;
use App\Models\Admin\KategoriModel;
use App\Models\Admin\SizeModel;

use PhpOffice\PhpSpreadsheet\Reader\Xlsx;

use App\Controllers\BaseApiController;

class Stok extends BaseApiController
{
    protected $stokModel;
    protected $storeModel;
    protected $produkModel;
    protected $brandModel;
    protected $kategoriModel;
    protected $sizeModel;

    public function __construct()
    {
        $this->stokModel     = new StokModel();
        $this->storeModel    = new StoreModel();
        $this->produkModel   = new ProdukModel();
        $this->brandModel    = new BrandModel();
        $this->kategoriModel = new KategoriModel();
        $this->sizeModel     = new SizeModel();
    }

    public function getIndex()
    {
        $data = [
            'title'      => 'Data Stok',
            'content'    => 'admin/stok/index',
            'extra'      => 'admin/stok/js/js_index',
            'mn_master'  => 'active',
            'colmas'     => 'collapse in',
            'colset'     => 'collapse',
            'collap'     => 'collapse',
            'side15'      => 'active'
        ];
        echo view('layout/wrapper', $data);
    }

    public function postListdata()
    {
        $columns = ['barcode', 'namaproduk', 'namabrand', 'size', 'stok', 'store'];

        $start = (int)$this->request->getPost('start');
        $limit = (int)$this->request->getPost('length');
        $orderColumnIndex = $this->request->getPost('order')[0]['column'] ?? 0;
        $order = $columns[$orderColumnIndex] ?? 'barcode';
        $dir = $this->request->getPost('order')[0]['dir'] ?? 'asc';

        $totalData = $this->stokModel->allposts_count();
        $totalFiltered = $totalData;

        $searchValue = $this->request->getPost('search')['value'] ?? '';

        if (empty($searchValue)) {
            $result = $this->stokModel->allposts($limit, $start, $order, $dir);
        } else {
            $result = $this->stokModel->posts_search($limit, $start, $searchValue, $order, $dir);
            $totalFiltered = $this->stokModel->posts_search_count($searchValue);
        }

        return $this->response->setJSON([
            'recordsTotal'    => $totalData,
            'recordsFiltered' => $totalFiltered,
            'produk'          => $result,
        ]);
    }

    public function getTambah()
    {
        $size   = $this->sizeModel->Listsize();
        $store  = $this->storeModel->Liststore();
        $produk = $this->produkModel->listproduksize();

        $data = [
            'title'     => 'Tambah Data Stok',
            'content'   => 'admin/stok/tambah',
            'extra'     => 'admin/stok/js/js_tambah',
            'extracss'  => 'admin/stok/css/css_tambah',
            'size'      => $size,
            'store'     => $store,
            'produk'    => $produk,
            'restock'   => 0,
            'mn_master' => 'active',
            'colmas'    => 'collapse in',
            'colset'    => 'collapse',
            'collap'    => 'collapse',
            'side15'     => 'active',
        ];
        echo view('layout/wrapper', $data);
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
                'rules' => 'required',
                'errors' => [
                    'required'    => '{field} wajib diisi.',
                ]
            ],
            'stok' => [
                'label' => 'Jumlah Stok',
                'rules' => 'required|trim|is_natural_no_zero|max_length[10]',
                'errors' => [
                    'required'           => '{field} wajib diisi.',
                    'is_natural_no_zero' => '{field} harus berupa angka dan minimal 1.',
                    'max_length'         => '{field} maksimal 10 digit.'
                ]
            ],
            'store' => [
                'label' => 'Nama Store',
                'rules' => 'required|trim|max_length[30]|alpha_numeric_space',
                'errors' => [
                    'required'             => '{field} wajib diisi.',
                    'alpha_numeric_space'  => '{field} hanya boleh berisi huruf, angka, dan spasi.',
                    'max_length'           => '{field} maksimal 30 karakter.'
                ]
            ]
        ];

        if (! $this->validate($rules)) {
            session()->setFlashdata('message', $this->validator->listErrors());
            return redirect()->to('/admin/stok/tambah')->withInput();
        }

        $barcode = esc($this->request->getPost('barcode'));
        $size    = esc($this->request->getPost('size'));
        $stok    = esc($this->request->getPost('stok'));
        $storeid = esc($this->request->getPost('store'));
        $restock = esc($this->request->getPost('restock'));
        $userid  = session()->get('logged_status')['username'];

        $keterangan = ($restock == 0) ? 'Stok Awal' : 'Restock';

        $data = [
            'barcode'    => $barcode,
            'storeid'    => $storeid,
            'size'       => $size,
            'tanggal'    => date('Y-m-d'),
            'jumlah'     => $stok,
            'keterangan' => $keterangan,
            'approved'   => 1,
            'userid'     => $userid,
        ];

        $result = $this->stokModel->insertData($data);

        if (isset($result['code']) && $result['code'] == 0) {
            session()->setFlashdata('message', 'Data berhasil disimpan.');
            return redirect()->to('/admin/stok');
        } else {
            session()->setFlashdata('message', 'Data gagal disimpan.');
            return redirect()->to('/admin/stok/tambah')->withInput();
        }
    }

    // Batas Insert, Update, Delete

    public function getRestok()
    {
        $size   = $this->sizeModel->Listsize();
        $store  = $this->storeModel->Liststore();
        $produk = $this->produkModel->listproduk();

        $data = [
            'title'     => 'Tambah Data Stok',
            'content'   => 'admin/stok/tambah',
            'extra'     => 'admin/stok/js/js_tambah',
            'extracss'  => 'admin/stok/css/css_tambah',
            'size'      => $size,
            'store'     => $store,
            'produk'    => $produk,
            'restock'   => 1,
            'mn_master' => 'active',
            'colmas'    => 'collapse in',
            'colset'    => 'collapse',
            'side15'    => 'active',
        ];
        echo view('layout/wrapper', $data);
    }

    public function getDetail($barcode)
    {
        $barcode = esc($barcode);
        $result = $this->produkModel->getProduk($barcode);
        return $this->response->setJSON($result);
    }

    public function postImport()
    {
        $userid = session()->get('logged_status')['username'];

        $file = $this->request->getFile('stok');
        if (!$file || !$file->isValid()) {
            session()->setFlashdata('message', 'File tidak valid atau tidak ditemukan.');
            return redirect()->to('/admin/stok/tambah');
        }

        // Validasi MIME type
        $allowedTypes = [
            'application/vnd.ms-excel',
            'application/excel',
            'application/vnd.msexcel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
        ];

        if (!in_array($file->getMimeType(), $allowedTypes)) {
            session()->setFlashdata('message', 'Format file tidak didukung.');
            return redirect()->to('/admin/stok/tambah');
        }

        $reader = new Xlsx();
        $spreadsheet = $reader->load($file->getTempName());
        $sheetData = $spreadsheet->getActiveSheet()->toArray();

        // Bersihkan data kosong dan duplikat
        $array = array_filter(array_map('array_filter', $sheetData));
        $input = array_map('unserialize', array_unique(array_map('serialize', $array)));

        $data = [];
        foreach ($input as $dt) {
            $data[] = [
                'barcode'   => $dt[0] ?? '',
                'storeid'   => 6, // Kalau perlu diganti jadi dinamis
                'size'      => $dt[1] ?? '',
                'tanggal'   => date('Y-m-d'),
                'jumlah'    => $dt[2] ?? 0,
                'keterangan'=> 'Stok Awal',
                'userid'    => $userid,
                'approved'  => 1,
            ];
        }

        $result = $this->stokModel->insertBatchData($data);

        if (isset($result['code']) && $result['code'] === 0) {
            session()->setFlashdata('message', 'Data berhasil diimpor.');
            return redirect()->to('/admin/stok/tambah');
        } else {
            session()->setFlashdata('message', 'Data gagal diimpor.');
            return redirect()->to('/admin/stok/tambah');
        }
    }
}

