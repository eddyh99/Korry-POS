<?php

namespace App\Models\Admin;

use CodeIgniter\Model;

class StokbahanbakuModel extends Model
{
    protected $table      = 'stok_bahanbaku';

    public function listStokbahanbaku()
    {
        $sql = "SELECT sm.*, b.namabahan
                FROM {$this->table} sm
                LEFT JOIN bahanbaku b ON b.id = sm.idbahan";
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

        $query = $this->db->query($sql);

        if ($query) {
            return ["code" => 0, "message" => ""];
        } else {
            return $this->db->error();
        }
    }

}
