<?php

namespace App\Models\Admin;

use CodeIgniter\Model;

class StoreModel extends Model
{
    protected $table      = 'store';
    protected $primaryKey = 'storeid';
	protected $allowedFields = [
									'store',
									'alamat',
									'keterangan',
									'kontak',
									'status',
									'userid'
								];


    public function listStore()
    {
        $sql = "SELECT * FROM {$this->table} WHERE status='0'";
        $query = $this->db->query($sql);

        if ($query) {
            return $query->getResultArray();
        } else {
            return $this->db->error();
        }
    }

    public function getStore($storeid)
    {
        $sql = "SELECT * FROM {$this->table} WHERE status='0' AND storeid=?";
        $query = $this->db->query($sql, [$storeid]);

        if ($query) {
            return $query->getResult();
        } else {
            return $this->db->error();
        }
    }

    public function insertData($data)
    {
        $sql = $this->db->table($this->table)->set($data)->getCompiledInsert()
             . " ON DUPLICATE KEY UPDATE alamat=?, keterangan=?, kontak=?, status=0";

        $query = $this->db->query($sql, [
            $data['alamat'],
            $data['keterangan'],
            $data['kontak']
        ]);

        if ($query) {
            return ["code" => 0, "message" => ""];
        } else {
            return $this->db->error();
        }
    }

    public function updateData($data, $storeid)
    {
        $builder = $this->db->table($this->table)->where('storeid', $storeid);
        $query = $builder->update($data);

        if ($query) {
            return ["code" => 0, "message" => ""];
        } else {
            return $this->db->error();
        }
    }

    public function hapusData($data, $storeid)
    {
        $builder = $this->db->table($this->table)->where('storeid', $storeid);
        $query = $builder->update($data);

        if ($query) {
            return ["code" => 0, "message" => ""];
        } else {
            return $this->db->error();
        }
    }
}
