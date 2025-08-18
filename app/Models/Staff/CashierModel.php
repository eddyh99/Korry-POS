<?php

namespace App\Models\Staff;

use CodeIgniter\Model;

class CashierModel extends Model
{
    protected $produksize       = 'produksize';
    protected $penjualan        = 'penjualan';
    protected $penjualan_detail = 'penjualan_detail';
    protected $penyesuaian      = 'penyesuaian';
    protected $pindah           = 'pindah';
    protected $pindah_detail    = 'pindah_detail';
    protected $produk           = 'produk';
    protected $store            = 'store';
    protected $pengguna         = 'pengguna';

    public function allposts_count()
    {
        $sql = "
            SELECT a.barcode,a.namaproduk,a.namabrand,b.size,
                   IFNULL(SUM(x.total),0) AS stok, y.store
            FROM {$this->produk} a
            INNER JOIN {$this->produksize} b ON a.barcode = b.barcode
            LEFT JOIN (
                SELECT barcode, SUM(jumlah) * -1 AS total, size, storeid
                FROM {$this->penjualan} c
                INNER JOIN {$this->penjualan_detail} d ON c.id = d.id
                GROUP BY barcode, size, storeid

                UNION ALL
                SELECT barcode, SUM(jumlah) AS total, size, storeid
                FROM {$this->penyesuaian}
                WHERE approved = '1'
                GROUP BY barcode, size, storeid

                UNION ALL
                SELECT barcode, SUM(jumlah) * -1 AS total, size, dari AS storeid
                FROM {$this->pindah} e
                INNER JOIN {$this->pindah_detail} f ON e.mutasi_id = f.mutasi_id
                WHERE e.approved = '1'
                GROUP BY barcode, size, storeid

                UNION ALL
                SELECT barcode, SUM(jumlah) AS total, size, tujuan AS storeid
                FROM {$this->pindah} e
                INNER JOIN {$this->pindah_detail} f ON e.mutasi_id = f.mutasi_id
                WHERE e.approved = '1'
                GROUP BY barcode, size, storeid
            ) x ON a.barcode = x.barcode AND b.size = x.size
            INNER JOIN {$this->store} y ON x.storeid = y.storeid
            WHERE x.storeid IS NOT NULL
            GROUP BY a.barcode, x.size, x.storeid
        ";

        return $this->db->query($sql)->getNumRows();
    }

    public function allposts($limit, $start, $col, $dir)
    {
        $sql = "
            SELECT a.barcode,a.namaproduk,a.namabrand,b.size,
                   IFNULL(SUM(x.total),0) AS stok, y.store
            FROM {$this->produk} a
            INNER JOIN {$this->produksize} b ON a.barcode = b.barcode
            LEFT JOIN (
                SELECT barcode, SUM(jumlah) * -1 AS total, size, storeid
                FROM {$this->penjualan} c
                INNER JOIN {$this->penjualan_detail} d ON c.id = d.id
                GROUP BY barcode, size, storeid

                UNION ALL
                SELECT barcode, SUM(jumlah) AS total, size, storeid
                FROM {$this->penyesuaian}
                WHERE approved = '1'
                GROUP BY barcode, size, storeid

                UNION ALL
                SELECT barcode, SUM(jumlah) * -1 AS total, size, dari AS storeid
                FROM {$this->pindah} e
                INNER JOIN {$this->pindah_detail} f ON e.mutasi_id = f.mutasi_id
                WHERE e.approved = '1'
                GROUP BY barcode, size, storeid

                UNION ALL
                SELECT barcode, SUM(jumlah) AS total, size, tujuan AS storeid
                FROM {$this->pindah} e
                INNER JOIN {$this->pindah_detail} f ON e.mutasi_id = f.mutasi_id
                WHERE e.approved = '1'
                GROUP BY barcode, size, storeid
            ) x ON a.barcode = x.barcode AND b.size = x.size
            INNER JOIN {$this->store} y ON x.storeid = y.storeid
            CROSS JOIN (SELECT @cnt := 0) AS dummy
            WHERE x.storeid IS NOT NULL
            GROUP BY a.barcode, x.size, x.storeid
            ORDER BY {$col} {$dir}
            LIMIT {$start}, {$limit}
        ";

        return $this->db->query($sql)->getResultArray();
    }

    public function posts_search($limit, $start, $search, $col, $dir)
    {
        $search = $this->db->escapeLikeString($search);

        $sql = "
            SELECT a.barcode,a.namaproduk,a.namabrand,b.size,
                   IFNULL(SUM(x.total),0) AS stok, y.store
            FROM {$this->produk} a
            INNER JOIN {$this->produksize} b ON a.barcode = b.barcode
            LEFT JOIN (
                SELECT barcode, SUM(jumlah) * -1 AS total, size, storeid
                FROM {$this->penjualan} c
                INNER JOIN {$this->penjualan_detail} d ON c.id = d.id
                GROUP BY barcode, size, storeid

                UNION ALL
                SELECT barcode, SUM(jumlah) AS total, size, storeid
                FROM {$this->penyesuaian}
                WHERE approved = '1'
                GROUP BY barcode, size, storeid

                UNION ALL
                SELECT barcode, SUM(jumlah) * -1 AS total, size, dari AS storeid
                FROM {$this->pindah} e
                INNER JOIN {$this->pindah_detail} f ON e.mutasi_id = f.mutasi_id
                WHERE e.approved = '1'
                GROUP BY barcode, size, storeid

                UNION ALL
                SELECT barcode, SUM(jumlah) AS total, size, tujuan AS storeid
                FROM {$this->pindah} e
                INNER JOIN {$this->pindah_detail} f ON e.mutasi_id = f.mutasi_id
                WHERE e.approved = '1'
                GROUP BY barcode, size, storeid
            ) x ON a.barcode = x.barcode AND b.size = x.size
            INNER JOIN {$this->store} y ON x.storeid = y.storeid
            CROSS JOIN (SELECT @cnt := 0) AS dummy
            WHERE x.storeid IS NOT NULL
              AND (a.barcode LIKE '%{$search}%' 
                   OR a.namaproduk LIKE '%{$search}%' 
                   OR y.store LIKE '%{$search}%')
            GROUP BY a.barcode, x.size, x.storeid
            ORDER BY {$col} {$dir}
            LIMIT {$start}, {$limit}
        ";

        return $this->db->query($sql)->getResultArray();
    }

    public function posts_search_count($search)
    {
        $search = $this->db->escapeLikeString($search);

        $sql = "
            SELECT a.barcode,a.namaproduk,a.namabrand,b.size,
                   IFNULL(SUM(x.total),0) AS stok, y.store
            FROM {$this->produk} a
            INNER JOIN {$this->produksize} b ON a.barcode = b.barcode
            LEFT JOIN (
                SELECT barcode, SUM(jumlah) * -1 AS total, size, storeid
                FROM {$this->penjualan} c
                INNER JOIN {$this->penjualan_detail} d ON c.id = d.id
                GROUP BY barcode, size, storeid

                UNION ALL
                SELECT barcode, SUM(jumlah) AS total, size, storeid
                FROM {$this->penyesuaian}
                WHERE approved = '1'
                GROUP BY barcode, size, storeid

                UNION ALL
                SELECT barcode, SUM(jumlah) * -1 AS total, size, dari AS storeid
                FROM {$this->pindah} e
                INNER JOIN {$this->pindah_detail} f ON e.mutasi_id = f.mutasi_id
                WHERE e.approved = '1'
                GROUP BY barcode, size, storeid

                UNION ALL
                SELECT barcode, SUM(jumlah) AS total, size, tujuan AS storeid
                FROM {$this->pindah} e
                INNER JOIN {$this->pindah_detail} f ON e.mutasi_id = f.mutasi_id
                WHERE e.approved = '1'
                GROUP BY barcode, size, storeid
            ) x ON a.barcode = x.barcode AND b.size = x.size
            INNER JOIN {$this->store} y ON x.storeid = y.storeid
            CROSS JOIN (SELECT @cnt := 0) AS dummy
            WHERE x.storeid IS NOT NULL
              AND (a.barcode LIKE '%{$search}%' 
                   OR a.namaproduk LIKE '%{$search}%' 
                   OR y.store LIKE '%{$search}%')
            GROUP BY a.barcode, x.size, x.storeid
        ";

        return $this->db->query($sql)->getNumRows();
    }

// 

    public function readitem($barcode)
    {
        $sql   = "SELECT size FROM {$this->produksize} WHERE barcode=?";
        $query = $this->db->query($sql, [$barcode]);
        return $query->getResult();
    }

    public function getLastnota()
    {
        $sql   = "SELECT RIGHT(RIGHT(nonota,5)+100001,5) as last FROM {$this->penjualan} ORDER BY id DESC";
        $query = $this->db->query($sql);

        if ($query->getNumRows() == 0) {
            $nonota = '00001';
        } else {
            $nonota = $query->getRow()->last;
        }
        return $nonota;
    }

    // public function insertData($jual, $barang)
    // {
    //     $opnameModel = model(\App\Models\Admin\OpnameModel::class);

    //     if ($_SESSION["identify"] == $_SESSION["nota_komplit"]) {
    //         return 611;
    //     }

    //     $this->db->transStart();

    //     // insert ke tabel penjualan
    //     $this->db->table($this->penjualan)->insert($jual);
    //     $id = $this->db->insertID();

    //     // detail penjualan
    //     $detail = [];
    //     foreach ($barang as $dt) {
    //         $temp["id"]      = $id;
    //         $temp["barcode"] = $dt[0];
    //         $temp["size"]    = strtoupper($dt[2]);

    //         $sisa = $opnameModel->getStok($dt[0], $_SESSION["logged_status"]["storeid"], strtoupper($dt[2]));

    //         if ($sisa - $dt[3] < 0) {
    //             $temp["jumlah"] = $sisa;
    //         } elseif ($sisa - $dt[3] >= 0) {
    //             $temp["jumlah"] = $dt[3];
    //         } elseif ($sisa == 0) {
    //             return "511";
    //         }

    //         $temp["diskonn"] = $dt[5];
    //         $temp["diskonp"] = $dt[6];
    //         $temp["alasan"]  = $dt[8];

    //         $detail[] = $temp;
    //     }

    //     // insert ke tabel detail penjualan
    //     $this->db->table($this->penjualan_detail)->insertBatch($detail);

    //     $this->db->transComplete();

    //     $_SESSION["nota_komplit"] = $_SESSION["identify"];
    //     return $id;
    // }
public function insertData($jual, $barang)
{
    $opnameModel = model(\App\Models\Admin\OpnameModel::class);

    if (isset($_SESSION["identify"]) && $_SESSION["identify"] == ($_SESSION["nota_komplit"] ?? null)) {
        return 611;
    }

    $this->db->transStart();

    // insert ke tabel penjualan
    $this->db->table($this->penjualan)->insert($jual);
    $id = $this->db->insertID();

    // detail penjualan
    $detail = [];
    foreach ($barang as $dt) {
        $temp["id"]      = $id;
        $temp["barcode"] = $dt[0] ?? null;
        $temp["size"]    = strtoupper($dt[2] ?? '');

        $sisa = $opnameModel->getStok(
            $dt[0] ?? '',
            $_SESSION["logged_status"]["storeid"] ?? '',
            strtoupper($dt[2] ?? '')
        );

        if ($sisa - ($dt[3] ?? 0) < 0) {
            $temp["jumlah"] = $sisa;
        } elseif ($sisa - ($dt[3] ?? 0) >= 0) {
            $temp["jumlah"] = $dt[3] ?? 0;
        } elseif ($sisa == 0) {
            return 511;
        }

        $temp["diskonn"] = $dt[5] ?? 0;
        $temp["diskonp"] = $dt[6] ?? 0;
        $temp["alasan"]  = $dt[8] ?? '';

        $detail[] = $temp;
    }

    // insert ke tabel detail penjualan
    if (!empty($detail)) {
        $this->db->table($this->penjualan_detail)->insertBatch($detail);
    }

    $this->db->transComplete();

    $_SESSION["nota_komplit"] = $_SESSION["identify"] ?? null;
    return $id;
}

    public function getallnota($id)
    {
        $sql = "SELECT a.*,b.nama 
                FROM {$this->penjualan} a 
                INNER JOIN {$this->pengguna} b ON a.userid=b.username 
                WHERE a.id=?";
        $mdata["header"] = $this->db->query($sql, [$id])->getRow();

        $sql = "SELECT a.*,b.namaproduk 
                FROM {$this->penjualan_detail} a 
                INNER JOIN {$this->produk} b ON a.barcode=b.barcode 
                WHERE a.id=?";
        $detail = $this->db->query($sql, [$id])->getResultArray();

        $i = 0;
        foreach ($detail as $detjual) {
            $mdata["detail"][$i]["barcode"]    = $detjual["barcode"];
            $mdata["detail"][$i]["namaproduk"] = $detjual["namaproduk"];
            $mdata["detail"][$i]["jumlah"]     = $detjual["jumlah"];
            $mdata["detail"][$i]["size"]       = $detjual["size"];
            $mdata["detail"][$i]["diskonn"]    = $detjual["diskonn"];
            $mdata["detail"][$i]["diskonp"]    = $detjual["diskonp"];

            $sql   = "SELECT harga, barcode, tanggal 
                      FROM harga 
                      WHERE tanggal<=? AND barcode=? 
                      ORDER BY tanggal DESC 
                      LIMIT 1";
            $harga = $this->db->query($sql, [$mdata["header"]->tanggal, $detjual["barcode"]])
                              ->getRow()
                              ->harga;

            $mdata["detail"][$i]["harga"] = $harga;
            $i++;
        }

        return $mdata;
    }
}
