<?php

namespace App\Controllers\Admin;

use App\Models\Admin\MetodebayarModel;

use App\Controllers\BaseApiController;

class Metodebayar extends BaseApiController
{
    protected $metodebayarModel;

    public function __construct()
    {
        $this->metodebayarModel = new MetodebayarModel();
    }

    public function getIndex()
    {
        $data = [
            'title'      => 'Data Metode Bayar',
            'content'    => 'admin/metodebayar/index',
            'extra'      => 'admin/metodebayar/js/js_index',
            'mn_setting' => 'active',
            'colmas'     => 'collapse',
            'colset'     => 'collapse in',
            'collap'     => 'collapse',
            'side4'      => 'active',
        ];
        return view('layout/wrapper', $data);
    }

    public function postListdata()
    {
        $result = $this->metodebayarModel->listMetodebayar();
        return $this->response->setJSON($result);
    }

    public function getUbah($noakun)
    {
        $noakun = base64_decode($noakun);
        $result = $this->metodebayarModel->getMetodebayar($noakun);

        $data = [
            'title'      => 'Ubah Data Metode Bayar',
            'content'    => 'admin/metodebayar/ubah',
            'detail'     => $result,
            'mn_setting' => 'active',
            'colmas'     => 'collapse',
            'colset'     => 'collapse in',
            'collap'     => 'collapse',
            'side4'      => 'active',
        ];
        return view('layout/wrapper', $data);
    }

    // Handle Post Ubah    

    public function postUpdateData()
    {
        $rules = [
            'namaakun' => [
                'label'  => 'Account Name',
                'rules'  => 'required|max_length[50]',
                'errors' => [
                    'required'   => '{field} wajib diisi.',
                    'max_length' => '{field} maksimal 50 karakter.'
                ]
            ],
            'noakun' => [
                'label'  => 'Account Number',
                'rules'  => 'required|numeric|max_length[30]',
                'errors' => [
                    'required'   => '{field} wajib diisi.',
                    'numeric'    => '{field} harus berupa angka.',
                    'max_length' => '{field} maksimal 30 karakter.'
                ]
            ],
            'namabank' => [
                'label'  => 'Bank Name',
                'rules'  => 'required|max_length[50]',
                'errors' => [
                    'required'   => '{field} wajib diisi.',
                    'max_length' => '{field} maksimal 50 karakter.'
                ]
            ],
            'cabangbank' => [
                'label'  => 'Branch',
                'rules'  => 'required|max_length[50]',
                'errors' => [
                    'required'   => '{field} wajib diisi.',
                    'max_length' => '{field} maksimal 50 karakter.'
                ]
            ],
            'kodeswift' => [
                'label'  => 'SWIFT Code',
                'rules'  => 'permit_empty|max_length[20]',
                'errors' => [
                    'max_length' => '{field} maksimal 20 karakter.'
                ]
            ],
            'matauang' => [
                'label'  => 'Currency',
                'rules'  => 'required|max_length[10]',
                'errors' => [
                    'required'   => '{field} wajib diisi.',
                    'max_length' => '{field} maksimal 10 karakter.'
                ]
            ],
            'negara' => [
                'label'  => 'Country',
                'rules'  => 'required|max_length[50]',
                'errors' => [
                    'required'   => '{field} wajib diisi.',
                    'max_length' => '{field} maksimal 50 karakter.'
                ]
            ],
        ];

        $oldnoakun = $this->request->getPost('oldnoakun');

        if (! $this->validate($rules)) {
            $this->session->setFlashdata('message', implode('<br>', $this->validator->getErrors()));
            return redirect()->to('/admin/metodebayar/ubah/' . base64_encode($oldnoakun));
        }

        $data = [
            'namaakun'   => trim($this->request->getPost('namaakun')),
            'noakun'     => trim($this->request->getPost('noakun')),
            'namabank'   => trim($this->request->getPost('namabank')),
            'cabangbank' => trim($this->request->getPost('cabangbank')),
            'kodeswift'  => trim($this->request->getPost('kodeswift')),
            'matauang'   => trim($this->request->getPost('matauang')),
            'negara'     => trim($this->request->getPost('negara')),
        ];

        $result = $this->metodebayarModel->updateData($data, $oldnoakun);

        $msg = ($result['code'] == 0) ? 'Data berhasil diubah.' : 'Data gagal diubah: ' . $result['message'];
        $this->session->setFlashdata('message', $msg);

        return redirect()->to('/admin/metodebayar');
    }
}
