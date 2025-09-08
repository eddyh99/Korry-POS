<?php

namespace App\Controllers\Admin;

use App\Models\Admin\PenggunaModel;

use App\Controllers\BaseApiController;

class Pengguna extends BaseApiController
{
    protected $penggunaModel;

     public function __construct()
    {
         $this->penggunaModel = new PenggunaModel();
    }

    public function getIndex()
    {
        return view('layout/wrapper', [
            'title'      => 'Data Pengguna',
            'content'    => 'admin/pengguna/index',
            'extra'      => 'admin/pengguna/js/js_index',
            'mn_setting' => 'active',
            'colset'     => 'collapse in',
            'colmas'     => 'collapse',
            'colkonsi'   => 'collapse',
            'colwho'     => 'collapse',
            'collap'     => 'collapse',
            'side1'      => 'active',
        ]);
    }

    public function postListdata()
    {
        return $this->response->setJSON($this->penggunaModel->listpengguna());
    }

    public function getTambah()
    {
        return view('layout/wrapper', [
            'title'      => 'Tambah Data Pengguna',
            'content'    => 'admin/pengguna/tambah',
            // 'mn_master'  => 'active',
            'mn_setting'  => 'active',
            'colset'     => 'collapse in',
            'colmas'     => 'collapse',
            'colkonsi'   => 'collapse',
            'colwho'     => 'collapse',
            'collap'     => 'collapse',
            'side1'      => 'active',
        ]);
    }

    // public function postAddData()
    // {
    //     $rules = [
    //         'username' => 'required',
    //         'password' => 'required',
    //         'nama'     => 'required'
    //     ];

    //     if (!$this->validate($rules)) {
    //         $this->session->setFlashdata('message', implode('<br>', $this->validator->getErrors()));
    //         return redirect()->to(base_url('admin/pengguna/tambah'));
    //     }

    //     $data = [
    //         'username' => esc($this->request->getPost('username')),
    //         'passwd'   => sha1(esc($this->request->getPost('password'))),
    //         'nama'     => esc($this->request->getPost('nama')),
    //         'role'     => esc($this->request->getPost('role')),
    //     ];

    //     $result = $this->penggunaModel->insertData($data);

    //     if ($result['code'] == 0) {
    //         $this->session->setFlashdata('message', 'Data berhasil disimpan.');
    //         return redirect()->to(base_url('admin/pengguna'));
    //     }

    //     $this->session->setFlashdata('message', 'Gagal: ' . $result['message']);
    //     return redirect()->to(base_url('admin/pengguna/tambah'));
    // }
    public function postAddData()
    {
        $rules = [
            'username' => [
                'label' => 'Username',
                'rules' => 'required|alpha_numeric|min_length[8]|max_length[20]|is_unique[pengguna.username]',
                'errors' => [
                    'required'      => '{field} wajib diisi.',
                    'alpha_numeric' => '{field} hanya boleh huruf dan angka.',
                    'min_length'    => '{field} minimal 8 karakter.',
                    'max_length'    => '{field} maksimal 20 karakter.',
                    'is_unique'     => '{field} sudah digunakan.'
                ],
            ],
            'password' => [
                'label' => 'Password',
                'rules' => 'required|min_length[8]|regex_match[^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[^a-zA-Z0-9]).+$]',
                'errors' => [
                    'required'    => '{field} wajib diisi.',
                    'min_length'  => '{field} minimal 8 karakter.',
                    'regex_match' => '{field} harus mengandung huruf besar, huruf kecil, angka, dan karakter khusus.'
                ],
            ],
            'nama' => [
                'label' => 'Nama Lengkap',
                'rules' => 'required|max_length[50]',
                'errors' => [
                    'required'   => '{field} wajib diisi.',
                    'max_length' => '{field} maksimal 50 karakter.',
                ],
            ],
        ];

        if (!$this->validate($rules)) {
            $this->session->setFlashdata('message', implode('<br>', $this->validator->getErrors()));
            return redirect()->to(base_url('admin/pengguna/tambah'));
        }

        $username = esc($this->request->getPost('username'));
        $password = $this->request->getPost('password'); // jangan gunakan esc() agar Hash konsisten
        $nama     = esc($this->request->getPost('nama'));
        $role     = esc($this->request->getPost('role'));

        // Hash password → HARUS pakai sha1 supaya sinkron sama verifyLogin()
        $hashedPass = sha1($password);

        $data = [
            'username' => $username,
            'passwd'   => $hashedPass,
            'nama'     => $nama,
            'role'     => $role,
        ];

        $result = $this->penggunaModel->insertData($data);

        if ($result['code'] == 0) {
            $this->session->setFlashdata('message', 'Data berhasil disimpan.');
            return redirect()->to(base_url('admin/pengguna'));
        }

        $this->session->setFlashdata('message', 'Gagal: ' . $result['message']);
        return redirect()->to(base_url('admin/pengguna/tambah'));
    }

    public function getUbah($username)
    {
        $username = base64_decode($username);
        $result   = $this->penggunaModel->getUser($username);

        return view('layout/wrapper', [
            'title'      => 'Ubah Data Pengguna',
            'content'    => 'admin/pengguna/ubah',
            'detail'     => $result,
            'mn_setting' => 'active',
            'colset'     => 'collapse in',
            'colmas'     => 'collapse',
            'colkonsi'   => 'collapse',
            'colwho'     => 'collapse',
            'collap'     => 'collapse',
            'side1'      => 'active',
        ]);
    }

    // public function postUpdateData()
    // {
    //     $rules = [
    //         'username' => 'required',
    //         'nama'     => 'required'
    //     ];

    //     $username = esc($this->request->getPost('username'));

    //     if (!$this->validate($rules)) {
    //         $this->session->setFlashdata('message', implode('<br>', $this->validator->getErrors()));
    //         return redirect()->to(base_url('admin/pengguna/ubah/' . base64_encode($username)));
    //     }

    //     $password = esc($this->request->getPost('password'));
    //     $nama     = esc($this->request->getPost('nama'));
    //     $role     = esc($this->request->getPost('role'));

    //     $data = [
    //         'username' => $username,
    //         'nama'     => $nama,
    //         'role'     => $role
    //     ];

    //     if (!empty($password)) {
    //         $data['passwd'] = sha1($password);
    //     }

    //     $result = $this->penggunaModel->updateData($data, $username);

    //     if ($result['code'] == 0) {
    //         $this->session->setFlashdata('message', 'Data Berhasil Disimpan');
    //         if ($this->session->get('logged_status')['role'] == 'Staff') {
    //             return redirect()->to(base_url('staff/dashboard'));
    //         }
    //         return redirect()->to(base_url('admin/pengguna'));
    //     }

    //     $this->session->setFlashdata('message', 'Gagal: ' . $result['message']);
    //     return redirect()->to(base_url('admin/pengguna/ubah/' . base64_encode($username)));
    // }
    public function postUpdateData()
    {
        $rules = [
            'username' => [
                'label' => 'Username',
                'rules' => 'required|alpha_numeric|min_length[8]|max_length[20]',
                'errors' => [
                    'required' => '{field} wajib diisi.',
                    'alpha_numeric' => '{field} hanya boleh huruf dan angka.',
                    'min_length' => '{field} minimal 8 karakter.',
                    'max_length' => '{field} maksimal 20 karakter.'
                ]
            ],
            'nama' => [
                'label' => 'Nama Lengkap',
                'rules' => 'required|max_length[50]',
                'errors' => [
                    'required' => '{field} wajib diisi.',
                    'max_length' => '{field} maksimal 50 karakter.'
                ]
            ],
            'password' => [
                'label' => 'Password',
                'rules' => 'permit_empty|min_length[8]|regex_match[^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[^a-zA-Z0-9]).+$]',
                'errors' => [
                    'min_length' => '{field} minimal 8 karakter jika ingin diganti.',
                    'regex_match' => '{field} harus mengandung huruf besar, huruf kecil, angka, dan karakter khusus.'
                ]
            ],
        ];

        $username = esc($this->request->getPost('username'));

        if (!$this->validate($rules)) {
            $this->session->setFlashdata('message', implode('<br>', $this->validator->getErrors()));
            return redirect()->to(base_url('admin/pengguna/ubah/' . base64_encode($username)));
        }

        $password = esc($this->request->getPost('password'));
        $nama     = esc($this->request->getPost('nama'));
        $role     = esc($this->request->getPost('role'));

        $data = [
            'username' => $username,
            'nama'     => $nama,
            'role'     => $role
        ];

        if (!empty($password)) {
            $data['passwd'] = sha1($password);
        }

        $result = $this->penggunaModel->updateData($data, $username);

        if ($result['code'] == 0) {
            $this->session->setFlashdata('message', 'Data Berhasil Diubah');

            if ($this->session->get('logged_status')['role'] == 'Staff') {
                return redirect()->to(base_url('staff/dashboard'));
            }
            return redirect()->to(base_url('admin/pengguna'));
        }
        $this->session->setFlashdata('message', 'Gagal: ' . $result['message']);
        return redirect()->to(base_url('admin/pengguna/ubah/' . base64_encode($username)));
    }

    public function getHapus($username)
    {
        $username = base64_decode($username);
        $data     = ['status' => 1];
        $result   = $this->penggunaModel->hapusData($data, $username);

        $msg = $result['code'] == 0 ? 'Data berhasil dihapus' : 'Gagal: ' . $result['message'];
        $this->session->setFlashdata('message', $msg);
        return redirect()->to(base_url('admin/pengguna'));
    }
}
