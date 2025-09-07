<?php

namespace App\Controllers\Admin;

use App\Models\Admin\StoreModel;

use App\Controllers\BaseApiController;

class Store extends BaseApiController
{
    protected $storeModel;

    public function __construct()
    {
        $this->storeModel = new StoreModel();
    }

    public function getIndex()
    {
        $data = [
            'title'      => 'Data Store',
            'content'    => 'admin/store/index',
            'extra'      => 'admin/store/js/js_index',
            'mn_setting' => 'active',
            'colmas'     => 'collapse',
            'colset'     => 'collapse in',
            'collap'     => 'collapse',
            'side2'      => 'active',
        ];
        return view('layout/wrapper', $data);
    }

    public function postListdata()
    {
        $result = $this->storeModel->listStore();
        return $this->response->setJSON($result);
    }

    public function getTambah()
    {
        if (!$this->session->get('logged_status')) {
            return redirect()->to(base_url());
        }

        $data = [
            'title'      => 'Tambah Data Store',
            'content'    => 'admin/store/tambah',
            'mn_master'  => 'active',
            'colmas'     => 'collapse',
            'colset'     => 'collapse in',
            'collap'     => 'collapse',
            'side2'      => 'active',
        ];
        return view('layout/wrapper', $data);
    }

    public function postAddData()
    {
        $rules = [
            'store' => [
                'label' => 'Nama Store',
                'rules' => 'required|trim|max_length[30]|alpha_numeric_space',
                'errors' => [
                    'required' => '{field} wajib diisi.',
                    'alpha_numeric_space' => '{field} hanya boleh berisi huruf, angka, dan spasi.',
                    'max_length' => '{field} maksimal 30 karakter.'
                ]
            ],
            'alamat' => [
                'label' => 'Alamat',
                'rules' => 'required|trim|max_length[50]|alpha_numeric_punct',
                'errors' => [
                    'required' => '{field} wajib diisi.',
                    'alpha_numeric_punct' => '{field} hanya boleh berisi huruf, angka, spasi, dan tanda baca tertentu.',
                    'max_length' => '{field} maksimal 50 karakter.'
                ]
            ],
            'kontak' => [
                'label' => 'Nomor Telepon',
                'rules' => 'required|regex_match[/^((\+62|62|0)8[1-9][0-9]{6,9}|0[2-9][0-9]{1,3}[0-9]{5,8})$/]',
                'errors' => [
                    'required' => '{field} wajib diisi.',
                    'regex_match' => '{field} tidak valid. Gunakan format +62..., 08..., atau 0361....'
                ]
            ],
            'keterangan' => [
                'label' => 'Keterangan',
                'rules' => 'permit_empty|trim|max_length[100]|alpha_numeric_punct',
                'errors' => [
                    'required' => '{field} wajib diisi.',
                    'alpha_numeric_punct' => '{field} hanya boleh berisi huruf, angka, spasi, dan tanda baca tertentu.',
                    'max_length' => '{field} maksimal 100 karakter.'
                ]
            ]
        ];

        if (!$this->validate($rules)) {
            $this->session->setFlashdata('message', implode('<br>', $this->validator->getErrors()));
            return redirect()->to(base_url("admin/store/tambah"));
        }

        $data = [
            "store"      => esc($this->request->getPost('store')),
            "alamat"     => esc($this->request->getPost('alamat')),
            "kontak"     => esc($this->request->getPost('kontak')),
            "keterangan" => esc($this->request->getPost('keterangan')),
            "userid"     => $this->session->get('logged_status')['username']
        ];

        $result = $this->storeModel->insertData($data);

        if ($result["code"] == 0) {
            $this->session->setFlashdata('message', 'Data berhasil disimpan.');
            return redirect()->to(base_url("admin/store"));
        } else {
            $this->session->setFlashdata('message', 'Data gagal disimpan.');
            return redirect()->to(base_url("admin/store/tambah"));
        }
    }

    public function getUbah($storeid)
    {
        $storeid = base64_decode(esc($storeid));
        $result  = $this->storeModel->getStore($storeid);

        $data = [
            'title'      => 'Ubah Data Store',
            'content'    => 'admin/store/ubah',
            'detail'     => $result,
            'mn_master'  => 'active',
            'colmas'     => 'collapse',
            'colset'     => 'collapse in',
            'collap'     => 'collapse',
            'side2'      => 'active',
        ];
        return view('layout/wrapper', $data);
    }

    
    public function postUpdateData()
    {
        $rules = [
            'store' => [
                'label' => 'Nama Cabang',
                'rules' => 'required|trim|max_length[100]|alpha_numeric_space',
                'errors' => [
                    'required' => '{field} wajib diisi.',
                    'alpha_numeric_space' => '{field} hanya boleh berisi huruf, angka, dan spasi.',
                ]
            ],
            'alamat' => [
                'label' => 'Alamat',
                'rules' => 'required|trim|max_length[255]|alpha_numeric_space',
                'errors' => [
                    'required' => '{field} wajib diisi.',
                    'alpha_numeric_space' => '{field} hanya boleh berisi huruf, angka, dan spasi.',
                ]
            ],
            'kontak' => [
                'label' => 'Nomor Telepon',
                'rules' => 'required|regex_match[/^((\+62|62|0)8[1-9][0-9]{6,9}|0[2-9][0-9]{1,3}[0-9]{5,8})$/]',
                'errors' => [
                    'required' => '{field} wajib diisi.',
                    'regex_match' => '{field} tidak valid. Masukkan nomor HP atau telepon rumah yang benar.',
                ]
            ],
            'keterangan' => [
                'label' => 'Keterangan',
                'rules' => 'permit_empty|max_length[100]|alpha_numeric_space',
                'errors' => [
                    'required' => '{field} wajib diisi.',
                    'alpha_numeric_space' => '{field} hanya boleh berisi huruf, angka, dan spasi.',
                ]
            ],
        ];

        $storeid = esc($this->request->getPost('storeid'));

        if (!$this->validate($rules)) {
            log_message('error', 'Validasi gagal pada postUpdateData Store ID ' . $storeid . ': ' . json_encode($this->validator->getErrors()));

            $this->session->setFlashdata('message', implode('<br>', $this->validator->getErrors()));
            return redirect()->to(base_url("admin/store/ubah/" . base64_encode($storeid)));
        }

        $data = [
            "store"      => esc($this->request->getPost('store')),
            "alamat"     => esc($this->request->getPost('alamat')),
            "kontak"     => esc($this->request->getPost('kontak')),
            "keterangan" => esc($this->request->getPost('keterangan')),
            "userid"     => $this->session->get('logged_status')['username']
        ];

        $result = $this->storeModel->updateData($data, $storeid);

        if ($result["code"] == 0) {
            log_message('info', 'Store ID ' . $storeid . ' berhasil diubah.');
            $this->session->setFlashdata('message', 'Data berhasil diubah.');
            return redirect()->to(base_url("admin/store"));
        } else {
            log_message('error', 'Gagal update Store ID ' . $storeid . ': ' . $result['message']);
            $this->session->setFlashdata('message', 'Data gagal diubah.');
            return redirect()->to(base_url("admin/store/ubah/" . base64_encode($storeid)));
        }
    }

    public function getHapus($storeid)
    {
        $storeid = base64_decode(esc($storeid));

        $data = [
            "status" => 1,
            "userid" => $this->session->get('logged_status')['username']
        ];

        $result = $this->storeModel->hapusData($data, $storeid);

        if ($result["code"] == 0) {
            $this->session->setFlashdata('message', 'Data berhasil dihapus.');
        } else {
            $this->session->setFlashdata('message', 'Data gagal dihapus.');
        }
        return redirect()->to(base_url("admin/store"));
    }
}
