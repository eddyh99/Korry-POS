<?php

namespace App\Models\Admin;

use CodeIgniter\Model;

class PenggunaModel extends Model
{
    protected $table      = 'pengguna';
    protected $primaryKey = 'username';
    protected $allowedFields = ['username', 'passwd', 'nama', 'role', 'status'];

    private $assignstore = 'assignstore';

    public function listpengguna()
    {
        $sql = "SELECT username, nama, role 
                FROM {$this->table} 
                WHERE status='0' AND role!='admin'";
        return $this->db->query($sql)->getResultArray();
    }

    public function getNonAdmin()
    {
        $sql = "SELECT username, nama, role 
                FROM {$this->table} 
                WHERE status='0' 
                AND (role!='Admin' AND role!='Owner') 
                AND username NOT IN (
                    SELECT username FROM {$this->assignstore} WHERE status='0'
                )";
        return $this->db->query($sql)->getResultArray();
    }

    // public function getUser($username)
    // {
    //     $sql = "SELECT username, nama, role 
    //             FROM {$this->table} 
    //             WHERE status='0' AND username=?";
    //     return $this->db->query($sql, [$username])->getRowArray();
    // }
	public function getUser($username)
	{
		$sql = "SELECT username, nama, role 
				FROM {$this->table} 
				WHERE status='0' AND username=?";
		return $this->db->query($sql, [$username])->getResult();
		
	}

    public function insertData($data)
    {
        $sql = $this->db->table($this->table)
                        ->set($data)
                        ->getCompiledInsert() . 
               " ON DUPLICATE KEY UPDATE passwd=?, nama=?, role=?, status='0'";

        if ($this->db->query($sql, [$data['passwd'], $data['nama'], $data['role']])) {
            return ['code' => 0, 'message' => ''];
        }
        return $this->db->error();
    }

    public function updateData($data, $username)
    {
        if ($this->db->table($this->table)->where('username', $username)->update($data)) {
            return ['code' => 0, 'message' => ''];
        }
        return $this->db->error();
    }

    public function hapusData($data, $username)
    {
        $this->db->transStart();

        $this->db->table($this->table)->where('username', $username)->update($data);
        $this->db->table($this->assignstore)->where('username', $username)->update($data);

        $this->db->transComplete();

        if ($this->db->transStatus() === false) {
            return $this->db->error();
        }
        return ['code' => 0, 'message' => ''];
    }
}
