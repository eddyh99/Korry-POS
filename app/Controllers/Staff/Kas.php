<?php

namespace App\Controllers\Staff;

use App\Models\Staff\KasModel;
use App\Models\Admin\StoreModel;
use App\Models\Admin\PengeluaranModel;

use App\Controllers\BaseApiController;

class Kas extends BaseApiController
{
    protected $kasModel;
    protected $storeModel;

    public function __construct()
    {
        $this->kasModel         = new KasModel();
        $this->storeModel       = new StoreModel();
        $this->pengeluaranModel = new PengeluaranModel();
    }

    public function getIndex()
    {
        $data = [
            'title'   => 'Input Kas',
            'content' => 'staff/kas/danakas',
            'extra'   => 'staff/kas/js/js_danakas',
            'mn_cash' => 'active',
            'colmas'  => 'collapse',
            'colset'  => 'collapse',
            'collap'  => 'collapse',
        ];
        return view('layout/wrapper', $data);
    }

    public function postListdata()
    {
        $result = $this->kasModel->listkas();
        return $this->response->setJSON($result);
    }

    public function getTambah()
    {
        $pengeluaran = $this->pengeluaranModel->listPengeluaran();

        $data = [
            'title'   => 'Tambah Data Kas',
            'content' => 'staff/kas/tambah',
            'pengeluaran' => $pengeluaran,
            'mn_cash' => 'active',
            'colmas'  => 'collapse',
            'colset'  => 'collapse',
            'collap'  => 'collapse',
        ];
        return view('layout/wrapper', $data);
    }

    public function postAddData()
    {
        $rules = [
            'nominal'    => 'trim|required',
            'jenis'      => 'trim|required',
            'keterangan' => 'trim|required',
        ];

        if (!$this->validate($rules)) {
            $this->session->setFlashdata('message', implode(' ', $this->validator->getErrors()));
            return redirect()->to('/staff/kas/tambah')->withInput();
        }

        $data = [
            'nominal'    => $this->request->getPost('nominal'),
            'jenis'      => $this->request->getPost('jenis'),
            'dateonly'   => date("Y-m-d"),
            'storeid'    => $_SESSION['logged_status']['storeid'],
            'keterangan' => $this->request->getPost('keterangan'),
            'userid'     => $_SESSION['logged_status']['username']
        ];

        $result = $this->kasModel->insertData($data);

        if ($result['code'] == 0) {
            $this->session->setFlashdata('message', 'Data berhasil disimpan.');
            return redirect()->to('/staff/kas');
        } else {
            $this->session->setFlashdata('message', 'Data gagal disimpan: ' . $result['message']);
            return redirect()->to('/staff/kas/tambah')->withInput();
        }
    }

    public function getTutupharian()
    {
        if (!$this->request->getGet('tgl')) {
            $tgl     = date("d M Y");
            $tglcari = date("Y-m-d");
        } else {
            $tgl     = $this->request->getGet('tgl');
            $tglcari = date_format(date_create($tgl), "Y-m-d");
        }

        $storeid = $this->request->getGet('store');
        $store   = $this->storeModel->liststore();

        if ($_SESSION["logged_status"]["role"] == "Office Manager" || $_SESSION["logged_status"]["role"] == "Office Staff") {
            $result = $this->kasModel->lastSaldo($tglcari, $storeid);
        } else {
            $result = $this->kasModel->lastSaldo($tglcari);
        }

        $data = [
            'title'     => 'Rekapan Harian',
            'content'   => 'staff/kas/tutup',
            'extra'     => 'staff/kas/js/js_tutup',
            'mn_tutup'  => 'active',
            'penjualan' => $result,
            'tgl'       => $tgl,
            'store'     => $store,
            'storeid'   => $storeid,
            'colmas'    => 'collapse',
            'colset'    => 'collapse',
            'collap'    => 'collapse',
        ];
        return view('layout/wrapper', $data);
    }

    public function postSisakas()
    {
        $tgl  = date_format(date_create($this->request->getPost('tglback')), "Y-m-d");
        $sisa = $this->request->getPost('sisa');

        if ($_SESSION["logged_status"]["role"] == "Store Manager") {
            $data = [
                'nominal'    => $sisa,
                'jenis'      => 'Kas Sisa',
                'dateonly'   => $tgl,
                'tanggal'    => $tgl,
                'storeid'    => $_SESSION['logged_status']['storeid'],
                'keterangan' => 'Sisa Kas',
                'userid'     => $_SESSION['logged_status']['username']
            ];
        } elseif ($_SESSION["logged_status"]["role"] == "Staff") {
            $data = [
                'nominal'    => $sisa,
                'jenis'      => 'Kas Sisa',
                'storeid'    => $_SESSION['logged_status']['storeid'],
                'keterangan' => 'Sisa Kas',
                'userid'     => $_SESSION['logged_status']['username']
            ];
        } elseif ($_SESSION["logged_status"]["role"] == "Office Manager") {
            $data = [
                'nominal'    => $sisa,
                'jenis'      => 'Kas Sisa',
                'dateonly'   => $tgl,
                'tanggal'    => $tgl,
                'storeid'    => $this->request->getPost('storeid'),
                'keterangan' => 'Sisa Kas',
                'userid'     => $_SESSION['logged_status']['username']
            ];
        }

        $result = $this->kasModel->setSisa($data);

        if ($result["code"] == 5051) {
            $this->session->setFlashdata('message', 'Tutup kas sudah dilakukan.');
        } else {
            $this->session->setFlashdata('message', 'Tutup Kas Selesai.');
        }
        return redirect()->to('/staff/kas/tutupharian');
    }
}
