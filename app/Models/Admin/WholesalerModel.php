<?php

namespace App\Models\Admin;

use CodeIgniter\Model;

class WholesalerModel extends Model
{
    protected $table      = 'wholesaler';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'nama',
        'alamat',
        'kontak',
        'status'
    ];

    public function listWholesaler()
    {
        $sql = "SELECT * FROM {$this->table} WHERE status='0'";
        $query = $this->db->query($sql);

        if ($query) {
            return $query->getResultArray();
        } else {
            return $this->db->error();
        }
    }

    public function getWholesaler($wsalerid)
    {
        $sql = "SELECT * FROM {$this->table} WHERE status='0' AND id=?";
        $query = $this->db->query($sql, [$wsalerid]);

        if ($query) {
            return $query->getResult();
        } else {
            return $this->db->error();
        }
    }

    public function insertData($data)
    {
        $sql = $this->db->table($this->table)->set($data)->getCompiledInsert()
             . " ON DUPLICATE KEY UPDATE alamat=?, kontak=?, status=0";

        $query = $this->db->query($sql, [
            $data['alamat'],
            $data['kontak']
        ]);

        if ($query) {
            return ["code" => 0, "message" => ""];
        } else {
            return $this->db->error();
        }
    }

    public function updateData($data, $wsalerid)
    {
        $builder = $this->db->table($this->table)->where('id', $wsalerid);
        $query = $builder->update($data);

        if ($query) {
            return ["code" => 0, "message" => ""];
        } else {
            return $this->db->error();
        }
    }

    public function hapusData($data, $wsalerid)
    {
        $builder = $this->db->table($this->table)->where('id', $wsalerid);
        $query = $builder->update($data);

        if ($query) {
            return ["code" => 0, "message" => ""];
        } else {
            return $this->db->error();
        }
    }
}
