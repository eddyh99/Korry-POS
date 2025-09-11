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

    private $nota_konsinyasi        = 'nota_konsinyasi';
    private $nota_konsinyasi_detail = 'nota_konsinyasi_detail';
    private $wholesale_order        = 'wholesale_order';
    private $wholesale_order_detail = 'wholesale_order_detail';

    private $produksi           = 'produksi';
        private $produksi_detail           = 'produksi_detail';

    // Mutasi
    public function getmutasi($bulan, $tahun, $storeid, $brand, $kategori)
    {
        $awal  = $tahun . "-" . $bulan . "-01";
        $akhir = date("Y-m-t", strtotime($awal));

        $sql = "
            SELECT
                a.barcode,
                a.namaproduk,
                a.sku,
                COALESCE(opening.stok_awal,0) AS stok_awal,

                COALESCE(period.penjualan,0) AS penjualan,
                COALESCE(period.wholesale_out,0) AS wholesale_out,
                COALESCE(period.nota_konsinyasi_out,0) AS consignment_sold_non,
                COALESCE(period.consignment_sold,0) AS consignment_sold,

                COALESCE(period.penyesuaian,0) AS penyesuaian,
                COALESCE(period.retur,0) AS retur,
                COALESCE(period.pindah_in,0) AS pindah_in,
                COALESCE(period.produksi_in,0) AS produksi_in,
                COALESCE(period.pindah_out,0) AS pindah_out,
                COALESCE(period.pinjam_out,0) AS pinjam_out,
                COALESCE(period.do_konsinyasi_out,0) AS do_konsinyasi_out,
                COALESCE(period.retur_konsinyasi_in,0) AS retur_konsinyasi_in,

                COALESCE(period.do_konsinyasi_out,0) AS consignment_sent,
                COALESCE(period.consignment_sold,0) AS consignment_sold_confirmed,
                ( COALESCE(period.do_konsinyasi_out,0) - COALESCE(period.consignment_sold,0) ) AS consignment_unsold,

                (
                    COALESCE(opening.stok_awal,0)
                    + (COALESCE(period.produksi_in,0) + COALESCE(period.pindah_in,0))
                    - (COALESCE(period.pindah_out,0) + COALESCE(period.pinjam_out,0))
                    + (COALESCE(period.retur,0) + COALESCE(period.retur_konsinyasi_in,0))
                    - ( COALESCE(period.wholesale_out,0) + COALESCE(period.nota_konsinyasi_out,0) + COALESCE(period.penjualan,0) + COALESCE(period.consignment_sold,0) )
                    - ( COALESCE(period.do_konsinyasi_out,0) - COALESCE(period.consignment_sold,0) )
                    + COALESCE(period.penyesuaian,0)
                ) AS sisa

            FROM produk a

            LEFT JOIN (
                SELECT x.barcode, COALESCE(SUM(x.total),0) AS stok_awal
                FROM (
                    SELECT d.barcode, SUM(d.jumlah) * -1 AS total
                    FROM penjualan c
                    INNER JOIN penjualan_detail d ON c.id=d.id
                    WHERE DATE(c.tanggal) < ? AND IF(? != 'All', c.storeid, 'All') = ?
                    GROUP BY d.barcode

                    UNION ALL

                    SELECT barcode, SUM(jumlah) AS total
                    FROM penyesuaian
                    WHERE approved='1' AND DATE(tanggal) < ? AND IF(? != 'All', storeid, 'All') = ?
                    GROUP BY barcode

                    UNION ALL

                    SELECT b.barcode, SUM(b.jumlah) AS total
                    FROM retur a
                    INNER JOIN retur_detail b ON a.id=b.id
                    WHERE DATE(a.tanggal) < ? AND IF(? != 'All', a.storeid, 'All') = ?
                    GROUP BY b.barcode

                    UNION ALL

                    SELECT f.barcode, SUM(f.jumlah) * -1 AS total
                    FROM pindah e
                    INNER JOIN pindah_detail f ON e.mutasi_id=f.mutasi_id
                    WHERE e.approved='1' AND DATE(e.tanggal) < ? AND IF(? != 'All', e.dari, 'All') = ?
                    GROUP BY f.barcode

                    UNION ALL

                    SELECT f.barcode, SUM(f.jumlah) AS total
                    FROM pindah e
                    INNER JOIN pindah_detail f ON e.mutasi_id=f.mutasi_id
                    WHERE e.approved='1' AND DATE(e.tanggal) < ? AND IF(? != 'All', e.tujuan, 'All') = ?
                    GROUP BY f.barcode

                    UNION ALL

                    SELECT b.barcode, SUM(b.jumlah) * -1 AS total
                    FROM pinjam a
                    INNER JOIN pinjam_detail b ON a.id=b.id
                    WHERE (ISNULL(b.kembali) OR b.status='tidak') AND DATE(a.tanggal) < ? AND IF(? != 'All', a.storeid, 'All') = ?
                    GROUP BY b.barcode

                    UNION ALL

                    SELECT b.barcode, SUM(b.jumlah) AS total
                    FROM produksi a
                    INNER JOIN produksi_detail b ON a.nonota=b.nonota
                    WHERE a.is_complete=1 AND a.status=0 AND DATE(a.tanggal) < ? AND IF(? != 'All', a.storeid, 'All') = ?
                    GROUP BY b.barcode

                    UNION ALL

                    SELECT b.barcode, SUM(b.jumlah) * -1 AS total
                    FROM do_konsinyasi a
                    INNER JOIN do_konsinyasi_detail b ON a.nonota=b.nonota
                    WHERE a.is_void=0 AND DATE(a.tanggal) < ? AND IF(? != 'All', a.storeid, 'All') = ?
                    GROUP BY b.barcode

                    UNION ALL

                    SELECT b.barcode, SUM(b.jumlah) AS total
                    FROM retur_konsinyasi a
                    INNER JOIN retur_konsinyasi_detail b ON a.noretur=b.noretur
                    WHERE a.is_void=0 AND DATE(a.tanggal) < ? AND IF(? != 'All', a.storeid, 'All') = ?
                    GROUP BY b.barcode

                    UNION ALL

                    SELECT a.barcode, SUM(a.jumlah) * -1 AS total
                    FROM nota_konsinyasi_detail a
                    INNER JOIN nota_konsinyasi b ON a.notajual=b.notajual
                    WHERE DATE(b.tanggal) < ? AND a.notakonsinyasi IS NULL AND IF(? != 'All', b.storeid, 'All') = ?
                    GROUP BY a.barcode

                    UNION ALL

                    SELECT b.barcode, SUM(b.jumlah) * -1 AS total
                    FROM wholesale_order a
                    INNER JOIN wholesale_order_detail b ON a.notaorder=b.notaorder
                    WHERE a.is_void=0 AND a.is_complete=1 AND DATE(a.tanggal) < ? AND IF(? != 'All', a.storeid, 'All') = ?
                    GROUP BY b.barcode

                    UNION ALL

                    SELECT nkd.barcode, SUM(nkd.jumlah) * -1 AS total
                    FROM nota_konsinyasi_detail nkd
                    INNER JOIN nota_konsinyasi nk ON nkd.notajual = nk.notajual
                    INNER JOIN do_konsinyasi dk ON nkd.notakonsinyasi = dk.nonota
                    INNER JOIN do_konsinyasi_detail dkd ON dk.nonota = dkd.nonota AND dkd.barcode = nkd.barcode AND dkd.size = nkd.size
                    WHERE DATE(nk.tanggal) < ? AND IF(? != 'All', nk.storeid, 'All') = ?
                    GROUP BY nkd.barcode

                ) x
                GROUP BY x.barcode
            ) opening ON opening.barcode = a.barcode

            LEFT JOIN (
                SELECT
                    x.barcode,
                    SUM(CASE WHEN x.t='penjualan' THEN x.total ELSE 0 END) AS penjualan,
                    SUM(CASE WHEN x.t='penyesuaian' THEN x.total ELSE 0 END) AS penyesuaian,
                    SUM(CASE WHEN x.t='retur' THEN x.total ELSE 0 END) AS retur,
                    SUM(CASE WHEN x.t='pindah_in' THEN x.total ELSE 0 END) AS pindah_in,
                    SUM(CASE WHEN x.t='produksi_in' THEN x.total ELSE 0 END) AS produksi_in,
                    SUM(CASE WHEN x.t='pindah_out' THEN x.total ELSE 0 END) AS pindah_out,
                    SUM(CASE WHEN x.t='pinjam_out' THEN x.total ELSE 0 END) AS pinjam_out,
                    SUM(CASE WHEN x.t='do_konsinyasi_out' THEN x.total ELSE 0 END) AS do_konsinyasi_out,
                    SUM(CASE WHEN x.t='retur_konsinyasi_in' THEN x.total ELSE 0 END) AS retur_konsinyasi_in,
                    SUM(CASE WHEN x.t='nota_konsinyasi_out' THEN x.total ELSE 0 END) AS nota_konsinyasi_out,
                    SUM(CASE WHEN x.t='wholesale_out' THEN x.total ELSE 0 END) AS wholesale_out,
                    SUM(CASE WHEN x.t='consignment_sold' THEN x.total ELSE 0 END) AS consignment_sold
                FROM (
                    SELECT d.barcode, SUM(d.jumlah) AS total, 'penjualan' AS t
                    FROM penjualan c
                    INNER JOIN penjualan_detail d ON c.id=d.id
                    WHERE DATE(c.tanggal) BETWEEN ? AND ? AND IF(? != 'All', c.storeid, 'All') = ?
                    GROUP BY d.barcode

                    UNION ALL

                    SELECT barcode, SUM(jumlah) AS total, 'penyesuaian' AS t
                    FROM penyesuaian
                    WHERE approved='1' AND DATE(tanggal) BETWEEN ? AND ? AND IF(? != 'All', storeid, 'All') = ?
                    GROUP BY barcode

                    UNION ALL

                    SELECT b.barcode, SUM(b.jumlah) AS total, 'retur' AS t
                    FROM retur a
                    INNER JOIN retur_detail b ON a.id=b.id
                    WHERE DATE(a.tanggal) BETWEEN ? AND ? AND IF(? != 'All', a.storeid, 'All') = ?
                    GROUP BY b.barcode

                    UNION ALL

                    SELECT f.barcode, SUM(f.jumlah) AS total, 'pindah_in' AS t
                    FROM pindah e
                    INNER JOIN pindah_detail f ON e.mutasi_id=f.mutasi_id
                    WHERE e.approved='1' AND DATE(e.tanggal) BETWEEN ? AND ? AND IF(? != 'All', e.tujuan, 'All') = ?
                    GROUP BY f.barcode

                    UNION ALL

                    SELECT b.barcode, SUM(b.jumlah) AS total, 'produksi_in' AS t
                    FROM produksi a
                    INNER JOIN produksi_detail b ON a.nonota=b.nonota
                    WHERE a.is_complete=1 AND a.status=0 AND DATE(a.tanggal) BETWEEN ? AND ? AND IF(? != 'All', a.storeid, 'All') = ?
                    GROUP BY b.barcode

                    UNION ALL

                    SELECT f.barcode, SUM(f.jumlah) AS total, 'pindah_out' AS t
                    FROM pindah e
                    INNER JOIN pindah_detail f ON e.mutasi_id=f.mutasi_id
                    WHERE e.approved='1' AND DATE(e.tanggal) BETWEEN ? AND ? AND IF(? != 'All', e.dari, 'All') = ?
                    GROUP BY f.barcode

                    UNION ALL

                    SELECT b.barcode, SUM(b.jumlah) AS total, 'pinjam_out' AS t
                    FROM pinjam a
                    INNER JOIN pinjam_detail b ON a.id=b.id
                    WHERE (ISNULL(b.kembali) OR b.status='tidak') AND DATE(a.tanggal) BETWEEN ? AND ? AND IF(? != 'All', a.storeid, 'All') = ?
                    GROUP BY b.barcode

                    UNION ALL

                    SELECT b.barcode, SUM(b.jumlah) AS total, 'do_konsinyasi_out' AS t
                    FROM do_konsinyasi a
                    INNER JOIN do_konsinyasi_detail b ON a.nonota=b.nonota
                    WHERE a.is_void=0 AND DATE(a.tanggal) BETWEEN ? AND ? AND IF(? != 'All', a.storeid, 'All') = ?
                    GROUP BY b.barcode

                    UNION ALL

                    SELECT b.barcode, SUM(b.jumlah) AS total, 'retur_konsinyasi_in' AS t
                    FROM retur_konsinyasi a
                    INNER JOIN retur_konsinyasi_detail b ON a.noretur=b.noretur
                    WHERE a.is_void=0 AND DATE(a.tanggal) BETWEEN ? AND ? AND IF(? != 'All', a.storeid, 'All') = ?
                    GROUP BY b.barcode

                    UNION ALL

                    SELECT a.barcode, SUM(a.jumlah) AS total, 'nota_konsinyasi_out' AS t
                    FROM nota_konsinyasi_detail a
                    INNER JOIN nota_konsinyasi b ON a.notajual=b.notajual
                    WHERE DATE(b.tanggal) BETWEEN ? AND ? AND a.notakonsinyasi IS NULL AND IF(? != 'All', b.storeid, 'All') = ?
                    GROUP BY a.barcode

                    UNION ALL

                    SELECT b.barcode, SUM(b.jumlah) AS total, 'wholesale_out' AS t
                    FROM wholesale_order a
                    INNER JOIN wholesale_order_detail b ON a.notaorder=b.notaorder
                    WHERE a.is_void=0 AND a.is_complete=1 AND DATE(a.tanggal) BETWEEN ? AND ? AND IF(? != 'All', a.storeid, 'All') = ?
                    GROUP BY b.barcode

                    UNION ALL

                    SELECT nkd.barcode, SUM(nkd.jumlah) AS total, 'consignment_sold' AS t
                    FROM nota_konsinyasi_detail nkd
                    INNER JOIN nota_konsinyasi nk ON nkd.notajual = nk.notajual
                    INNER JOIN do_konsinyasi dk ON nkd.notakonsinyasi = dk.nonota
                    INNER JOIN do_konsinyasi_detail dkd ON dk.nonota = dkd.nonota AND dkd.barcode = nkd.barcode AND dkd.size = nkd.size
                    WHERE DATE(nk.tanggal) BETWEEN ? AND ? AND IF(? != 'All', nk.storeid, 'All') = ?
                    GROUP BY nkd.barcode

                ) x
                GROUP BY x.barcode
            ) period ON period.barcode = a.barcode

            WHERE a.status='0'
            AND IF(? != 'All', a.namakategori, 'All') = ?
            AND IF(? != 'All', a.namabrand, 'All') = ?
            GROUP BY a.barcode, a.namaproduk, opening.stok_awal
            ORDER BY a.barcode
            ";


        $params = [];

        // OPENING: 12 blok, tiap blok butuh (awal, storeid, storeid)
        for ($i = 0; $i < 12; $i++) {
            $params[] = $awal;      // DATE < awal
            $params[] = $storeid;   // IF(? != 'All', <store>, 'All') = ?
            $params[] = $storeid;
        }

        // PERIOD: 12 blok, tiap blok butuh (awal, akhir, storeid, storeid)
        for ($i = 0; $i < 12; $i++) {
            $params[] = $awal;      // DATE BETWEEN awal
            $params[] = $akhir;     // AND akhir
            $params[] = $storeid;   // IF(? != 'All', <store>, 'All') = ?
            $params[] = $storeid;
        }

        // akhir WHERE: kategori (2x), brand (2x)
        $params[] = $kategori;
        $params[] = $kategori;
        $params[] = $brand;
        $params[] = $brand;

        // lalu eksekusi (CodeIgniter style)
        $result = $this->db->query($sql, $params)->getResultArray();
        return $result;
    }




    // Mutasi Detail
    public function getmutasidetail($bulan, $tahun, $storeid, $brand, $kategori)
    {
        $awal  = $tahun . "-" . $bulan . "-01";
        $akhir = date("Y-m-t", strtotime($awal));

        $sql="
            SELECT
                b.barcode,
                a.namaproduk,
                b.size,
                COALESCE(opening.stok_awal,0) AS stok_awal,

                COALESCE(period.penjualan,0) AS penjualan,
                COALESCE(period.wholesale_out,0) AS wholesale_out,
                COALESCE(period.nota_konsinyasi_out,0) AS consignment_sold_non,
                COALESCE(period.consignment_sold,0) AS consignment_sold,

                COALESCE(period.penyesuaian,0) AS penyesuaian,
                COALESCE(period.retur,0) AS retur,
                COALESCE(period.pindah_in,0) AS pindah_in,
                COALESCE(period.produksi_in,0) AS produksi_in,
                COALESCE(period.pindah_out,0) AS pindah_out,
                COALESCE(period.pinjam_out,0) AS pinjam_out,
                COALESCE(period.do_konsinyasi_out,0) AS do_konsinyasi_out,
                COALESCE(period.retur_konsinyasi_in,0) AS retur_konsinyasi_in,

                COALESCE(period.do_konsinyasi_out,0) AS consignment_sent,
                COALESCE(period.consignment_sold,0) AS consignment_sold_confirmed,
                ( COALESCE(period.do_konsinyasi_out,0) - COALESCE(period.consignment_sold,0) ) AS consignment_unsold,

                (
                    COALESCE(opening.stok_awal,0)
                    + (COALESCE(period.produksi_in,0) + COALESCE(period.pindah_in,0))
                    - (COALESCE(period.pindah_out,0) + COALESCE(period.pinjam_out,0))
                    + (COALESCE(period.retur,0) + COALESCE(period.retur_konsinyasi_in,0))
                    - ( COALESCE(period.wholesale_out,0) + COALESCE(period.nota_konsinyasi_out,0) + COALESCE(period.penjualan,0) + COALESCE(period.consignment_sold,0) )
                    - ( COALESCE(period.do_konsinyasi_out,0) - COALESCE(period.consignment_sold,0) )
                    + COALESCE(period.penyesuaian,0)
                ) AS sisa

            FROM produk a
            INNER JOIN produksize b ON a.barcode = b.barcode

            LEFT JOIN (
                -- opening per barcode+size (transactions before awal)
                SELECT x.barcode, x.size, COALESCE(SUM(x.total),0) AS stok_awal
                FROM (
                    -- penjualan (mengurangi)
                    SELECT d.barcode, d.size, SUM(d.jumlah) * -1 AS total
                    FROM penjualan c
                    INNER JOIN penjualan_detail d ON c.id=d.id
                    WHERE DATE(c.tanggal) < ? AND IF(? != 'All', c.storeid, 'All') = ?
                    GROUP BY d.barcode, d.size

                    UNION ALL

                    -- penyesuaian
                    SELECT barcode, size, SUM(jumlah) AS total
                    FROM penyesuaian
                    WHERE approved='1' AND DATE(tanggal) < ? AND IF(? != 'All', storeid, 'All') = ?
                    GROUP BY barcode, size

                    UNION ALL

                    -- retur pelanggan
                    SELECT b.barcode, b.size, SUM(b.jumlah) AS total
                    FROM retur a
                    INNER JOIN retur_detail b ON a.id=b.id
                    WHERE DATE(a.tanggal) < ? AND IF(? != 'All', a.storeid, 'All') = ?
                    GROUP BY b.barcode, b.size

                    UNION ALL

                    -- pindah keluar
                    SELECT f.barcode, f.size, SUM(f.jumlah) * -1 AS total
                    FROM pindah e
                    INNER JOIN pindah_detail f ON e.mutasi_id=f.mutasi_id
                    WHERE e.approved='1' AND DATE(e.tanggal) < ? AND IF(? != 'All', e.dari, 'All') = ?
                    GROUP BY f.barcode, f.size

                    UNION ALL

                    -- pindah masuk
                    SELECT f.barcode, f.size, SUM(f.jumlah) AS total
                    FROM pindah e
                    INNER JOIN pindah_detail f ON e.mutasi_id=f.mutasi_id
                    WHERE e.approved='1' AND DATE(e.tanggal) < ? AND IF(? != 'All', e.tujuan, 'All') = ?
                    GROUP BY f.barcode, f.size

                    UNION ALL

                    -- pinjam keluar belum kembali (pakai pinjam_detail)
                    SELECT b.barcode, b.size, SUM(b.jumlah) * -1 AS total
                    FROM pinjam a
                    INNER JOIN pinjam_detail b ON a.id=b.id
                    WHERE (ISNULL(b.kembali) OR b.status='tidak') AND DATE(a.tanggal) < ? AND IF(? != 'All', a.storeid, 'All') = ?
                    GROUP BY b.barcode, b.size

                    UNION ALL

                    -- produksi complete masuk stok
                    SELECT b.barcode, b.size, SUM(b.jumlah) AS total
                    FROM produksi a
                    INNER JOIN produksi_detail b ON a.nonota=b.nonota
                    WHERE a.is_complete=1 AND a.status=0 AND DATE(a.tanggal) < ? AND IF(? != 'All', a.storeid, 'All') = ?
                    GROUP BY b.barcode, b.size

                    UNION ALL

                    -- do_konsinyasi keluar (mengurangi)
                    SELECT b.barcode, b.size, SUM(b.jumlah) * -1 AS total
                    FROM do_konsinyasi a
                    INNER JOIN do_konsinyasi_detail b ON a.nonota=b.nonota
                    WHERE a.is_void=0 AND DATE(a.tanggal) < ? AND IF(? != 'All', a.storeid, 'All') = ?
                    GROUP BY b.barcode, b.size

                    UNION ALL

                    -- retur_konsinyasi masuk
                    SELECT b.barcode, b.size, SUM(b.jumlah) AS total
                    FROM retur_konsinyasi a
                    INNER JOIN retur_konsinyasi_detail b ON a.noretur=b.noretur
                    WHERE a.is_void=0 AND DATE(a.tanggal) < ? AND IF(? != 'All', a.storeid, 'All') = ?
                    GROUP BY b.barcode, b.size

                    UNION ALL

                    -- nota_konsinyasi (penjualan konsinyasi) minus
                    SELECT a.barcode, a.size, SUM(a.jumlah) * -1 AS total
                    FROM nota_konsinyasi_detail a
                    INNER JOIN nota_konsinyasi b ON a.notajual=b.notajual
                    WHERE DATE(b.tanggal) < ? AND a.notakonsinyasi IS NULL AND IF(? != 'All', b.storeid, 'All') = ?
                    GROUP BY a.barcode, a.size

                    UNION ALL

                    -- wholesale_order keluar (mengurangi)
                    SELECT b.barcode, b.size, SUM(b.jumlah) * -1 AS total
                    FROM wholesale_order a
                    INNER JOIN wholesale_order_detail b ON a.notaorder=b.notaorder
                    WHERE a.is_void=0 AND a.is_complete=1 AND DATE(a.tanggal) < ? AND IF(? != 'All', a.storeid, 'All') = ?
                    GROUP BY b.barcode, b.size

                    UNION ALL

                    -- consignment_sold linked to DO (reduce stock)
                    SELECT nkd.barcode, nkd.size, SUM(nkd.jumlah) * -1 AS total
                    FROM nota_konsinyasi_detail nkd
                    INNER JOIN nota_konsinyasi nk ON nkd.notajual = nk.notajual
                    INNER JOIN do_konsinyasi dk ON nkd.notakonsinyasi = dk.nonota
                    INNER JOIN do_konsinyasi_detail dkd ON dk.nonota = dkd.nonota
                        AND dkd.barcode = nkd.barcode AND dkd.size = nkd.size
                    WHERE DATE(nk.tanggal) < ? AND IF(? != 'All', nk.storeid, 'All') = ?
                    GROUP BY nkd.barcode, nkd.size

                ) x
                GROUP BY x.barcode, x.size
            ) opening ON opening.barcode = b.barcode AND opening.size = b.size

            LEFT JOIN (
                -- period aggregated per barcode+size
                SELECT x.barcode, x.size,
                    SUM(CASE WHEN x.t='penjualan' THEN x.total ELSE 0 END) AS penjualan,
                    SUM(CASE WHEN x.t='penyesuaian' THEN x.total ELSE 0 END) AS penyesuaian,
                    SUM(CASE WHEN x.t='retur' THEN x.total ELSE 0 END) AS retur,
                    SUM(CASE WHEN x.t='pindah_in' THEN x.total ELSE 0 END) AS pindah_in,
                    SUM(CASE WHEN x.t='produksi_in' THEN x.total ELSE 0 END) AS produksi_in,
                    SUM(CASE WHEN x.t='pindah_out' THEN x.total ELSE 0 END) AS pindah_out,
                    SUM(CASE WHEN x.t='pinjam_out' THEN x.total ELSE 0 END) AS pinjam_out,
                    SUM(CASE WHEN x.t='do_konsinyasi_out' THEN x.total ELSE 0 END) AS do_konsinyasi_out,
                    SUM(CASE WHEN x.t='retur_konsinyasi_in' THEN x.total ELSE 0 END) AS retur_konsinyasi_in,
                    SUM(CASE WHEN x.t='nota_konsinyasi_out' THEN x.total ELSE 0 END) AS nota_konsinyasi_out,
                    SUM(CASE WHEN x.t='wholesale_out' THEN x.total ELSE 0 END) AS wholesale_out,
                    SUM(CASE WHEN x.t='consignment_sold' THEN x.total ELSE 0 END) AS consignment_sold
                FROM (
                    -- penjualan (retail)
                    SELECT d.barcode, d.size, SUM(d.jumlah) AS total, 'penjualan' AS t
                    FROM penjualan c
                    INNER JOIN penjualan_detail d ON c.id=d.id
                    WHERE DATE(c.tanggal) BETWEEN ? AND ? AND IF(? != 'All', c.storeid, 'All') = ?
                    GROUP BY d.barcode, d.size

                    UNION ALL

                    -- penyesuaian
                    SELECT barcode, size, SUM(jumlah) AS total, 'penyesuaian' AS t
                    FROM penyesuaian
                    WHERE approved='1' AND DATE(tanggal) BETWEEN ? AND ? AND IF(? != 'All', storeid, 'All') = ?
                    GROUP BY barcode, size

                    UNION ALL

                    -- retur pelanggan
                    SELECT b.barcode, b.size, SUM(b.jumlah) AS total, 'retur' AS t
                    FROM retur a
                    INNER JOIN retur_detail b ON a.id=b.id
                    WHERE DATE(a.tanggal) BETWEEN ? AND ? AND IF(? != 'All', a.storeid, 'All') = ?
                    GROUP BY b.barcode, b.size

                    UNION ALL

                    -- pindah masuk
                    SELECT f.barcode, f.size, SUM(f.jumlah) AS total, 'pindah_in' AS t
                    FROM pindah e
                    INNER JOIN pindah_detail f ON e.mutasi_id=f.mutasi_id
                    WHERE e.approved='1' AND DATE(e.tanggal) BETWEEN ? AND ? AND IF(? != 'All', e.tujuan, 'All') = ?
                    GROUP BY f.barcode, f.size

                    UNION ALL

                    -- produksi_in
                    SELECT b.barcode, b.size, SUM(b.jumlah) AS total, 'produksi_in' AS t
                    FROM produksi a
                    INNER JOIN produksi_detail b ON a.nonota=b.nonota
                    WHERE a.is_complete=1 AND a.status=0 AND DATE(a.tanggal) BETWEEN ? AND ? AND IF(? != 'All', a.storeid, 'All') = ?
                    GROUP BY b.barcode, b.size

                    UNION ALL

                    -- pindah keluar
                    SELECT f.barcode, f.size, SUM(f.jumlah) AS total, 'pindah_out' AS t
                    FROM pindah e
                    INNER JOIN pindah_detail f ON e.mutasi_id=f.mutasi_id
                    WHERE e.approved='1' AND DATE(e.tanggal) BETWEEN ? AND ? AND IF(? != 'All', e.dari, 'All') = ?
                    GROUP BY f.barcode, f.size

                    UNION ALL

                    -- pinjam_out
                    SELECT b.barcode, b.size, SUM(b.jumlah) AS total, 'pinjam_out' AS t
                    FROM pinjam a
                    INNER JOIN pinjam_detail b ON a.id=b.id
                    WHERE (ISNULL(b.kembali) OR b.status='tidak') AND DATE(a.tanggal) BETWEEN ? AND ? AND IF(? != 'All', a.storeid, 'All') = ?
                    GROUP BY b.barcode, b.size

                    UNION ALL

                    -- do_konsinyasi_out
                    SELECT b.barcode, b.size, SUM(b.jumlah) AS total, 'do_konsinyasi_out' AS t
                    FROM do_konsinyasi a
                    INNER JOIN do_konsinyasi_detail b ON a.nonota=b.nonota
                    WHERE a.is_void=0 AND DATE(a.tanggal) BETWEEN ? AND ? AND IF(? != 'All', a.storeid, 'All') = ?
                    GROUP BY b.barcode, b.size

                    UNION ALL

                    -- retur_konsinyasi_in
                    SELECT b.barcode, b.size, SUM(b.jumlah) AS total, 'retur_konsinyasi_in' AS t
                    FROM retur_konsinyasi a
                    INNER JOIN retur_konsinyasi_detail b ON a.noretur=b.noretur
                    WHERE a.is_void=0 AND DATE(a.tanggal) BETWEEN ? AND ? AND IF(? != 'All', a.storeid, 'All') = ?
                    GROUP BY b.barcode, b.size

                    UNION ALL

                    -- nota_konsinyasi_out (all nota_konsinyasi_detail with notakonsinyasi IS NULL)
                    SELECT a.barcode, a.size, SUM(a.jumlah) AS total, 'nota_konsinyasi_out' AS t
                    FROM nota_konsinyasi_detail a
                    INNER JOIN nota_konsinyasi b ON a.notajual=b.notajual
                    WHERE DATE(b.tanggal) BETWEEN ? AND ? AND a.notakonsinyasi IS NULL AND IF(? != 'All', b.storeid, 'All') = ?
                    GROUP BY a.barcode, a.size

                    UNION ALL

                    -- wholesale_out
                    SELECT b.barcode, b.size, SUM(b.jumlah) AS total, 'wholesale_out' AS t
                    FROM wholesale_order a
                    INNER JOIN wholesale_order_detail b ON a.notaorder=b.notaorder
                    WHERE a.is_void=0 AND a.is_complete=1 AND DATE(a.tanggal) BETWEEN ? AND ? AND IF(? != 'All', a.storeid, 'All') = ?
                    GROUP BY b.barcode, b.size

                    UNION ALL

                    -- consignment_sold (nota_konsinyasi_detail linked to DO with same barcode+size)
                    SELECT nkd.barcode, nkd.size, SUM(nkd.jumlah) AS total, 'consignment_sold' AS t
                    FROM nota_konsinyasi_detail nkd
                    INNER JOIN nota_konsinyasi nk ON nkd.notajual = nk.notajual
                    INNER JOIN do_konsinyasi dk ON nkd.notakonsinyasi = dk.nonota
                    INNER JOIN do_konsinyasi_detail dkd ON dk.nonota = dkd.nonota
                        AND dkd.barcode = nkd.barcode AND dkd.size = nkd.size
                    WHERE DATE(nk.tanggal) BETWEEN ? AND ? AND IF(? != 'All', nk.storeid, 'All') = ?
                    GROUP BY nkd.barcode, nkd.size

                ) x
                GROUP BY x.barcode, x.size
            ) period ON period.barcode = b.barcode AND period.size = b.size

            WHERE a.status='0'
            AND IF(? != 'All', a.namakategori, 'All') = ?
            AND IF(? != 'All', a.namabrand, 'All') = ?
            GROUP BY b.barcode, b.size, a.namaproduk, opening.stok_awal
            ORDER BY b.barcode, b.size";

            $params = [];

            // Opening block (12 UNION ALL × 3 params)
            for ($i = 0; $i < 12; $i++) {
                $params[] = $awal;     // tanggal awal periode
                $params[] = $storeid;  // store filter
                $params[] = $storeid;  // store filter ulang
            }

            // Period block (12 UNION ALL × 4 params)
            for ($i = 0; $i < 12; $i++) {
                $params[] = $awal;     // tanggal awal periode
                $params[] = $akhir;    // tanggal akhir periode
                $params[] = $storeid;  // store filter
                $params[] = $storeid;  // store filter ulang
            }

            // Filter terakhir: kategori & brand
            $params[] = $kategori;
            $params[] = $kategori;
            $params[] = $brand;
            $params[] = $brand;

        $result = $this->db->query($sql, $params)->getResultArray();

        return $result;
    }

    // Penjualan
    public function getpenjualan($awal, $akhir, $storeid)
    {
        $sql = "
            SELECT
                a.id,
                a.nonota,
                a.tanggal,
                COALESCE(d.nama, '') AS member,
                c.nama AS kasir,
                a.method,
                COALESCE(SUM(pd.diskonn), 0) AS diskonn,
                COALESCE(SUM(pd.diskonp), 0) AS diskonp,
                COALESCE(SUM(
                    (pd.jumlah * COALESCE((
                        SELECT h.harga
                        FROM {$this->harga} h
                        WHERE h.barcode = pd.barcode
                        AND h.tanggal <= a.tanggal
                        ORDER BY h.tanggal DESC
                        LIMIT 1
                    ), 0))
                    - pd.diskonn - pd.diskonp
                ), 0) AS total
            FROM {$this->penjualan} a
            INNER JOIN pengguna c ON a.userid = c.username
            LEFT JOIN member d ON a.member_id = d.member_id
            INNER JOIN {$this->penjualan_detail} pd ON pd.id = a.id
            WHERE DATE(a.tanggal) BETWEEN ? AND ?
            AND (? = 'All' OR a.storeid = ?)
            AND a.id NOT IN (SELECT jual_id FROM retur)
            GROUP BY a.id, a.nonota, a.tanggal, d.nama, c.nama, a.method
            ORDER BY a.tanggal
        ";

        $rows = $this->db->query($sql, [$awal, $akhir, $storeid, $storeid])->getResultArray();

        // Pastikan tipe data konsisten sebelum dikembalikan
        return array_map(function($r) {
            return [
                'id'      => $r['id'],
                'nonota'  => $r['nonota'],
                'tanggal' => $r['tanggal'],
                'member'  => $r['member'],
                'kasir'   => $r['kasir'],
                'method'  => $r['method'],
                'diskonn' => (int)$r['diskonn'],
                'diskonp' => (int)$r['diskonp'],
                'total'   => (float)$r['total'],
            ];
        }, $rows);
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
            ";

            $params = [$awal, $akhir, $storeid];
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
            FROM kas a
            INNER JOIN store b ON a.storeid = b.storeid
            WHERE 
                IF (? != 'All', a.storeid, 'All') = ?
                AND (DATE(a.tanggal) BETWEEN ? AND ?)
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

    public function getprodukterlaris($bulan, $tahun)
    {
        // tentukan range tanggal
        if ($bulan !== "all-time" && $tahun !== "-") {
            $bulan = str_pad($bulan, 2, "0", STR_PAD_LEFT);
            $awal  = $tahun . "-" . $bulan . "-01";
            $akhir = date("Y-m-t", strtotime($awal));
        } else {
            // all-time: gunakan rentang sangat lebar
            $awal  = '1970-01-01';
            $akhir = '9999-12-31';
        }

        $sql = "
            SELECT x.barcode, pr.namaproduk, pr.namabrand,
                SUM(x.qty) AS total_qty,
                SUM(x.total_jual) / SUM(x.qty) AS avg_jual,
                m.avg_modal,
                (SUM(x.total_jual) / SUM(x.qty) - m.avg_modal) AS avg_profit
            FROM (
                -- penjualan (retail)
                SELECT d.barcode, SUM(d.jumlah) AS qty,
                    SUM((h.harga - d.diskonn - (d.diskonp/100.0*h.harga)) * d.jumlah) AS total_jual
                FROM penjualan_detail d
                JOIN penjualan p ON p.id = d.id
                JOIN harga h ON h.barcode = d.barcode
                    AND h.tanggal = (
                        SELECT MAX(h2.tanggal) FROM harga h2
                        WHERE h2.barcode = d.barcode
                        AND h2.tanggal <= p.tanggal
                    )
                WHERE DATE(p.tanggal) BETWEEN ? AND ?
                GROUP BY d.barcode

                UNION ALL

                -- nota_konsinyasi_out (nota_konsinyasi_detail dengan notakonsinyasi IS NULL)
                SELECT a.barcode, SUM(a.jumlah) AS qty,
                    SUM(h.harga_konsinyasi * a.jumlah) AS total_jual
                FROM nota_konsinyasi_detail a
                JOIN nota_konsinyasi b ON a.notajual = b.notajual
                JOIN harga h ON h.barcode = a.barcode
                    AND h.tanggal = (
                        SELECT MAX(h2.tanggal) FROM harga h2
                        WHERE h2.barcode = a.barcode
                        AND h2.tanggal <= b.tanggal
                    )
                WHERE DATE(b.tanggal) BETWEEN ? AND ?
                AND (a.notakonsinyasi IS NULL OR a.notakonsinyasi = '')
                AND b.status != 'void'
                GROUP BY a.barcode

                UNION ALL

                -- consignment_sold (nota_konsinyasi_detail yang terkait DO)
                SELECT nkd.barcode, SUM(nkd.jumlah) AS qty,
                    SUM(h.harga_konsinyasi * nkd.jumlah) AS total_jual
                FROM nota_konsinyasi_detail nkd
                JOIN nota_konsinyasi nk ON nkd.notajual = nk.notajual
                JOIN do_konsinyasi dk ON nkd.notakonsinyasi = dk.nonota
                JOIN do_konsinyasi_detail dkd ON dk.nonota = dkd.nonota
                    AND dkd.barcode = nkd.barcode AND dkd.size = nkd.size
                JOIN harga h ON h.barcode = nkd.barcode
                    AND h.tanggal = (
                        SELECT MAX(h2.tanggal) FROM harga h2
                        WHERE h2.barcode = nkd.barcode
                        AND h2.tanggal <= nk.tanggal
                    )
                WHERE DATE(nk.tanggal) BETWEEN ? AND ?
                AND nk.status != 'void'
                GROUP BY nkd.barcode

                UNION ALL

                -- wholesale
                SELECT d.barcode, SUM(d.jumlah) AS qty,
                    SUM((h.harga_wholesale - d.potongan) * d.jumlah) AS total_jual
                FROM wholesale_order_detail d
                JOIN wholesale_order w ON w.notaorder = d.notaorder
                JOIN harga h ON h.barcode = d.barcode
                    AND h.tanggal = (
                        SELECT MAX(h2.tanggal) FROM harga h2
                        WHERE h2.barcode = d.barcode
                        AND h2.tanggal <= w.tanggal
                    )
                WHERE w.is_void = 0 AND DATE(w.tanggal) BETWEEN ? AND ?
                GROUP BY d.barcode
            ) x
            JOIN produk pr ON pr.barcode = x.barcode
            LEFT JOIN (
                -- modal rata2 per barcode (dari produksi)
                SELECT d.barcode, AVG(d.harga) AS avg_modal
                FROM produksi_detail d
                JOIN produksi p ON p.nonota = d.nonota
                WHERE p.is_complete = 1
                GROUP BY d.barcode
            ) m ON m.barcode = x.barcode
            GROUP BY x.barcode, pr.namaproduk, pr.namabrand
            ORDER BY total_qty DESC
            LIMIT 10
        ";

        // susun params sesuai urutan placeholder (?) di query
        $params = [
            // penjualan
            $awal, $akhir,
            // nota_konsinyasi_out
            $awal, $akhir,
            // consignment_sold
            $awal, $akhir,
            // wholesale
            $awal, $akhir
        ];

        return $this->db->query($sql, $params)->getResultArray();
    }


    public function getNeraca($tanggal)
    {
        $result = [];

        // 1. Hitung Kas dengan detail
        $sqlKas = "
            SELECT kategori, COALESCE(SUM(total), 0) AS jumlah
            FROM (
                -- 1. Penjualan Retail (cash/debit/QRIS)
                SELECT 'Penjualan Retail' AS kategori,
                    SUM((h.harga - d.diskonn - (d.diskonp/100.0 * h.harga)) * d.jumlah) AS total
                FROM penjualan_detail d
                JOIN penjualan p ON p.id = d.id
                JOIN harga h ON h.barcode = d.barcode
                AND h.tanggal = (
                    SELECT MAX(h2.tanggal) FROM harga h2
                    WHERE h2.barcode = d.barcode AND h2.tanggal <= p.tanggal
                )
                WHERE DATE(p.tanggal) <= ?

                UNION ALL

                -- 2. Penjualan Konsinyasi (sudah setor/bayar)
                SELECT 'Penjualan Konsinyasi' AS kategori,
                    SUM(h.harga_konsinyasi * d.jumlah) AS total
                FROM nota_konsinyasi_detail d
                JOIN nota_konsinyasi n ON n.notajual = d.notajual
                JOIN harga h ON h.barcode = d.barcode
                AND h.tanggal = (
                    SELECT MAX(h2.tanggal) FROM harga h2
                    WHERE h2.barcode = d.barcode AND h2.tanggal <= n.tanggal
                )
                WHERE n.status != 'void'
                AND DATE(n.tanggal) <= ?

                UNION ALL

                -- 3. Wholesale (DP + cicilan)
                SELECT 'Pembayaran Wholesale' AS kategori,
                    SUM(bayar) AS total
                FROM wholesale_cicilan
                WHERE DATE(tanggal) <= ?

                UNION ALL

                -- 4. Expenses
                SELECT 'Expenses' AS kategori,
                    SUM(nominal) * -1 AS total
                FROM kas
                WHERE DATE(tanggal) <= ?
            ) x
            GROUP BY kategori
        ";

        $kasDetails = $this->db->query($sqlKas, [$tanggal, $tanggal, $tanggal, $tanggal])->getResultArray();
        
        $totalKas = 0;
        $kasBreakdown = [];
        foreach ($kasDetails as $detail) {
            $totalKas += $detail['jumlah'];
            $kasBreakdown[$detail['kategori']] = $detail['jumlah'];
        }
        
        $result['kas'] = [
            'total' => $totalKas,
            'breakdown' => $kasBreakdown
        ];

        // 2. Piutang Usaha
        $sqlPiutang = "
            SELECT COALESCE(SUM(total_order - total_bayar), 0) AS total_piutang_usaha
            FROM (
                SELECT 
                    wo.notaorder,
                    SUM(d.jumlah * (h.harga_wholesale - d.potongan)) AS total_order,
                    COALESCE((
                        SELECT SUM(c.bayar) 
                        FROM wholesale_cicilan c 
                        WHERE c.notaorder = wo.notaorder
                        AND DATE(c.tanggal) <= ?
                    ), 0) AS total_bayar
                FROM wholesale_order wo
                JOIN wholesale_order_detail d ON d.notaorder = wo.notaorder
                JOIN harga h ON h.barcode = d.barcode
                AND h.tanggal = (
                    SELECT MAX(h2.tanggal) FROM harga h2
                    WHERE h2.barcode = d.barcode AND h2.tanggal <= wo.tanggal
                )
                WHERE wo.is_void = 0
                AND DATE(wo.tanggal) <= ?
                GROUP BY wo.notaorder
            ) x
        ";
        
        $piutangRow = $this->db->query($sqlPiutang, [$tanggal, $tanggal])->getRow();
        $piutang = $piutangRow ? (int)$piutangRow->total_piutang_usaha : 0;
        $result['piutang'] = $piutang;

        // 3. Hutang Usaha
        $sqlHutang = "
            SELECT COALESCE(SUM(total - dp), 0) as hutang
            FROM produksi
            WHERE DATE(tanggal) <= ?
            AND is_complete = 0
        ";
        
        $hutangRow = $this->db->query($sqlHutang, [$tanggal])->getRow();
        $hutang = $hutangRow ? (int)$hutangRow->hutang : 0;
        $result['hutang'] = $hutang;

        // 4. Persediaan
        $sqlPersediaan = "
            SELECT COALESCE(SUM(nilai_persediaan), 0) AS total_persediaan
            FROM (
                -- Produk Jadi
                SELECT 
                    SUM(stok_akhir * avg_cost) AS nilai_persediaan
                FROM (
                    SELECT 
                        barcode,
                        size,
                        storeid,
                        COALESCE(SUM(total), 0) AS stok_akhir
                    FROM (
                        -- Semua transaksi yang mempengaruhi stok
                        SELECT 
                            d.barcode, 
                            d.size, 
                            c.storeid,
                            SUM(d.jumlah) * -1 AS total
                        FROM penjualan c 
                        INNER JOIN penjualan_detail d ON c.id = d.id
                        WHERE c.tanggal <= ?
                        GROUP BY d.barcode, d.size, c.storeid

                        UNION ALL

                        SELECT 
                            barcode, 
                            size, 
                            storeid,
                            SUM(jumlah) AS total
                        FROM penyesuaian
                        WHERE approved = 1 AND tanggal <= ?
                        GROUP BY barcode, size, storeid

                        UNION ALL

                        SELECT 
                            b.barcode, 
                            b.size, 
                            a.storeid,
                            SUM(b.jumlah) AS total
                        FROM retur a 
                        INNER JOIN retur_detail b ON a.id = b.id
                        WHERE a.tanggal <= ?
                        GROUP BY b.barcode, b.size, a.storeid

                        UNION ALL

                        SELECT 
                            f.barcode, 
                            f.size, 
                            e.dari AS storeid,
                            SUM(f.jumlah) * -1 AS total
                        FROM pindah e 
                        INNER JOIN pindah_detail f ON e.mutasi_id = f.mutasi_id
                        WHERE e.approved = 1 AND e.tanggal <= ?
                        GROUP BY f.barcode, f.size, e.dari

                        UNION ALL

                        SELECT 
                            f.barcode, 
                            f.size, 
                            e.tujuan AS storeid,
                            SUM(f.jumlah) AS total
                        FROM pindah e 
                        INNER JOIN pindah_detail f ON e.mutasi_id = f.mutasi_id
                        WHERE e.approved = 1 AND e.tanggal <= ?
                        GROUP BY f.barcode, f.size, e.tujuan

                        UNION ALL

                        SELECT 
                            b.barcode, 
                            b.size, 
                            a.storeid,
                            SUM(b.jumlah) * -1 AS total
                        FROM pinjam a 
                        INNER JOIN pinjam_detail b ON a.id = b.id
                        WHERE (b.kembali IS NULL OR b.status = 'tidak') 
                        AND a.tanggal <= ?
                        GROUP BY b.barcode, b.size, a.storeid

                        UNION ALL

                        SELECT 
                            b.barcode, 
                            b.size, 
                            a.storeid,
                            SUM(b.jumlah) AS total
                        FROM produksi a 
                        INNER JOIN produksi_detail b ON a.nonota = b.nonota
                        WHERE a.is_complete = 1 
                        AND a.status = 0 
                        AND a.tanggal <= ?
                        GROUP BY b.barcode, b.size, a.storeid

                        UNION ALL

                        SELECT 
                            b.barcode, 
                            b.size, 
                            a.storeid,
                            SUM(b.jumlah) * -1 AS total
                        FROM do_konsinyasi a 
                        INNER JOIN do_konsinyasi_detail b ON a.nonota = b.nonota
                        WHERE a.is_void = 0 
                        AND a.tanggal <= ?
                        GROUP BY b.barcode, b.size, a.storeid

                        UNION ALL

                        SELECT 
                            b.barcode, 
                            b.size, 
                            a.storeid,
                            SUM(b.jumlah) AS total
                        FROM retur_konsinyasi a 
                        INNER JOIN retur_konsinyasi_detail b ON a.noretur = b.noretur
                        WHERE a.is_void = 0 
                        AND a.tanggal <= ?
                        GROUP BY b.barcode, b.size, a.storeid

                        UNION ALL

                        SELECT 
                            a.barcode, 
                            a.size, 
                            b.storeid,
                            SUM(a.jumlah) * -1 AS total
                        FROM nota_konsinyasi_detail a 
                        INNER JOIN nota_konsinyasi b ON a.notajual = b.notajual
                        WHERE a.notakonsinyasi IS NULL 
                        AND b.tanggal <= ?
                        GROUP BY a.barcode, a.size, b.storeid

                        UNION ALL

                        SELECT 
                            b.barcode, 
                            b.size, 
                            a.storeid,
                            SUM(b.jumlah) * -1 AS total
                        FROM wholesale_order a 
                        INNER JOIN wholesale_order_detail b ON a.notaorder = b.notaorder
                        WHERE a.is_void = 0 
                        AND a.is_complete = 1 
                        AND a.tanggal <= ?
                        GROUP BY b.barcode, b.size, a.storeid
                    ) AS transaksi
                    GROUP BY barcode, size, storeid
                ) AS stok
                INNER JOIN (
                    SELECT 
                        barcode,
                        size,
                        SUM((harga + biaya) * jumlah) / SUM(jumlah) AS avg_cost
                    FROM produksi_detail pd
                    INNER JOIN produksi p ON pd.nonota = p.nonota
                    WHERE p.is_complete = 1 
                    AND p.status = 0 
                    AND p.tanggal <= ?
                    GROUP BY barcode, size
                ) AS cost ON stok.barcode = cost.barcode AND stok.size = cost.size
                WHERE stok_akhir > 0

                UNION ALL

                -- Bahan Baku
                SELECT 
                    SUM(sisa * avg_cost) AS nilai_persediaan
                FROM (
                    SELECT 
                        bb.idbahan,
                        SUM(bb.jumlah) - COALESCE((
                            SELECT SUM(pb.jumlah * pd.jumlah)
                            FROM produk_bahan pb
                            INNER JOIN produksi_detail pd ON pb.barcode = pd.barcode
                            INNER JOIN produksi p ON pd.nonota = p.nonota
                            WHERE pb.idbahan = bb.idbahan
                            AND p.is_complete = 1
                            AND p.status = 0
                            AND p.tanggal <= ?
                        ), 0) AS sisa,
                        SUM(bb.jumlah * bb.harga) / NULLIF(SUM(bb.jumlah), 0) AS avg_cost
                    FROM stok_bahanbaku bb
                    WHERE bb.tanggal <= ?
                    GROUP BY bb.idbahan
                    HAVING sisa > 0
                ) AS bahan_baku
            ) AS persediaan
        ";
        
        $persediaanRow = $this->db->query($sqlPersediaan, [
            $tanggal, $tanggal, $tanggal, $tanggal, $tanggal, 
            $tanggal, $tanggal, $tanggal, $tanggal, $tanggal,
            $tanggal, $tanggal, $tanggal, $tanggal
        ])->getRow();
        
        $persediaan = $persediaanRow ? (int)$persediaanRow->total_persediaan : 0;
        $result['persediaan'] = $persediaan;

        // 5. Hitung Total Aktiva & Pasiva
        $totalAktiva = $totalKas + $piutang + $persediaan;
        $modal = $totalAktiva - $hutang;
        $totalPasiva = $hutang + $modal;

        $result['total_aktiva'] = $totalAktiva;
        $result['total_pasiva'] = $totalPasiva;
        $result['modal'] = $modal;

        return $result;
    }

    public function getLabaRugi($tgl_awal, $tgl_akhir)
    {
        $result = [];

        // 1. PENDAPATAN KOTOR
        $pendapatanKotor = 0;
        
        // a. Penjualan Retail
        $sqlRetail = "
            SELECT COALESCE(SUM((h.harga - d.diskonn - (d.diskonp/100.0 * h.harga)) * d.jumlah), 0) AS total
            FROM penjualan_detail d
            JOIN penjualan p ON p.id = d.id
            JOIN harga h ON h.barcode = d.barcode
            AND h.tanggal = (
                SELECT MAX(h2.tanggal) FROM harga h2
                WHERE h2.barcode = d.barcode AND h2.tanggal <= p.tanggal
            )
            WHERE p.tanggal BETWEEN ? AND ?
        ";
        $retail = $this->db->query($sqlRetail, [$tgl_awal, $tgl_akhir])->getRow();
        $pendapatanKotor += $retail->total;
        $result[] = ["keterangan" => "Pendapatan Retail", "jumlah" => $retail->total];

        // b. Penjualan Konsinyasi
        $sqlKonsinyasi = "
            SELECT COALESCE(SUM(h.harga_konsinyasi * d.jumlah), 0) AS total
            FROM nota_konsinyasi_detail d
            JOIN nota_konsinyasi n ON n.notajual = d.notajual
            JOIN harga h ON h.barcode = d.barcode
            AND h.tanggal = (
                SELECT MAX(h2.tanggal) FROM harga h2
                WHERE h2.barcode = d.barcode AND h2.tanggal <= n.tanggal
            )
            WHERE n.status = 'paid'
            AND n.tanggal BETWEEN ? AND ?
        ";
        $konsinyasi = $this->db->query($sqlKonsinyasi, [$tgl_awal, $tgl_akhir])->getRow();
        $pendapatanKotor += $konsinyasi->total;
        $result[] = ["keterangan" => "Pendapatan Konsinyasi", "jumlah" => $konsinyasi->total];

        // c. Penjualan Wholesale
        $sqlWholesale = "
            SELECT COALESCE(SUM(h.harga_wholesale * d.jumlah), 0) AS total
            FROM wholesale_order_detail d
            JOIN wholesale_order wo ON wo.notaorder = d.notaorder
            JOIN harga h ON h.barcode = d.barcode
            AND h.tanggal = (
                SELECT MAX(h2.tanggal) FROM harga h2
                WHERE h2.barcode = d.barcode AND h2.tanggal <= wo.tanggal
            )
            WHERE wo.is_complete = 1
            AND wo.tanggal BETWEEN ? AND ?
        ";
        $wholesale = $this->db->query($sqlWholesale, [$tgl_awal, $tgl_akhir])->getRow();
        $pendapatanKotor += $wholesale->total;
        $result[] = ["keterangan" => "Pendapatan Wholesale", "jumlah" => $wholesale->total];
        
        $result[] = ["keterangan" => "Total Pendapatan Kotor", "jumlah" => $pendapatanKotor];

        // 2. RETUR & DISKON
        $totalReturDiskon = 0;
        
        // a. Retur Retail
        $sqlReturRetail = "
            SELECT COALESCE(SUM(
                (h.harga - (d.diskonn/d.jumlah) - (d.diskonp/100.0 * h.harga)) * r.jumlah
            ), 0) AS total
            FROM retur_detail r
            JOIN retur rt ON rt.id = r.id
            JOIN penjualan p ON p.id = rt.jual_id
            JOIN penjualan_detail d ON d.id = p.id AND d.barcode = r.barcode AND d.size = r.size
            JOIN harga h ON h.barcode = r.barcode
            AND h.tanggal = (
                SELECT MAX(h2.tanggal) FROM harga h2
                WHERE h2.barcode = r.barcode AND h2.tanggal <= p.tanggal
            )
            WHERE rt.tanggal BETWEEN ? AND ?
        ";
        $returRetail = $this->db->query($sqlReturRetail, [$tgl_awal, $tgl_akhir])->getRow();
        $totalReturDiskon += $returRetail->total;
        $result[] = ["keterangan" => "Retur Retail", "jumlah" => $returRetail->total];

        // b. Retur Konsinyasi
        $sqlReturKonsinyasi = "
            SELECT COALESCE(SUM(h.harga_konsinyasi * r.jumlah), 0) AS total
            FROM retur_konsinyasi_detail r
            JOIN retur_konsinyasi rk ON rk.noretur = r.noretur
            JOIN do_konsinyasi dk ON dk.nonota = rk.nokonsinyasi
            JOIN harga h ON h.barcode = r.barcode
            AND h.tanggal = (
                SELECT MAX(h2.tanggal) FROM harga h2
                WHERE h2.barcode = r.barcode AND h2.tanggal <= dk.tanggal
            )
            WHERE rk.tanggal BETWEEN ? AND ?
        ";
        $returKonsinyasi = $this->db->query($sqlReturKonsinyasi, [$tgl_awal, $tgl_akhir])->getRow();
        $totalReturDiskon += $returKonsinyasi->total;
        $result[] = ["keterangan" => "Retur Konsinyasi", "jumlah" => $returKonsinyasi->total];
        
        $result[] = ["keterangan" => "Total Retur & Diskon", "jumlah" => $totalReturDiskon];

        // 3. PENDAPATAN BERSIH
        $pendapatanBersih = $pendapatanKotor - $totalReturDiskon;
        $result[] = ["keterangan" => "Pendapatan Bersih", "jumlah" => $pendapatanBersih];

        // 4. HPP (HARGA POKOK PENJUALAN)
        // Hitung weighted average cost per item
        $sqlAvgCost = "
            SELECT 
                barcode,
                size,
                SUM((harga + biaya) * jumlah) / SUM(jumlah) AS avg_cost
            FROM produksi_detail pd
            INNER JOIN produksi p ON pd.nonota = p.nonota
            WHERE p.is_complete = 1 
            AND p.status = 0 
            AND p.tanggal <= ?
            GROUP BY barcode, size
        ";
        $avgCosts = $this->db->query($sqlAvgCost, [$tgl_akhir])->getResultArray();
        
        // Buat temporary array untuk avg_cost
        $avgCostMap = [];
        foreach ($avgCosts as $ac) {
            $key = $ac['barcode'] . '|' . $ac['size'];
            $avgCostMap[$key] = $ac['avg_cost'];
        }

        // Hitung quantity terjual per item
        $sqlQtyTerjual = "
            SELECT 
                barcode, 
                size, 
                SUM(qty) AS qty
            FROM (
                -- Retail sales
                SELECT d.barcode, d.size, d.jumlah AS qty
                FROM penjualan_detail d
                JOIN penjualan p ON p.id = d.id
                WHERE p.tanggal BETWEEN ? AND ?
                
                UNION ALL
                
                -- Konsinyasi sales
                SELECT d.barcode, d.size, d.jumlah AS qty
                FROM nota_konsinyasi_detail d
                JOIN nota_konsinyasi n ON n.notajual = d.notajual
                WHERE n.status = 'paid'
                AND n.tanggal BETWEEN ? AND ?
                
                UNION ALL
                
                -- Wholesale sales
                SELECT d.barcode, d.size, d.jumlah AS qty
                FROM wholesale_order_detail d
                JOIN wholesale_order wo ON wo.notaorder = d.notaorder
                WHERE wo.is_complete = 1
                AND wo.tanggal BETWEEN ? AND ?
                
                UNION ALL
                
                -- Retur retail (qty negative)
                SELECT r.barcode, r.size, -1 * r.jumlah AS qty
                FROM retur_detail r
                JOIN retur rt ON rt.id = r.id
                WHERE rt.tanggal BETWEEN ? AND ?
                
                UNION ALL
                
                -- Retur konsinyasi (qty negative)
                SELECT r.barcode, r.size, -1 * r.jumlah AS qty
                FROM retur_konsinyasi_detail r
                JOIN retur_konsinyasi rk ON rk.noretur = r.noretur
                WHERE rk.tanggal BETWEEN ? AND ?
            ) AS gross
            GROUP BY barcode, size
        ";
        
        $qtyTerjual = $this->db->query($sqlQtyTerjual, [
            $tgl_awal, $tgl_akhir,  // Retail
            $tgl_awal, $tgl_akhir,  // Konsinyasi
            $tgl_awal, $tgl_akhir,  // Wholesale
            $tgl_awal, $tgl_akhir,  // Retur retail
            $tgl_awal, $tgl_akhir   // Retur konsinyasi
        ])->getResultArray();

        // Hitung HPP
        $hpp = 0;
        foreach ($qtyTerjual as $qt) {
            $key = $qt['barcode'] . '|' . $qt['size'];
            if (isset($avgCostMap[$key]) && $qt['qty'] > 0) {
                $hpp += $qt['qty'] * $avgCostMap[$key];
            }
        }
        
        $result[] = ["keterangan" => "Harga Pokok Penjualan (HPP)", "jumlah" => $hpp];

        // 5. LABA KOTOR
        $labaKotor = $pendapatanBersih - $hpp;
        $result[] = ["keterangan" => "Laba Kotor", "jumlah" => $labaKotor];

        // 6. BEBAN OPERASIONAL
        $sqlBeban = "
            SELECT COALESCE(SUM(nominal), 0) as total
            FROM kas
            WHERE tanggal BETWEEN ? AND ?
        ";
        $bebanRow = $this->db->query($sqlBeban, [$tgl_awal, $tgl_akhir])->getRow();
        $beban = $bebanRow ? (int)$bebanRow->total : 0;
        
        $result[] = ["keterangan" => "Beban Operasional", "jumlah" => $beban];

        // 7. LABA BERSIH
        $labaBersih = $labaKotor - $beban;
        $result[] = ["keterangan" => "Laba Bersih", "jumlah" => $labaBersih];

        return $result;
    }
}
