<?php

namespace App\Models\Admin;

use CodeIgniter\Model;

class AssignModel extends Model
{
    protected $table         = 'assignstore';
    protected $primaryKey    = 'username';
    protected $allowedFields = ['username', 'storeid', 'status', 'userid'];

    private $pengguna    = 'pengguna';
    private $store       = 'store';
    private $assignstore = 'assignstore';

    public function ListStaff()
    {
        $sql = "
            SELECT a.username, b.nama, c.store, c.alamat, a.storeid
            FROM {$this->assignstore} a
            INNER JOIN {$this->pengguna} b ON a.username = b.username
            INNER JOIN {$this->store} c ON a.storeid = c.storeid
            WHERE a.status = '0'
              AND (b.role <> 'Admin')
        ";

        $query = $this->db->query($sql);
        return $query->getResultArray();
    }

    public function getStoreID($username)
    {
        $sql = "
            SELECT a.*, b.store
            FROM {$this->assignstore} a
            INNER JOIN {$this->store} b ON a.storeid = b.storeid
            WHERE a.status = '0'
              AND a.username = ?
        ";

        $query = $this->db->query($sql, [$username]);
        return $query->getRow();
    }

    public function insertData(array $data)
    {
        // di CI4 tidak ada insert_string, langsung raw SQL
        $columns = implode(',', array_keys($data));
        $values  = implode(',', array_map(fn($v) => $this->db->escape($v), array_values($data)));

        $sql = "
            INSERT INTO {$this->assignstore} ({$columns})
            VALUES ({$values})
            ON DUPLICATE KEY UPDATE status='0'
        ";

        if ($this->db->query($sql)) {
            return ['code' => 0, 'message' => ''];
        }
        return $this->db->error();
    }

    public function hapusData(array $data, array $where)
    {
        $builder = $this->db->table($this->assignstore);
        $builder->where($where);

        if ($builder->update($data)) {
            return ['code' => 0, 'message' => ''];
        }
        return $this->db->error();
    }
}
