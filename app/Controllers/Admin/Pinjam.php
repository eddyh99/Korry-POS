<?php

namespace App\Controllers\Admin;

use App\Models\Admin\StoreModel;
use App\Models\Admin\ProdukModel;
use App\Models\Admin\PinjamModel;
use App\Models\Admin\OpnameModel;

use App\Controllers\BaseApiController;

class Pinjam extends BaseApiController
{
    protected $storeModel;
    protected $produkModel;
    protected $pinjamModel;
    protected $opnameModel;

    public function __construct()
    {
        $this->storeModel   = new StoreModel();
        $this->produkModel  = new ProdukModel();
        $this->pinjamModel  = new PinjamModel();
        $this->opnameModel  = new OpnameModel();

        // inject OpnameModel ke PinjamModel
        $this->pinjamModel->setOpnameModel($this->opnameModel);
    }

    public function getIndex()
    {
        $store = $this->storeModel->liststore();
        $data = [
            'title'     => 'Stok Out',
            'content'   => 'admin/pinjam/index',
            'extra'     => 'admin/pinjam/js/js_index',
            'store'     => $store,
            'mn_pinjam' => 'active',
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
        $store = $this->request->getPost('store', FILTER_SANITIZE_STRING);
        $result = $this->pinjamModel->Listnota($store);
        return $this->response->setJSON($result);
    }

    public function getTutup($id)
    {
        $id = filter_var($id, FILTER_SANITIZE_NUMBER_INT);
        $result = $this->pinjamModel->setTutup($id);

        if ($result) {
            $this->session->setFlashdata('message', 'Data berhasil tersimpan.');
        } else {
            $this->session->setFlashdata('message', 'Data gagal tersimpan.');
        }

        return redirect()->to(base_url('admin/pinjam'));
    }

    public function getDetailpinjam($key)
    {
        $produk = $this->produkModel->listproduk();
        $data = [
            'title'     => 'Detail Stok Out',
            'content'   => 'admin/pinjam/detail',
            'extra'     => 'admin/pinjam/js/js_detail',
            'mn_pinjam' => 'active',
            'colmas'    => 'collapse',
            'colset'    => 'collapse',
            'collap'    => 'collapse',
            'colkonsi'   => 'collapse',
            'colwho'     => 'collapse',
            'produk'    => $produk,
            'key'       => $key,
        ];

        return view('layout/wrapper', $data);
    }

    public function postListpinjam()
    {
        $key = $this->request->getPost('key', FILTER_SANITIZE_STRING);
        $result = $this->pinjamModel->getDetail($key);
        return $this->response->setJSON($result);
    }

    public function getTambah()
    {
        $produk = $this->produkModel->listproduk();
        $data = [
            'title'      => 'Tambah Data Peminjaman',
            'content'    => 'admin/pinjam/tambah',
            'extra'      => 'admin/pinjam/js/js_tambah',
            'extracss'   => 'admin/pinjam/css/css_tambah',
            'mn_pinjam'  => 'active',
            'colmas'     => 'collapse',
            'colset'     => 'collapse',
            'collap'     => 'collapse',
            'colkonsi'   => 'collapse',
            'colwho'     => 'collapse',
            'produk'     => $produk,
        ];

        return view('layout/wrapper', $data);
    }

    public function postAddData()
    {
        $nama       = $this->request->getPost('nama', FILTER_SANITIZE_STRING);
        $keterangan = $this->request->getPost('keterangan', FILTER_SANITIZE_STRING);
        $barang     = json_decode($this->request->getPost('barang'), true);

        $userid  = $this->session->get('logged_status')['username'];
        $storeid = $this->session->get('logged_status')['storeid'];

        $data = [
            'nama'       => $nama,
            'storeid'    => $storeid,
            'keterangan' => $keterangan,
            'userid'     => $userid,
        ];

        $result = $this->pinjamModel->insertData($data, $barang);

        if ($result) {
            $this->session->setFlashdata('message', 'Data berhasil tersimpan.');
        } else {
            $this->session->setFlashdata('message', 'Data gagal tersimpan.');
        }

        return redirect()->to(base_url('admin/pinjam'));
    }

    public function postSimpandata()
    {
        $id = $this->request->getPost('id', FILTER_SANITIZE_NUMBER_INT);
        $barang = json_decode($this->request->getPost('barang'), true);

        $this->pinjamModel->setKembali($id, $barang);

        return $this->response->setJSON(['code' => 0]);
    }
}

