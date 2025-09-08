<?php

namespace App\Controllers\Admin;

use App\Models\Admin\PengeluaranModel;

use App\Controllers\BaseApiController;

class Pengeluaran extends BaseApiController
{
    protected $pengeluaranModel;

    public function __construct()
    {
        $this->pengeluaranModel = new PengeluaranModel();
    }

    public function getIndex()
    {
        $data = [
            'title'      => 'Data Pengeluaran',
            'content'    => 'admin/pengeluaran/index',
            'extra'      => 'admin/pengeluaran/js/js_index',
            'mn_setting' => 'active',
            'colset'     => 'collapse in',
            'colmas'     => 'collapse',
            'colkonsi'   => 'collapse',
            'colwho'     => 'collapse',
            'collap'     => 'collapse',
            'side9'      => 'active',
        ];
        return view('layout/wrapper', $data);
    }

    public function postListdata()
    {
        $result = $this->pengeluaranModel->listPengeluaran();
        return $this->response->setJSON($result);
    }

    public function getTambah()
    {
        $data = [
            'title'      => 'Tambah Data Pengeluaran',
            'content'    => 'admin/pengeluaran/tambah',
            'mn_setting' => 'active',
            'colset'     => 'collapse in',
            'colmas'     => 'collapse',
            'colkonsi'   => 'collapse',
            'colwho'     => 'collapse',
            'collap'     => 'collapse',
            'side9'      => 'active',
        ];
        return view('layout/wrapper', $data);
    }

    public function postAddData()
    {
        $rules = [
            'pengeluaran' => [
                'label' => 'Pengeluaran',
                'rules' => 'required|alpha_numeric_space|max_length[20]|is_unique[pengeluaran.namapengeluaran]',
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
            return redirect()->to('/admin/pengeluaran/tambah');
        }

        $pengeluaran = ucfirst(trim($this->request->getPost('pengeluaran')));
        $userid   = $this->session->get('logged_status')['username'] ?? '';

        $data = [
            'namapengeluaran' => $pengeluaran,
            'userid'       => $userid
        ];

        $result = $this->pengeluaranModel->insertData($data);

        $msg = ($result['code'] == 0) ? 'Data berhasil disimpan.' : 'Data gagal disimpan.';
        $this->session->setFlashdata('message', $msg);

        return redirect()->to('/admin/pengeluaran');
    }

    public function getUbah($pengeluaran)
    {
        $pengeluaran = base64_decode($pengeluaran);
        $result   = $this->pengeluaranModel->getPengeluaran($pengeluaran);

        $data = [
            'title'      => 'Ubah Data Pengeluaran',
            'content'    => 'admin/pengeluaran/ubah',
            'detail'     => $result,
            'mn_setting' => 'active',
            'colset'     => 'collapse in',
            'colmas'     => 'collapse',
            'colkonsi'   => 'collapse',
            'colwho'     => 'collapse',
            'collap'     => 'collapse',
            'side9'      => 'active',
        ];
        return view('layout/wrapper', $data);
    }

    public function postUpdateData()
    {
        $rules = [
            'pengeluaran' => [
                'label' => 'Pengeluaran',
                'rules' => 'required|alpha_numeric_space|max_length[20]|is_unique[pengeluaran.namapengeluaran]',
                'errors' => [
                    'required' => '{field} wajib diisi.',
                    'alpha_numeric_space' => '{field} hanya boleh huruf, angka, dan spasi.',
                    'max_length' => '{field} maksimal 20 karakter.',
                    'is_unique'     => '{field} sudah digunakan.'
                ]
            ]
        ];

        $oldPengeluaran = $this->request->getPost('oldpengeluaran');

        if (! $this->validate($rules)) {
            $this->session->setFlashdata('message', implode('<br>', $this->validator->getErrors()));
            return redirect()->to('/admin/pengeluaran/ubah/' . base64_encode($oldPengeluaran));
        }

        $pengeluaran = ucfirst(trim($this->request->getPost('pengeluaran')));
        $userid   = $this->session->get('logged_status')['username'] ?? '';

        $data = [
            'namapengeluaran' => $pengeluaran,
            'userid'       => $userid
        ];

        $result = $this->pengeluaranModel->updateData($data, $oldPengeluaran);

        $msg = ($result['code'] == 0) ? 'Data berhasil diubah.' : 'Data gagal diubah.';
        $this->session->setFlashdata('message', $msg);

        return redirect()->to('/admin/pengeluaran');
    }

    public function getHapus($pengeluaran)
    {
        $pengeluaran = base64_decode($pengeluaran);

        $data = ['status' => 1];
        $result = $this->pengeluaranModel->hapusData($data, $pengeluaran);

        if ($result['code'] == 0) {
            $this->session->setFlashdata('message', 'Data berhasil dihapus.');
        } else {
            $this->session->setFlashdata('message', 'Data gagal dihapus.');
        }

        return redirect()->to('/admin/pengeluaran');
    }
}
