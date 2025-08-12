<?php

namespace App\Models\Admin;

use CodeIgniter\Model;

class BrandModel extends Model
{
    protected $table = 'brand';

    public function listBrand()
    {
        $sql = "SELECT namabrand, keterangan FROM {$this->table} WHERE status='0'";
        $query = $this->db->query($sql);
        return $query ? $query->getResultArray() : $this->db->error();
    }

    public function getBrand($brand)
    {
        $sql = "SELECT namabrand, keterangan FROM {$this->table} WHERE status='0' AND namabrand=?";
        $query = $this->db->query($sql, [$brand]);
        return $query ? $query->getResult() : $this->db->error();
    }

    public function insertData($data)
    {
        $sql = $this->db->table($this->table)->set($data)->getCompiledInsert() . " ON DUPLICATE KEY UPDATE status='0', namabrand=?";
        if ($this->db->query($sql, [$data['namabrand']])) {
            return ['code' => 0, 'message' => ''];
        }
        return $this->db->error();
    }

    public function updateData($data, $oldbrand)
    {
        if ($this->db->table($this->table)->where('namabrand', $oldbrand)->update($data)) {
            return ['code' => 0, 'message' => ''];
        }
        return $this->db->error();
    }

    public function hapusData($data, $brand)
    {
        if ($this->db->table($this->table)->where('namabrand', $brand)->update($data)) {
            return ['code' => 0, 'message' => ''];
        }
        return $this->db->error();
    }
}
