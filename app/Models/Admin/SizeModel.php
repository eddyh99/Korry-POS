<?php

namespace App\Models\Admin;

use CodeIgniter\Model;

class SizeModel extends Model
{
    protected $table      = 'size';
    protected $primaryKey = 'nama';
    protected $allowedFields = ['nama', 'userid', 'status'];

    public function listSize()
    {
        $sql = "SELECT nama, userid FROM {$this->table} WHERE status='0'";
        $query = $this->db->query($sql);
        return $query ? $query->getResultArray() : $this->db->error();
    }

    public function getSize($nama)
    {
        $sql = "SELECT nama, userid FROM {$this->table} WHERE status='0' AND nama=?";
        $query = $this->db->query($sql, [$nama]);
        return $query ? $query->getResult() : $this->db->error();
    }

    public function insertData($data)
    {
        // ON DUPLICATE KEY UPDATE untuk aktifkan kembali jika sebelumnya soft-delete
        $sql = $this->db->table($this->table)
                        ->set($data)
                        ->getCompiledInsert() 
             . " ON DUPLICATE KEY UPDATE status='0', nama=?";
             
        if ($this->db->query($sql, [$data['nama']])) {
            return ['code' => 0, 'message' => ''];
        }
        return $this->db->error();
    }

    public function updateData($data, $oldNama)
    {
        if ($this->db->table($this->table)->where('nama', $oldNama)->update($data)) {
            return ['code' => 0, 'message' => ''];
        }
        return $this->db->error();
    }

    public function hapusData($data, $nama)
    {
        if ($this->db->table($this->table)->where('nama', $nama)->update($data)) {
            return ['code' => 0, 'message' => ''];
        }
        return $this->db->error();
    }
}
