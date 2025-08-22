<?php

namespace App\Models\Admin;

use CodeIgniter\Model;

class BahanbakuModel extends Model
{
    protected $table      = 'bahanbaku';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'namabahan',
        'min',
        'is_delete',
        'user_id',
        'lastupdate'
    ];
    public $useTimestamps = false;

    public function Listbahanbaku()
    {
        $sql = "SELECT * FROM {$this->table} WHERE is_delete='0'";
        $query = $this->db->query($sql);

        if ($query) {
            return $query->getResultArray();
        } else {
            return $this->db->error();
        }
    }
    public function listBahanbakuWithStok()
    {
        $sql = "SELECT b.id,
                    b.namabahan,
                    b.min,
                    IFNULL(SUM(pb.jumlah), 0) - IFNULL(SUM(pd.jumlah), 0) AS stok
                FROM {$this->table} b
                LEFT JOIN produk_bahan pb ON pb.idbahan = b.id
                LEFT JOIN produksi_detail pd ON pd.barcode = pb.barcode
                WHERE b.is_delete = 0
                GROUP BY b.id, b.namabahan, b.min";

        $query = $this->db->query($sql);

        return $query->getResultArray();
    }

    public function getBahanbaku($bbakuid)
    {
        $sql = "SELECT * FROM {$this->table} WHERE is_delete='0' AND id=?";
        $query = $this->db->query($sql, [$bbakuid]);

        if ($query) {
            return $query->getResult();
        } else {
            return $this->db->error();
        }
    }

    public function insertData($data)
    {
        $data['lastupdate'] = date('Y-m-d H:i:s');
        $data['is_delete']  = 0;

        $query = $this->insert($data);

        if ($query) {
            return ["code" => 0, "message" => ""];
        } else {
            return ["code" => 1, "message" => implode(", ", $this->errors())];
        }
    }

    public function updateData($data, $bbakuid)
    {
        $builder = $this->db->table($this->table)->where('id', $bbakuid);
        $query = $builder->update($data);

        if ($query) {
            return ["code" => 0, "message" => ""];
        } else {
            return $this->db->error();
        }
    }

    public function hapusData($data, $bbakuid)
    {
        $builder = $this->db->table($this->table)->where('id', $bbakuid);
        $query = $builder->update($data);

        if ($query) {
            return ["code" => 0, "message" => ""];
        } else {
            return $this->db->error();
        }
    }
}
