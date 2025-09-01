<?php

namespace App\Models\Admin;

use CodeIgniter\Model;

class StokbahanbakuModel extends Model
{
    protected $table      = 'stok_bahanbaku';

    public function listStokbahanbaku()
    {
        $sql = "SELECT 
                    b.id,
                    b.namabahan,
                    b.min,
                    s.satuan,
                    COALESCE(stok.total_masuk, 0) - COALESCE(pakai.total_keluar, 0) AS stok_akhir,
                    COALESCE(stok.total_masuk, 0) AS total_masuk,
                    COALESCE(pakai.total_keluar, 0) AS total_keluar,
                    COALESCE(stok.avg_harga, 0) AS harga_rata2,
                    COALESCE(stok.last_harga, 0) AS harga_terakhir
                FROM bahanbaku b
                LEFT JOIN (
                    -- total bahan masuk + harga
                    SELECT 
                        idbahan, 
                        SUM(jumlah) AS total_masuk,
                        AVG(harga) AS avg_harga,
                        MAX(harga) AS last_harga
                    FROM stok_bahanbaku
                    GROUP BY idbahan
                ) stok ON stok.idbahan = b.id
                LEFT JOIN (
                    -- total bahan keluar dari produksi
                    SELECT 
                        pb.idbahan, 
                        SUM(pd.jumlah * pb.jumlah) AS total_keluar
                    FROM produksi_detail pd
                    JOIN produk_bahan pb ON pb.barcode = pd.barcode
                    GROUP BY pb.idbahan
                ) pakai ON pakai.idbahan = b.id
                LEFT JOIN (
                    -- ambil satuan dari stok_bahanbaku (misal input terbaru)
                    SELECT idbahan, MAX(satuan) AS satuan
                    FROM stok_bahanbaku
                    GROUP BY idbahan
                ) s ON s.idbahan = b.id;";
        $query = $this->db->query($sql);

        if ($query) {
            return $query->getResultArray();
        } else {
            return $this->db->error();
        }
    }

    public function insertData($data) 
    {
        // compiled insert
        $sql = $this->db->table($this->table)
            ->set($data)
            ->getCompiledInsert();

        $query = $this->db->query($sql);

        if ($query) {
            return ["code" => 0, "message" => ""];
        } else {
            return $this->db->error();
        }
    }

}
