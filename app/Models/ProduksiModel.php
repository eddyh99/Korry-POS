<?php

namespace App\Models;

use CodeIgniter\Model;

class ProduksiModel extends Model
{
    protected $table      = 'produksi';
    // protected $primaryKey = 'member_id';
    // protected $returnType = 'array';

    // private $member = 'member';

    public function listProduksi()
    {
        $sql = "SELECT * FROM {$this->table}";
        $query = $this->db->query($sql);

        if ($query) {
            return $query->getResultArray();
        } else {
            return $this->db->error();
        }
    }

    public function insertData($data)
    {
        $builder = $this->db->table($this->member);
        if ($builder->insert($data)) {
            return ["code" => 0, "message" => ""];
        } else {
            return $this->db->error();
        }
    }

    public function hapusData($data, $memberid)
    {
        $builder = $this->db->table($this->member)->where("member_id", $memberid);
        if ($builder->update($data)) {
            return ["code" => 0, "message" => ""];
        } else {
            return $this->db->error();
        }
    }
}
