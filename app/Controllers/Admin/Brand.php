<?php

namespace App\Controllers\Admin;

use App\Models\Admin\BrandModel;

use App\Controllers\BaseApiController;

class Brand extends BaseApiController
{
    protected $brandModel;

    public function __construct()
    {
        $this->brandModel = new BrandModel();
    }

    public function getIndex()
    {
        $data = [
            'title'      => 'Data Brand',
            'content'    => 'admin/brand/index',
            'extra'      => 'admin/brand/js/js_index',
            'mn_setting' => 'active',
            'colset'     => 'collapse in',
            'colmas'     => 'collapse',
            'colkonsi'   => 'collapse',
            'colwho'     => 'collapse',
            'collap'     => 'collapse',
            'side3'      => 'active',
        ];
        return view('layout/wrapper', $data);
    }

    public function postListdata()
    {
        $result = $this->brandModel->listBrand();
        return $this->response->setJSON($result);
    }

    public function getTambah()
    {
        $data = [
            'title'      => 'Tambah Data Brand',
            'content'    => 'admin/brand/tambah',
            'mn_setting' => 'active',
            'colset'     => 'collapse in',
            'colmas'     => 'collapse',
            'colkonsi'   => 'collapse',
            'colwho'     => 'collapse',
            'collap'     => 'collapse',
            'side3'      => 'active',
        ];
        return view('layout/wrapper', $data);
    }

    public function postAddData()
    {
        $rules = [
            'brand' => [
                'label'  => 'Nama Brand',
                'rules'  => 'required|trim|max_length[50]|alpha_numeric_space',
                'errors' => [
                    'required'             => '{field} wajib diisi.',
                    'alpha_numeric_space'  => '{field} hanya boleh berisi huruf, angka, dan spasi.',
                    'max_length'           => '{field} maksimal 50 karakter.'
                ]
            ],
            'keterangan' => [
                'label'  => 'Keterangan',
                'rules'  => 'permit_empty|trim|max_length[100]|alpha_numeric_punct',
                'errors' => [
                    'required'             => '{field} wajib diisi.',
                    'alpha_numeric_punct'  => '{field} hanya boleh berisi huruf, angka, spasi, dan tanda baca tertentu.',
                    'max_length'           => '{field} maksimal 100 karakter.'
                ]
            ]
        ];

        if (! $this->validate($rules)) {
            session()->setFlashdata('message', implode('<br>', $this->validator->getErrors()));
            return redirect()->to('/admin/brand/tambah')->withInput();
        }

        $data = [
            'namabrand'  => ucfirst(esc($this->request->getPost('brand'))),
            'keterangan' => esc($this->request->getPost('keterangan')),
            'userid'     => session()->get('logged_status')['username']
        ];

        $result = $this->brandModel->insertData($data);

        if ($result['code'] == 0) {
            session()->setFlashdata('message', 'Data berhasil disimpan.');
            return redirect()->to('/admin/brand');
        }

        session()->setFlashdata('message', 'Data gagal disimpan: ' . ($result['message'] ?? ''));
        return redirect()->to('/admin/brand/tambah')->withInput();
    }

    public function getUbah($brand)
    {
        $brand = base64_decode(esc($brand));
        $result = $this->brandModel->getBrand($brand);

        $data = [
            'title'      => 'Ubah Data Brand',
            'content'    => 'admin/brand/ubah',
            'detail'     => $result,
            'mn_setting' => 'active',
            'colset'     => 'collapse in',
            'colmas'     => 'collapse',
            'colkonsi'   => 'collapse',
            'colwho'     => 'collapse',
            'collap'     => 'collapse',
            'side3'      => 'active',
        ];
        return view('layout/wrapper', $data);
    }

    public function postUpdateData()
    {
        $rules = [
            'brand' => [
                'label'  => 'Nama Brand',
                'rules'  => 'required|trim|max_length[50]|alpha_numeric_space',
                'errors' => [
                    'required'             => '{field} wajib diisi.',
                    'alpha_numeric_space'  => '{field} hanya boleh berisi huruf, angka, dan spasi.',
                    'max_length'           => '{field} maksimal 50 karakter.'
                ]
            ],
            'keterangan' => [
                'label'  => 'Keterangan',
                'rules'  => 'permit_empty|trim|max_length[100]|alpha_numeric_punct',
                'errors' => [
                    'required'             => '{field} wajib diisi.',
                    'alpha_numeric_punct'  => '{field} hanya boleh berisi huruf, angka, spasi, dan tanda baca tertentu.',
                    'max_length'           => '{field} maksimal 100 karakter.'
                ]
            ]
        ];

        $oldbrand = esc($this->request->getPost('oldbrand'));

        if (! $this->validate($rules)) {
            session()->setFlashdata('message', implode('<br>', $this->validator->getErrors()));
            return redirect()->to('/admin/brand/ubah/' . base64_encode($oldbrand))->withInput();
        }

        $data = [
            'namabrand'  => ucfirst(esc($this->request->getPost('brand'))),
            'keterangan' => esc($this->request->getPost('keterangan')),
            'userid'     => session()->get('logged_status')['username']
        ];

        $result = $this->brandModel->updateData($data, $oldbrand);

        if ($result['code'] == 0) {
            session()->setFlashdata('message', 'Data berhasil diubah.');
            return redirect()->to('/admin/brand');
        }

        session()->setFlashdata('message', 'Data gagal diubah: ' . ($result['message'] ?? ''));
        return redirect()->to('/admin/brand/ubah/' . base64_encode($oldbrand))->withInput();
    }

    public function getHapus($brand)
    {
        $userid = session()->get('logged_status')['username'];
        $brand = base64_decode(esc($brand));

        $data = [
            'status' => 1,
            'userid' => $userid
        ];

        $result = $this->brandModel->hapusData($data, $brand);

        if ($result['code'] == 0) {
            $this->session->setFlashdata('message', 'Data berhasil dihapus.');
        } else {
            $this->session->setFlashdata('message', 'Data gagal dihapus.');
        }

        return redirect()->to('/admin/brand');
    }
}
