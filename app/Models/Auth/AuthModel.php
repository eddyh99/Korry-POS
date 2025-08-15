<?php

namespace App\Models\Auth;

use CodeIgniter\Model;

class AuthModel extends Model
{
    protected $table      = 'pengguna';
    protected $primaryKey = 'username';
    protected $allowedFields = ['username', 'passwd', 'nama', 'role', 'status'];
    protected $returnType    = 'object'; // biar sama dengan CI3 row() object

    // public function verifyLogin($username, $password)
    // {
    //     $pass = sha1($password);

    //     $query = $this->where([
    //         'username' => $username,
    //         'passwd'   => $pass
    //     ])->get();

    //     if ($query->getNumRows() > 0) {
    //         return $query->getRow();
    //     } else {
    //         return false;
    //     }
    // }

    // public function verifyLogin($username, $password)
    // {
    //     $pass = sha1($password);

    //     $sql = "
    //         SELECT p.username, p.nama, p.role, s.storeid
    //         FROM {$this->table} p
    //         LEFT JOIN (
    //             SELECT a.username, a.storeid
    //             FROM assignstore a
    //             WHERE a.status = '0'
    //             UNION
    //             SELECT st.userid AS username, st.storeid
    //             FROM store st
    //             WHERE st.status = '0'
    //         ) s ON p.username = s.username
    //         WHERE p.username = ? AND p.passwd = ?
    //         ORDER BY s.storeid ASC
    //         LIMIT 1
    //     ";

    //     $query = $this->db->query($sql, [$username, $pass]);

    //     if ($query->getNumRows() > 0) {
    //         return $query->getRow();
    //     }
    //     return false;
    // }

    public function verifyLogin($username, $password)
    {
        $pass = sha1($password);

        // Ambil dulu role user
        $user = $this->where([
            'username' => $username,
            'passwd'   => $pass
        ])->get()->getRow();

        if (!$user) {
            return false;
        }

        // Kalau bukan Admin/Owner → ambil store dari assignstore
        if (!in_array($user->role, ['Admin', 'Owner'])) {
            $sql = "
                SELECT p.username, p.nama, p.role, a.storeid, s.store
                FROM {$this->table} p
                INNER JOIN assignstore a ON p.username = a.username
                INNER JOIN store s ON a.storeid = s.storeid
                WHERE p.username = ? AND p.passwd = ? AND a.status = '0' AND s.status = '0'
                LIMIT 1
            ";
            $query = $this->db->query($sql, [$username, $pass]);
            return $query->getRow();
        }

        // Kalau Admin/Owner → ambil store dari tabel store (default ke toko pertama)
        $sql = "
            SELECT p.username, p.nama, p.role, s.storeid, s.store
            FROM {$this->table} p
            INNER JOIN store s ON p.username = s.userid
            WHERE p.username = ? AND p.passwd = ? AND s.status = '0'
            ORDER BY s.storeid ASC
            LIMIT 1
        ";
        $query = $this->db->query($sql, [$username, $pass]);
        return $query->getRow();
    }
}
