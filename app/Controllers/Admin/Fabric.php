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
        $data = [
            'title'      => 'Data Fabric',
            'content'    => 'admin/fabric/index',
            'extra'      => 'admin/fabric/js/js_index',
            'mn_setting' => 'active',
            'colset'     => 'collapse in',
            'colmas'     => 'collapse',
            'colkonsi'   => 'collapse',
            'colwho'     => 'collapse',
            'collap'     => 'collapse',
            'side7'      => 'active',
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
        $data = [
            'title'      => 'Tambah Data Fabric',
            'content'    => 'admin/fabric/tambah',
            'mn_setting' => 'active',
            'colset'     => 'collapse in',
            'colmas'     => 'collapse',
            'colkonsi'   => 'collapse',
            'colwho'     => 'collapse',
            'collap'     => 'collapse',
            'side7'      => 'active',
        ];
        return view('layout/wrapper', $data);
    }

    public function getUbah($fabric)
    {
        $fabric = base64_decode($fabric);
        $result   = $this->fabricModel->getFabric($fabric);

        $data = [
            'title'      => 'Ubah Data Fabric',
            'content'    => 'admin/fabric/ubah',
            'detail'     => $result,
            'mn_setting' => 'active',
            'colset'     => 'collapse in',
            'colmas'     => 'collapse',
            'colkonsi'   => 'collapse',
            'colwho'     => 'collapse',
            'collap'     => 'collapse',
            'side7'      => 'active',
        ];
        return view('layout/wrapper', $data);
    }

    public function getHapus($fabric)
    {
        $fabric = base64_decode($fabric);

        $data = ['status' => 1];
        $result = $this->fabricModel->hapusData($data, $fabric);

        if ($result['code'] == 0) {
            $this->session->setFlashdata('message', 'Data berhasil dihapus.');
        } else {
            $this->session->setFlashdata('message', 'Data gagal dihapus.');
        }

        return redirect()->to('/admin/fabric');
    }

    // Handle Post Tambah & Ubah    

    public function postAddData()
    {
        $rules = [
            'fabric' => [
                'label' => 'Fabric',
                'rules' => 'required|alpha_numeric_space|max_length[50]|is_unique[fabric.namafabric]',
                'errors' => [
                    'required' => '{field} wajib diisi.',
                    'alpha_numeric_space' => '{field} hanya boleh huruf, angka, dan spasi.',
                    'max_length' => '{field} maksimal 50 karakter.',
                    'is_unique' => '{field} sudah digunakan.'
                ]
            ]
        ];

        if (! $this->validate($rules)) {
            $this->session->setFlashdata('message', implode('<br>', $this->validator->getErrors()));
            return redirect()->to('/admin/fabric/tambah');
        }

        $fabric = ucfirst(trim($this->request->getPost('fabric')));
        $userid   = $this->session->get('logged_status')['username'] ?? '';

        $data = [
            'namafabric' => $fabric,
            'userid'       => $userid
        ];

        $result = $this->fabricModel->insertData($data);

        $msg = ($result['code'] == 0) ? 'Data berhasil disimpan.' : 'Data gagal disimpan.';
        $this->session->setFlashdata('message', $msg);

        return redirect()->to('/admin/fabric');
    }

    public function postUpdateData()
    {
        $rules = [
            'fabric' => [
                'label' => 'Fabric',
                'rules' => 'required|alpha_numeric_space|max_length[50]|is_unique[fabric.namafabric]',
                'errors' => [
                    'required' => '{field} wajib diisi.',
                    'alpha_numeric_space' => '{field} hanya boleh huruf, angka, dan spasi.',
                    'max_length' => '{field} maksimal 50 karakter.',
                    'is_unique' => '{field} sudah digunakan.'
                ]
            ]
        ];

        $oldFabric = $this->request->getPost('oldfabric');

        if (! $this->validate($rules)) {
            $this->session->setFlashdata('message', implode('<br>', $this->validator->getErrors()));
            return redirect()->to('/admin/fabric/ubah/' . base64_encode($oldFabric));
        }

        $fabric = ucfirst(trim($this->request->getPost('fabric')));
        $userid   = $this->session->get('logged_status')['username'] ?? '';

        $data = [
            'namafabric' => $fabric,
            'userid'       => $userid
        ];

        $result = $this->fabricModel->updateData($data, $oldFabric);

        $msg = ($result['code'] == 0) ? 'Data berhasil diubah.' : 'Data gagal diubah.';
        $this->session->setFlashdata('message', $msg);

        return redirect()->to('/admin/fabric');
    }
}
