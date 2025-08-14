<?php

namespace App\Models\Admin;

use CodeIgniter\Model;

class PinjamModel extends Model
{
    protected $pinjam    = 'pinjam';
    protected $detpinjam = 'pinjam_detail';
    protected $harga     = 'harga';
    protected $produk    = 'produk';

    protected $opnameModel;

    public function setOpnameModel($model)
    {
        // inject OpnameModel dari controller Pinjam.php
        $this->opnameModel = $model;
    }

    public function Listnota($storeid)
    {
        $sql = "SELECT a.* 
                FROM {$this->pinjam} a 
                INNER JOIN {$this->detpinjam} b ON a.id=b.id 
                WHERE ISNULL(b.kembali) AND status='kembali' AND a.storeid=?";
        $query = $this->db->query($sql, [$storeid]);
        return $query ? $query->getResultArray() : $this->db->error();
    }

    public function getDetail($key)
    {
        $sql = "SELECT b.namaproduk, b.namabrand, a.* 
                FROM {$this->detpinjam} a 
                INNER JOIN {$this->produk} b ON a.barcode=b.barcode 
                WHERE a.id=? AND ISNULL(a.kembali) AND a.status='kembali'";
        return $this->db->query($sql, [$key])->getResultArray();
    }

    public function insertData($data, $barang)
    {
        $this->db->transStart();

        // insert ke tabel pinjam
        $builder = $this->db->table($this->pinjam);
        $builder->insert($data);
        $id = $this->db->insertID();

        foreach ($barang as $dt) {
            $temp = [
                'id'      => $id,
                'barcode' => $dt[0],
                'size'    => strtoupper($dt[2]),
            ];

            $stok = $this->opnameModel->getStok($dt[0], $data['storeid'], strtoupper($dt[2]));
            $temp['jumlah'] = ($stok - $dt[3] < 0) ? $stok : $dt[3];

            $this->db->table($this->detpinjam)->insert($temp);
        }

        $this->db->transComplete();

        if ($this->db->transStatus() === FALSE) {
            $this->db->transRollback();
            return FALSE;
        } else {
            $this->db->transCommit();
            return ['code' => 0, 'message' => ''];
        }
    }

    public function setTutup($id)
    {
        $sql = "UPDATE {$this->detpinjam} SET status='tidak' WHERE id=?";
        return $this->db->query($sql, [$id]);
    }

    public function setKembali($id, $barang)
    {
        $this->db->transStart();
        $now = ['kembali' => date('Y-m-d H:i:s')];

        $builder = $this->db->table($this->detpinjam);
        foreach ($barang as $dt) {
            $builder->where(['id' => $id, 'barcode' => $dt[0]]);
            $builder->update($now);
        }

        $this->db->transComplete();
    }
}
