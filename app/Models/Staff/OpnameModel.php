<?php

namespace App\Models\Staff;

use CodeIgniter\Model;

class OpnameModel extends Model
{
    protected $table = 'kas';
    protected $primaryKey = 'id';
    protected $allowedFields = ['tanggal', 'storeid', 'jenis', 'nominal', 'keterangan', 'dateonly'];

    public function insertData($data)
    {
        if ($this->insert($data)) {
            return ["code" => 0, "message" => ""];
        } else {
            return $this->db->error(); // sama seperti CI3
        }
    }

    public function listkas()
    {
        $now = date("Y-m-d");
        $sql = "SELECT * FROM {$this->table} WHERE DATE(tanggal) = ?";
        $query = $this->db->query($sql, [$now]);
        return $query->getResultArray();
    }
}
