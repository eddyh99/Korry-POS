<?php

namespace App\Controllers\Admin;

use App\Models\Staff\ReturModel;

use App\Controllers\BaseApiController;

class Bayar extends BaseApiController
{
    protected $returModel;

    public function __construct()
    {
        $this->returModel = new ReturModel();
    }

    public function getIndex()
    {
        $data = [
            'title'    => 'Ganti Cara Bayar',
            'content'  => 'admin/bayar/index',
            'extra'    => 'admin/bayar/js/js_index',
            'mn_bayar' => 'active',
            'colset'   => 'collapse',
            'colmas'   => 'collapse',
            'colkonsi' => 'collapse',
            'colwho'   => 'collapse',
            'collap'   => 'collapse',
        ];

        return view('layout/wrapper', $data);
    }

    public function postListdata()
    {
        $result = $this->returModel->listnota();
        return $this->response->setJSON($result);
    }

    public function getGanti($id)
    {
        $key    = base64_decode($id);
        $detail = $this->returModel->detailnota($key);

        $data = [
            'title'    => 'Ganti Cara Bayar',
            'content'  => 'admin/bayar/ganti',
            'extra'    => 'admin/bayar/js/js_ganti',
            'mn_bayar' => 'active',
            'detail'   => $detail,
            'colset'   => 'collapse',
            'colmas'   => 'collapse',
            'colkonsi' => 'collapse',
            'colwho'   => 'collapse',
            'collap'   => 'collapse',
        ];

        return view('layout/wrapper', $data);
    }

    public function postGantibayar()
    {
        $key = $this->request->getPost('key');

        // Validasi input
        $rules = [
            'carabayar' => [
                'label' => 'Cara Pembayaran',
                'rules' => 'trim|required'
            ],
            'fee' => [
                'label' => 'Fee',
                'rules' => 'permit_empty|decimal'
            ]
        ];

        if (! $this->validate($rules)) {
            session()->setFlashdata('gagal', $this->message->error_msg($this->validator->listErrors()));
            return redirect()->to("/admin/bayar/ganti/" . base64_encode($key));
        }

        $carabayar = $this->request->getPost('carabayar');
        $fee       = $this->request->getPost('fee');

        if ($carabayar === 'credit') {
            if ($fee <= 0) {
                session()->setFlashdata('gagal', $this->message->error_msg('Fee harus lebih dari 0 untuk pembayaran kredit.'));
                return redirect()->to("/admin/bayar/ganti/" . base64_encode($key));
            }
        } else {
            $fee = 0;
        }

        $mdata = [
            'method' => $carabayar,
            'fee'    => $fee
        ];

        $result = $this->returModel->ganti_bayar($key, $mdata);

        if ($result) {
            session()->setFlashdata('sukses', 'Metode pembayaran berhasil diubah.');
        } else {
            session()->setFlashdata('gagal', 'Gagal mengubah metode pembayaran.');
        }

        return redirect()->to("/admin/bayar");
    }
}
