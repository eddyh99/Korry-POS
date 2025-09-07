<?php

namespace App\Controllers\Admin;

use App\Models\Admin\StokbahanbakuModel;
use App\Models\Admin\BahanbakuModel;
use App\Models\Admin\ProdukModel;

use App\Controllers\BaseApiController;

class Stokbahanbaku extends BaseApiController
{
    protected $stokbahanbakuModel;

    public function __construct()
    {
        $this->stokbahanbakuModel   = new StokbahanbakuModel();
        $this->bahanbakuModel       = new BahanbakuModel();
        $this->produkModel          = new ProdukModel();
    }

    public function getIndex()
    {

        $data = [
            'title'      => 'Data Stok Bahan Baku',
            'content'    => 'admin/stokbahanbaku/index',
            'extra'      => 'admin/stokbahanbaku/js/js_index', 
            'mn_master' => 'active',
            'colmas'     => 'collapse',
            'colset'     => 'collapse in',
            'collap'     => 'collapse',
            'side16'     => 'active',
        ];
        return view('layout/wrapper', $data);
    }

    public function postListdata()
    {
        $result = $this->stokbahanbakuModel->listStokbahanbaku();
        return $this->response->setJSON($result);
    }

    public function getTambah()
    {
        if (!$this->session->get('logged_status')) {
            return redirect()->to(base_url());
        }

        $bahanbaku  = $this->bahanbakuModel->Listbahanbaku();
        $data = [
            'title'      => 'Tambah Stok Bahan Baku',
            'content'    => 'admin/stokbahanbaku/tambah',
            'extra'      => 'admin/stokbahanbaku/js/js_tambah',
            'bahanbaku'  => $bahanbaku,
            'mn_master'  => 'active',
            'colmas'     => 'collapse in',
            'colset'     => 'collapse',
            'collap'     => 'collapse',
            'side16'     => 'active',
        ];
        return view('layout/wrapper', $data);
    }

    // Handle Post Tambah

public function postAddData()
{
    // Rules utk array input (gunakan wildcard *)
    $rules = [
        'idbahan.*' => [
            'label' => 'Bahan Baku',
            'rules' => 'required|integer',
            'errors' => [
                'required' => '{field} wajib dipilih.',
                'integer'  => '{field} tidak valid.'
            ]
        ],
        'jumlah.*' => [
            'label' => 'Jumlah',
            'rules' => 'required|numeric|greater_than[0]',
            'errors' => [
                'required'     => '{field} wajib diisi.',
                'numeric'      => '{field} harus berupa angka.',
                'greater_than' => '{field} harus lebih dari 0.'
            ]
        ],
        'satuan.*' => [
            'label' => 'Satuan',
            'rules' => 'required|in_list[yard,meter,pcs]',
            'errors' => [
                'required' => '{field} wajib dipilih.',
                'in_list'  => '{field} tidak valid.'
            ]
        ],
        'harga.*' => [
            'label' => 'Harga',
            'rules' => 'required|numeric|greater_than[0]',
            'errors' => [
                'required'     => '{field} wajib diisi.',
                'numeric'      => '{field} harus berupa angka.',
                'greater_than' => '{field} harus lebih dari 0.'
            ]
        ],
    ];

    if (!$this->validate($rules)) {
        $this->session->setFlashdata('message', implode('<br>', $this->validator->getErrors()));
        return redirect()->to(base_url("admin/stokbahanbaku/tambah"))->withInput();
    }

    // Ambil array input
    $idbahan = $this->request->getPost('idbahan');
    $jumlah  = $this->request->getPost('jumlah');
    $satuan  = $this->request->getPost('satuan');
    $harga   = $this->request->getPost('harga');

    $saved = true;

    // Loop tiap row
    foreach ($idbahan as $i => $id) {
        $idbahanVal = (int) esc($id);
        $jumlahVal  = (float) esc($jumlah[$i]);
        $satuanVal  = esc($satuan[$i]);
        $hargaVal   = (int) esc($harga[$i]);

        // Konversi yard -> meter
        if ($satuanVal === 'yard') {
            $jumlahVal = $jumlahVal * 0.9144;
            $satuanVal = 'meter';
        }

        $data = [
            "tanggal" => date('Y-m-d'),
            "idbahan" => $idbahanVal,
            "jumlah"  => $jumlahVal,
            "satuan"  => $satuanVal,
            "harga"   => $hargaVal
        ];

        $result = $this->stokbahanbakuModel->insertData($data);

        if ($result["code"] != 0) {
            $saved = false;
            break;
        }
    }

    if ($saved) {
        $this->session->setFlashdata('message', 'Data berhasil disimpan.');
        return redirect()->to(base_url("admin/stokbahanbaku"));
    } else {
        $this->session->setFlashdata('message', 'Data gagal disimpan.');
        return redirect()->to(base_url("admin/stokbahanbaku/tambah"));
    }
}

}
