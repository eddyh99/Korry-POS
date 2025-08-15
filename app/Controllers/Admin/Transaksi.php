<?php

namespace App\Controllers\Admin;

use App\Models\Admin\TransaksiModel;
use App\Models\Admin\StoreModel;

use App\Controllers\BaseApiController;

class Transaksi extends BaseApiController
{
    protected $transaksiModel;
    protected $storeModel;

    public function __construct()
    {
        $this->transaksiModel = new TransaksiModel();
        $this->storeModel     = new StoreModel();
    }

    public function index()
    {
        $store = $this->storeModel->Liststore();

        $data = [
            'title'      => 'Data Transaksi',
            'content'    => 'admin/transaksi/index',
            'extra'      => 'admin/transaksi/js/js_index',
            'extracss'   => 'admin/transaksi/css/css_index',
            'store'      => $store,
            'mn_method'  => 'active',
            'colmas'     => 'collapse',
            'colset'     => 'collapse in',
            'collap'     => 'collapse',
        ];

        return view('layout/wrapper', $data);
    }

    public function Listdata()
    {
        $tanggalPost = $this->request->getPost('tanggal');
        $storeid     = $this->request->getPost('storeid');

        if (!$tanggalPost || !$storeid) {
            return $this->response->setJSON([]);
        }

        $tanggal = date_create($tanggalPost);
        $tanggal = date_format($tanggal, "Y-m-d");

        $result = $this->transaksiModel->listtransaksi($tanggal, $storeid);
        return $this->response->setJSON($result);
    }

    public function listdetail()
    {
        $nonota  = $this->request->getPost('nonota');
        $tanggal = $this->request->getPost('tanggal');

        if (!$nonota || !$tanggal) {
            return $this->response->setJSON([]);
        }

        $result = $this->transaksiModel->detailtransaksi($nonota, $tanggal);
        return $this->response->setJSON($result);
    }

    public function simpanbayar()
    {
        $nonota = $this->request->getPost('nonota');
        $bayar  = $this->request->getPost('bayar');

        if ($nonota && $bayar) {
            $this->transaksiModel->changepayment($nonota, $bayar);
        }

        return $this->response->setJSON(['status' => 'ok']);
    }
}
