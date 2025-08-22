<?php

namespace App\Models\Admin;

use CodeIgniter\Model;

class FabricModel extends Model
{
    protected $table      = 'fabric';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'nama',
        'status'
    ];

    public function listFabric()
    {
        $sql = "SELECT * FROM {$this->table} WHERE status='0'";
        $query = $this->db->query($sql);

        if ($query) {
            return $query->getResultArray();
        } else {
            return $this->db->error();
        }
    }

    public function getFabric($fabricid)
    {
        $sql = "SELECT * FROM {$this->table} WHERE status='0' AND id=?";
        $query = $this->db->query($sql, [$fabricid]);

        if ($query) {
            return $query->getResult();
        } else {
            return $this->db->error();
        }
    }

    public function insertData($data)
    {
        // compile insert
        $sql = $this->db->table($this->table)->set($data)->getCompiledInsert()
            . " ON DUPLICATE KEY UPDATE nama=?, status=0";

        // jalankan query, isi param sesuai kolom yg ada
        $query = $this->db->query($sql, [
            $data['nama']
        ]);

        if ($query) {
            return ["code" => 0, "message" => ""];
        } else {
            return $this->db->error();
        }
    }

    public function updateData($data, $fabricid)
    {
        $builder = $this->db->table($this->table)->where('id', $fabricid);
        $query = $builder->update($data);

        if ($query) {
            return ["code" => 0, "message" => ""];
        } else {
            return $this->db->error();
        }
    }

    public function hapusData($data, $fabricid)
    {
        $builder = $this->db->table($this->table)->where('id', $fabricid);
        $query = $builder->update($data);

        if ($query) {
            return ["code" => 0, "message" => ""];
        } else {
            return $this->db->error();
        }
    }
}
