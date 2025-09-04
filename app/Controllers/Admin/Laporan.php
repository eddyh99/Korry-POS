<?php

namespace App\Controllers\Admin;

use App\Models\Admin\StoreModel;
use App\Models\Admin\BrandModel;
use App\Models\Admin\KategoriModel;
use App\Models\Admin\LaporanModel;

use App\Models\Admin\PengeluaranModel;

use App\Controllers\BaseApiController;

class Laporan extends BaseApiController
{
    protected $store;
    protected $brand;
    protected $kategori;
    protected $laporan;

    protected $pengeluaran;

    public function __construct()
    {
        $this->store    = new StoreModel();
        $this->brand    = new BrandModel();
        $this->kategori = new KategoriModel();
        $this->laporan  = new LaporanModel();

        $this->pengeluaran  = new PengeluaranModel();
    }

    // Mutasi

    public function getMutasi()
    {
        $data = [
            'title'      => 'Laporan Mutasi Stok',
            'content'    => 'admin/laporan/mutasi',
            'extra'      => 'admin/laporan/js/js_mutasi',
            'store'      => $this->store->listStore(),
            'brand'      => $this->brand->listBrand(),
            'kategori'   => $this->kategori->listKategori(),
            'mn_laporan' => 'active',
            'collap'     => 'collapse in',
            'colmas'     => 'collapse',
            'colset'     => 'collapse',
            'side10'     => 'active'
        ];

        return view('layout/wrapper', $data);
    }

    public function postListmutasi()
    {
        $bulan    = $this->request->getPost('bulan');
        $tahun    = $this->request->getPost('tahun');
        $brand    = $this->request->getPost('brand');
        $kategori = $this->request->getPost('kategori');
        $storeid  = $this->request->getPost('storeid');

        $result = $this->laporan->getmutasi($bulan, $tahun, $storeid, $brand, $kategori);

        return $this->response->setJSON($result);
    }

    // Mutasi Detail

    public function getMutasidetail()
    {
        $data = [
            'title'      => 'Laporan Mutasi Stok Detail',
            'content'    => 'admin/laporan/mutasidetail',
            'extra'      => 'admin/laporan/js/js_mutasidetail',
            'store'      => $this->store->liststore(),
            'brand'      => $this->brand->listbrand(),
            'kategori'   => $this->kategori->listkategori(),
            'mn_laporan' => 'active',
            'collap'     => 'collapse in',
            'colmas'     => 'collapse',
            'colset'     => 'collapse',
            'side20'     => 'active'
        ];

        return view('layout/wrapper', $data);
    }

    public function postListmutasidetail()
    {
        $bulan    = $this->request->getPost('bulan');
        $tahun    = $this->request->getPost('tahun');
        $storeid  = $this->request->getPost('storeid');
        $kategori = $this->request->getPost('kategori');
        $brand    = $this->request->getPost('brand');

        $result = $this->laporan->getmutasidetail($bulan, $tahun, $storeid, $brand, $kategori);

        return $this->response->setJSON($result);
    }

    // Penjualan
    public function getPenjualan()
    {
        $toko = $this->store->liststore();

        $data = [
            'title'      => 'Laporan Penjualan',
            'content'    => 'admin/laporan/penjualan',
            'extra'      => 'admin/laporan/js/js_penjualan',
            'store'      => $toko,
            'mn_laporan' => 'active',
            'collap'     => 'collapse in',
            'colmas'     => 'collapse',
            'colset'     => 'collapse',
            'side11'     => 'active'
        ];

        return view('layout/wrapper', $data);
    }

    public function postListpenjualan()
    {
        $tgl     = explode("-", $this->request->getPost('tgl'));
        $storeid = $this->request->getPost('storeid');

        $awal  = date_format(date_create(trim($tgl[0])), "Y-m-d");
        $akhir = date_format(date_create(trim($tgl[1])), "Y-m-d");

        $result = $this->laporan->getpenjualan($awal, $akhir, $storeid);
        return $this->response->setJSON($result);
    }

    // DETAIL Penjualan
    public function getDetailpenjualan($id)
    {
        $data = [
            'title'      => 'Laporan Penjualan',
            'content'    => 'admin/laporan/detailpenjualan',
            'extra'      => 'admin/laporan/js/js_detailpenjualan',
            'mn_laporan' => 'active',
            'key'        => $id,
            'collap'     => 'collapse in',
            'colmas'     => 'collapse',
            'colset'     => 'collapse',
            'side11'     => 'active'
        ];

        return view('layout/wrapper', $data);
    }

    public function postListdetailpenjualan()
    {
        $id = $this->request->getPost('key');
        $result = $this->laporan->detailpenjualan($id);
        return $this->response->setJSON($result);
    }

    // Brand
    public function getBrand()
    {
        $toko     = $this->store->liststore();
        $brand    = $this->brand->listbrand();
        $kategori = $this->kategori->listkategori();

        $data = [
            'title'      => 'Laporan Detail Brand',
            'content'    => 'admin/laporan/brand',
            'extra'      => 'admin/laporan/js/js_brand',
            'store'      => $toko,
            'brand'      => $brand,
            'kategori'   => $kategori,
            'mn_laporan' => 'active',
            'collap'     => 'collapse in',
            'colmas'     => 'collapse',
            'colset'     => 'collapse',
            'side15'     => 'active'
        ];

        return view('layout/wrapper', $data);
    }

    public function postListbrand()
    {
        $tgl      = explode("-", $this->request->getPost('tgl'));
        $storeid  = $this->request->getPost('storeid');
        $brand    = $this->request->getPost('brand');
        $kategori = $this->request->getPost('kategori');

        $awal  = date_format(date_create(trim($tgl[0])), "Y-m-d");
        $akhir = date_format(date_create(trim($tgl[1])), "Y-m-d");

        $result = $this->laporan->getbrand($awal, $akhir, $storeid, $brand, $kategori);
        return $this->response->setJSON($result);
    }

    // Barang
    public function getBarang()
    {
        $toko = $this->store->liststore();

        $data = [
            'title'      => 'Laporan Mutasi Barang',
            'content'    => 'admin/laporan/barang',
            'extra'      => 'admin/laporan/js/js_barang',
            'store'      => $toko,
            'mn_laporan' => 'active',
            'collap'     => 'collapse in',
            'colmas'     => 'collapse',
            'colset'     => 'collapse',
            'side12'     => 'active'
        ];

        return view('layout/wrapper', $data);
    }

    public function postListbarang()
    {
        $tgl     = explode("-", $this->request->getPost('tgl'));
        $storeid = $this->request->getPost('storeid');
        $jenis   = $this->request->getPost('jenis');

        $awal   = date_format(date_create(trim($tgl[0])), "Y-m-d");
        $akhir  = date_format(date_create(trim($tgl[1])), "Y-m-d");

        $result = $this->laporan->getBarang($awal, $akhir, $storeid, $jenis);

        return $this->response->setJSON($result);
    }

    // Non-Tunai
    public function getNontunai()
    {
        $toko = $this->store->liststore();
        $data = [
            'title'      => 'Laporan Transaksi Non Tunai',
            'content'    => 'admin/laporan/nontunai',
            'extra'      => 'admin/laporan/js/js_nontunai',
            'store'      => $toko,
            'mn_laporan' => 'active',
            'collap'     => 'collapse in',
            'colmas'     => 'collapse',
            'colset'     => 'collapse',
            'side14'     => 'active'
        ];

        return view('layout/wrapper', $data);
    }

    public function postListnontunai()
    {
        $tgl     = explode("-", $this->request->getPost('tgl'));
        $storeid = $this->request->getPost('storeid');

        $awal  = date_format(date_create(trim($tgl[0])), "Y-m-d");
        $akhir = date_format(date_create(trim($tgl[1])), "Y-m-d");

        $result = $this->laporan->getnontunai($awal, $akhir, $storeid);
        return $this->response->setJSON($result);
    }

    // Permintaan
    public function getRequest()
    {
        $toko = $this->store->liststore();

        $data = [
            'title'      => 'Laporan Permintaan',
            'content'    => 'admin/laporan/permintaan',
            'extra'      => 'admin/laporan/js/js_permintaan',
            'store'      => $toko,
            'mn_laporan' => 'active',
            'collap'     => 'collapse in',
            'colmas'     => 'collapse',
            'colset'     => 'collapse',
            'side17'     => 'active'
        ];

        return view('layout/wrapper', $data);
    }

    public function postListrequest()
    {
        $tgl     = explode("-", $this->request->getPost('tgl'));
        $storeid = $this->request->getPost('storeid');
        $jenis   = $this->request->getPost('jenis');

        $awal  = date_format(date_create(trim($tgl[0])), "Y-m-d");
        $akhir = date_format(date_create(trim($tgl[1])), "Y-m-d");

        $result = $this->laporan->getrequest($awal, $akhir, $storeid, $jenis);

        return $this->response->setJSON($result);
    }

    // Retur
    public function getRetur()
    {
        $toko = $this->store->liststore();

        $data = [
            'title'      => 'Laporan Retur',
            'content'    => 'admin/laporan/retur',
            'extra'      => 'admin/laporan/js/js_retur',
            'store'      => $toko,
            'mn_laporan' => 'active',
            'collap'     => 'collapse in',
            'colmas'     => 'collapse',
            'colset'     => 'collapse',
            'side18'     => 'active'
        ];

        return view('layout/wrapper', $data);
    }

    public function postListretur()
    {
        $tgl     = explode("-", $this->request->getPost('tgl'));
        $storeid = $this->request->getPost('storeid');

        $awal   = date_format(date_create(trim($tgl[0])), "Y-m-d");
        $akhir  = date_format(date_create(trim($tgl[1])), "Y-m-d");

        $result = $this->laporan->getretur($awal, $akhir, $storeid);

        return $this->response->setJSON($result);
    }

    // Stok Out
    public function getStokout()
    {
        $toko = $this->store->liststore();

        $data = [
            'title'      => 'Laporan Stok Out',
            'content'    => 'admin/laporan/stokout',
            'extra'      => 'admin/laporan/js/js_stokout',
            'store'      => $toko,
            'mn_laporan' => 'active',
            'collap'     => 'collapse in',
            'colmas'     => 'collapse',
            'colset'     => 'collapse',
            'side19'     => 'active'
        ];

        return view('layout/wrapper', $data);
    }

    public function postListstokout()
    {
        $tgl     = explode("-", $this->request->getPost('tgl'));
        $storeid = $this->request->getPost('storeid');

        $awal  = date_format(date_create(trim($tgl[0])), "Y-m-d");
        $akhir = date_format(date_create(trim($tgl[1])), "Y-m-d");

        $result = $this->laporan->getStokout($awal, $akhir, $storeid);

        return $this->response->setJSON($result);
    }

    // Kas Keluar
    public function getKaskeluar()
    {
        $toko = $this->store->liststore();

        $data = [
            'title'      => 'Laporan Kas Keluar',
            'content'    => 'admin/laporan/kaskeluar',
            'extra'      => 'admin/laporan/js/js_kaskeluar',
            'store'      => $toko,
            'mn_laporan' => 'active',
            'collap'     => 'collapse in',
            'colmas'     => 'collapse',
            'colset'     => 'collapse',
            'side21'     => 'active'
        ];

        return view('layout/wrapper', $data);
    }

    public function postListkaskeluar()
    {
        $tgl     = explode("-", $this->request->getPost('tgl'));
        $storeid = $this->request->getPost('storeid');

        $awal  = date_format(date_create(trim($tgl[0])), "Y-m-d");
        $akhir = date_format(date_create(trim($tgl[1])), "Y-m-d");

        $result = $this->laporan->getKaskeluar($awal, $akhir, $storeid);

        return $this->response->setJSON($result);
    }

    // 4 Sept 2025 

    // Laporan Neraca,
    // Laporan Laba Rugi,
    // Laporan Produk Terlaris + 10 Margin Untung

    // Laporan Pos Bulanan

    public function getPospengeluaran()
    {
        $data = [
            'title'      => 'Laporan Pos Pengeluaran',
            'content'    => 'admin/laporan/pospengeluaran',
            'extra'      => 'admin/laporan/js/js_pospengeluaran',
            'store'      => $this->store->listStore(),
            'pengeluaran'=> $this->pengeluaran->listPengeluaran(),
            'mn_laporan' => 'active',
            'collap'     => 'collapse in',
            'colmas'     => 'collapse',
            'colset'     => 'collapse',
            'side21'     => 'active'
        ];

        return view('layout/wrapper', $data);
    }

    public function postListpospengeluaran()
    {
        $bulan    = $this->request->getPost('bulan');
        $tahun    = $this->request->getPost('tahun');
        $storeid  = $this->request->getPost('storeid');
        $pengeluaran  = $this->request->getPost('pengeluaran');

        $result = $this->laporan->getpospengeluaran($bulan, $tahun, $storeid, $pengeluaran);

        return $this->response->setJSON($result);
    }

    public function getProdukterlaris()
    {
        $data = [
            'title'      => 'Laporan 10 Produk Terlaris',
            'content'    => 'admin/laporan/produkterlaris',
            'extra'      => 'admin/laporan/js/js_produkterlaris',
            'mn_laporan' => 'active',
            'collap'     => 'collapse in',
            'colmas'     => 'collapse',
            'colset'     => 'collapse',
            'side21'     => 'active'
        ];

        return view('layout/wrapper', $data);
    }

    public function postListprodukterlaris()
    {
        $bulan    = $this->request->getPost('bulan');
        $tahun    = $this->request->getPost('tahun');

        $result = $this->laporan->getprodukterlaris($bulan, $tahun);

        return $this->response->setJSON($result);
    }

    public function getNeraca()
    {
        $data = [
            'title'      => 'Laporan Neraca',
            'content'    => 'admin/laporan/neraca',
            'extra'      => 'admin/laporan/js/js_neraca',
            'mn_laporan' => 'active',
            'collap'     => 'collapse in',
            'colmas'     => 'collapse',
            'colset'     => 'collapse',
            'side21'     => 'active'
        ];

        return view('layout/wrapper', $data);
    }

    public function postListneraca()
    {
        $tahun    = $this->request->getPost('tahun');

        $result = $this->laporan->getneraca($tahun);

        return $this->response->setJSON($result);
    }

    public function getLabarugi()
    {
        $data = [
            'title'      => 'Laporan Laba-Rugi',
            'content'    => 'admin/laporan/labarugi',
            'extra'      => 'admin/laporan/js/js_labarugi',
            'mn_laporan' => 'active',
            'collap'     => 'collapse in',
            'colmas'     => 'collapse',
            'colset'     => 'collapse',
            'side21'     => 'active'
        ];

        return view('layout/wrapper', $data);
    }

    public function postListlabarugi()
    {
        $tahun    = $this->request->getPost('tahun');

        $result = $this->laporan->getlabarugi($tahun);

        return $this->response->setJSON($result);
    }
}
