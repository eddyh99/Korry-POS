<?php

namespace App\Controllers\Staff;

use App\Models\Staff\KasModel;
use App\Models\Staff\ReturModel;
use App\Models\Admin\ProdukModel;
use App\Models\Staff\CashierModel;

use App\Controllers\BaseApiController;

class Retur extends BaseApiController
{
    protected $kasModel;
    protected $returModel;
    protected $produkModel;
    protected $cashierModel;

    public function __construct()
    {
        $this->kasModel     = new KasModel();
        $this->returModel   = new ReturModel();
        $this->produkModel  = new ProdukModel();
        $this->cashierModel = new CashierModel();
    }

    public function getIndex()
    {
        $data = [
            'title'     => 'Retur Penjualan',
            'content'   => 'staff/retur/index',
            'extra'     => 'staff/retur/js/js_index',
            'mn_retur'  => 'active',
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
        $result = $this->returModel->listnota();
        return $this->response->setJSON($result);
    }

    public function getDetailretur($key, $member)
    {
        $produk = $this->produkModel->listproduk();
        $data = [
            'title'     => 'Detail Retur',
            'content'   => 'staff/retur/retur',
            'extra'     => 'staff/retur/js/js_retur',
            'css'       => 'staff/retur/css/css_retur',
            'mn_retur'  => 'active',
            'colmas'    => 'collapse',
            'colset'    => 'collapse',
            'collap'    => 'collapse',
            'colkonsi'  => 'collapse',
            'colwho'    => 'collapse',
            'produk'    => $produk,
            'key'       => $key,
            'memberid'  => $member
        ];
        return view('layout/wrapper', $data);
    }

    public function postListretur()
    {
        $key    = $this->request->getPost('key');
        $result = $this->returModel->getDetail($key);
        return $this->response->setJSON($result);
    }

    public function getTambah()
    {
        $data = [
            'title'      => 'Tambah Data Retur',
            'content'    => 'staff/kas/tambah',
            'mn_cash'    => 'active',
            'colmas'     => 'collapse',
            'colset'     => 'collapse',
            'collap'     => 'collapse',
            'colkonsi'   => 'collapse',
            'colwho'     => 'collapse',
        ];
        return view('layout/wrapper', $data);
    }

    public function postAddData()
    {
        $rules = [
            'nominal'    => 'required|trim',
            'jenis'      => 'required|trim',
            'keterangan' => 'required|trim'
        ];

        if (!$this->validate($rules)) {
            $this->session->setFlashdata('message', 'Data gagal disimpan: ' . implode(', ', $this->validator->getErrors()));
            return redirect()->to('/staff/kas/tambah');
        }

        $data = [
            "nominal"    => $this->request->getPost('nominal'),
            "jenis"      => $this->request->getPost('jenis'),
            "storeid"    => $_SESSION["logged_status"]["storeid"],
            "keterangan" => $this->request->getPost('keterangan'),
            "userid"     => $_SESSION["logged_status"]["username"]
        ];

        $result = $this->kasModel->insertData($data);

        if ($result["code"] == 0) {
            $this->session->setFlashdata('message', 'Data berhasil disimpan.');
            return redirect()->to('/staff/kas');
        } else {
            $this->session->setFlashdata('message', 'Data gagal disimpan: ' . $result["message"]);
            return redirect()->to('/staff/kas/tambah');
        }
    }

    public function getTutupharian()
    {
        $result = $this->kasModel->lastSaldo();
        $data = [
            'title'     => 'Tutup Kas Harian',
            'content'   => 'staff/kas/tutup',
            'mn_tutup'  => 'active',
            'penjualan' => $result,
            'colmas'    => 'collapse',
            'colset'    => 'collapse',
            'collap'    => 'collapse',
            'colkonsi'  => 'collapse',
            'colwho'    => 'collapse',
        ];
        return view('layout/wrapper', $data);
    }

    public function getSisakas()
    {
        $sisa = $this->request->getPost('sisa');

        $data = [
            "nominal"    => $sisa,
            "jenis"      => "Kas Sisa",
            "storeid"    => $_SESSION["logged_status"]["storeid"],
            "keterangan" => "Sisa Kas",
            "userid"     => $_SESSION["logged_status"]["username"]
        ];

        $result = $this->kasModel->setSisa($data);

        if ($result["code"] == 5051) {
            $this->session->setFlashdata('message', 'Tutup kas sudah dilakukan.');
        } else {
            $this->session->setFlashdata('message', 'Tutup Kas Selesai.');
        }

        return redirect()->to('/staff/kas/tutupharian');
    }

    public function postAddRetur()
    {
        $id         = $this->request->getPost('id');
        $memberid   = $this->request->getPost('memberid');
        $fee        = $this->request->getPost('fee');
        $method     = $this->request->getPost('method');
        $barang     = json_decode($this->request->getPost('barang'));
        $barangretur= json_decode($this->request->getPost('brgretur'));

        if ($memberid === 'null') {
            $memberid = null;
        }

        $nota = $this->cashierModel->getLastnota();

        $jual = [
            "nonota"    => $nota,
            "storeid"   => $_SESSION["logged_status"]["storeid"],
            "tanggal"   => date("Y-m-d H:i:s"),
            "method"    => $method,
            "fee"       => $fee,
            "member_id" => $memberid,
            "userid"    => $_SESSION["logged_status"]["username"]
        ];

        $retur = [
            "storeid"   => $_SESSION["logged_status"]["storeid"],
            "jual_id"   => $id,
            "userid"    => $_SESSION["logged_status"]["username"]
        ];

        $this->returModel->insertData($jual, $barang, $retur, $barangretur);
        return $this->response->setBody("0");
    }

    public function getBatalnota($id)
    {
        $retur = [
            "storeid"   => $_SESSION["logged_status"]["storeid"],
            "jual_id"   => $id,
            "userid"    => $_SESSION["logged_status"]["username"]
        ];

        $this->returModel->batalData($id, $retur);
        $this->session->setFlashdata("message", "Nota berhasil dibatalkan");
        return redirect()->to(base_url("staff/retur"));
    }
}
