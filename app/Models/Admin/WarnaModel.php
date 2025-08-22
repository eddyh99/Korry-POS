<?php

namespace App\Models\Admin;

use CodeIgniter\Model;

class WarnaModel extends Model
{
    protected $table      = 'warna';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'nama',
        'status'
    ];

    public function listWarna()
    {
        $sql = "SELECT * FROM {$this->table} WHERE status='0'";
        $query = $this->db->query($sql);

        if ($query) {
            return $query->getResultArray();
        } else {
            return $this->db->error();
        }
    }

    public function getWarna($warnaid)
    {
        $sql = "SELECT * FROM {$this->table} WHERE status='0' AND id=?";
        $query = $this->db->query($sql, [$warnaid]);

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

    public function updateData($data, $warnaid)
    {
        $builder = $this->db->table($this->table)->where('id', $warnaid);
        $query = $builder->update($data);

        if ($query) {
            return ["code" => 0, "message" => ""];
        } else {
            return $this->db->error();
        }
    }

    public function hapusData($data, $warnaid)
    {
        $builder = $this->db->table($this->table)->where('id', $warnaid);
        $query = $builder->update($data);

        if ($query) {
            return ["code" => 0, "message" => ""];
        } else {
            return $this->db->error();
        }
    }
}
