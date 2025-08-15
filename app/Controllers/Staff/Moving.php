<?php

namespace App\Controllers\Staff;

use App\Models\Admin\MovingModel;
use App\Models\Admin\StoreModel;
use App\Models\Admin\BrandModel;
use App\Models\Admin\KategoriModel;
use App\Models\Admin\ProdukModel;

use App\Controllers\BaseApiController;

class Moving extends BaseApiController
{
    protected $movingModel;
    protected $storeModel;
    protected $brandModel;
    protected $kategoriModel;
    protected $produkModel;

    public function __construct()
    {
        $this->movingModel   = new MovingModel();
        $this->storeModel    = new StoreModel();
        $this->brandModel    = new BrandModel();
        $this->kategoriModel = new KategoriModel();
        $this->produkModel   = new ProdukModel();
    }

    public function index()
    {
        $data = [
            'title'   => 'Data Permintaan',
            'content' => 'staff/moving/index',
            'extra'   => 'staff/moving/js/js_index',
            'collap'  => 'collapse',
            'mn_req'  => 'active'
        ];
        return view('layout/wrapper', $data);
    }

    public function cekstok()
    {
        $barcode = $this->request->getPost('barcode');
        $tujuan  = $this->request->getPost('tujuan');
        $size    = $this->request->getPost('size');

        $result = $this->movingModel->cekstokProduk($barcode, $tujuan, $size);
        return $this->response->setJSON($result);
    }

    public function Listdata()
    {
        $columns = ['mutasi_id', 'tanggal', 'dari', 'tujuan'];
        $start   = $this->request->getPost('start');
        $limit   = $this->request->getPost('length');

        $orderCol = $columns[$this->request->getPost('order')[0]['column']];
        $dir      = $this->request->getPost('order')[0]['dir'];

        $totalData     = $this->movingModel->allposts_count();
        $totalFiltered = $totalData;

        if (empty($this->request->getPost('search')['value'])) {
            $result = $this->movingModel->allposts($limit, $start, $orderCol, $dir);
        } else {
            $search        = $this->request->getPost('search')['value'];
            $result        = $this->movingModel->posts_search($limit, $start, $search, $orderCol, $dir);
            $totalFiltered = $this->movingModel->posts_search_count($search);
        }

        $data = [
            "recordsTotal"    => $totalData,
            "recordsFiltered" => $totalFiltered,
            "produk"          => $result,
        ];
        return $this->response->setJSON($data);
    }

    public function Listdatakonfirm()
    {
        $columns = ['mutasi_id', 'tanggal', 'dari', 'tujuan'];
        $start   = $this->request->getPost('start');
        $limit   = $this->request->getPost('length');

        $orderCol = $columns[$this->request->getPost('order')[0]['column']];
        $dir      = $this->request->getPost('order')[0]['dir'];

        $totalData     = $this->movingModel->allposts_countkonfirm();
        $totalFiltered = $totalData;

        if (empty($this->request->getPost('search')['value'])) {
            $result = $this->movingModel->allpostskonfirm($limit, $start, $orderCol, $dir);
        } else {
            $search        = $this->request->getPost('search')['value'];
            $result        = $this->movingModel->posts_searchkonfirm($limit, $start, $search, $orderCol, $dir);
            $totalFiltered = $this->movingModel->posts_search_countkonfirm($search);
        }

        $data = [
            "recordsTotal"    => $totalData,
            "recordsFiltered" => $totalFiltered,
            "produk"          => $result,
        ];
        return $this->response->setJSON($data);
    }

    public function tambah()
    {
        $store  = $this->storeModel->Liststore();
        $produk = $this->produkModel->listproduk();

        $data = [
            'title'    => 'Tambah Data',
            'content'  => 'staff/moving/tambah',
            'extra'    => 'staff/moving/js/js_tambah',
            'extracss' => 'staff/moving/css/css_tambah',
            'store'    => $store,
            'produk'   => $produk,
            'collap'   => 'collapse',
            'mn_req'   => 'active'
        ];
        return view('layout/wrapper', $data);
    }

    public function AddData()
    {
        $asal   = $this->request->getPost('asal');
        $tujuan = $this->request->getPost('tujuan');
        $barang = json_decode($this->request->getPost('barang'));

        $pindah = [
            "tanggal" => date("Y-m-d H:i:s"),
            "dari"    => $tujuan,
            "tujuan"  => $asal,
            "userid"  => session('logged_status')['username']
        ];

        $result = $this->movingModel->insertData($pindah, $barang);

        if ($result) {
            $this->session->setFlashdata('message', 'Data berhasil disimpan.');
        } else {
            $this->session->setFlashdata('message', 'Data gagal disimpan.');
        }

        return $this->response->setBody("0");
    }

    public function batal($mutasi_id)
    {
        $mutasi_id = base64_decode($mutasi_id);
        $data = [
            "approved" => 2,
            "userid"   => session('logged_status')['username']
        ];

        $result = $this->movingModel->voidData($data, $mutasi_id);

        if ($result["code"] == 0) {
            $this->session->setFlashdata('message', 'Data berhasil diubah.');
        } else {
            $this->session->setFlashdata('message', 'Data gagal diubah.');
        }

        return redirect()->to('/staff/moving');
    }

    public function terima($mutasi_id)
    {
        $mutasi_id = base64_decode($mutasi_id);
        $data = [
            "approved" => 1,
            "userid"   => session('logged_status')['username']
        ];

        $result = $this->movingModel->acceptData($data, $mutasi_id);

        if ($result["code"] == 0) {
            $this->session->setFlashdata('message', 'Data berhasil diubah.');
        } else {
            $this->session->setFlashdata('message', 'Data gagal diubah.');
        }

        return redirect()->to('/staff/moving');
    }

    public function detail($mutasi_id, $stat)
    {
        $req = ($stat == 0) ? "active" : "";
        $kon = ($stat != 0) ? "active" : "";

        $mutasi_id   = base64_decode($mutasi_id);
        $permintaan  = $this->movingModel->getMoving($mutasi_id);

        $data = [
            'title'   => 'Tambah Data',
            'content' => 'staff/moving/detail',
            'extra'   => 'staff/moving/js/js_detail',
            'asal'    => $permintaan[0]["asal"],
            'tujuan'  => $permintaan[0]["tujuan"],
            'key'     => $mutasi_id,
            'collap'  => 'collapse',
            'mn_req'  => 'active'
        ];

        return view('layout/wrapper', $data);
    }

    public function listdetail()
    {
        $mutasi_id = $this->request->getPost('mutasi_id');
        $result    = $this->movingModel->getdetail($mutasi_id);
        return $this->response->setJSON($result);
    }
}
