<?php

namespace App\Models\Admin;

use CodeIgniter\Model;

class PengeluaranModel extends Model
{
    protected $table      = 'pengeluaran';
    protected $primaryKey = 'namapengeluaran';
    protected $allowedFields = ['namapengeluaran', 'userid', 'status'];

    public function listPengeluaran()
    {
        $sql = "SELECT namapengeluaran FROM {$this->table} WHERE status = '0'";
        $query = $this->db->query($sql);
        return $query->getResultArray();
    }

    public function getPengeluaran($pengeluaran)
    {
        $sql = "SELECT namapengeluaran FROM {$this->table} WHERE status='0' AND namapengeluaran=?";
        $query = $this->db->query($sql, [$pengeluaran]);
        return $query->getResult();
    }

    public function insertData($data)
    {
        $sql = $this->db->table($this->table)->set($data)->getCompiledInsert() . " ON DUPLICATE KEY UPDATE status='0', namapengeluaran=?";
        if ($this->db->query($sql, [$data['namapengeluaran']])) {
            return ['code' => 0, 'message' => ''];
        } else {
            return $this->db->error();
        }
    }

    public function updateData($data, $oldPengeluaran)
    {
        if ($this->db->table($this->table)->where('namapengeluaran', $oldPengeluaran)->update($data)) {
            return ['code' => 0, 'message' => ''];
        } else {
            return $this->db->error();
        }
    }

    public function hapusData($data, $pengeluaran)
    {
        if ($this->db->table($this->table)->where('namapengeluaran', $pengeluaran)->update($data)) {
            return ['code' => 0, 'message' => ''];
        } else {
            return $this->db->error();
        }
    }
}
