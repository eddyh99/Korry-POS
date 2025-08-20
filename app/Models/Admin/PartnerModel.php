<?php

namespace App\Models\Admin;

use CodeIgniter\Model;

class PartnerModel extends Model
{
    protected $table      = 'partner_konsinyasi';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'nama',
        'alamat',
        'kontak',
        'status'
    ];

    public function listPartner()
    {
        $sql = "SELECT * FROM {$this->table} WHERE status='0'";
        $query = $this->db->query($sql);

        if ($query) {
            return $query->getResultArray();
        } else {
            return $this->db->error();
        }
    }

    public function getPartner($partnerid)
    {
        $sql = "SELECT * FROM {$this->table} WHERE status='0' AND id=?";
        $query = $this->db->query($sql, [$partnerid]);

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

    public function updateData($data, $partnerid)
    {
        $builder = $this->db->table($this->table)->where('id', $partnerid);
        $query = $builder->update($data);

        if ($query) {
            return ["code" => 0, "message" => ""];
        } else {
            return $this->db->error();
        }
    }

    public function hapusData($data, $partnerid)
    {
        $builder = $this->db->table($this->table)->where('id', $partnerid);
        $query = $builder->update($data);

        if ($query) {
            return ["code" => 0, "message" => ""];
        } else {
            return $this->db->error();
        }
    }
}
