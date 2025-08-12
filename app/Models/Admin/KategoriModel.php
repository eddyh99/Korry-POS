<?php

namespace App\Models\Admin;

use CodeIgniter\Model;

class KategoriModel extends Model
{
    protected $table      = 'kategori';
    protected $primaryKey = 'namakategori';
    protected $allowedFields = ['namakategori', 'userid', 'status'];

    public function listKategori()
    {
        $sql = "SELECT namakategori FROM {$this->table} WHERE status = '0'";
        $query = $this->db->query($sql);
        return $query->getResultArray();
    }

    public function getKategori($kategori)
    {
        $sql = "SELECT namakategori FROM {$this->table} WHERE status='0' AND namakategori=?";
        $query = $this->db->query($sql, [$kategori]);
        return $query->getResult();
    }

    public function insertData($data)
    {
        $sql = $this->db->table($this->table)->set($data)->getCompiledInsert() . " ON DUPLICATE KEY UPDATE status='0', namakategori=?";
        if ($this->db->query($sql, [$data['namakategori']])) {
            return ['code' => 0, 'message' => ''];
        } else {
            return $this->db->error();
        }
    }

    public function updateData($data, $oldKategori)
    {
        if ($this->db->table($this->table)->where('namakategori', $oldKategori)->update($data)) {
            return ['code' => 0, 'message' => ''];
        } else {
            return $this->db->error();
        }
    }

    public function hapusData($data, $kategori)
    {
        if ($this->db->table($this->table)->where('namakategori', $kategori)->update($data)) {
            return ['code' => 0, 'message' => ''];
        } else {
            return $this->db->error();
        }
    }
}
