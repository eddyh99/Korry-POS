<?php

namespace App\Models;

use CodeIgniter\Model;

class ProduksiModel extends Model
{
    protected $table      = 'produksi';
    protected $primaryKey = 'nonota';
    protected $returnType = 'array';

    private $produksi           = 'produksi';
    private $produksi_detail    = 'produksi_detail';
    private $vendor             = 'vendor';


    // public function listProduksi()
    // {
    //     $sql = "SELECT * FROM {$this->table} WHERE status='0'";
    //     $query = $this->db->query($sql);

    //     if ($query) {
    //         return $query->getResultArray();
    //     } else {
    //         return $this->db->error();
    //     }
    // }
    public function listProduksi()
    {
        $sql = "
            SELECT 
                p.*,
                v.nama AS vendor_nama
            FROM {$this->produksi} p
            JOIN {$this->vendor} v ON v.id = p.idvendor
            WHERE p.status = 0 AND v.status = 0
        ";

        $query = $this->db->query($sql);

        if ($query) {
            return $query->getResultArray();
        } else {
            return $this->db->error();
        }
    }

    public function insertData($data)
    {
        // data utama untuk tabel produksi
        $produksi = [
            'nonota'     => $data["nonota"],
            'tanggal'    => date("Y-m-d H:i:s"),
            'idvendor'   => $data["idvendor"],
            'estimasi'   => $data["estimasi"],
            'dp'         => $data["dp"],
            'total'      => $data["total"],
            'user_id'    => $data["user_id"],
            'lastupdate' => date("Y-m-d H:i:s")
        ];

        // data untuk tabel produksi detail
        $produksi_detail = [
            'nonota'  => $data["nonota"],
            'barcode' => $data["barcode"],
            'jumlah'  => $data["jumlah"]
        ];

        $this->db->transStart();

        $this->db->table($this->produksi)->insert($produksi);

        $this->db->table($this->produksi_detail)->insert($produksi_detail);

        $this->db->transComplete();

        if ($this->db->transStatus() === false) {
            $this->db->transRollback();
            return ["code" => 511, "message" => "Data gagal disimpan"];
        } else {
            $this->db->transCommit();
            return ["code" => 0, "message" => "Data berhasil disimpan"];
        }
    }

    public function hapusData($data, $nonota)
    {
        $builder = $this->db->table('produksi');
        $builder->where("nonota", $nonota);

        if ($builder->update($data)) {
            return ["code" => 0, "message" => ""];
        } else {
            return $this->db->error();
        }
    }
}
