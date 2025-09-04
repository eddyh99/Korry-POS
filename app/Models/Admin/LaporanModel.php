<?php

namespace App\Models\Admin;

use CodeIgniter\Model;

class LaporanModel extends Model
{
    protected $DBGroup = 'default';

    private $brand            = 'brand';
    private $harga            = 'harga';
    private $produksize       = 'produksize';
    private $penjualan        = 'penjualan';
    private $penjualan_detail = 'penjualan_detail';
    private $penyesuaian      = 'penyesuaian';
    private $pindah           = 'pindah';
    private $pindah_detail    = 'pindah_detail';
    private $produk           = 'produk';
    private $store            = 'store';
    private $kas              = 'kas';
    private $pengeluaran      = 'pengeluaran';

    // Mutasi
    public function getmutasi($bulan, $tahun, $storeid, $brand, $kategori)
    {
        $awal  = $tahun . "-" . $bulan . "-01";
        $akhir = date("Y-m-t", strtotime($awal));

        // SQL 1 - Stok Awal
        $sql = "SELECT b.barcode,a.namaproduk,a.namabrand, IFNULL(SUM(x.total),0) AS stok,b.size
                FROM {$this->produk} a
                INNER JOIN {$this->produksize} b ON a.barcode=b.barcode
                LEFT JOIN (
                    SELECT barcode, SUM(jumlah)*-1 AS total, storeid,size
                    FROM {$this->penjualan} c
                    INNER JOIN {$this->penjualan_detail} d ON c.id=d.id
                    WHERE DATE(tanggal)<'{$awal}' 
                    AND IF ('{$storeid}'!='All',storeid,'All')='{$storeid}'
                    GROUP BY barcode
                    UNION ALL
                    SELECT barcode, SUM(jumlah) AS total, storeid,size
                    FROM {$this->penyesuaian}
                    WHERE approved='1' AND tanggal<'{$awal}' 
                    AND IF ('{$storeid}'!='All',storeid,'All')='{$storeid}'
                    GROUP BY barcode
                    UNION ALL
                    SELECT barcode, SUM(jumlah)*-1 AS total,dari AS storeid,size
                    FROM {$this->pindah} e
                    INNER JOIN {$this->pindah_detail} f ON e.mutasi_id=f.mutasi_id
                    WHERE e.approved='1' AND DATE(tanggal)<'{$awal}' 
                    AND IF ('{$storeid}'!='All',dari,'All')='{$storeid}'
                    GROUP BY barcode
                    UNION ALL
                    SELECT barcode, SUM(jumlah) AS total,tujuan AS storeid,size
                    FROM {$this->pindah} e
                    INNER JOIN {$this->pindah_detail} f ON e.mutasi_id=f.mutasi_id
                    WHERE e.approved='1' AND DATE(tanggal)<'{$awal}' 
                    AND IF ('{$storeid}'!='All',tujuan,'All')='{$storeid}'
                    GROUP BY barcode
                    UNION ALL
                    SELECT barcode, SUM(jumlah) AS total, storeid,size
                    FROM retur a
                    INNER JOIN retur_detail b ON a.id=b.id
                    WHERE DATE(tanggal)<'{$awal}' 
                    AND IF ('{$storeid}'!='All',storeid,'All')='{$storeid}'
                    GROUP BY barcode
                    UNION ALL
                    SELECT barcode, SUM(jumlah)*-1 AS total, storeid,size
                    FROM pinjam a
                    INNER JOIN pinjam_detail b ON a.id=b.id
                    WHERE (ISNULL(kembali) OR status='tidak') 
                    AND DATE(tanggal)<'{$awal}' 
                    AND IF ('{$storeid}'!='All',storeid,'All')='{$storeid}'
                    GROUP BY barcode
                ) x ON b.barcode=x.barcode AND b.size=x.size
                WHERE a.status='0'
                AND IF ('{$kategori}'!='All',namakategori,'All')='{$kategori}'
                AND IF ('{$brand}'!='All',namabrand,'All')='{$brand}'
                GROUP BY b.barcode";

        $stokawal = $this->db->query($sql)->getResultArray();

        // SQL 2 - Keluar
        $sql2 = "SELECT x.barcode, SUM(x.total) AS total FROM (
                    SELECT barcode, SUM(jumlah) AS total
                    FROM {$this->pindah} e
                    INNER JOIN {$this->pindah_detail} f ON e.mutasi_id=f.mutasi_id
                    WHERE e.approved='1' 
                    AND (DATE(tanggal) BETWEEN '{$awal}' AND '{$akhir}')
                    AND IF ('{$storeid}'!='All',dari,'All')='{$storeid}'
                    GROUP BY barcode
                    UNION ALL
                    SELECT barcode, SUM(jumlah) AS total
                    FROM pinjam a
                    INNER JOIN pinjam_detail b ON a.id=b.id
                    WHERE (ISNULL(kembali) OR status='tidak') 
                    AND (DATE(tanggal) BETWEEN '{$awal}' AND '{$akhir}')
                    AND IF ('{$storeid}'!='All',storeid,'All')='{$storeid}'
                    GROUP BY barcode
                ) x GROUP BY x.barcode";
        $keluar = $this->db->query($sql2)->getResultArray();

        // SQL 3 - Masuk
        $sql3 = "SELECT x.barcode, SUM(x.total) AS total FROM (
                    SELECT barcode, SUM(jumlah) AS total
                    FROM pindah e
                    INNER JOIN pindah_detail f ON e.mutasi_id=f.mutasi_id
                    WHERE e.approved='1' 
                    AND (DATE(tanggal) BETWEEN '{$awal}' AND '{$akhir}')
                    AND IF ('{$storeid}'!='All',tujuan,'All')='{$storeid}'
                    GROUP BY barcode
                    UNION ALL
                    SELECT barcode, SUM(jumlah) AS total
                    FROM retur a
                    INNER JOIN retur_detail b ON a.id=b.id
                    WHERE (DATE(tanggal) BETWEEN '{$awal}' AND '{$akhir}')
                    AND IF ('{$storeid}'!='All',storeid,'All')='{$storeid}'
                    GROUP BY barcode
                ) x GROUP BY x.barcode";
        $masuk = $this->db->query($sql3)->getResultArray();

        // SQL 4 - Penyesuaian
        $sql4 = "SELECT barcode, SUM(jumlah) AS total, storeid
                FROM {$this->penyesuaian}
                WHERE approved='1' 
                AND (tanggal BETWEEN '{$awal}' AND '{$akhir}')
                AND IF ('{$storeid}'!='All',storeid,'All')='{$storeid}'
                GROUP BY barcode";
        $sesuai = $this->db->query($sql4)->getResultArray();

        // SQL 5 - Penjualan
        $sql5 = "SELECT barcode, SUM(jumlah) AS total
                FROM {$this->penjualan} c
                INNER JOIN {$this->penjualan_detail} d ON c.id=d.id
                WHERE (DATE(tanggal) BETWEEN '{$awal}' AND '{$akhir}')
                AND IF ('{$storeid}'!='All',storeid,'All')='{$storeid}'
                GROUP BY barcode";
        $penjualan = $this->db->query($sql5)->getResultArray();

        // Gabung data
        $mdata = [];
        foreach ($stokawal as $dt) {
            $temp['namaproduk'] = $dt['namaproduk'];
            $temp['namabrand']  = $dt['namabrand'];

            // Ambil harga terakhir
            $sqlharga = "SELECT a.harga
                        FROM harga a
                        INNER JOIN (
                            SELECT MAX(tanggal) AS tanggal, barcode
                            FROM harga GROUP BY barcode
                        ) x ON a.barcode=x.barcode AND a.tanggal=x.tanggal
                        WHERE a.barcode='{$dt['barcode']}'";
            $temp['harga'] = $this->db->query($sqlharga)->getRow()->harga ?? 0;

            $temp['awal'] = $dt['stok'];

            $temp['jual'] = 0;
            foreach ($penjualan as $jl) {
                if ($dt['barcode'] == $jl['barcode']) {
                    $temp['jual'] = $jl['total'];
                }
            }

            $temp['keluar'] = 0;
            foreach ($keluar as $klr) {
                if ($dt['barcode'] == $klr['barcode']) {
                    $temp['keluar'] = $klr['total'];
                }
            }

            $temp['masuk'] = 0;
            foreach ($masuk as $msk) {
                if ($dt['barcode'] == $msk['barcode']) {
                    $temp['masuk'] = $msk['total'];
                }
            }

            $temp['sesuai'] = 0;
            foreach ($sesuai as $suai) {
                if ($dt['barcode'] == $suai['barcode']) {
                    $temp['sesuai'] = $suai['total'];
                }
            }

            $temp['sisa'] = $temp['awal'] + $temp['masuk'] - $temp['keluar'] - $temp['jual'] + $temp['sesuai'];

            $mdata[] = $temp;
        }

        return $mdata;
    }

    // Mutasi Detail
    public function getmutasidetail($bulan, $tahun, $storeid, $brand, $kategori)
    {
        $awal  = $tahun . "-" . $bulan . "-01";
        $akhir = date("Y-m-t", strtotime($awal));

        // 1. Stok Awal
        $sql = "
            SELECT b.barcode, a.namaproduk, a.namabrand, IFNULL(SUM(x.total), 0) AS stok, b.size
            FROM produk a
            INNER JOIN produksize b ON a.barcode = b.barcode
            LEFT JOIN (
                SELECT barcode, SUM(jumlah)*-1 AS total, storeid, size
                FROM penjualan c
                INNER JOIN penjualan_detail d ON c.id = d.id
                WHERE DATE(tanggal) < :awal:
                AND (:storeid: = 'All' OR storeid = :storeid:)
                GROUP BY barcode, size

                UNION ALL

                SELECT barcode, SUM(jumlah) AS total, storeid, size
                FROM penyesuaian
                WHERE approved = '1'
                AND tanggal < :awal:
                AND (:storeid: = 'All' OR storeid = :storeid:)
                GROUP BY barcode, size

                UNION ALL

                SELECT barcode, SUM(jumlah)*-1 AS total, dari AS storeid, size
                FROM pindah e
                INNER JOIN pindah_detail f ON e.mutasi_id = f.mutasi_id
                WHERE e.approved = '1'
                AND DATE(tanggal) < :awal:
                AND (:storeid: = 'All' OR dari = :storeid:)
                GROUP BY barcode, size

                UNION ALL

                SELECT barcode, SUM(jumlah) AS total, tujuan AS storeid, size
                FROM pindah e
                INNER JOIN pindah_detail f ON e.mutasi_id = f.mutasi_id
                WHERE e.approved = '1'
                AND DATE(tanggal) < :awal:
                AND (:storeid: = 'All' OR tujuan = :storeid:)
                GROUP BY barcode, size

                UNION ALL

                SELECT barcode, SUM(jumlah) AS total, storeid, size
                FROM retur a
                INNER JOIN retur_detail b ON a.id = b.id
                WHERE DATE(tanggal) < :awal:
                AND (:storeid: = 'All' OR storeid = :storeid:)
                GROUP BY barcode, size

                UNION ALL

                SELECT barcode, SUM(jumlah)*-1 AS total, storeid, size
                FROM pinjam a
                INNER JOIN pinjam_detail b ON a.id = b.id
                WHERE (ISNULL(kembali) OR status = 'tidak')
                AND DATE(tanggal) < :awal:
                AND (:storeid: = 'All' OR storeid = :storeid:)
                GROUP BY barcode, size
            ) x ON b.barcode = x.barcode AND b.size = x.size
            WHERE a.status = '0'
            AND (:kategori: = 'All' OR namakategori = :kategori:)
            AND (:brand: = 'All' OR namabrand = :brand:)
            GROUP BY b.barcode, b.size
        ";
        $stokAwal = $this->db->query($sql, [
            'awal' => $awal,
            'storeid' => $storeid,
            'kategori' => $kategori,
            'brand' => $brand
        ])->getResultArray();

        // 2. Data Keluar
        $sql2 = "
            SELECT x.barcode, SUM(x.total) AS total, size
            FROM (
                SELECT barcode, SUM(jumlah) AS total, size
                FROM pindah e
                INNER JOIN pindah_detail f ON e.mutasi_id = f.mutasi_id
                WHERE e.approved = '1'
                AND (DATE(tanggal) BETWEEN :awal: AND :akhir:)
                AND (:storeid: = 'All' OR dari = :storeid:)
                GROUP BY barcode, size

                UNION ALL

                SELECT barcode, SUM(jumlah) AS total, size
                FROM pinjam a
                INNER JOIN pinjam_detail b ON a.id = b.id
                WHERE (ISNULL(kembali) OR status = 'tidak')
                AND (DATE(tanggal) BETWEEN :awal: AND :akhir:)
                AND (:storeid: = 'All' OR storeid = :storeid:)
                GROUP BY barcode, size
            ) x
            GROUP BY x.barcode, x.size
        ";
        $keluar = $this->db->query($sql2, [
            'awal' => $awal,
            'akhir' => $akhir,
            'storeid' => $storeid
        ])->getResultArray();

        // 3. Data Masuk
        $sql3 = "
            SELECT x.barcode, SUM(x.total) AS total, size
            FROM (
                SELECT barcode, SUM(jumlah) AS total, size
                FROM pindah e
                INNER JOIN pindah_detail f ON e.mutasi_id = f.mutasi_id
                WHERE e.approved = '1'
                AND (DATE(tanggal) BETWEEN :awal: AND :akhir:)
                AND (:storeid: = 'All' OR tujuan = :storeid:)
                GROUP BY barcode, size

                UNION ALL

                SELECT barcode, SUM(jumlah) AS total, size
                FROM retur a
                INNER JOIN retur_detail b ON a.id = b.id
                WHERE (DATE(tanggal) BETWEEN :awal: AND :akhir:)
                AND (:storeid: = 'All' OR storeid = :storeid:)
                GROUP BY barcode, size
            ) x
            GROUP BY x.barcode, x.size
        ";
        $masuk = $this->db->query($sql3, [
            'awal' => $awal,
            'akhir' => $akhir,
            'storeid' => $storeid
        ])->getResultArray();

        // 4. Data Penyesuaian
        $sql4 = "
            SELECT barcode, SUM(jumlah) AS total, storeid, size
            FROM penyesuaian
            WHERE approved = '1'
            AND (tanggal BETWEEN :awal: AND :akhir:)
            AND (:storeid: = 'All' OR storeid = :storeid:)
            GROUP BY barcode, size
        ";
        $sesuai = $this->db->query($sql4, [
            'awal' => $awal,
            'akhir' => $akhir,
            'storeid' => $storeid
        ])->getResultArray();

        // 5. Data Penjualan
        $sql5 = "
            SELECT barcode, SUM(jumlah) AS total, size
            FROM penjualan c
            INNER JOIN penjualan_detail d ON c.id = d.id
            WHERE (DATE(tanggal) BETWEEN :awal: AND :akhir:)
            AND (:storeid: = 'All' OR storeid = :storeid:)
            GROUP BY barcode, size
        ";
        $penjualan = $this->db->query($sql5, [
            'awal' => $awal,
            'akhir' => $akhir,
            'storeid' => $storeid
        ])->getResultArray();

        // Gabung semua data seperti di CI3
        $mdata = [];
        foreach ($stokAwal as $dt) {
            $temp = [
                "namaproduk" => $dt["namaproduk"],
                "namabrand" => $dt["namabrand"],
                "awal"      => $dt["stok"],
                "size"      => $dt["size"],
                "keluar"    => 0,
                "jual"      => 0,
                "masuk"     => 0,
                "sesuai"    => 0
            ];

            foreach ($keluar as $klr) {
                if ($dt["barcode"] == $klr["barcode"] && $dt["size"] == $klr["size"]) {
                    $temp["keluar"] = $klr["total"];
                }
            }

            foreach ($penjualan as $jl) {
                if ($dt["barcode"] == $jl["barcode"] && $dt["size"] == $jl["size"]) {
                    $temp["jual"] = $jl["total"];
                }
            }

            foreach ($masuk as $msk) {
                if ($dt["barcode"] == $msk["barcode"] && $dt["size"] == $msk["size"]) {
                    $temp["masuk"] = $msk["total"];
                }
            }

            foreach ($sesuai as $suai) {
                if ($dt["barcode"] == $suai["barcode"] && $dt["size"] == $suai["size"]) {
                    $temp["sesuai"] = $suai["total"];
                }
            }

            $temp["sisa"] = $temp["awal"] + $temp["masuk"] - $temp["keluar"] - $temp["jual"] + $temp["sesuai"];
            $mdata[] = $temp;
        }

        return $mdata;
    }

    // Penjualan
    public function getpenjualan($awal, $akhir, $storeid)
    {
        $sql = "
            SELECT a.*, c.nama AS kasir, d.nama AS member 
            FROM {$this->penjualan} a
            INNER JOIN pengguna c ON a.userid = c.username
            LEFT JOIN member d ON a.member_id = d.member_id
            WHERE DATE(a.tanggal) BETWEEN ? AND ?
            AND IF(? != 'All', storeid, 'All') = ?
            AND a.id NOT IN (SELECT jual_id FROM retur)
        ";

        $penjualan = $this->db->query($sql, [$awal, $akhir, $storeid, $storeid])->getResultArray();

        $mdata = [];
        foreach ($penjualan as $dt) {
            $temp = [
                "id"      => $dt["id"],
                "nonota"  => $dt["nonota"],
                "tanggal" => $dt["tanggal"],
                "member"  => $dt["member"],
                "kasir"   => $dt["kasir"],
                "method"  => $dt["method"],
                "diskonn" => 0,
                "diskonp" => 0,
                "total"   => 0
            ];

            // Ambil detail penjualan
            $dsql = "SELECT * FROM {$this->penjualan_detail} WHERE id = ?";
            $detail = $this->db->query($dsql, [$dt["id"]])->getResultArray();

            foreach ($detail as $det) {
                $temp["diskonn"] += $det["diskonn"];
                $temp["diskonp"] += $det["diskonp"];

                // Ambil harga terbaru sebelum tanggal transaksi
                $sqlHarga = "
                    SELECT harga 
                    FROM {$this->harga} 
                    WHERE tanggal <= ? AND barcode = ?
                    ORDER BY tanggal DESC 
                    LIMIT 1
                ";
                $hargaRow = $this->db->query($sqlHarga, [$dt["tanggal"], $det["barcode"]])->getRow();

                $harga = $hargaRow ? $hargaRow->harga : 0;
                $temp["total"] += ($det["jumlah"] * $harga) - $det["diskonn"] - $det["diskonp"];
            }

            $mdata[] = $temp;
        }

        return $mdata;
    }

    // DETAIL Penjualan
    public function detailpenjualan($id)
    {
        $builder = $this->db->query("
            SELECT a.nonota, a.tanggal, b.*, c.namaproduk, c.namabrand 
            FROM {$this->penjualan} a 
            INNER JOIN {$this->penjualan_detail} b ON a.id = b.id 
            INNER JOIN {$this->produk} c ON b.barcode = c.barcode 
            WHERE a.id = ?", [$id]);

        $detail = $builder->getResultArray();

        $mdata = [];
        foreach ($detail as $det) {
            $temp["nonota"]     = $det["nonota"];
            $temp["barcode"]    = $det["barcode"];
            $temp["namaproduk"] = $det["namaproduk"];
            $temp["namabrand"]  = $det["namabrand"];
            $temp["size"]       = $det["size"];
            $temp["jumlah"]     = $det["jumlah"];
            $temp["diskonn"]    = $det["diskonn"];
            $temp["diskonp"]    = $det["diskonp"];
            $temp["alasan"]     = $det["alasan"];

            $hargaRow = $this->db->query("
                SELECT harga 
                FROM {$this->harga} 
                WHERE tanggal <= ? AND barcode = ? 
                ORDER BY tanggal DESC 
                LIMIT 1", [$det["tanggal"], $det["barcode"]])
                ->getRow();

            $harga = $hargaRow ? $hargaRow->harga : 0;

            $temp["harga"] = $harga;
            $temp["total"] = ($det["jumlah"] * $harga) - $det["diskonn"] - $det["diskonp"];

            $mdata[] = $temp;
        }

        return $mdata;
    }

    // Brand
    public function getbrand($awal, $akhir, $storeid, $brand, $kategori)
    {
        $dsql = "SELECT a.nonota, a.tanggal, b.*, c.namaproduk, c.namabrand
                FROM {$this->penjualan} a
                INNER JOIN {$this->penjualan_detail} b ON a.id = b.id
                INNER JOIN {$this->produk} c ON b.barcode = c.barcode
                WHERE a.id NOT IN (SELECT jual_id FROM retur)
                AND DATE(a.tanggal) BETWEEN ? AND ?
                AND IF (? != 'All', c.namabrand, 'All') = ?
                AND IF (? != 'All', storeid, 'All') = ?
                AND IF (? != 'All', c.namakategori, 'All') = ?";

        $detail = $this->db->query($dsql, [
            $awal, $akhir,
            $brand, $brand,
            $storeid, $storeid,
            $kategori, $kategori
        ])->getResultArray();

        $mdata = [];
        foreach ($detail as $det) {
            $temp = [
                "nonota"     => $det["nonota"],
                "tanggal"    => $det["tanggal"],
                "barcode"    => $det["barcode"],
                "namaproduk" => $det["namaproduk"],
                "namabrand"  => $det["namabrand"],
                "size"       => $det["size"],
                "jumlah"     => $det["jumlah"],
            ];

            $sql = "SELECT harga
                    FROM {$this->harga}
                    WHERE tanggal <= ? AND barcode = ?
                    ORDER BY tanggal DESC
                    LIMIT 1";

            $harga = $this->db->query($sql, [$det["tanggal"], $det["barcode"]])->getRow()->harga ?? 0;

            $temp["harga"] = $harga;
            $temp["total"] = ($det["jumlah"] * $harga) - $det["diskonn"] - $det["diskonp"];

            $mdata[] = $temp;
        }

        return $mdata;
    }

    // Barang
    public function getBarang($awal, $akhir, $storeid, $jenis)
    {
        if ($jenis == "keluar") {
            $sql = "
                SELECT a.mutasi_id, a.tanggal, b.barcode, b.size, b.jumlah, c.namaproduk, store 
                FROM pindah a 
                INNER JOIN pindah_detail b ON a.mutasi_id = b.mutasi_id
                INNER JOIN produk c ON b.barcode = c.barcode
                INNER JOIN store d ON a.tujuan = d.storeid
                WHERE DATE(a.tanggal) BETWEEN ? AND ?
                AND dari = ? AND a.approved = '1'
                
                UNION ALL
                
                SELECT a.id AS mutasi_id, a.tanggal, b.barcode, b.size, b.jumlah, c.namaproduk, a.keterangan AS store
                FROM pinjam a 
                INNER JOIN pinjam_detail b ON a.id = b.id 
                INNER JOIN produk c ON b.barcode = c.barcode
                INNER JOIN store d ON a.storeid = d.storeid
                WHERE (ISNULL(b.kembali) OR b.status = 'tidak') 
                AND (DATE(tanggal) BETWEEN ? AND ?) 
                AND a.storeid = ?
            ";

            $params = [$awal, $akhir, $storeid, $awal, $akhir, $storeid];
        } else {
            $sql = "
                SELECT a.mutasi_id, a.tanggal, b.barcode, b.size, b.jumlah, c.namaproduk, store 
                FROM pindah a 
                INNER JOIN pindah_detail b ON a.mutasi_id = b.mutasi_id
                INNER JOIN produk c ON b.barcode = c.barcode
                INNER JOIN store d ON a.dari = d.storeid
                WHERE DATE(a.tanggal) BETWEEN ? AND ?
                AND tujuan = ? AND a.approved = '1'
            ";

            $params = [$awal, $akhir, $storeid];
        }

        return $this->db->query($sql, $params)->getResultArray();
    }

    // Non-Tunai
    public function getnontunai($awal, $akhir, $storeid)
    {
        $builder = $this->db->table($this->penjualan);
        $builder->where("DATE(tanggal) >=", $awal);
        $builder->where("DATE(tanggal) <=", $akhir);
        $builder->where("storeid", $storeid);
        $builder->where("method !=", 'cash');
        $penjualan = $builder->get()->getResultArray();

        $mdata = [];

        foreach ($penjualan as $dt) {
            $temp = [
                "id"       => $dt["id"],
                "nonota"   => $dt["nonota"],
                "tanggal"  => $dt["tanggal"],
                "method"   => $dt["method"],
                "persen"   => $dt["fee"],
            ];

            // Ambil detail penjualan
            $detail = $this->db->table($this->penjualan_detail)
                ->where("id", $dt["id"])
                ->get()
                ->getResultArray();

            $temp["total"] = 0;
            foreach ($detail as $det) {
                // Ambil harga terbaru sebelum/tanggal transaksi
                $hargaRow = $this->db->table($this->harga)
                    ->where("tanggal <=", $dt["tanggal"])
                    ->where("barcode", $det["barcode"])
                    ->orderBy("tanggal", "DESC")
                    ->limit(1)
                    ->get()
                    ->getRow();

                $harga = $hargaRow ? $hargaRow->harga : 0;

                $temp["total"] += ($det["jumlah"] * $harga) - $det["diskonn"] - $det["diskonp"];
            }

            $temp["fee"]       = $dt["fee"] / 100 * $temp["total"];
            $temp["grandttl"]  = $temp["total"] + $temp["fee"];

            $mdata[] = $temp;
        }

        return $mdata;
    }

    // Permintaan
    public function getrequest($awal, $akhir, $storeid, $jenis)
    {
        if ($jenis == "keluar") {
            $sql = "
                SELECT a.mutasi_id, a.tanggal, b.barcode, b.size, b.jumlah, 
                    c.namaproduk, store, 
                    IF(a.approved=1,'Diterima', 
                        IF(a.approved=2, 'Batal', 
                            IF(a.approved=3,'Dikirim','Belum Dikirim')
                        )
                    ) as status 
                FROM {$this->pindah} a
                INNER JOIN {$this->pindah_detail} b ON a.mutasi_id=b.mutasi_id
                INNER JOIN {$this->produk} c ON b.barcode=c.barcode
                INNER JOIN {$this->store} d ON a.tujuan=d.storeid
                WHERE DATE(a.tanggal) BETWEEN ? AND ?
                AND dari=?
            ";
            $params = [$awal, $akhir, $storeid];
        } else {
            $sql = "
                SELECT a.mutasi_id, a.tanggal, b.barcode, b.size, b.jumlah, 
                    c.namaproduk, store, 
                    IF(a.approved=1,'Diterima', 
                        IF(a.approved=2, 'Batal', 
                            IF(a.approved=3,'Dikirim','Belum Dikirim')
                        )
                    ) as status 
                FROM {$this->pindah} a
                INNER JOIN {$this->pindah_detail} b ON a.mutasi_id=b.mutasi_id
                INNER JOIN {$this->produk} c ON b.barcode=c.barcode
                INNER JOIN {$this->store} d ON a.dari=d.storeid
                WHERE DATE(a.tanggal) BETWEEN ? AND ?
                AND tujuan=? 
                AND a.approved='1'
            ";
            $params = [$awal, $akhir, $storeid];
        }

        return $this->db->query($sql, $params)->getResultArray();
    }

    // Retur
    public function getretur($awal, $akhir, $storeid)
    {
        $sql = "
            SELECT a.id, a.tanggal, d.tanggal as tgljual, a.jual_id, b.*, c.namaproduk, c.namabrand
            FROM retur a
            INNER JOIN retur_detail b ON a.id = b.id
            INNER JOIN produk c ON b.barcode = c.barcode
            INNER JOIN penjualan d ON a.jual_id = d.id
            WHERE DATE(a.tanggal) BETWEEN ? AND ?
            AND IF(? != 'All', a.storeid, 'All') = ?
        ";

        $detail = $this->db->query($sql, [$awal, $akhir, $storeid, $storeid])->getResultArray();

        $mdata = [];
        foreach ($detail as $det) {
            $temp["id"]         = $det["id"];
            $temp["tanggal"]    = $det["tanggal"];
            $temp["jual_id"]    = $det["jual_id"];
            $temp["namaproduk"] = $det["namaproduk"];
            $temp["namabrand"]  = $det["namabrand"];
            $temp["jumlah"]     = $det["jumlah"];

            $hargaSql = "
                SELECT harga
                FROM {$this->harga}
                WHERE tanggal <= ? AND barcode = ?
                ORDER BY tanggal DESC
                LIMIT 1
            ";
            $hargaRow = $this->db->query($hargaSql, [$det["tgljual"], $det["barcode"]])->getRow();

            $harga = $hargaRow ? $hargaRow->harga : 0;

            $temp["harga"] = $harga;
            $temp["total"] = ($det["jumlah"] * $harga) - $det["diskonn"] - $det["diskonp"];

            $mdata[] = $temp;
        }

        return $mdata;
    }

    // Stok Out
    public function getStokout($awal, $akhir, $storeid)
    {
        $dsql = "
            SELECT 
                a.*, 
                b.size, 
                b.jumlah,
                IF (
                    (ISNULL(b.kembali) AND b.status='kembali'),
                    'Sedang Dipinjam',
                    IF (b.status='tidak', 'Tidak Kembali', b.kembali)
                ) as status, 
                c.namaproduk,
                c.namabrand 
            FROM pinjam a 
            INNER JOIN pinjam_detail b ON a.id = b.id 
            INNER JOIN produk c ON b.barcode = c.barcode 
            WHERE DATE(a.tanggal) BETWEEN ? AND ? 
            AND IF (? != 'All', storeid, 'All') = ?
        ";

        return $this->db->query($dsql, [$awal, $akhir, $storeid, $storeid])->getResultArray();
    }

    // Kas Keluar
    public function getKaskeluar($awal, $akhir, $storeid)
    {
        $sql = "
            SELECT 
                a.*,
                b.store
            FROM {$this->kas} a
            INNER JOIN {$this->store} b ON a.storeid = b.storeid
            WHERE 
                IF (? != 'All', a.storeid, 'All') = ?
                AND (DATE(a.tanggal) BETWEEN ? AND ?)
                AND (a.jenis = 'Keluar' OR a.jenis = 'Masuk')
        ";

        return $this->db->query($sql, [$storeid, $storeid, $awal, $akhir])->getResultArray();
    }

    // Laporan Pengeluaran per Pos Bulanan

    public function getpospengeluaran($bulan, $tahun, $storeid, $pengeluaran)
    {
        $awal  = $tahun . "-" . str_pad($bulan, 2, "0", STR_PAD_LEFT) . "-01";
        $akhir = date("Y-m-t", strtotime($awal));

        $sql = "
            SELECT 
                s.store,
                p.namapengeluaran,
                SUM(k.nominal) AS total
            FROM {$this->kas} k
            LEFT JOIN {$this->store} s ON s.storeid = k.storeid
            LEFT JOIN {$this->pengeluaran} p ON p.namapengeluaran = k.jenis
            WHERE k.dateonly BETWEEN ? AND ?
        ";

        $params = [$awal, $akhir];

        if ($storeid !== "all") {
            $sql .= " AND k.storeid = ? ";
            $params[] = $storeid;
        }

        if ($pengeluaran !== "all") {
            $sql .= " AND k.jenis = ? ";
            $params[] = $pengeluaran;
        }

        $sql .= " GROUP BY s.store, p.namapengeluaran 
                ORDER BY s.store ASC, p.namapengeluaran ASC";

        $query = $this->db->query($sql, $params);

        return $query->getResultArray();
    }

    public function getprodukterlaris($bulan, $tahun, $storeid, $pengeluaran)
    {
        $awal  = $tahun . "-" . $bulan . "-01";
        $akhir = date("Y-m-t", strtotime($awal));

        // jgn pakai nama tabel di DB-nya langsung, gunakan {$this->kas}, {$this->store}, {$this->pengeluaran}

        return $mdata;
    }

}
