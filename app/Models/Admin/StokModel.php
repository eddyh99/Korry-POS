<?php

namespace App\Models\Admin;

use CodeIgniter\Model;

class StokModel extends Model
{
    // Nama tabel
    protected $harga            = 'harga';
    protected $produksize       = 'produksize';
    protected $penjualan        = 'penjualan';
    protected $penjualan_detail = 'penjualan_detail';
    protected $penyesuaian      = 'penyesuaian';
    protected $pindah           = 'pindah';
    protected $pindah_detail    = 'pindah_detail';
    protected $produk           = 'produk';
    protected $store            = 'store';

    /**
     * Ambil data produk berdasarkan barcode
     * Raw SQL
     */
    public function getProduk(string $barcode)
    {
        $sql = "
            SELECT a.*, x.harga
            FROM {$this->produk} a
            INNER JOIN (
                SELECT harga, barcode, MAX(tanggal)
                FROM {$this->harga}
                GROUP BY barcode
            ) x ON a.barcode = x.barcode
            WHERE a.barcode = ?
        ";

        $query = $this->db->query($sql, [$barcode]);
        if ($query) {
            return $query->getRow();
        }
        return $this->db->error();
    }

    /**
     * Ambil stok berdasarkan barcode & size
     * Raw SQL
     */
    public function getStok(string $barcode, string $size)
    {
        $sql = "
            SELECT 
                a.barcode,
                x.size,
                IFNULL(SUM(x.total), 0) AS stok
            FROM {$this->produk} a
            LEFT JOIN (
                SELECT barcode, SUM(jumlah) * -1 AS total, size
                FROM {$this->penjualan_detail}
                GROUP BY barcode, size

                UNION ALL

                SELECT barcode, SUM(jumlah) AS total, size
                FROM {$this->penyesuaian}
                WHERE approved = '1'
                GROUP BY barcode, size
            ) x ON a.barcode = x.barcode
            WHERE a.barcode = ? AND x.size = ?
            GROUP BY a.barcode, x.size
        ";

        $query = $this->db->query($sql, [$barcode, $size]);
        return $query->getRow();
    }

    /**
     * Insert single data ke tabel penyesuaian
     * Query Builder
     */
    public function insertData(array $data)
    {
        $builder = $this->db->table($this->penyesuaian);
        if ($builder->insert($data)) {
            return ["code" => 0, "message" => ""];
        }
        return $this->db->error();
    }

    /**
     * Insert batch data ke tabel penyesuaian
     * Query Builder
     */
    public function insertBatchData(array $data)
    {
        $builder = $this->db->table($this->penyesuaian);
        if ($builder->insertBatch($data)) {
            return ["code" => 0, "message" => ""];
        }
        return $this->db->error();
    }

    // Batas

    public function listproduk_withstok()
{
    $storeid = $_SESSION["logged_status"]["storeid"];

    $sql = "SELECT a.barcode,a.namaproduk, a.namabrand,IFNULL(SUM(x.total),0) AS stok
            FROM {$this->produk} a 
            INNER JOIN {$this->produksize} b ON a.barcode=b.barcode
            LEFT JOIN (
                SELECT barcode, SUM(jumlah)*-1 AS total, storeid 
                FROM penjualan c 
                INNER JOIN penjualan_detail d ON c.id=d.id 
                WHERE storeid='{$storeid}' 
                GROUP BY barcode
                
                UNION ALL
                
                SELECT barcode, SUM(jumlah) AS total, storeid 
                FROM penyesuaian 
                WHERE approved='1' AND storeid='{$storeid}' 
                GROUP BY barcode
                
                UNION ALL

                SELECT barcode, SUM(jumlah)*-1 AS total, dari AS storeid 
                FROM pindah e 
                INNER JOIN pindah_detail f ON e.mutasi_id=f.mutasi_id 
                WHERE e.approved='1' AND dari='{$storeid}' 
                GROUP BY barcode

                UNION ALL
                
                SELECT barcode, SUM(jumlah) AS total, tujuan AS storeid 
                FROM pindah e 
                INNER JOIN pindah_detail f ON e.mutasi_id=f.mutasi_id 
                WHERE e.approved='1' AND tujuan='{$storeid}' 
                GROUP BY barcode
                
                UNION ALL

                SELECT barcode, SUM(jumlah) AS total, storeid 
                FROM retur a 
                INNER JOIN retur_detail b ON a.id=b.id 
                WHERE storeid='{$storeid}' 
                GROUP BY barcode

                UNION ALL
                
                SELECT barcode, SUM(jumlah)*-1 AS total, storeid 
                FROM pinjam a 
                INNER JOIN pinjam_detail b ON a.id=b.id 
                WHERE (ISNULL(kembali) OR status='tidak') 
                AND storeid='{$storeid}' 
                GROUP BY barcode
            ) x ON a.barcode=x.barcode
            GROUP BY a.barcode";

    $hasil = $this->db->query($sql)->getResultArray();

    $mdata = [];
    foreach ($hasil as $dt) {
        if ($dt["stok"] > 0) {
            $mdata[] = [
                "barcode"    => $dt["barcode"],
                "namaproduk" => $dt["namaproduk"],
                "namabrand"  => $dt["namabrand"],
                "stok"       => $dt["stok"]
            ];
        }
    }
    return $mdata;
}

public function allposts_count()
{
    $sql = "SELECT a.barcode,a.namaproduk, a.namabrand,b.size,IFNULL(SUM(x.total),0) AS stok,y.store
            FROM {$this->produk} a 
            INNER JOIN {$this->produksize} b ON a.barcode=b.barcode
            LEFT JOIN (
              SELECT barcode, SUM(jumlah)*-1 AS total,size, storeid 
              FROM {$this->penjualan} c 
              INNER JOIN {$this->penjualan_detail} d ON c.id=d.id 
              GROUP BY barcode,size, storeid
              UNION ALL
              SELECT barcode, SUM(jumlah) AS total, size, storeid 
              FROM {$this->penyesuaian} 
              WHERE approved='1' 
              GROUP BY barcode,size, storeid
              UNION ALL
              SELECT barcode, SUM(jumlah)*-1 AS total, size,dari AS storeid 
              FROM {$this->pindah} e 
              INNER JOIN {$this->pindah_detail} f ON e.mutasi_id=f.mutasi_id 
              WHERE e.approved='1' 
              GROUP BY barcode, size, storeid
              UNION ALL
              SELECT barcode, SUM(jumlah) AS total, size,tujuan AS storeid 
              FROM {$this->pindah} e 
              INNER JOIN {$this->pindah_detail} f ON e.mutasi_id=f.mutasi_id  
              WHERE e.approved='1' 
              GROUP BY barcode, size, storeid
              UNION ALL
              SELECT barcode, SUM(jumlah) AS total,size, storeid 
              FROM retur a 
              INNER JOIN retur_detail b ON a.id=b.id 
              GROUP BY barcode,size, storeid
              UNION ALL
              SELECT barcode, SUM(jumlah)*-1 AS total, size, storeid 
              FROM pinjam a 
              INNER JOIN pinjam_detail b ON a.id=b.id 
              WHERE (ISNULL(kembali) OR status='tidak') 
              GROUP BY barcode,size, storeid 
            ) x ON a.barcode=x.barcode AND b.size=x.size 
            INNER JOIN {$this->store} y ON x.storeid=y.storeid
            WHERE x.storeid IS NOT NULL 
            GROUP BY a.barcode, x.size,x.storeid";

    return $this->db->query($sql)->getNumRows();
}

public function allposts($limit, $start, $col, $dir)
{
    $sql = "SELECT 
                a.barcode,
                a.namaproduk, 
                a.namabrand,
                b.size,
                IFNULL(SUM(x.total),0) AS stok,
                y.store,
                z.harga
            FROM produk a
            INNER JOIN produksize b 
                ON a.barcode = b.barcode
            LEFT JOIN (
                -- penjualan
                SELECT d.barcode, SUM(d.jumlah)*-1 AS total, d.size, c.storeid 
                FROM penjualan c 
                INNER JOIN penjualan_detail d ON c.id = d.id 
                GROUP BY d.barcode, d.size, c.storeid

                UNION ALL
                -- penyesuaian
                SELECT barcode, SUM(jumlah) AS total, size, storeid 
                FROM penyesuaian
                WHERE approved = '1'
                GROUP BY barcode, size, storeid

                UNION ALL
                -- pindah keluar
                SELECT f.barcode, SUM(f.jumlah)*-1 AS total, f.size, e.dari AS storeid
                FROM pindah e 
                INNER JOIN pindah_detail f ON e.mutasi_id = f.mutasi_id
                WHERE e.approved = '1'
                GROUP BY f.barcode, f.size, e.dari

                UNION ALL
                -- pindah masuk
                SELECT f.barcode, SUM(f.jumlah) AS total, f.size, e.tujuan AS storeid
                FROM pindah e 
                INNER JOIN pindah_detail f ON e.mutasi_id = f.mutasi_id
                WHERE e.approved = '1'
                GROUP BY f.barcode, f.size, e.tujuan

                UNION ALL
                -- retur
                SELECT b.barcode, SUM(b.jumlah) AS total, b.size, a.storeid
                FROM retur a
                INNER JOIN retur_detail b ON a.id = b.id
                GROUP BY b.barcode, b.size, a.storeid

                UNION ALL
                -- pinjam
                SELECT b.barcode, SUM(b.jumlah)*-1 AS total, b.size, a.storeid
                FROM pinjam a
                INNER JOIN pinjam_detail b ON a.id = b.id
                WHERE (ISNULL(b.kembali) OR b.status='tidak')
                GROUP BY b.barcode, b.size, a.storeid

                UNION ALL
                -- produksi complete masuk stok
                SELECT pd.barcode, SUM(pd.jumlah) AS total, pd.size, p.storeid
                FROM produksi p
                INNER JOIN produksi_detail pd ON p.nonota = pd.nonota
                WHERE p.is_complete = 1 AND p.status = 0
                GROUP BY pd.barcode, pd.size, p.storeid

                UNION ALL
                -- DO Konsinyasi (barang keluar ke toko konsinyasi)
                SELECT d.barcode, SUM(d.jumlah)*-1 AS total, d.size, a.storeid
                FROM do_konsinyasi a 
                INNER JOIN do_konsinyasi_detail d ON a.nonota = d.nonota
                WHERE a.is_void = 0
                GROUP BY d.barcode, d.size, a.storeid

                UNION ALL
                -- Retur Konsinyasi (barang kembali dari konsinyasi)
                SELECT d.barcode, SUM(d.jumlah) AS total, d.size, a.storeid
                FROM retur_konsinyasi a 
                INNER JOIN retur_konsinyasi_detail d ON a.noretur = d.noretur
                WHERE a.is_void = 0
                GROUP BY d.barcode, d.size, a.storeid

                UNION ALL
                -- Nota Konsinyasi (terjual di toko konsinyasi, keluar stok)
                SELECT barcode, SUM(jumlah)*-1 AS total, size, storeid
                FROM nota_konsinyasi_detail a INNER JOIN nota_konsinyasi b ON a.notajual=b.notajual
                WHERE a.notakonsinyasi IS NULL
                GROUP BY barcode, size, storeid

                UNION ALL
                -- Wholesale order (barang keluar ke wholesale)
                SELECT d.barcode, SUM(d.jumlah)*-1 AS total, d.size, a.storeid
                FROM wholesale_order a 
                INNER JOIN wholesale_order_detail d ON a.notaorder = d.notaorder
                WHERE a.is_void = 0 AND a.is_complete = 1
                GROUP BY d.barcode, d.size, a.storeid
            ) x ON a.barcode = x.barcode AND b.size = x.size
            INNER JOIN store y ON x.storeid = y.storeid
            INNER JOIN (
                SELECT a.harga, a.barcode
                FROM harga a
                INNER JOIN (
                    SELECT MAX(tanggal) AS tanggal, barcode
                    FROM harga
                    GROUP BY barcode
                ) x ON a.barcode = x.barcode AND a.tanggal = x.tanggal
            ) z ON a.barcode = z.barcode
            WHERE x.storeid IS NOT NULL
            GROUP BY a.barcode, x.size, x.storeid 
            ORDER BY {$col} {$dir} 
            LIMIT {$start}, {$limit}";

    return $this->db->query($sql)->getResultArray();
}

public function posts_search($limit, $start, $search, $col, $dir)
{
    $sql = "SELECT 
                a.barcode,
                a.namaproduk, 
                a.namabrand,
                b.size,
                IFNULL(SUM(x.total),0) AS stok,
                y.store,
                z.harga
            FROM produk a
            INNER JOIN produksize b 
                ON a.barcode = b.barcode
            LEFT JOIN (
                -- penjualan
                SELECT d.barcode, SUM(d.jumlah)*-1 AS total, d.size, c.storeid 
                FROM penjualan c 
                INNER JOIN penjualan_detail d ON c.id = d.id 
                GROUP BY d.barcode, d.size, c.storeid

                UNION ALL
                -- penyesuaian
                SELECT barcode, SUM(jumlah) AS total, size, storeid 
                FROM penyesuaian
                WHERE approved = '1'
                GROUP BY barcode, size, storeid

                UNION ALL
                -- pindah keluar
                SELECT f.barcode, SUM(f.jumlah)*-1 AS total, f.size, e.dari AS storeid
                FROM pindah e 
                INNER JOIN pindah_detail f ON e.mutasi_id = f.mutasi_id
                WHERE e.approved = '1'
                GROUP BY f.barcode, f.size, e.dari

                UNION ALL
                -- pindah masuk
                SELECT f.barcode, SUM(f.jumlah) AS total, f.size, e.tujuan AS storeid
                FROM pindah e 
                INNER JOIN pindah_detail f ON e.mutasi_id = f.mutasi_id
                WHERE e.approved = '1'
                GROUP BY f.barcode, f.size, e.tujuan

                UNION ALL
                -- retur
                SELECT b.barcode, SUM(b.jumlah) AS total, b.size, a.storeid
                FROM retur a
                INNER JOIN retur_detail b ON a.id = b.id
                GROUP BY b.barcode, b.size, a.storeid

                UNION ALL
                -- pinjam
                SELECT b.barcode, SUM(b.jumlah)*-1 AS total, b.size, a.storeid
                FROM pinjam a
                INNER JOIN pinjam_detail b ON a.id = b.id
                WHERE (ISNULL(b.kembali) OR b.status='tidak')
                GROUP BY b.barcode, b.size, a.storeid

                UNION ALL
                -- produksi complete masuk stok
                SELECT pd.barcode, SUM(pd.jumlah) AS total, pd.size, p.storeid
                FROM produksi p
                INNER JOIN produksi_detail pd ON p.nonota = pd.nonota
                WHERE p.is_complete = 1 AND p.status = 0
                GROUP BY pd.barcode, pd.size, p.storeid

                UNION ALL
                -- DO Konsinyasi (barang keluar ke toko konsinyasi)
                SELECT d.barcode, SUM(d.jumlah)*-1 AS total, d.size, a.storeid
                FROM do_konsinyasi a 
                INNER JOIN do_konsinyasi_detail d ON a.nonota = d.nonota
                WHERE a.is_void = 0
                GROUP BY d.barcode, d.size, a.storeid

                UNION ALL
                -- Retur Konsinyasi (barang kembali dari konsinyasi)
                SELECT d.barcode, SUM(d.jumlah) AS total, d.size, a.storeid
                FROM retur_konsinyasi a 
                INNER JOIN retur_konsinyasi_detail d ON a.noretur = d.noretur
                WHERE a.is_void = 0
                GROUP BY d.barcode, d.size, a.storeid

                UNION ALL
                -- Nota Konsinyasi (terjual di toko konsinyasi, keluar stok)
                SELECT barcode, SUM(jumlah)*-1 AS total, size, storeid
                FROM nota_konsinyasi_detail a INNER JOIN nota_konsinyasi b ON a.notajual=b.notajual
                WHERE a.notakonsinyasi IS NULL
                GROUP BY barcode, size, storeid

                UNION ALL
                -- Wholesale order (barang keluar ke wholesale)
                SELECT d.barcode, SUM(d.jumlah)*-1 AS total, d.size, a.storeid
                FROM wholesale_order a 
                INNER JOIN wholesale_order_detail d ON a.notaorder = d.notaorder
                WHERE a.is_void = 0 AND a.is_complete = 1
                GROUP BY d.barcode, d.size, a.storeid
            ) x ON a.barcode = x.barcode AND b.size = x.size
            INNER JOIN store y ON x.storeid = y.storeid
            INNER JOIN (
                SELECT a.harga, a.barcode
                FROM harga a
                INNER JOIN (
                    SELECT MAX(tanggal) AS tanggal, barcode
                    FROM harga
                    GROUP BY barcode
                ) x ON a.barcode = x.barcode AND a.tanggal = x.tanggal
            ) z ON a.barcode = z.barcode
            WHERE x.storeid IS NOT NULL
            GROUP BY a.barcode, x.size, x.storeid
            ORDER BY {$col} {$dir} 
            LIMIT {$start}, {$limit}";

    return $this->db->query($sql)->getResultArray();
}

public function posts_search_count($search)
{
    $sql = "SELECT 
                a.barcode,
                a.namaproduk, 
                a.namabrand,
                b.size,
                IFNULL(SUM(x.total),0) AS stok,
                y.store,
                z.harga
            FROM produk a
            INNER JOIN produksize b 
                ON a.barcode = b.barcode
            LEFT JOIN (
                -- penjualan
                SELECT d.barcode, SUM(d.jumlah)*-1 AS total, d.size, c.storeid 
                FROM penjualan c 
                INNER JOIN penjualan_detail d ON c.id = d.id 
                GROUP BY d.barcode, d.size, c.storeid

                UNION ALL
                -- penyesuaian
                SELECT barcode, SUM(jumlah) AS total, size, storeid 
                FROM penyesuaian
                WHERE approved = '1'
                GROUP BY barcode, size, storeid

                UNION ALL
                -- pindah keluar
                SELECT f.barcode, SUM(f.jumlah)*-1 AS total, f.size, e.dari AS storeid
                FROM pindah e 
                INNER JOIN pindah_detail f ON e.mutasi_id = f.mutasi_id
                WHERE e.approved = '1'
                GROUP BY f.barcode, f.size, e.dari

                UNION ALL
                -- pindah masuk
                SELECT f.barcode, SUM(f.jumlah) AS total, f.size, e.tujuan AS storeid
                FROM pindah e 
                INNER JOIN pindah_detail f ON e.mutasi_id = f.mutasi_id
                WHERE e.approved = '1'
                GROUP BY f.barcode, f.size, e.tujuan

                UNION ALL
                -- retur
                SELECT b.barcode, SUM(b.jumlah) AS total, b.size, a.storeid
                FROM retur a
                INNER JOIN retur_detail b ON a.id = b.id
                GROUP BY b.barcode, b.size, a.storeid

                UNION ALL
                -- pinjam
                SELECT b.barcode, SUM(b.jumlah)*-1 AS total, b.size, a.storeid
                FROM pinjam a
                INNER JOIN pinjam_detail b ON a.id = b.id
                WHERE (ISNULL(b.kembali) OR b.status='tidak')
                GROUP BY b.barcode, b.size, a.storeid

                UNION ALL
                -- produksi complete masuk stok
                SELECT pd.barcode, SUM(pd.jumlah) AS total, pd.size, p.storeid
                FROM produksi p
                INNER JOIN produksi_detail pd ON p.nonota = pd.nonota
                WHERE p.is_complete = 1 AND p.status = 0
                GROUP BY pd.barcode, pd.size, p.storeid

                UNION ALL
                -- DO Konsinyasi (barang keluar ke toko konsinyasi)
                SELECT d.barcode, SUM(d.jumlah)*-1 AS total, d.size, a.storeid
                FROM do_konsinyasi a 
                INNER JOIN do_konsinyasi_detail d ON a.nonota = d.nonota
                WHERE a.is_void = 0
                GROUP BY d.barcode, d.size, a.storeid

                UNION ALL
                -- Retur Konsinyasi (barang kembali dari konsinyasi)
                SELECT d.barcode, SUM(d.jumlah) AS total, d.size, a.storeid
                FROM retur_konsinyasi a 
                INNER JOIN retur_konsinyasi_detail d ON a.noretur = d.noretur
                WHERE a.is_void = 0
                GROUP BY d.barcode, d.size, a.storeid

                UNION ALL
                -- Nota Konsinyasi (terjual di toko konsinyasi, keluar stok)
                SELECT barcode, SUM(jumlah)*-1 AS total, size, storeid
                FROM nota_konsinyasi_detail a INNER JOIN nota_konsinyasi b ON a.notajual=b.notajual
                WHERE a.notakonsinyasi IS NULL
                GROUP BY barcode, size, storeid

                UNION ALL
                -- Wholesale order (barang keluar ke wholesale)
                SELECT d.barcode, SUM(d.jumlah)*-1 AS total, d.size, a.storeid
                FROM wholesale_order a 
                INNER JOIN wholesale_order_detail d ON a.notaorder = d.notaorder
                WHERE a.is_void = 0 AND a.is_complete = 1
                GROUP BY d.barcode, d.size, a.storeid
            ) x ON a.barcode = x.barcode AND b.size = x.size
            INNER JOIN store y ON x.storeid = y.storeid
            INNER JOIN (
                SELECT a.harga, a.barcode
                FROM harga a
                INNER JOIN (
                    SELECT MAX(tanggal) AS tanggal, barcode
                    FROM harga
                    GROUP BY barcode
                ) x ON a.barcode = x.barcode AND a.tanggal = x.tanggal
            ) z ON a.barcode = z.barcode
            WHERE x.storeid IS NOT NULL
              AND (a.barcode LIKE '%{$search}%' OR a.namaproduk LIKE '%{$search}%' OR y.store LIKE '%{$search}%') 
            GROUP BY a.barcode, x.size,x.storeid";

    return $this->db->query($sql)->getNumRows();
}

}
