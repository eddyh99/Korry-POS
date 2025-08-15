<?php

namespace App\Controllers\Staff;

use App\Models\Staff\CashierModel;
use App\Models\Staff\KasModel;
use App\Models\Admin\ProdukModel;
use App\Models\Admin\StoreModel;
use App\Models\Admin\StokModel;
use App\Models\MemberModel;

use App\Controllers\BaseApiController;

class Cashier extends BaseApiController
{
    protected $cashierModel;
    protected $kasModel;
    protected $produkModel;
    protected $storeModel;
    protected $stokModel;
    protected $memberModel; 

    public function __construct()
    {
        $this->cashierModel = new CashierModel();
        $this->kasModel     = new KasModel();
        $this->produkModel  = new ProdukModel();
        $this->storeModel   = new StoreModel();
        $this->stokModel    = new StokModel();
        $this->memberModel  = new MemberModel();
    }

    public function getIndex()
    {
        $cekstatus = $this->kasModel->openkas();

        if ($cekstatus == 5051) {
            $this->session->setFlashdata('message', 'Silahkan masukkan kas awal dulu');
            return redirect()->to(base_url('staff/kas'));
        } elseif ($cekstatus == 5052) {
            $this->session->setFlashdata('message', 'Silahkan tutup kas, dengan menghubungi store manager');
            return redirect()->to(base_url('staff/kas/tutupharian'));
        } elseif ($cekstatus == 5053) {
            $this->session->setFlashdata('message', 'Store sudah di tutup, silahkan buka kembali besok');
            return redirect()->to(base_url('staff/dashboard'));
        }

        $produk = $this->stokModel->listproduk_withstok();

        $data = [
            'title'    => 'Penjualan',
            'content'  => 'staff/cashier/index',
            'extra'    => 'staff/cashier/js/js_cash',
            'extracss' => 'staff/cashier/css/css_cash',
            'produk'   => $produk,
        ];

        return view('layout/wrapper', $data);
    }

    public function postReadbarcode()
    {
        $barcode = $this->request->getPost('barcode', FILTER_SANITIZE_STRING);
        $result  = $this->cashierModel->readitem($barcode);
        return $this->response->setJSON($result);
    }

    public function postGetharga()
    {
        $barcode = $this->request->getPost('barcode', FILTER_SANITIZE_STRING);
        $result  = $this->produkModel->getProduk($barcode);
        return $this->response->setJSON($result);
    }

    public function postGetdetail()
    {
        $memberid = $this->request->getPost('memberid', FILTER_SANITIZE_STRING);
        $result   = $this->memberModel->getMember($memberid);

        if ($result['code'] == 0) {
            return $this->response->setJSON($result['message']);
        } else {
            return $this->response->setStatusCode(404)->setBody('404');
        }
    }

    public function getNewnota()
    {
        session()->set('identify', rand(1000, 9999));
        session()->set('nota_komplit', '');
    }

    public function postAddData()
    {
        $memberid = $this->request->getPost('memberid', FILTER_SANITIZE_STRING);
        $fee      = $this->request->getPost('fee', FILTER_SANITIZE_STRING);
        $method   = $this->request->getPost('method', FILTER_SANITIZE_STRING);
        $barang   = json_decode($this->request->getPost('barang', FILTER_SANITIZE_STRING));

        if (empty($memberid)) {
            $memberid = null;
        }

        $nota = $this->cashierModel->getLastnota();

        $jual = [
            'nonota'    => $nota,
            'storeid'   => $_SESSION['logged_status']['storeid'],
            'tanggal'   => date('Y-m-d H:i:s'),
            'method'    => $method,
            'fee'       => $fee,
            'member_id' => $memberid,
            'userid'    => $_SESSION['logged_status']['username']
        ];

        $result = $this->cashierModel->insertData($jual, $barang);

        return $this->response->setBody($result);
    }

    public function postCetaknota($id)
    {
        $store = $this->storeModel->getStore($_SESSION['logged_status']['storeid']);
        $data  = $this->cashierModel->getallnota($id);

        $nota = [
            'store' => $store[0],
            'data'  => $data
        ];

        return view('staff/cashier/print', $nota);
    }
}
