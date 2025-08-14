<?php

namespace App\Models;

use CodeIgniter\Model;

class MemberModel extends Model
{
    protected $table      = 'member';
    protected $primaryKey = 'member_id';
    protected $returnType = 'array';

    private $member = 'member';

    public function allposts_count()
    {
        $sql = "SELECT * FROM {$this->member} WHERE status='0'";
        $query = $this->db->query($sql);
        return $query->getNumRows();
    }

    public function allposts($limit, $start, $col, $dir)
    {
        $sql = "SELECT * FROM {$this->member} 
                WHERE status='0' 
                ORDER BY {$col} {$dir} 
                LIMIT {$start}, {$limit}";
        $query = $this->db->query($sql);
        return $query->getResultArray();
    }

    public function posts_search($limit, $start, $search, $col, $dir)
    {
        $sql = "SELECT * FROM {$this->member} 
                WHERE status='0' 
                AND (member_id LIKE '%{$search}%' 
                OR nama LIKE '%{$search}%' 
                OR nope LIKE '%{$search}%' 
                OR email LIKE '%{$search}%') 
                ORDER BY {$col} {$dir} 
                LIMIT {$start}, {$limit}";
        $query = $this->db->query($sql);
        return $query->getResultArray();
    }

    public function posts_search_count($search)
    {
        $sql = "SELECT * FROM {$this->member} 
                WHERE status='0' 
                AND (member_id LIKE '%{$search}%' 
                OR nama LIKE '%{$search}%' 
                OR nope LIKE '%{$search}%' 
                OR email LIKE '%{$search}%')";
        $query = $this->db->query($sql);
        return $query->getNumRows();
    }

    public function getLastmember()
    {
        $sql = "SELECT RIGHT(RIGHT(member_id,5)+100001,5) as last 
                FROM {$this->member} 
                ORDER BY member_id DESC";
        $query = $this->db->query($sql);

        if ($query->getNumRows() == 0) {
            return '00001';
        } else {
            return $query->getRow()->last;
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

    public function getMember($memberid)
    {
        $sql = "SELECT * FROM {$this->member} WHERE member_id = ?";
        $query = $this->db->query($sql, [$memberid]);

        if ($query->getNumRows() > 0) {
            return ["code" => 0, "message" => $query->getRow()];
        } else {
            return ["code" => 4004];
        }
    }

    public function updateData($data, $memberid)
    {
        $builder = $this->db->table($this->member)->where("member_id", $memberid);
        if ($builder->update($data)) {
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
