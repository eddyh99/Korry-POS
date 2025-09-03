<?php

namespace App\Models\Admin;

use CodeIgniter\Model;

class MetodebayarModel extends Model
{
    protected $table      = 'metode_bayar';
    protected $primaryKey = 'noakun';
    protected $allowedFields = ['namaakun', 'noakun', 'namabank', 'cabangbank', 'kodeswift', 'matauang', 'negara'];

    public function listMetodebayar()
    {
        $sql = "SELECT * FROM {$this->table}";
        $query = $this->db->query($sql);
        return $query->getResultArray();
    }

    public function getMetodebayar($noakun)
    {
        $sql = "SELECT * FROM {$this->table} WHERE noakun=?";
        $query = $this->db->query($sql, [$noakun]);
        return $query->getResult();
    }

    public function updateData($data, $oldnoakun)
    {
        if ($this->db->table($this->table)->where('noakun', $oldnoakun)->update($data)) {
            return ['code' => 0, 'message' => ''];
        } else {
            return $this->db->error();
        }
    }
}
