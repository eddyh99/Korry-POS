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
        $wherePenjualan  = "";
        $whereKonsinyasi = "";
        $whereWholesale  = "";

        if ($bulan !== "all-time" && $tahun !== "-") {
            $bulan = str_pad($bulan, 2, "0", STR_PAD_LEFT);
            $awal  = $tahun . "-" . $bulan . "-01";
            $akhir = date("Y-m-t", strtotime($awal));

            $wherePenjualan  = "AND p.tanggal BETWEEN '{$awal}' AND '{$akhir}'";
            $whereKonsinyasi = "AND n.tanggal BETWEEN '{$awal}' AND '{$akhir}'";
            $whereWholesale  = "AND w.tanggal BETWEEN '{$awal}' AND '{$akhir}'";
        }

        $sql = "
            SELECT x.barcode, pr.namaproduk, pr.namabrand,
                SUM(x.qty) AS total_qty,
                SUM(x.total_jual) / SUM(x.qty) AS avg_jual,
                m.avg_modal,
                (SUM(x.total_jual) / SUM(x.qty) - m.avg_modal) AS avg_profit
            FROM (
                -- penjualan
                SELECT d.barcode, SUM(d.jumlah) AS qty,
                    SUM((h.harga - d.diskonn - (d.diskonp/100.0*h.harga)) * d.jumlah) AS total_jual
                FROM {$this->penjualan_detail} d
                JOIN {$this->penjualan} p ON p.id = d.id
                JOIN {$this->harga} h ON h.barcode = d.barcode
                            AND h.tanggal = (
                                SELECT MAX(h2.tanggal) FROM {$this->harga} h2
                                WHERE h2.barcode = d.barcode
                                AND h2.tanggal <= p.tanggal
                            )
                WHERE 1=1 {$wherePenjualan}
                GROUP BY d.barcode

                UNION ALL

                -- konsinyasi
                SELECT d.barcode, SUM(d.jumlah) AS qty,
                    SUM(h.harga_konsinyasi * d.jumlah) AS total_jual
                FROM {$this->nota_konsinyasi_detail} d
                JOIN {$this->nota_konsinyasi} n ON n.notajual = d.notajual
                JOIN {$this->harga} h ON h.barcode = d.barcode
                            AND h.tanggal = (
                                SELECT MAX(h2.tanggal) FROM {$this->harga} h2
                                WHERE h2.barcode = d.barcode
                                AND h2.tanggal <= n.tanggal
                            )
                WHERE n.status != 'void' {$whereKonsinyasi}
                GROUP BY d.barcode

                UNION ALL

                -- wholesale
                SELECT d.barcode, SUM(d.jumlah) AS qty,
                    SUM((h.harga_wholesale - d.potongan) * d.jumlah) AS total_jual
                FROM {$this->wholesale_order_detail} d
                JOIN {$this->wholesale_order} w ON w.notaorder = d.notaorder
                JOIN {$this->harga} h ON h.barcode = d.barcode
                            AND h.tanggal = (
                                SELECT MAX(h2.tanggal) FROM {$this->harga} h2
                                WHERE h2.barcode = d.barcode
                                AND h2.tanggal <= w.tanggal
                            )
                WHERE w.is_void = 0 {$whereWholesale}
                GROUP BY d.barcode
            ) x
            JOIN {$this->produk} pr ON pr.barcode = x.barcode
            LEFT JOIN (
                -- modal rata2 per barcode (dari produksi)
                SELECT d.barcode, AVG(d.harga) AS avg_modal
                FROM {$this->produksi_detail} d
                JOIN {$this->produksi} p ON p.nonota = d.nonota
                WHERE p.is_complete = 1
                GROUP BY d.barcode
            ) m ON m.barcode = x.barcode
            GROUP BY x.barcode, pr.namaproduk, pr.namabrand
            ORDER BY total_qty DESC
            LIMIT 10
        ";

        return $this->db->query($sql)->getResultArray();
    }

    public function getNeraca($tahun)
    {
        $result = [];

        // 1. Kas / Saldo
        $sqlKas = "
            SELECT 
                SUM(CASE WHEN jenis='Masuk' THEN nominal ELSE 0 END) -
                SUM(CASE WHEN jenis='Keluar' THEN nominal ELSE 0 END) as saldo
            FROM {$this->kas}
            WHERE YEAR(tanggal) = ?
        ";
        $kasRow = $this->db->query($sqlKas, [$tahun])->getRow();
        $kas = $kasRow ? (int)$kasRow->saldo : 0;
        $result[] = ["akun" => "Kas / Saldo", "saldo" => $kas];

        // 2. Piutang Usaha (penjualan kredit)
        $sqlPiutang = "
            SELECT a.id, a.tanggal, a.method
            FROM {$this->penjualan} a
            WHERE YEAR(a.tanggal) = ?
            AND a.method = 'credit'
            AND a.id NOT IN (SELECT jual_id FROM retur)
        ";
        $penjualanKredit = $this->db->query($sqlPiutang, [$tahun])->getResultArray();
        $piutang = 0;
        foreach ($penjualanKredit as $pj) {
            $dsql = "SELECT * FROM {$this->penjualan_detail} WHERE id = ?";
            $detail = $this->db->query($dsql, [$pj["id"]])->getResultArray();

            foreach ($detail as $det) {
                $sqlHarga = "
                    SELECT harga 
                    FROM {$this->harga}
                    WHERE tanggal <= ? AND barcode = ?
                    ORDER BY tanggal DESC 
                    LIMIT 1
                ";
                $hargaRow = $this->db->query($sqlHarga, [$pj["tanggal"], $det["barcode"]])->getRow();
                $harga = $hargaRow ? $hargaRow->harga : 0;
                $piutang += ($det["jumlah"] * $harga) - $det["diskonn"] - $det["diskonp"];
            }
        }
        $result[] = ["akun" => "Piutang Usaha", "saldo" => $piutang];

        // 3. Hutang Usaha (produksi ke vendor belum lunas)
        $sqlHutang = "
            SELECT SUM(total - dp) as hutang
            FROM {$this->produksi}
            WHERE YEAR(tanggal) = ?
            AND total > dp
        ";
        $hutangRow = $this->db->query($sqlHutang, [$tahun])->getRow();
        $hutang = $hutangRow ? (int)$hutangRow->hutang : 0;
        $result[] = ["akun" => "Hutang Usaha", "saldo" => $hutang];

        // 4. Persediaan (stok akhir × harga perolehan)
        $sqlPersediaan = "
            SELECT d.barcode, SUM(d.jumlah) as jumlah, d.harga
            FROM {$this->produksi} p
            INNER JOIN {$this->produksi_detail} d ON p.nonota = d.nonota
            WHERE YEAR(p.tanggal) <= ?
            GROUP BY d.barcode, d.harga
        ";
        $rows = $this->db->query($sqlPersediaan, [$tahun])->getResultArray();
        $persediaan = 0;
        foreach ($rows as $row) {
            $persediaan += $row["jumlah"] * $row["harga"];
        }
        $result[] = ["akun" => "Persediaan", "saldo" => $persediaan];

        // Total Aktiva & Pasiva
        $totalAktiva = $kas + $piutang + $persediaan;
        $totalPasiva = $hutang + ($totalAktiva - $hutang); // modal = selisih

        $result[] = ["akun" => "Total Aktiva", "saldo" => $totalAktiva];
        $result[] = ["akun" => "Total Pasiva (Hutang + Modal)", "saldo" => $totalPasiva];

        return $result;
    }

    public function getLabaRugi($tahun)
    {
        $result = [];

        // 1. Pendapatan Penjualan
        $sqlPenjualan = "
            SELECT a.id, a.tanggal
            FROM {$this->penjualan} a
            WHERE YEAR(a.tanggal) = ?
            AND a.id NOT IN (SELECT jual_id FROM retur)
        ";
        $rows = $this->db->query($sqlPenjualan, [$tahun])->getResultArray();

        $pendapatan = 0;
        foreach ($rows as $row) {
            $dsql = "SELECT * FROM {$this->penjualan_detail} WHERE id = ?";
            $detail = $this->db->query($dsql, [$row["id"]])->getResultArray();

            foreach ($detail as $det) {
                // Ambil harga jual saat itu
                $sqlHarga = "
                    SELECT harga 
                    FROM {$this->harga} 
                    WHERE tanggal <= ? AND barcode = ?
                    ORDER BY tanggal DESC 
                    LIMIT 1
                ";
                $hargaRow = $this->db->query($sqlHarga, [$row["tanggal"], $det["barcode"]])->getRow();
                $hargaJual = $hargaRow ? $hargaRow->harga : 0;

                $pendapatan += ($det["jumlah"] * $hargaJual) - $det["diskonn"] - $det["diskonp"];
            }
        }
        $result[] = ["keterangan" => "Pendapatan Penjualan", "jumlah" => $pendapatan];

        // 2. HPP (Harga Pokok Penjualan)
        $sqlHpp = "
            SELECT d.jumlah, d.harga
            FROM {$this->produksi} p
            INNER JOIN {$this->produksi_detail} d ON p.nonota = d.nonota
            WHERE YEAR(p.tanggal) = ?
        ";
        $hppRows = $this->db->query($sqlHpp, [$tahun])->getResultArray();

        $hpp = 0;
        foreach ($hppRows as $row) {
            $hpp += $row["jumlah"] * $row["harga"];
        }
        $result[] = ["keterangan" => "Harga Pokok Penjualan (HPP)", "jumlah" => $hpp];

        // 3. Laba Kotor
        $labaKotor = $pendapatan - $hpp;
        $result[] = ["keterangan" => "Laba Kotor", "jumlah" => $labaKotor];

        // 4. Beban Operasional
        $sqlBeban = "
            SELECT SUM(nominal) as total
            FROM {$this->kas}
            WHERE YEAR(tanggal) = ?
            AND jenis = 'Keluar'
        ";
        $bebanRow = $this->db->query($sqlBeban, [$tahun])->getRow();
        $beban = $bebanRow ? (int)$bebanRow->total : 0;

        $result[] = ["keterangan" => "Beban Operasional", "jumlah" => $beban];

        // 5. Laba Bersih
        $labaBersih = $labaKotor - $beban;
        $result[] = ["keterangan" => "Laba Bersih", "jumlah" => $labaBersih];

        return $result;
    }
}
