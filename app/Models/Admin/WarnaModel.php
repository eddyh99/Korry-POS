<?php

namespace App\Models\Admin;

use CodeIgniter\Model;

class WarnaModel extends Model
{
    protected $table      = 'warna';
    protected $primaryKey = 'namawarna';
    protected $allowedFields = ['namawarna', 'userid', 'status'];

    public function listWarna()
    {
        $sql = "SELECT namawarna FROM {$this->table} WHERE status = '0'";
        $query = $this->db->query($sql);
        return $query->getResultArray();
    }

    public function getWarna($warna)
    {
        $sql = "SELECT namawarna FROM {$this->table} WHERE status='0' AND namawarna=?";
        $query = $this->db->query($sql, [$warna]);
        return $query->getResult();
    }

    public function insertData($data)
    {
        $sql = $this->db->table($this->table)->set($data)->getCompiledInsert() . " ON DUPLICATE KEY UPDATE status='0', namawarna=?";
        if ($this->db->query($sql, [$data['namawarna']])) {
            return ['code' => 0, 'message' => ''];
        } else {
            return $this->db->error();
        }
    }

    public function updateData($data, $oldWarna)
    {
        if ($this->db->table($this->table)->where('namawarna', $oldWarna)->update($data)) {
            return ['code' => 0, 'message' => ''];
        } else {
            return $this->db->error();
        }
    }

    public function hapusData($data, $warna)
    {
        if ($this->db->table($this->table)->where('namawarna', $warna)->update($data)) {
            return ['code' => 0, 'message' => ''];
        } else {
            return $this->db->error();
        }
    }
}
