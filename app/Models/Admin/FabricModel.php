<?php

namespace App\Models\Admin;

use CodeIgniter\Model;

class FabricModel extends Model
{
    protected $table      = 'fabric';
    protected $primaryKey = 'namafabric';
    protected $allowedFields = ['namafabric', 'userid', 'status'];

    public function listFabric()
    {
        $sql = "SELECT namafabric FROM {$this->table} WHERE status = '0'";
        $query = $this->db->query($sql);
        return $query->getResultArray();
    }

    public function getFabric($fabric)
    {
        $sql = "SELECT namafabric FROM {$this->table} WHERE status='0' AND namafabric=?";
        $query = $this->db->query($sql, [$fabric]);
        return $query->getResult();
    }

    public function insertData($data)
    {
        $sql = $this->db->table($this->table)->set($data)->getCompiledInsert() . " ON DUPLICATE KEY UPDATE status='0', namafabric=?";
        if ($this->db->query($sql, [$data['namafabric']])) {
            return ['code' => 0, 'message' => ''];
        } else {
            return $this->db->error();
        }
    }

    public function updateData($data, $oldFabric)
    {
        if ($this->db->table($this->table)->where('namafabric', $oldFabric)->update($data)) {
            return ['code' => 0, 'message' => ''];
        } else {
            return $this->db->error();
        }
    }

    public function hapusData($data, $fabric)
    {
        if ($this->db->table($this->table)->where('namafabric', $fabric)->update($data)) {
            return ['code' => 0, 'message' => ''];
        } else {
            return $this->db->error();
        }
    }
}
