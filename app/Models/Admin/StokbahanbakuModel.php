<?php

namespace App\Models\Admin;

use CodeIgniter\Model;

class StokbahanbakuModel extends Model
{
    protected $table      = 'produk_bahan';
    // protected $primaryKey = 'id';
    // protected $allowedFields = [
    //     'nama',
    //     'alamat',    
    //     'kontak',
    //     'status'
    // ];

    // public function listStokbahanbaku()
    // {
    //     $sql = "SELECT * FROM {$this->table}";
    //     $query = $this->db->query($sql);

    //     if ($query) {
    //         return $query->getResultArray();
    //     } else {
    //         return $this->db->error();
    //     }
    // }
    public function listStokbahanbaku()
    {
        $sql = "SELECT pb.barcode, 
                    pb.idbahan, 
                    b.namabahan, 
                    pb.jumlah
                FROM {$this->table} pb
                LEFT JOIN bahanbaku b ON b.id = pb.idbahan";
        $query = $this->db->query($sql);

        if ($query) {
            return $query->getResultArray();
        } else {
            return $this->db->error();
        }
    }

    public function insertData($data) 
    {
        // compiled insert
        $sql = $this->db->table($this->table)
            ->set($data)
            ->getCompiledInsert();

        // tambahkan ON DUPLICATE KEY UPDATE
        $sql .= " ON DUPLICATE KEY UPDATE jumlah = VALUES(jumlah), satuan = VALUES(satuan)";

        $query = $this->db->query($sql);

        if ($query) {
            return ["code" => 0, "message" => ""];
        } else {
            return $this->db->error();
        }
    }

}
