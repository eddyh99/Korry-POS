<?php

namespace App\Controllers\Admin;

use App\Models\Admin\ProdukModel;
use App\Models\Admin\OpnameModel;
use App\Models\Admin\StoreModel;
use App\Models\Staff\CashierModel;

use App\Controllers\BaseApiController;

class Opname extends BaseApiController
{
    protected $produkModel;
    protected $opnameModel;
    protected $storeModel;
    protected $cashierModel;

    public function __construct()
    {
        $this->produkModel  = new ProdukModel();
        $this->opnameModel  = new OpnameModel();
        $this->storeModel   = new StoreModel();
        $this->cashierModel = new CashierModel();
    }

    public function getIndex()
    {
        $produk = $this->produkModel->listproduk();

        $data = [
            'title'     => 'Opname',
            'content'   => 'admin/opname/index',
            'extra'     => 'admin/opname/js/js_index',
            'extracss'  => 'admin/opname/css/css_index',
            'mn_opname' => 'active',
            'produk'    => $produk,
            'colset'    => 'collapse',
            'colmas'    => 'collapse',
            'colkonsi'  => 'collapse',
            'colwho'    => 'collapse',
            'collap'    => 'collapse',
        ];

        return view('layout/wrapper', $data);
    }

    public function getKonfirm()
    {
        $store = $this->storeModel->Liststore();

        $data = [
            'title'        => 'Konfirm Opname',
            'content'      => 'admin/opname/konfirm',
            'extra'        => 'admin/opname/js/js_konfirm',
            'mn_appopname' => 'active',
            'store'        => $store,
            'colset'       => 'collapse',
            'colmas'       => 'collapse',
            'colkonsi'     => 'collapse',
            'colwho'       => 'collapse',
            'collap'       => 'collapse',
        ];

        return view('layout/wrapper', $data);
    }

    public function postListstokopname()
    {
        $storeid = $this->request->getPost('storeid');
        $stok    = $this->opnameModel->listopname($storeid);

        return $this->response->setJSON($stok);
    }

    public function postCekstok()
    {
        $barcode = $this->request->getPost('barcode');
        $size    = $this->request->getPost('size');
        $storeid = $this->request->getPost('tujuan');

        $stok = $this->opnameModel->getStok($barcode, $storeid, $size);

        return $this->response->setBody($stok);
    }

    public function postSimpandata()
    {
        $barcode    = $this->request->getPost('produk');
        $size       = $this->request->getPost('size');
        $riil       = $this->request->getPost('riil');
        $keterangan = $this->request->getPost('keterangan');
        $storeid    = $_SESSION["logged_status"]["storeid"];

        $stok   = $this->opnameModel->getStok($barcode, $storeid, $size);
        $jumlah = $riil - $stok;

        if ($_SESSION["logged_status"]["role"] != "Office Manager") {
            $mdata = [
                "barcode"    => $barcode,
                "size"       => $size,
                "storeid"    => $storeid,
                "tanggal"    => date("Y-m-d"),
                "jumlah"     => $jumlah,
                "keterangan" => $keterangan,
                "userid"     => $_SESSION["logged_status"]["username"]
            ];
        } else {
            $mdata = [
                "barcode"    => $barcode,
                "size"       => $size,
                "storeid"    => $storeid,
                "tanggal"    => date("Y-m-d"),
                "jumlah"     => $jumlah,
                "keterangan" => $keterangan,
                "approved"   => 1,
                "userid"     => $_SESSION["logged_status"]["username"]
            ];
        }

        $result = $this->opnameModel->insertData($mdata);

        if ($result) {
            $this->session->setFlashdata('message', 'Stok berhasil diupdate.');
        } else {
            $this->session->setFlashdata('message', 'Stok gagal diupdate.');
        }

        return redirect()->to(base_url("admin/opname"));
    }

    public function postUpdatedata()
    {
        $storeid = $this->request->getPost('store');
        $this->opnameModel->setapprove($storeid);

        return redirect()->to(base_url("admin/opname/konfirm"));
    }
}
