<?php

namespace App\Models\Admin;

use CodeIgniter\Model;

class AssignModel extends Model
{
    protected $table           = 'assignstore';
    protected $primaryKey      = 'username';
    protected $allowedFields   = ['username', 'storeid', 'status', 'userid', 'lastupdate'];
    protected $returnType      = 'object';
    protected $useTimestamps   = false;

    protected $tablePengguna   = 'pengguna';
    protected $tableStore      = 'store';

    // Daftar Staff dengan join
    public function listStaff()
    {
        $sql = "SELECT a.username, b.nama, c.store, c.alamat, a.storeid
                FROM {$this->table} a
                INNER JOIN {$this->tablePengguna} b ON a.username = b.username
                INNER JOIN {$this->tableStore} c ON a.storeid = c.storeid
                WHERE a.status = '0' AND (b.role <> 'Admin')";
        return $this->db->query($sql)->getResult();
    }

    // Get store berdasarkan username assign
    public function getStoreID($username)
    {
        $sql = "SELECT a.*, b.store
                FROM {$this->table} a
                INNER JOIN {$this->tableStore} b ON a.storeid = b.storeid
                WHERE a.status = '0' AND a.username = ?";
        return $this->db->query($sql, [$username])->getRow();
    }

    // Insert data assignstore, jika duplicate update status jadi 0 (aktif)
    public function insertData(array $data)
    {
        // Gunakan query manual ON DUPLICATE KEY UPDATE
        $builder = $this->db->table($this->table);
        $sql = $builder->set($data)->getCompiledInsert() . " ON DUPLICATE KEY UPDATE status = '0', lastupdate = CURRENT_TIMESTAMP";

        return $this->db->query($sql);
    }

    // Update data dengan where kondisi tertentu
    public function hapusData(array $data, array $where)
    {
        $builder = $this->db->table($this->table);
        return $builder->where($where)->update($data);
    }
}
