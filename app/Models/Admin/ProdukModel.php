<?php

namespace App\Models\Admin;

use CodeIgniter\Model;

class ProdukModel extends Model
{
    protected $table      = 'produk'; // default table
    protected $primaryKey = 'barcode';
    protected $returnType = 'array';

    private $produk = 'produk';
    private $harga  = 'harga';

    public function Listproduk()
    {
        $sql = "SELECT * FROM {$this->produk} WHERE status='0'";
        $query = $this->db->query($sql);

        if ($query) {
            return $query->getResultArray();
        } else {
            return $this->db->error();
        }
    }

    public function getProduk($barcode)
    {
        $sql = "SELECT a.*, x.harga, x.diskon
                FROM produk a
                INNER JOIN (
                    SELECT a.harga, a.diskon, a.barcode
                    FROM harga a
                    INNER JOIN (
                        SELECT MAX(tanggal) as tanggal, barcode 
                        FROM harga 
                        GROUP BY barcode
                    ) x 
                    ON a.barcode = x.barcode 
                    AND a.tanggal = x.tanggal
                ) x ON a.barcode = x.barcode
                WHERE a.barcode = ?";
        
        $query = $this->db->query($sql, [$barcode]);

        if ($query) {
            return $query->getRow();
        } else {
            return $this->db->error();
        }
    }

    public function insertData($data)
    {
        $produk = [
            'barcode'      => $data["barcode"],
            'namaproduk'   => $data["namaproduk"],
            'namabrand'    => $data["namabrand"],
            'namakategori' => $data["namakategori"],
            'userid'       => $data["userid"]
        ];

        $price = [
            'barcode' => $data["barcode"],
            'tanggal' => date("Y-m-d H:i:s"),
            'harga'   => $data["harga"],
            'diskon'  => $data["diskon"] ?? 0,
            'userid'  => $data["userid"]
        ];

        $this->db->transStart();

        // Insert produk
        $this->db->table($this->produk)->insert($produk);

        // Insert harga
        $this->db->table($this->harga)->insert($price);

        $this->db->transComplete();

        if ($this->db->transStatus() === false) {
            $this->db->transRollback();
            return ["code" => 511, "message" => "Data sudah pernah digunakan"];
        } else {
            $this->db->transCommit();
            return ["code" => 0, "message" => ""];
        }
    }

    public function hapusData($data, $barcode)
    {
        $builder = $this->db->table($this->produk);
        $builder->where("barcode", $barcode);

        if ($builder->update($data)) {
            return ["code" => 0, "message" => ""];
        } else {
            return $this->db->error();
        }
    }

    // Batas Insert, Update, Delete

    public function allposts_count()
    {
        return $this->db->table($this->table)->countAllResults();
    }

    public function allposts($limit, $start, $col, $dir)
    {
        $sql = "SELECT a.*, x.harga, x.diskon
                FROM {$this->produk} a
                INNER JOIN (
                    SELECT a.harga, a.barcode, a.diskon
                    FROM {$this->harga} a
                    INNER JOIN (
                        SELECT MAX(tanggal) as tanggal, barcode
                        FROM {$this->harga}
                        GROUP BY barcode
                    ) x 
                    ON a.barcode = x.barcode 
                    AND a.tanggal = x.tanggal
                ) x ON a.barcode = x.barcode
                ORDER BY {$col} {$dir}
                LIMIT {$start}, {$limit}";

        return $this->db->query($sql)->getResultArray();
    }

    public function posts_search($limit, $start, $search, $col, $dir)
    {
        $sql = "SELECT a.*, x.harga, x.diskon
                FROM {$this->produk} a
                INNER JOIN (
                    SELECT a.harga, a.barcode, a.diskon
                    FROM {$this->harga} a
                    INNER JOIN (
                        SELECT MAX(tanggal) as tanggal, barcode
                        FROM {$this->harga}
                        GROUP BY barcode
                    ) x 
                    ON a.barcode = x.barcode 
                    AND a.tanggal = x.tanggal
                ) x ON a.barcode = x.barcode
                WHERE (a.barcode LIKE ? 
                    OR namaproduk LIKE ? 
                    OR namabrand LIKE ? 
                    OR namakategori LIKE ? 
                    OR harga LIKE ?)
                ORDER BY {$col} {$dir}
                LIMIT {$start}, {$limit}";

        $like = "%{$search}%";
        return $this->db->query($sql, [$like, $like, $like, $like, $like])->getResultArray();
    }

    public function posts_search_count($search)
    {
        $sql = "SELECT a.*, x.harga, x.diskon
                FROM {$this->produk} a
                INNER JOIN (
                    SELECT a.harga, a.barcode, a.diskon
                    FROM {$this->harga} a
                    INNER JOIN (
                        SELECT MAX(tanggal) as tanggal, barcode
                        FROM {$this->harga}
                        GROUP BY barcode
                    ) x 
                    ON a.barcode = x.barcode 
                    AND a.tanggal = x.tanggal
                ) x ON a.barcode = x.barcode
                WHERE (a.barcode LIKE ? 
                    OR namaproduk LIKE ? 
                    OR namabrand LIKE ? 
                    OR namakategori LIKE ? 
                    OR harga LIKE ?)";

        $like = "%{$search}%";
        return $this->db->query($sql, [$like, $like, $like, $like, $like])->getNumRows();
    }

    // public function insertbatchData($data)
    // {
    //     $this->db->transStart();

    //     foreach ($data as $dt) {
    //         $produk = [
    //             'barcode'      => $dt["barcode"],
    //             'namaproduk'   => $dt["namaproduk"],
    //             'namabrand'    => $dt["namabrand"],
    //             'namakategori' => $dt["namakategori"],
    //             'userid'       => $dt["userid"]
    //         ];

    //         $price = [
    //             'barcode' => $dt["barcode"],
    //             'tanggal' => date("Y-m-d H:i:s"),
    //             'harga'   => $dt["harga"],
    //             'diskon'  => $dt["diskon"] ?? 0,
    //             'userid'  => $dt["userid"]
    //         ];

    //         $this->db->table($this->table)->insert($produk);
    //         $this->db->table($this->harga)->insert($price);
    //     }

    //     $this->db->transComplete();

    //     if ($this->db->transStatus() === false) {
    //         return ["code" => 1, "message" => "Transaksi gagal"];
    //     }

    //     return ["code" => 0, "message" => ""];
    // }

    public function insertbatchData($data)
{
    $this->db->transStart();

    foreach ($data as $dt) {
        // Skip jika ini header, biasanya baris pertama berisi nama kolom
        if (isset($dt['barcode']) && strtolower($dt['barcode']) === 'barcode') {
            continue;
        }

        $produk = [
            'barcode'      => $dt["barcode"],
            'namaproduk'   => $dt["namaproduk"],
            'namabrand'    => $dt["namabrand"],
            'namakategori' => $dt["namakategori"],
            'userid'       => $dt["userid"]
        ];

        $price = [
            'barcode' => $dt["barcode"],
            'tanggal' => date("Y-m-d H:i:s"),
            'harga'   => $dt["harga"],
            'diskon'  => $dt["diskon"] ?? 0,
            'userid'  => $dt["userid"]
        ];

        $this->db->table($this->table)->insert($produk);
        $this->db->table($this->harga)->insert($price);
    }

    $this->db->transComplete();

    if ($this->db->transStatus() === false) {
        return ["code" => 1, "message" => "Transaksi gagal"];
    }

    return ["code" => 0, "message" => ""];
}

    // public function setData($data, $barcode)
    // {
    //     $produk = [
    //         'namaproduk'   => $data["namaproduk"],
    //         'namabrand'    => $data["namabrand"],
    //         'namakategori' => $data["namakategori"],
    //         'userid'       => $data["userid"]
    //     ];

    //     $this->db->table($this->table)->where("barcode", $barcode)->update($produk);

    //     // cek harga terakhir
    //     $lastharga = $this->getProduk($barcode);

    //     if (($data["harga"] != $lastharga->harga) || ($data["diskon"] != $lastharga->diskon)) {
    //         $price = [
    //             'barcode' => $barcode,
    //             'tanggal' => date("Y-m-d H:i:s"),
    //             'harga'   => $data["harga"],
    //             'diskon'  => $data["diskon"],
    //             'userid'  => $data["userid"]
    //         ];

    //         $this->db->table($this->hargaTable)->insert($price);
    //     }

    //     return ["code" => 0, "message" => ""];
    // }
    public function setData($data, $barcode)
    {
        $produk = [
            'namaproduk'   => $data["namaproduk"],
            'namabrand'    => $data["namabrand"],
            'namakategori' => $data["namakategori"],
            'userid'       => $data["userid"]
        ];

        $this->db->table($this->table)->where("barcode", $barcode)->update($produk);

        // cek harga terakhir
        $lastharga = $this->getProduk($barcode);

        if (($data["harga"] != $lastharga->harga) || ($data["diskon"] != $lastharga->diskon)) {
            $price = [
                'barcode' => $barcode,
                'tanggal' => date("Y-m-d H:i:s"),
                'harga'   => $data["harga"],
                'diskon'  => $data["diskon"],
                'userid'  => $data["userid"]
            ];

            $this->db->table($this->harga)->insert($price);
        }

        return ["code" => 0, "message" => ""];
    }

}


