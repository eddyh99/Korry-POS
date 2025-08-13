<?php

namespace App\Models\Admin;

use CodeIgniter\Model;

class ProduksizeModel extends Model
{
    protected $table      = 'produksize';
    protected $primaryKey = ['barcode', 'size'];
    protected $allowedFields = ['barcode', 'size', 'userid', 'status', 'lastupdate'];

    // Jika ingin aktifkan fitur update otomatis timestamp:
    protected $useTimestamps = true;
    protected $updatedField  = 'lastupdate';
    protected $createdField  = ''; // tidak ada field created_at

    // List data menggunakan raw SQL (karena join)
    public function listSize()
    {
        $sql = "SELECT a.barcode, a.size, b.namaproduk, b.namabrand 
                FROM produksize a 
                INNER JOIN produk b ON a.barcode = b.barcode 
                WHERE a.status = '0'";

        $query = $this->db->query($sql);
        return $query ? $query->getResultArray() : $this->db->error();
    }

    public function insertBatchData(array $data)
    {
        if ($this->db->table($this->table)->insertBatch($data)) {
            return ['code' => 0, 'message' => ''];
        }
        return $this->db->error();
    }

    public function insertData(array $data)
    {
        // Build insert with ON DUPLICATE KEY UPDATE status='0'
        $builder = $this->db->table($this->table);
        $sql = $builder->set($data)->getCompiledInsert() . " ON DUPLICATE KEY UPDATE status='0'";
        if ($this->db->query($sql)) {
            return ['code' => 0, 'message' => ''];
        }
        return $this->db->error();
    }

    public function hapusData(array $data, array $where)
    {
        $builder = $this->db->table($this->table);
        $builder->where($where);
        if ($builder->update($data)) {
            return ['code' => 0, 'message' => ''];
        }
        return $this->db->error();
    }
}
