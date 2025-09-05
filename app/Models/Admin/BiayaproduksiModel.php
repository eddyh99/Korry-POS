<?php

namespace App\Models\Admin;

use CodeIgniter\Model;

class BiayaproduksiModel extends Model
{
    protected $table      = 'biaya_produksi';
    protected $primaryKey = 'namabiayaproduksi';
    protected $allowedFields = ['namabiayaproduksi', 'userid', 'status'];

    public function listBiayaproduksi()
    {
        $sql = "SELECT namabiayaproduksi FROM {$this->table} WHERE status = '0'";
        $query = $this->db->query($sql);
        return $query->getResultArray();
    }

    public function getBiayaproduksi($biayaproduksi)
    {
        $sql = "SELECT namabiayaproduksi FROM {$this->table} WHERE status='0' AND namabiayaproduksi=?";
        $query = $this->db->query($sql, [$biayaproduksi]);
        return $query->getResult();
    }

    public function insertData($data)
    {
        $sql = $this->db->table($this->table)->set($data)->getCompiledInsert() . " ON DUPLICATE KEY UPDATE status='0', namabiayaproduksi=?";
        if ($this->db->query($sql, [$data['namabiayaproduksi']])) {
            return ['code' => 0, 'message' => ''];
        } else {
            return $this->db->error();
        }
    }

    public function updateData($data, $oldBiayaproduksi)
    {
        if ($this->db->table($this->table)->where('namabiayaproduksi', $oldBiayaproduksi)->update($data)) {
            return ['code' => 0, 'message' => ''];
        } else {
            return $this->db->error();
        }
    }

    public function hapusData($data, $biayaproduksi)
    {
        if ($this->db->table($this->table)->where('namabiayaproduksi', $biayaproduksi)->update($data)) {
            return ['code' => 0, 'message' => ''];
        } else {
            return $this->db->error();
        }
    }
}
