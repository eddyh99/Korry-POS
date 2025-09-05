<?php

namespace App\Models\Admin;

use CodeIgniter\Model;

class OpnameModel extends Model
{
    protected $penjualan          = 'penjualan';
    protected $penjualan_detail   = 'penjualan_detail';
    protected $penyesuaian        = 'penyesuaian';
    protected $pindah             = 'pindah';
    protected $pindah_detail      = 'pindah_detail';
    protected $produk             = 'produk';
    protected $produksize         = 'produksize';
    protected $store              = 'store';

    public function getStok($barcode, $storeid, $size)
    {
        $where = [
            $barcode, $size, $storeid,
            $barcode, $size, $storeid,
            $barcode, $size, $storeid,
            $barcode, $size, $storeid,
            $barcode, $size, $storeid,
            $barcode, $size, $storeid,
            $barcode, $size, $storeid,
            $barcode, $size, $storeid,
            $barcode, $size, $storeid,
            $barcode, $size, $storeid,
            $barcode, $size, $storeid
        ];

        $sql = "SELECT IFNULL(SUM(x.total),0) AS stok
                FROM (
                    SELECT barcode, SUM(jumlah)*-1 AS total, size, storeid
                    FROM penjualan c INNER JOIN penjualan_detail d ON c.id=d.id
                    WHERE barcode=? AND size=? AND storeid=?

                    UNION ALL

                    SELECT barcode, SUM(jumlah) AS total, size, storeid
                    FROM penyesuaian
                    WHERE approved='1' AND barcode=? AND size=? AND storeid=?

                    UNION ALL

                    SELECT barcode, SUM(jumlah) AS total, size, storeid
                    FROM retur a INNER JOIN retur_detail b ON a.id=b.id
                    WHERE barcode=? AND size=? AND storeid=?

                    UNION ALL

                    SELECT barcode, SUM(jumlah)*-1 AS total, size, dari AS storeid
                    FROM pindah e INNER JOIN pindah_detail f ON e.mutasi_id=f.mutasi_id
                    WHERE e.approved='1' AND barcode=? AND size=? AND dari=?

                    UNION ALL

                    SELECT barcode, SUM(jumlah) AS total, size, tujuan AS storeid
                    FROM pindah e INNER JOIN pindah_detail f ON e.mutasi_id=f.mutasi_id
                    WHERE e.approved='1' AND barcode=? AND size=? AND tujuan=?

                    UNION ALL

                    SELECT barcode, SUM(jumlah)*-1 AS total, size, storeid
                    FROM pinjam a INNER JOIN pinjam_detail b ON a.id=b.id
                    WHERE (ISNULL(kembali) OR status='tidak') AND barcode=? AND size=? AND storeid=?

                    UNION ALL

                    SELECT barcode, SUM(jumlah) AS total, size, '' as storeid
                    FROM produksi a INNER JOIN produksi_detail b ON a.nonota=b.nonota
                    WHERE is_complete=1 AND status=0 AND barcode=? AND size=? AMD storeid=?

                    UNION ALL

                    SELECT barcode, SUM(jumlah)*-1 AS total, size, '' as storeid
                    FROM do_konsinyasi a INNER JOIN do_konsinyasi_detail b ON a.nonota=b.nonota
                    WHERE is_void=0 AND barcode=? AND size=? AND storeid=?

                    UNION ALL

                    SELECT barcode, SUM(jumlah) AS total, size, '' as storeid
                    FROM retur_konsinyasi a INNER JOIN retur_konsinyasi_detail b ON a.nonota=b.nonota
                    WHERE is_void=0 AND barcode=? AND size=? AND storeid=?

                    UNION ALL

                    SELECT barcode, SUM(jumlah)*-1 AS total, size, '' as storeid
                    FROM nota_konsinyasi_detail
                    WHERE barcode=? AND size=? AND notakonsinyasi IS NULL AND storeid=?

                    UNION ALL

                    SELECT barcode, SUM(jumlah) AS total, size, '' as storeid
                    FROM wholesale_order a INNER JOIN wholesale_order_detail b ON a.nonota=b.nonota
                    WHERE is_void=0 AND is_complete=1 AND barcode=? AND size=? AND storeid=?
                ) x";

        return $this->db->query($sql, $where)->getRow()->stok;
    }

    public function insertData($data)
    {
        $builder = $this->db->table($this->penyesuaian);
        $result = $builder->insert($data);

        if (!$result) {
            return $this->db->error();
        }
        return $result; // tetap return result CI4
    }

    public function listopname($storeid)
    {
        $sql = "SELECT a.barcode, a.namaproduk, a.namabrand, b.size, IFNULL(SUM(x.total),0) AS stok, y.store
                FROM {$this->produk} a
                INNER JOIN {$this->produksize} b ON a.barcode=b.barcode
                LEFT JOIN (
                    SELECT barcode, SUM(jumlah)*-1 AS total, size, storeid
                    FROM {$this->penjualan} c INNER JOIN {$this->penjualan_detail} d ON c.id=d.id
                    WHERE storeid=?
                    GROUP BY barcode,size

                    UNION ALL

                    SELECT barcode, SUM(jumlah) AS total, size, storeid
                    FROM {$this->penyesuaian}
                    WHERE approved='1' AND storeid=?
                    GROUP BY barcode,size

                    UNION ALL

                    SELECT barcode, SUM(jumlah)*-1 AS total, size, dari AS storeid
                    FROM {$this->pindah} e INNER JOIN {$this->pindah_detail} f ON e.mutasi_id=f.mutasi_id
                    WHERE e.approved='1' AND dari=?
                    GROUP BY barcode,size

                    UNION ALL

                    SELECT barcode, SUM(jumlah) AS total, size, tujuan AS storeid
                    FROM {$this->pindah} e INNER JOIN {$this->pindah_detail} f ON e.mutasi_id=f.mutasi_id
                    WHERE e.approved='1' AND tujuan=?
                    GROUP BY barcode,size

                    UNION ALL

                    SELECT barcode, SUM(jumlah) AS total, size, storeid
                    FROM retur a INNER JOIN retur_detail b ON a.id=b.id
                    WHERE storeid=?
                    GROUP BY barcode,size

                    UNION ALL

                    SELECT barcode, SUM(jumlah)*-1 AS total, size, storeid
                    FROM pinjam a INNER JOIN pinjam_detail b ON a.id=b.id
                    WHERE (ISNULL(kembali) OR status='tidak') AND storeid=?
                    GROUP BY barcode,size
                ) x ON a.barcode=x.barcode AND b.size=x.size
                INNER JOIN {$this->store} y ON x.storeid=y.storeid
                GROUP BY a.barcode, x.size";

        $query = $this->db->query($sql, [$storeid, $storeid, $storeid, $storeid, $storeid, $storeid])->getResultArray();

        // ambil penyesuaian baru
        $sbaru = "SELECT barcode, SUM(jumlah) AS total, size, storeid
                  FROM {$this->penyesuaian}
                  WHERE approved='0' AND storeid=?
                  GROUP BY barcode,size, storeid";

        $qbaru = $this->db->query($sbaru, [$storeid])->getResultArray();

        $mdata = [];
        foreach ($query as $dt) {
            $temp = [
                'barcode' => $dt['barcode'],
                'produk'  => $dt['namaproduk'],
                'size'    => $dt['size'],
                'old'     => $dt['stok'],
                'baru'    => $dt['stok']
            ];

            foreach ($qbaru as $qb) {
                if ($dt['barcode'] == $qb['barcode'] && $dt['size'] == $qb['size']) {
                    $temp['baru'] = $dt['stok'] + $qb['total'];
                }
            }

            $mdata[] = $temp;
        }

        return $mdata;
    }

    public function setapprove($storeid)
    {
        $sql = "UPDATE {$this->penyesuaian} SET approved='1' WHERE storeid=?";
        return $this->db->query($sql, [$storeid]);
    }
}
