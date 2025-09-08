<?php

namespace App\Controllers\Admin;

use App\Models\Admin\BiayaproduksiModel;

use App\Controllers\BaseApiController;

class Biayaproduksi extends BaseApiController
{
    protected $biayaproduksiModel;

    public function __construct()
    {
        $this->biayaproduksiModel = new BiayaproduksiModel();
    }

    public function getIndex()
    {
        $data = [
            'title'      => 'Data Biaya Produksi',
            'content'    => 'admin/biayaproduksi/index',
            'extra'      => 'admin/biayaproduksi/js/js_index',
            'mn_setting' => 'active',
            'colset'     => 'collapse in',
            'colmas'     => 'collapse',
            'colkonsi'   => 'collapse',
            'colwho'     => 'collapse',
            'collap'     => 'collapse',
            'side11'     => 'active',
        ];
        return view('layout/wrapper', $data);
    }

    public function postListdata()
    {
        $result = $this->biayaproduksiModel->listBiayaproduksi();
        return $this->response->setJSON($result);
    }

    public function getTambah()
    {
        $data = [
            'title'      => 'Tambah Data Biaya Produksi',
            'content'    => 'admin/biayaproduksi/tambah',
            'mn_setting' => 'active',
            'colset'     => 'collapse in',
            'colmas'     => 'collapse',
            'colkonsi'   => 'collapse',
            'colwho'     => 'collapse',
            'collap'     => 'collapse',
            'side11'     => 'active',
        ];
        return view('layout/wrapper', $data);
    }

    public function postAddData()
    {
        $rules = [
            'biayaproduksi' => [
                'label' => 'Biaya Produksi',
                'rules' => 'required|alpha_numeric_space|max_length[20]|is_unique[biaya_produksi.namabiayaproduksi]',
                'errors' => [
                    'required' => '{field} wajib diisi.',
                    'alpha_numeric_space' => '{field} hanya boleh huruf, angka, dan spasi.',
                    'max_length' => '{field} maksimal 20 karakter.',
                    'is_unique'     => '{field} sudah digunakan.'
                ]
            ]
        ];

        if (! $this->validate($rules)) {
            $this->session->setFlashdata('message', implode('<br>', $this->validator->getErrors()));
            return redirect()->to('/admin/biayaproduksi/tambah');
        }

        $biayaproduksi = ucfirst(trim($this->request->getPost('biayaproduksi')));
        $userid   = $this->session->get('logged_status')['username'] ?? '';

        $data = [
            'namabiayaproduksi' => $biayaproduksi,
            'userid'       => $userid
        ];

        $result = $this->biayaproduksiModel->insertData($data);

        $msg = ($result['code'] == 0) ? 'Data berhasil disimpan.' : 'Data gagal disimpan.';
        $this->session->setFlashdata('message', $msg);

        return redirect()->to('/admin/biayaproduksi');
    }

    public function getUbah($biayaproduksi)
    {
        $biayaproduksi = base64_decode($biayaproduksi);
        $result   = $this->biayaproduksiModel->getBiayaproduksi($biayaproduksi);

        $data = [
            'title'      => 'Ubah Data Biaya Produksi',
            'content'    => 'admin/biayaproduksi/ubah',
            'detail'     => $result,
            'mn_setting' => 'active',
            'colset'     => 'collapse in',
            'colmas'     => 'collapse',
            'colkonsi'   => 'collapse',
            'colwho'     => 'collapse',
            'collap'     => 'collapse',
            'side11'     => 'active',
        ];
        return view('layout/wrapper', $data);
    }

    public function postUpdateData()
    {
        $rules = [
            'biayaproduksi' => [
                'label' => 'Biaya Produksi',
                'rules' => 'required|alpha_numeric_space|max_length[20]|is_unique[biaya_produksi.namabiayaproduksi]',
                'errors' => [
                    'required' => '{field} wajib diisi.',
                    'alpha_numeric_space' => '{field} hanya boleh huruf, angka, dan spasi.',
                    'max_length' => '{field} maksimal 20 karakter.',
                    'is_unique'     => '{field} sudah digunakan.'
                ]
            ]
        ];

        $oldBiayaproduksi = $this->request->getPost('oldbiayaproduksi');

        if (! $this->validate($rules)) {
            $this->session->setFlashdata('message', implode('<br>', $this->validator->getErrors()));
            return redirect()->to('/admin/biayaproduksi/ubah/' . base64_encode($oldBiayaproduksi));
        }

        $biayaproduksi = ucfirst(trim($this->request->getPost('biayaproduksi')));
        $userid   = $this->session->get('logged_status')['username'] ?? '';

        $data = [
            'namabiayaproduksi' => $biayaproduksi,
            'userid'       => $userid
        ];

        $result = $this->biayaproduksiModel->updateData($data, $oldBiayaproduksi);

        $msg = ($result['code'] == 0) ? 'Data berhasil diubah.' : 'Data gagal diubah.';
        $this->session->setFlashdata('message', $msg);

        return redirect()->to('/admin/biayaproduksi');
    }

    public function getHapus($biayaproduksi)
    {
        $biayaproduksi = base64_decode($biayaproduksi);

        $data = ['status' => 1];
        $result = $this->biayaproduksiModel->hapusData($data, $biayaproduksi);

        if ($result['code'] == 0) {
            $this->session->setFlashdata('message', 'Data berhasil dihapus.');
        } else {
            $this->session->setFlashdata('message', 'Data gagal dihapus.');
        }

        return redirect()->to('/admin/biayaproduksi');
    }
}
