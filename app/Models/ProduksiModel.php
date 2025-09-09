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
        $this->db->transStart();

        // Auto-generate No. Produksi
        $sql="SELECT LPAD(
                COALESCE(CAST(MAX(nonota) AS UNSIGNED), 0) + 1,
                5,
                '0'
            ) AS next_nonota
            FROM produksi";
            
        $nonota = $this->db->query($sql)->getRow()->next_nonota;

        // Data utama untuk tabel produksi
        $produksi = [
            'nonota'     => $nonota,
            'tanggal'    => date("Y-m-d H:i:s"),
            'idvendor'   => $data["idvendor"],
            'estimasi'   => $data["estimasi"],
            'dp'         => $data["dp"],
            'total'      => $data["total"],
            'user_id'    => $data["user_id"],
            'lastupdate' => date("Y-m-d H:i:s")
        ];

        $this->db->table($this->produksi)->insert($produksi);

        // Insert detail produksi (loop multi item)
        foreach ($data["detail"] as $row) {
            $detail = [
                'nonota'  => $nonota,
                'barcode' => $row["barcode"],
                'size'    => $row["size"],
                'jumlah'  => $row["jumlah"],
                'biaya'   => $row["biaya"],
                'harga'   => $row["harga"],
            ];
        }
        $this->db->table($this->produksi_detail)->insertBatch($detail);

        $this->db->transComplete();

        if ($this->db->transStatus() === false) {
            $this->db->transRollback();
            return [
                "status"  => false,
                "message" => "DB Error: " . $this->db->error()->message
            ];
        } else {
            $this->db->transCommit();
            return [
                "status"  => true,
                "message" => "Data berhasil disimpan"
            ];
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

    public function listdeadline(){
        $today = date("Y-m-d");
        $sql="SELECT 
                p.nonota,
                p.tanggal,
                p.estimasi,
                DATE_ADD(p.tanggal, INTERVAL p.estimasi DAY) AS deadline,
                p.dp,
                p.total,
                v.nama AS vendor_nama,
                v.tipe AS vendor_tipe
            FROM produksi p
            JOIN vendor v ON p.idvendor = v.id
            WHERE DATE_ADD(p.tanggal, INTERVAL p.estimasi DAY) <= ?
            AND p.status = 0
            AND v.status = 0
            AND p.is_complete = 0
        ";
        $query = $this->db->query($sql,$today)->getResultArray();
        return $query;
    }

    public function complete_produksi($nonota){
        $builder = $this->db->table('produksi');
        $builder->where("nonota", $nonota);
        $result = $builder->update(["is_complete" => 1]);

        if ($result) {
            return ["code" => 0, "message" => "Produksi sudah complete"];
        } else {
            return ["code" => 1, "message" => $this->db->error()];
        }
    }

    public function getProduksiDetail($nonota)
    {
        $mdata = [
            "header" => null,
            "detail" => []
        ];

        // === Ambil header produksi
        $sql1 = "SELECT 
                    p.nonota,
                    p.tanggal,
                    p.estimasi,
                    p.dp,
                    p.total,
                    p.user_id,
                    v.nama AS vendor_nama,
                    v.tipe AS vendor_tipe
                FROM {$this->produksi} p
                INNER JOIN {$this->vendor} v ON v.id = p.idvendor
                WHERE p.nonota = ?
                LIMIT 1";
        $header = $this->db->query($sql1, [$nonota])->getRow();

        if ($header) {
            $mdata["header"] = $header;
        } else {
            // fallback kalau data header tidak ada
            $mdata["header"] = (object) [
                "nonota"     => $nonota,
                "tanggal"    => null,
                "estimasi"   => null,
                "dp"         => null,
                "total"      => null,
                "user_id"    => null,
                "vendor_nama"=> "-",
                "vendor_tipe"=> "-"
            ];
        }

        // === Ambil detail produksi
        $sql2 = "SELECT 
                    d.barcode,
                    pr.namaproduk,
                    pr.sku,
                    d.size,
                    d.jumlah,
                    d.harga,
                    d.biaya
                FROM {$this->produksi_detail} d
                INNER JOIN produk pr ON pr.barcode = d.barcode
                WHERE d.nonota = ?";
        $detail = $this->db->query($sql2, [$nonota])->getResultArray();

        foreach ($detail as $i => $det) {
            $mdata["detail"][$i] = [
                "barcode"    => $det["barcode"],
                "namaproduk" => $det["namaproduk"],
                "sku"        => $det["sku"],
                "size"       => $det["size"],
                "jumlah"     => $det["jumlah"],
                "harga"      => $det["harga"],
                "biaya"      => $det["biaya"]
            ];
        }

        return $mdata;
    }
}
