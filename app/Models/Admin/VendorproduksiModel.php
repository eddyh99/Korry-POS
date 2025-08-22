<?php

namespace App\Models\Admin;

use CodeIgniter\Model;

class VendorproduksiModel extends Model
{
    protected $table      = 'vendor';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'nama',
        'kontak',
        'tipe',
        'status'
    ];

    public function listVendor()
    {
        $sql = "SELECT * FROM {$this->table} WHERE status='0'";
        $query = $this->db->query($sql);

        if ($query) {
            return $query->getResultArray();
        } else {
            return $this->db->error();
        }
    }

    public function getVendor($vendorid)
    {
        $sql = "SELECT * FROM {$this->table} WHERE status='0' AND id=?";
        $query = $this->db->query($sql, [$vendorid]);

        if ($query) {
            return $query->getResult();
        } else {
            return $this->db->error();
        }
    }

    public function insertData($data)
    {
        $sql = $this->db->table($this->table)->set($data)->getCompiledInsert()
             . " ON DUPLICATE KEY UPDATE kontak=?, tipe=?, status=0";

        $query = $this->db->query($sql, [
            $data['kontak'],
            $data['tipe']
        ]);

        if ($query) {
            return ["code" => 0, "message" => ""];
        } else {
            return $this->db->error();
        }
    }

    public function updateData($data, $vendorid)
    {
        $builder = $this->db->table($this->table)->where('id', $vendorid);
        $query = $builder->update($data);

        if ($query) {
            return ["code" => 0, "message" => ""];
        } else {
            return $this->db->error();
        }
    }

    public function hapusData($data, $vendorid)
    {
        $builder = $this->db->table($this->table)->where('id', $vendorid);
        $query = $builder->update($data);

        if ($query) {
            return ["code" => 0, "message" => ""];
        } else {
            return $this->db->error();
        }
    }
}
