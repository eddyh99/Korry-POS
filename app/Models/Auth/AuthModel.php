<?php

namespace App\Models\Auth;

use CodeIgniter\Model;

class AuthModel extends Model
{
    protected $table      = 'pengguna';
    protected $primaryKey = 'username';
    protected $allowedFields = ['username', 'passwd', 'nama', 'role', 'status'];
    protected $returnType    = 'object'; // biar sama dengan CI3 row() object

    public function verifyLogin($username, $password)
    {
        $pass = sha1($password);

        $query = $this->where([
            'username' => $username,
            'passwd'   => $pass
        ])->get();

        if ($query->getNumRows() > 0) {
            return $query->getRow();
        } else {
            return false;
        }
    }
}
