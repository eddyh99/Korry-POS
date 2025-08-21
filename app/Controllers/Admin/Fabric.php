<?php

namespace App\Controllers\Admin;

use App\Models\Admin\FabricModel;

use App\Controllers\BaseApiController;

class Fabric extends BaseApiController
{
    protected $fabricModel;

    public function __construct()
    {
        $this->fabricModel = new FabricModel();
    }

    public function getIndex()
    {
        if (!$this->session->get('logged_status')) {
            return redirect()->to(base_url());
        }

        $data = [
            'title'      => 'Data Fabric',
            'content'    => 'admin/fabric/index',
            'extra'      => 'admin/fabric/js/js_index', 
            'mn_setting' => 'active',
            'colmas'     => 'collapse',
            'colset'     => 'collapse in',
            'collap'     => 'collapse',
            'side11'     => 'active',
        ];
        return view('layout/wrapper', $data);
    }

    public function postListdata()
    {
        $result = $this->fabricModel->listFabric();
        return $this->response->setJSON($result);
    }

    public function getTambah()
    {
        if (!$this->session->get('logged_status')) {
            return redirect()->to(base_url());
        }

        $data = [
            'title'      => 'Tambah Fabric',
            'content'    => 'admin/fabric/tambah',
            'mn_master'  => 'active',
            'colmas'     => 'collapse',
            'colset'     => 'collapse in',
            'collap'     => 'collapse',
            'side11'      => 'active',
        ];
        return view('layout/wrapper', $data);
    }

    public function getUbah($fabricid)
    {
        $fabricid = base64_decode(esc($fabricid));
        $result  = $this->fabricModel->getFabric($fabricid);

        $data = [
            'title'      => 'Ubah Fabric',
            'content'    => 'admin/fabric/ubah',
            'detail'     => $result,
            'mn_master'  => 'active',
            'colmas'     => 'collapse',
            'colset'     => 'collapse in',
            'collap'     => 'collapse',
            'side2'      => 'active',
        ];
        return view('layout/wrapper', $data);
    }

    public function getHapus($fabricid)
    {
        $fabricid = base64_decode(esc($fabricid));

        $data = [
            "status" => 1
        ];

        $result = $this->fabricModel->hapusData($data, $fabricid);

        if ($result["code"] == 0) {
            $this->session->setFlashdata('message', 'Data berhasil dihapus.');
        } else {
            $this->session->setFlashdata('message', 'Data gagal dihapus.');
        }
        return redirect()->to(base_url("admin/fabric"));
    }

    // Handle Post Tambah & Ubah

    public function postAddData()
    {
        $rules = [
            'fabric' => [
                'label' => 'Nama Fabric',
                'rules' => 'required|trim|max_length[30]|alpha_numeric_space|is_unique[fabric.nama]',
                'errors' => [
                    'required' => '{field} wajib diisi.',
                    'max_length' => '{field} maksimal 30 karakter.',
                    'alpha_numeric_space' => '{field} hanya boleh berisi huruf, angka, dan spasi.',
                    'is_unique'     => '{field} sudah digunakan.'
                ]
            ]
        ];

        if (!$this->validate($rules)) {
            $this->session->setFlashdata('message', implode('<br>', $this->validator->getErrors()));
            return redirect()->to(base_url("admin/fabric/tambah"));
        }

        $data = [
            "nama"       => esc($this->request->getPost('fabric'))
        ];

        $result = $this->fabricModel->insertData($data);

        if ($result["code"] == 0) {
            $this->session->setFlashdata('message', 'Data berhasil disimpan.');
            return redirect()->to(base_url("admin/fabric"));
        } else {
            $this->session->setFlashdata('message', 'Data gagal disimpan.');
            return redirect()->to(base_url("admin/fabric/tambah"));
        }
    }

    public function postUpdateData()
    {
        $rules = [
            'fabric' => [
                'label' => 'Nama Fabric',
                'rules' => 'required|trim|max_length[30]|alpha_numeric_space|is_unique[fabric.nama]',
                'errors' => [
                    'required' => '{field} wajib diisi.',
                    'max_length' => '{field} maksimal 30 karakter.',
                    'alpha_numeric_space' => '{field} hanya boleh berisi huruf, angka, dan spasi.',
                    'is_unique'     => '{field} sudah digunakan.'
                ]
            ]
        ];

        $fabricid = esc($this->request->getPost('fabricid'));

        if (!$this->validate($rules)) {
            $this->session->setFlashdata('message', implode('<br>', $this->validator->getErrors()));
            return redirect()->to(base_url("admin/fabric/ubah/" . base64_encode($fabricid)));
        }

        $data = [
            "nama"      => esc($this->request->getPost('fabric'))
        ];

        $result = $this->fabricModel->updateData($data, $fabricid);

        if ($result["code"] == 0) {
            $this->session->setFlashdata('message', 'Data berhasil diubah.');
            return redirect()->to(base_url("admin/fabric"));
        } else {
            $this->session->setFlashdata('message', 'Data gagal diubah.');
            return redirect()->to(base_url("admin/fabric/ubah/" . base64_encode($fabricid)));
        }
    }
}
