<?php

namespace App\Controllers;

use App\Models\MemberModel;

use App\Controllers\BaseApiController;

class Member extends BaseApiController
{
    protected $memberModel;

    public function __construct()
    {
        $this->memberModel = new MemberModel();
    }

    public function getIndex()
    {
        $data = [
            'title'      => 'Data Member',
            'content'    => 'member/index',
            'extra'      => 'member/js/js_index',
            'mn_member'  => 'active',
            'colmas'     => 'collapse',
            'colset'     => 'collapse',
            'collap'     => 'collapse',
        ];

        return view('layout/wrapper', $data);
    }

    public function postListdata()
    {
        $columns = [
            0 => 'member_id',
            1 => 'nama',
            2 => 'nope',
            3 => 'email',
        ];

        $start  = $this->request->getPost('start');
        $limit  = $this->request->getPost('length');
        $order  = $columns[$this->request->getPost('order')[0]['column']];
        $dir    = $this->request->getPost('order')[0]['dir'];

        $totalData     = $this->memberModel->allposts_count();
        $totalFiltered = $totalData;

        if (empty($this->request->getPost('search')['value'])) {
            $result = $this->memberModel->allposts($limit, $start, $order, $dir);
        } else {
            $search = $this->request->getPost('search')['value'];
            $result = $this->memberModel->posts_search($limit, $start, $search, $order, $dir);
            $totalFiltered = $this->memberModel->posts_search_count($search);
        }

        return $this->response->setJSON([
            'recordsTotal'    => $totalData,
            'recordsFiltered' => $totalFiltered,
            'member'          => $result,
        ]);
    }

    // Insert, Update, Delete

    public function getTambah()
    {
        $data = [
            'title'      => 'Tambah Data Member',
            'content'    => 'member/tambah',
            'mn_member'  => 'active',
            'colmas'     => 'collapse',
            'colset'     => 'collapse',
            'collap'     => 'collapse',
        ];

        return view('layout/wrapper', $data);
    }

    public function getUbah($memberid)
    {
        $memberid = base64_decode($memberid);
        $result   = $this->memberModel->getMember($memberid);

        $data = [
            'title'      => 'Ubah Data Member',
            'content'    => 'member/ubah',
            'detail'     => $result['message'],
            'mn_member'  => 'active',
            'colmas'     => 'collapse',
            'colset'     => 'collapse',
            'collap'     => 'collapse',
        ];

        return view('layout/wrapper', $data);
    }

    public function getHapus($memberid)
    {
        $data = [
            'status' => 1,
        ];

        $memberid = base64_decode($memberid);
        $result   = $this->memberModel->hapusData($data, $memberid);

        if ($result['code'] == 0) {
            $this->session->setFlashdata('message', 'Data berhasil dihapus.');
        } else {
            $this->session->setFlashdata('message', 'Data gagal dihapus.');
        }

        return redirect()->to('/member');
    }

    // Handle simpan Tambah/Update
    public function postAddData()
    {
        $rules = [
            'nama' => [
                'label' => 'Nama Member',
                'rules' => 'required|max_length[50]',
                'errors' => [
                    'required'   => '{field} wajib diisi.',
                    'max_length' => '{field} maksimal 50 karakter.',
                ],
            ],
            'alamat' => [
                'label' => 'Alamat Member',
                'rules' => 'required|max_length[255]|alpha_numeric_space',
                'errors' => [
                    'required'            => '{field} wajib diisi.',
                    'alpha_numeric_space' => '{field} hanya boleh berisi huruf, angka, dan spasi.',
                ],
            ],
            'tempatlahir' => [
                'label'  => 'Tempat Lahir',
                'rules'  => 'permit_empty|max_length[50]|alpha_numeric_space',
            ],
            'tgllahir' => [
                'label'  => 'Tanggal Lahir',
                'rules'  => 'permit_empty|valid_date',
            ],
            'nope' => [
                'label' => 'Nomor HP',
                'rules' => 'required|regex_match[/^((\+62|62|0)8[1-9][0-9]{6,11})$/]',
                'errors' => [
                    'required'    => '{field} wajib diisi.',
                    'regex_match' => '{field} tidak valid. Masukkan nomor HP yang benar. (Format: +62 atau 08...)',
                ],
            ],
            'email' => [
                'label' => 'Email',
                'rules' => 'permit_empty|valid_email|max_length[100]',
                'errors' => [
                    'valid_email' => '{field} tidak valid.',
                    'max_length'  => '{field} maksimal 100 karakter.',
                ],
            ],
            'socmed' => [
                'label' => 'Sosial Media',
                'rules' => 'permit_empty|max_length[50]',
            ],
            'keterangan' => [
                'label' => 'Keterangan',
                'rules' => 'permit_empty|max_length[100]|alpha_numeric_punct',
                'errors' => [
                    'alpha_numeric_punct' => '{field} hanya boleh berisi huruf, angka, spasi, dan tanda baca tertentu.',
                    'max_length'          => '{field} maksimal 100 karakter.'
                ],
            ],
        ];

        if (! $this->validate($rules)) {
            $this->session->setFlashdata('message', implode('<br>', $this->validator->getErrors()));
            return redirect()->to('/member/tambah')->withInput();
        }

        // Ambil data POST
        $data = [
            'member_id'    => date("Ymd") . $this->memberModel->getLastmember(),
            'nama'         => $this->request->getPost('nama'),
            'alamat'       => $this->request->getPost('alamat'),
            'tempat_lahir' => $this->request->getPost('tempatlahir') ?: '-',
            'tgl_lahir'    => $this->request->getPost('tgllahir') ?: '0000-00-00',
            'nope'         => $this->request->getPost('nope'),
            'jnskel'       => $this->request->getPost('jnskel') ?: '-',
            'email'        => $this->request->getPost('email') ?: '-',
            'socmed'       => $this->request->getPost('socmed') ?: '-',
            'keterangan'   => $this->request->getPost('keterangan') ?: '-',
        ];

        $result = $this->memberModel->insertData($data);

        if ($result['code'] == 0) {
            $this->session->setFlashdata('message', 'Data berhasil disimpan.');
            return redirect()->to('/member');
        } else {
            $this->session->setFlashdata('message', 'Data gagal disimpan.');
            return redirect()->to('/member/tambah')->withInput();
        }
    }

    public function postUpdateData()
    {
        $rules = [
            'nama' => [
                'label' => 'Nama Member',
                'rules' => 'required|max_length[50]',
                'errors' => [
                    'required'   => '{field} wajib diisi.',
                    'max_length' => '{field} maksimal 50 karakter.',
                ],
            ],
            'alamat' => [
                'label' => 'Alamat Member',
                'rules' => 'required|max_length[255]|alpha_numeric_space',
                'errors' => [
                    'required'            => '{field} wajib diisi.',
                    'alpha_numeric_space' => '{field} hanya boleh berisi huruf, angka, dan spasi.',
                ],
            ],
            'tempatlahir' => [
                'label'  => 'Tempat Lahir',
                'rules'  => 'permit_empty|max_length[50]|alpha_numeric_space',
            ],
            'tgllahir' => [
                'label'  => 'Tanggal Lahir',
                'rules'  => 'permit_empty|valid_date',
            ],
            'nope' => [
                'label' => 'Nomor HP',
                'rules' => 'required|regex_match[/^((\+62|62|0)8[1-9][0-9]{6,11})$/]',
                'errors' => [
                    'required'    => '{field} wajib diisi.',
                    'regex_match' => '{field} tidak valid. Masukkan nomor HP yang benar. (Format: +62 atau 08...)',
                ],
            ],
            'email' => [
                'label' => 'Email',
                'rules' => 'permit_empty|valid_email|max_length[100]',
                'errors' => [
                    'valid_email' => '{field} tidak valid.',
                    'max_length'  => '{field} maksimal 100 karakter.',
                ],
            ],
            'socmed' => [
                'label' => 'Sosial Media',
                'rules' => 'permit_empty|max_length[50]',
            ],
            'keterangan' => [
                'label' => 'Keterangan',
                'rules' => 'permit_empty|max_length[100]|alpha_numeric_punct',
                'errors' => [
                    'alpha_numeric_punct' => '{field} hanya boleh berisi huruf, angka, spasi, dan tanda baca tertentu.',
                    'max_length'          => '{field} maksimal 100 karakter.'
                ],
            ],
        ];

        if (! $this->validate($rules)) {
            $this->session->setFlashdata('message', implode('<br>', $this->validator->getErrors()));
            return redirect()->to('/member/ubah/' . base64_encode($this->request->getPost('memberid')))->withInput();
        }

        $memberid = $this->request->getPost('memberid');

        $data = [
            'nama'         => $this->request->getPost('nama'),
            'alamat'       => $this->request->getPost('alamat'),
            'tempat_lahir' => $this->request->getPost('tempatlahir') ?: '-',
            'tgl_lahir'    => $this->request->getPost('tgllahir') ?: '0000-00-00',
            'nope'         => $this->request->getPost('nope'),
            'jnskel'       => $this->request->getPost('jnskel') ?: '-',
            'email'        => $this->request->getPost('email') ?: '-',
            'socmed'       => $this->request->getPost('socmed') ?: '-',
            'keterangan'   => $this->request->getPost('keterangan') ?: '-',
        ];

        $result = $this->memberModel->updateData($data, $memberid);

        if ($result['code'] == 0) {
            $this->session->setFlashdata('message', 'Data berhasil diubah.');
            return redirect()->to('/member');
        } else {
            $this->session->setFlashdata('message', 'Data gagal diubah.');
            return redirect()->to('/member/ubah/' . base64_encode($memberid))->withInput();
        }
    }
}
