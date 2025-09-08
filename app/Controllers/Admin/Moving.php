<?php

namespace App\Controllers\Admin;

use App\Models\Admin\MovingModel;
use App\Models\Admin\StoreModel;
use App\Models\Admin\BrandModel;
use App\Models\Admin\KategoriModel;
use App\Models\Admin\ProdukModel;

use App\Models\Admin\OpnameModel;

use App\Controllers\BaseApiController;

class Moving extends BaseApiController
{
    protected $movingModel;
    protected $storeModel;
    protected $brandModel;
    protected $kategoriModel;
    protected $produkModel;
    protected $opnameModel;

    public function __construct()
    {
        $this->movingModel      = new MovingModel();
        $this->storeModel       = new StoreModel();
        $this->brandModel       = new BrandModel();
        $this->kategoriModel    = new KategoriModel();
        $this->produkModel      = new ProdukModel();
        $this->opnameModel      = new OpnameModel();

        // inject OpnameModel ke MovingModel
        $this->movingModel->setOpnameModel($this->opnameModel);

        // cek session
        if (!session()->has('logged_status')) {
            header('Location: ' . base_url());
            exit;
        }
    }

    public function getIndex()
    {
        $data = [
            'title'     => 'Request Barang',
            'content'   => 'admin/moving/index',
            'extra'     => 'admin/moving/js/js_index',
            'mn_req'    => 'active',
            'colmas'    => 'collapse',
            'colset'    => 'collapse',
            'collap'    => 'collapse',
            'colkonsi'   => 'collapse',
            'colwho'     => 'collapse',
        ];
        return view('layout/wrapper', $data);
    }

    public function postListdata()
    {
        $columns = ['mutasi_id','tanggal','dari','tujuan'];
        $start   = $this->request->getPost('start');
        $limit   = $this->request->getPost('length');

        $order   = $columns[$this->request->getPost('order')[0]['column']];
        $dir     = $this->request->getPost('order')[0]['dir'];

        $searchValue = $this->request->getPost('search')['value'] ?? '';

        $totalData = $this->movingModel->allposts_count();
        $totalFiltered = $totalData;

        if (empty($searchValue)) {
            $result = $this->movingModel->allposts($limit,$start,$order,$dir);
        } else {
            $result = $this->movingModel->posts_search($limit,$start,$searchValue,$order,$dir);
            $totalFiltered = $this->movingModel->posts_search_count($searchValue);
        }

        return $this->response->setJSON([
            "recordsTotal"    => $totalData,
            "recordsFiltered" => $totalFiltered,
            "produk"          => $result,
        ]);
    }

    public function postListdatakonfirm()
    {
        $columns = ['mutasi_id','tanggal','dari','tujuan'];
        $start   = $this->request->getPost('start');
        $limit   = $this->request->getPost('length');

        $order   = $columns[$this->request->getPost('order')[0]['column']];
        $dir     = $this->request->getPost('order')[0]['dir'];

        $searchValue = $this->request->getPost('search')['value'] ?? '';

        $totalData = $this->movingModel->allposts_countkonfirm();
        $totalFiltered = $totalData;

        if (empty($searchValue)) {
            $result = $this->movingModel->allpostskonfirm($limit,$start,$order,$dir);
        } else {
            $result = $this->movingModel->posts_searchkonfirm($limit,$start,$searchValue,$order,$dir);
            $totalFiltered = $this->movingModel->posts_search_countkonfirm($searchValue);
        }

        return $this->response->setJSON([
            "recordsTotal"    => $totalData,
            "recordsFiltered" => $totalFiltered,
            "produk"          => $result,
        ]);
    }

    public function getTambah()
    {
        $store = $this->storeModel->Liststore();
        $produk = $this->produkModel->listproduk();

        $data = [
            'title'    => 'Tambah Data',
            'content'  => 'admin/moving/tambah',
            'extra'    => 'admin/moving/js/js_tambah',
            'extracss' => 'admin/moving/css/css_tambah',
            'store'    => $store,
            'produk'   => $produk,
            'mn_req'   => 'active',
            'colmas'   => 'collapse',
            'colset'   => 'collapse',
            'collap'   => 'collapse',
            'colkonsi'   => 'collapse',
            'colwho'     => 'collapse',
        ];

        return view('layout/wrapper', $data);
    }

    public function postAddData()
    {
        $asal   = $this->request->getPost('asal', FILTER_SANITIZE_STRING);
        $tujuan = $this->request->getPost('tujuan', FILTER_SANITIZE_STRING);
        $barang = json_decode($this->request->getPost('barang', FILTER_UNSAFE_RAW));

        $pindah = [
            "tanggal" => date("Y-m-d H:i:s"),
            "dari"    => $tujuan,
            "tujuan"  => $asal,
            "userid"  => session()->get('logged_status')['username']
        ];

        $result = $this->movingModel->insertData($pindah, $barang);

        if ($result) {
            session()->setFlashdata('message', 'Data berhasil disimpan.');
            return "0";
        } else {
            session()->setFlashdata('message', 'Data gagal disimpan.');
            return "1";
        }
    }

    public function batal($mutasi_id)
    {
        $mutasi_id = base64_decode($mutasi_id);
        $data = [
            "approved" => 2,
            "userid"   => session()->get('logged_status')['username']
        ];

        $result = $this->movingModel->voidData($data,$mutasi_id);

        if ($result["code"]==0) {
            session()->setFlashdata('message', 'Data berhasil diubah.');
        } else {
            session()->setFlashdata('message', 'Data gagal diubah.');
        }

        return redirect()->to(base_url('/admin/moving'));
    }

    public function terima($mutasi_id)
    {
        $mutasi_id = base64_decode($mutasi_id);
        $data = [
            "approved" => 1,
            "userid"   => session()->get('logged_status')['username']
        ];

        $result = $this->movingModel->acceptData($data,$mutasi_id);

        if ($result["code"]==0) {
            session()->setFlashdata('message', 'Data berhasil diubah.');
        } else {
            session()->setFlashdata('message', 'Data gagal diubah.');
        }

        return redirect()->to(base_url('/admin/moving'));
    }

    public function getKonfirm()
    {
        $data = [
            'title'      => 'Konfirm Permintaan',
            'content'    => 'admin/moving/konfirm',
            'extra'      => 'admin/moving/js/js_konfirm',
            'mn_confirm' => 'active',
            'colmas'     => 'collapse',
            'colset'     => 'collapse',
            'collap'     => 'collapse',
            'colkonsi'   => 'collapse',
            'colwho'     => 'collapse',
        ];

        return view('layout/wrapper', $data);
    }

    public function batalkonfirm($mutasi_id)
    {
        $mutasi_id = base64_decode($mutasi_id);
        $data = [
            "approved" => 2,
            "userid"   => session()->get('logged_status')['username']
        ];

        $result = $this->movingModel->voidData($data,$mutasi_id);

        if ($result["code"]==0) {
            session()->setFlashdata('message', 'Data berhasil diubah.');
        } else {
            session()->setFlashdata('message', 'Data gagal diubah.');
        }

        return redirect()->to(base_url('/admin/moving/konfirm'));
    }

    public function terimakonfirm($mutasi_id)
    {
        $mutasi_id = base64_decode($mutasi_id);
        $data = [
            "approved" => 3,
            "userid"   => session()->get('logged_status')['username']
        ];

        $result = $this->movingModel->acceptData($data,$mutasi_id);

        if ($result["code"]==0) {
            session()->setFlashdata('message', 'Data berhasil diubah.');
        } else {
            session()->setFlashdata('message', 'Data gagal diubah.');
        }

        return redirect()->to(base_url('/admin/moving/konfirm'));
    }

    public function detail($mutasi_id, $stat)
    {
        $mutasi_id = base64_decode($mutasi_id);
        $permintaan = $this->movingModel->getMoving($mutasi_id);

        $data = [
            'title'      => 'Detail Permintaan',
            'content'    => 'admin/moving/detail',
            'extra'      => 'admin/moving/js/js_detail',
            'asal'       => $permintaan[0]["asal"],
            'tujuan'     => $permintaan[0]["tujuan"],
            'key'        => $mutasi_id,
            'mn_req'     => ($stat==0) ? 'active' : '',
            'mn_confirm' => ($stat!=0) ? 'active' : '',
            'colmas'     => 'collapse',
            'colset'     => 'collapse',
            'collap'     => 'collapse',
            'colkonsi'   => 'collapse',
            'colwho'     => 'collapse',
        ];

        return view('layout/wrapper', $data);
    }

    public function listdetail()
    {
        $mutasi_id = $this->request->getPost('mutasi_id');
        return $this->response->setJSON($this->movingModel->getdetail($mutasi_id));
    }
}
