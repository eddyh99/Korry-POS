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
    private $produkbahan = 'produk_bahan';
    private $produksize = 'produksize';

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

    public function ListProdukProduksi()
    {
        $sql = "SELECT
                    p.barcode,
                    p.namaproduk,
                    p.namabiayaproduksi,
                    h.harga,
                    h.harga_produksi,
                    CONCAT(
                        '[',
                        GROUP_CONCAT(
                            CONCAT(
                                '{',
                                    '\"idbahan\":', '\"', IFNULL(pb.idbahan,''), '\"', ',',
                                    '\"namabahan\":', '\"', IFNULL(b.namabahan,''), '\"', ',',
                                    '\"jumlah\":', IFNULL(pb.jumlah,0), ',',
                                    '\"satuan\":', '\"', IFNULL(pb.satuan,''), '\"', ',',
                                    '\"stok\":', IFNULL(sb.total_stok,0) - IFNULL(kebutuhan.total_kebutuhan,0),
                                '}'
                            )
                            SEPARATOR ','
                        ),
                        ']'
                    ) AS bahan,
                    GROUP_CONCAT(DISTINCT ps.size ORDER BY ps.size SEPARATOR ',') AS size_available
                FROM produk p
                INNER JOIN (
                    SELECT h1.barcode, h1.harga, h1.harga_produksi
                    FROM harga h1
                    INNER JOIN (
                        SELECT barcode, MAX(tanggal) AS tanggal
                        FROM harga
                        GROUP BY barcode
                    ) h2 ON h1.barcode = h2.barcode AND h1.tanggal = h2.tanggal
                ) h ON p.barcode = h.barcode
                LEFT JOIN produk_bahan pb ON p.barcode = pb.barcode
                LEFT JOIN bahanbaku b ON pb.idbahan = b.id
                LEFT JOIN (
                    SELECT idbahan, SUM(jumlah) AS total_stok
                    FROM stok_bahanbaku
                    GROUP BY idbahan
                ) sb ON pb.idbahan = sb.idbahan
                LEFT JOIN (
                    SELECT pb.idbahan, SUM(pd.jumlah * pb.jumlah) AS total_kebutuhan
                    FROM produksi_detail pd
                    INNER JOIN produk_bahan pb ON pd.barcode = pb.barcode
                    GROUP BY pb.idbahan
                ) kebutuhan ON pb.idbahan = kebutuhan.idbahan
                LEFT JOIN produksize ps ON ps.barcode = p.barcode
                WHERE p.status = '0'
                GROUP BY p.barcode, p.namaproduk, p.namabiayaproduksi, h.harga, h.harga_produksi
                ORDER BY p.barcode;";

        $query = $this->db->query($sql);

        if ($query) {
            $result = $query->getResultArray();

            // parsing string jadi array JSON beneran
            foreach ($result as &$row) {
                if (!empty($row['bahan'])) {
                    $row['bahan'] = json_decode($row['bahan'], true);
                } else {
                    $row['bahan'] = [];
                }
            }

            return $result;
        } else {
            return $this->db->error();
        }
    }
    // public function ListProdukProduksi()
    // {
    //     $sql = "SELECT
    //                 p.barcode,
    //                 p.namaproduk,
    //                 h.harga,
    //                 CONCAT(
    //                     '[', 
    //                     GROUP_CONCAT(
    //                         CONCAT(
    //                             '{',
    //                                 '\"idbahan\":', '\"', IFNULL(pb.idbahan,''), '\"', ',',
    //                                 '\"namabahan\":', '\"', IFNULL(b.namabahan,''), '\"', ',',
    //                                 '\"jumlah\":', IFNULL(pb.jumlah,0), ',',
    //                                 '\"satuan\":', '\"', IFNULL(pb.satuan,''), '\"', ',',
    //                                 '\"stok\":', IFNULL(sb.total_stok,0),
    //                             '}'
    //                         )
    //                         SEPARATOR ','
    //                     ),
    //                     ']'
    //                 ) AS bahan
    //             FROM produk p
    //             INNER JOIN (
    //                 SELECT h1.barcode, h1.harga
    //                 FROM harga h1
    //                 INNER JOIN (
    //                     SELECT barcode, MAX(tanggal) AS tanggal
    //                     FROM harga
    //                     GROUP BY barcode
    //                 ) h2
    //                 ON h1.barcode = h2.barcode
    //                 AND h1.tanggal = h2.tanggal
    //             ) h ON p.barcode = h.barcode
    //             LEFT JOIN produk_bahan pb ON p.barcode = pb.barcode
    //             LEFT JOIN bahanbaku b ON pb.idbahan = b.id
    //             LEFT JOIN (
    //                 SELECT idbahan, SUM(jumlah) AS total_stok
    //                 FROM stok_bahanbaku
    //                 GROUP BY idbahan
    //             ) sb ON pb.idbahan = sb.idbahan
    //             WHERE p.status = '0'
    //             GROUP BY p.barcode, p.namaproduk, h.harga
    //             ORDER BY p.barcode;";

    //     $query = $this->db->query($sql);

    //     if ($query) {
    //         $result = $query->getResultArray();

    //         // parsing string jadi array JSON beneran
    //         foreach ($result as &$row) {
    //             if (!empty($row['bahan'])) {
    //                 $row['bahan'] = json_decode($row['bahan'], true);
    //             } else {
    //                 $row['bahan'] = [];
    //             }
    //         }

    //         return $result;
    //     } else {
    //         return $this->db->error();
    //     }
    // }
    // public function ListProdukProduksi()
    // {
    //     $sql = "SELECT
    //                 p.barcode,
    //                 p.namaproduk,
    //                 h.harga
    //             FROM produk p
    //             INNER JOIN (
    //                 SELECT h1.barcode, h1.harga
    //                 FROM harga h1
    //                 INNER JOIN (
    //                     SELECT barcode, MAX(tanggal) AS tanggal
    //                     FROM harga
    //                     GROUP BY barcode
    //                 ) h2
    //                 ON h1.barcode = h2.barcode
    //                 AND h1.tanggal = h2.tanggal
    //             ) h
    //             ON p.barcode = h.barcode
    //             WHERE p.status = '0'
    //             ORDER BY p.barcode;";

    //     $query = $this->db->query($sql);

    //     if ($query) {
    //         return $query->getResultArray();
    //     } else {
    //         return $this->db->error();
    //     }
    // }

    public function ListProdukOrderWholesale()
    {
        $sql = "SELECT a.*,
                    x.harga,
                    x.harga_konsinyasi,
                    x.harga_wholesale,
                    x.diskon,
                    GROUP_CONCAT(DISTINCT ps.size ORDER BY ps.size SEPARATOR ',') AS size_available
                FROM {$this->produk} a
                INNER JOIN (
                    SELECT h1.barcode, h1.harga, h1.harga_konsinyasi, h1.harga_wholesale, h1.diskon
                    FROM {$this->harga} h1
                    INNER JOIN (
                        SELECT barcode, MAX(tanggal) as tanggal
                        FROM {$this->harga}
                        GROUP BY barcode
                    ) h2 ON h1.barcode = h2.barcode AND h1.tanggal = h2.tanggal
                ) x ON a.barcode = x.barcode
                LEFT JOIN {$this->produksize} ps ON a.barcode = ps.barcode AND ps.status = 0
                WHERE a.status = '0'
                GROUP BY a.barcode";

        $query = $this->db->query($sql);

        if ($query) {
            return $query->getResultArray();
        } else {
            return $this->db->error();
        }
    }


    public function ListProdukDoKonsinyasi()
    {
        $sql = "SELECT
                    p.barcode,
                    p.namaproduk,
                    h.harga_konsinyasi,
                    COALESCE(pd.total_produksi, 0) - COALESCE(dod.total_do, 0) AS total_jumlah
                FROM produk p
                -- harga konsinyasi terbaru per barcode
                INNER JOIN (
                    SELECT h1.barcode, h1.harga_konsinyasi
                    FROM harga h1
                    INNER JOIN (
                        SELECT barcode, MAX(tanggal) AS tanggal
                        FROM harga
                        GROUP BY barcode
                    ) h2
                    ON h1.barcode = h2.barcode
                    AND h1.tanggal = h2.tanggal
                ) h
                ON p.barcode = h.barcode

                -- total produksi per barcode
                LEFT JOIN (
                    SELECT barcode, SUM(jumlah) AS total_produksi
                    FROM produksi_detail
                    GROUP BY barcode
                ) pd
                ON pd.barcode = p.barcode

                -- total yang sudah dibuat DO konsinyasi (non-void) per barcode
                LEFT JOIN (
                    SELECT d.barcode, SUM(d.jumlah) AS total_do
                    FROM do_konsinyasi_detail d
                    INNER JOIN do_konsinyasi o
                        ON o.nonota = d.nonota AND o.is_void = 0
                    GROUP BY d.barcode
                ) dod
                ON dod.barcode = p.barcode

                WHERE p.status = '0'
                HAVING total_jumlah > 0
                ORDER BY p.barcode;";

        $query = $this->db->query($sql);

        if ($query) {
            return $query->getResultArray();
        } else {
            return $this->db->error();
        }
    }
    public function ListProdukNotaKonsinyasi()
    {
        $sql = "SELECT a.*, x.harga, x.harga_konsinyasi, x.harga_wholesale, x.diskon
                FROM {$this->produk} a
                INNER JOIN (
                    SELECT h1.barcode, h1.harga, h1.harga_konsinyasi, h1.harga_wholesale, h1.diskon
                    FROM {$this->harga} h1
                    INNER JOIN (
                        SELECT barcode, MAX(tanggal) as tanggal
                        FROM {$this->harga}
                        GROUP BY barcode
                    ) h2 ON h1.barcode = h2.barcode AND h1.tanggal = h2.tanggal
                ) x ON a.barcode = x.barcode
                WHERE a.status = '0'";

        $query = $this->db->query($sql);

        if ($query) {
            return $query->getResultArray();
        } else {
            return $this->db->error();
        }
    }

    public function getProduk1($barcode)
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
    public function getProduk($barcode)
    {
        $sql = "SELECT a.*, x.harga, x.harga_konsinyasi, x.harga_wholesale, x.harga_produksi, x.diskon
                FROM produk a
                INNER JOIN (
                    SELECT a.harga, a.harga_konsinyasi, a.harga_wholesale, a.harga_produksi, a.diskon, a.barcode
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
            // Tambahan : Fabric & Warna
            'namafabric'   => $data["fabric"],
            'namawarna'    => $data["warna"],

            'namabrand'    => $data["namabrand"],
            'namakategori' => $data["namakategori"],
            'sku'          => $data["sku"],
            'userid'       => $data["userid"],
            // Tambahan : Biaya Produksi
            'namabiayaproduksi' => $data["namabiayaproduksi"]
        ];

        $price = [
            'barcode' => $data["barcode"],
            'tanggal' => date("Y-m-d H:i:s"),
            'harga'   => $data["harga"],
            // Tambahan : Harga Konsinyasi & Wholesale
            'harga_konsinyasi'   => $data["hargakonsinyasi"],
            'harga_wholesale'    => $data["hargawholesale"],
            // Tambahan : Biaya Produksi
            'harga_produksi'    => $data["hargaproduksi"],

            'diskon'  => $data["diskon"] ?? 0,
            'userid'  => $data["userid"]
        ];

        $this->db->transStart();

        $this->db->table($this->produk)->insert($produk);

        $this->db->table($this->harga)->insert($price);

        if (!empty($data["bahanbaku"])) {
            foreach ($data["bahanbaku"] as $i => $idbahan) {
                $jumlah = $data["jumlah"][$i];

                // anggap semua sudah dalam meter/pcs (yard dikonversi di sini jika ada logic tambahan)
                $produkbahan = [
                    'barcode' => $data["barcode"],
                    'idbahan' => $idbahan,
                    'jumlah'  => $jumlah
                    // 'satuan'  => 'meter', // default (kalau mau pcs, bisa diset berdasarkan jenis bahan di DB)
                    // 'userid'  => $data["userid"]
                ];

                $this->db->table($this->produkbahan)->insert($produkbahan);
            }
        }

        $this->db->transComplete();

        if ($this->db->transStatus() === false) {
            $this->db->transRollback();
            return ["code" => 511, "message" => "Data gagal disimpan"];
        } else {
            $this->db->transCommit();
            return ["code" => 0, "message" => "Data berhasil disimpan"];
        }
    }

    public function setData($data, $barcode)
    {
        $produk = [
            'namaproduk'   => $data["namaproduk"],
            'namabrand'    => $data["namabrand"],
            'namakategori' => $data["namakategori"],
            // Tambahan : Fabric & Warna
            'namafabric'   => $data["fabric"],
            'namawarna'    => $data["warna"],
            // Tambahan : Biaya Produksi
            'namabiayaproduksi'    => $data["biayaproduksi"],

            'userid'       => $data["userid"]
        ];

        $this->db->table($this->table)->where("barcode", $barcode)->update($produk);

        // cek harga terakhir
        $lastharga = $this->getProduk($barcode);

        if (($data["harga"] != $lastharga->harga) 
            || ($data["diskon"]          != $lastharga->diskon) 
            || ($data["hargakonsinyasi"] != $lastharga->harga_konsinyasi) 
            || ($data["hargawholesale"]  != $lastharga->harga_wholesale)
            || ($data["hargaproduksi"]   != $lastharga->harga_produksi)) {
            $price = [
                'barcode' => $barcode,
                'tanggal' => date("Y-m-d H:i:s"),
                'harga'   => $data["harga"],
                // Tambahan : Harga Konsinyasi & Wholesale
                'harga_konsinyasi'   => $data["hargakonsinyasi"],
                'harga_wholesale'    => $data["hargawholesale"],
                // Tambahan : Harga Produksi
                'harga_produksi'    => $data["hargaproduksi"],

                'diskon'  => $data["diskon"],
                'userid'  => $data["userid"]
            ];

            $this->db->table($this->harga)->insert($price);
        }

        return ["code" => 0, "message" => ""];
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
        $sql = "SELECT a.*, x.harga, x.harga_konsinyasi, x.harga_wholesale, x.diskon
                FROM {$this->produk} a
                INNER JOIN (
                    SELECT a.harga, a.harga_konsinyasi, a.harga_wholesale, a.barcode, a.diskon
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

// Update Produk (termasuk Multiple Input Bahan Baku)

    // public function getProdukBahan($barcode)
    // {
    //     return $this->db->table('produk_bahan pb')
    //         ->select('pb.idbahan, pb.jumlah, b.namabahan')
    //         ->join('bahanbaku b', 'b.id = pb.idbahan', 'left')
    //         ->where('pb.barcode', $barcode)
    //         ->get()
    //         ->getResult();
    // }
    public function getProdukBahan($barcode)
    {
        $sql = "SELECT pb.idbahan, pb.jumlah, b.namabahan
                FROM produk_bahan pb
                LEFT JOIN bahanbaku b ON b.id = pb.idbahan
                WHERE pb.barcode = ?";

        return $this->db->query($sql, [$barcode])->getResult();
    }


    public function setProdukBahan($barcode, $idbahanArr, $jumlahArr)
    {
        // hapus dulu bahan lama
        $this->db->table('produk_bahan')->where('barcode', $barcode)->delete();

        if (!empty($idbahanArr) && is_array($idbahanArr)) {
            foreach ($idbahanArr as $idx => $idbahan) {
                if (!empty($idbahan) && !empty($jumlahArr[$idx])) {
                    // konversi yard ke meter
                    $jumlah = $jumlahArr[$idx];
                    if (isset($_POST['satuan'][$idx]) && $_POST['satuan'][$idx] == 'yard') {
                        $jumlah = $jumlah * 0.9144;
                    }

                    $this->db->table('produk_bahan')->insert([
                        'barcode' => $barcode,
                        'idbahan' => $idbahan,
                        'jumlah'  => $jumlah
                    ]);
                }
            }
        }
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

    // public function insertData($data)
    // {
    //     $produk = [
    //         'barcode'      => $data["barcode"],
    //         'namaproduk'   => $data["namaproduk"],
    //         'namabrand'    => $data["namabrand"],
    //         'namakategori' => $data["namakategori"],
    //         'userid'       => $data["userid"]
    //     ];

    //     $price = [
    //         'barcode' => $data["barcode"],
    //         'tanggal' => date("Y-m-d H:i:s"),
    //         'harga'   => $data["harga"],
    //         'diskon'  => $data["diskon"] ?? 0,
    //         'userid'  => $data["userid"]
    //     ];

    //     $this->db->transStart();

    //     // Insert produk
    //     $this->db->table($this->produk)->insert($produk);

    //     // Insert harga
    //     $this->db->table($this->harga)->insert($price);

    //     $this->db->transComplete();

    //     if ($this->db->transStatus() === false) {
    //         $this->db->transRollback();
    //         return ["code" => 511, "message" => "Data sudah pernah digunakan"];
    //     } else {
    //         $this->db->transCommit();
    //         return ["code" => 0, "message" => ""];
    //     }
    // }
}


