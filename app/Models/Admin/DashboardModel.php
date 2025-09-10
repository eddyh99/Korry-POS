<?php  
namespace App\Models\Admin;

use CodeIgniter\Model;

class DashboardModel extends Model
{
    protected $DBGroup = 'default';

    private $brand               = 'brand';
    private $harga               = 'harga';
    private $penjualan           = 'penjualan';
    private $penjualan_detail    = 'penjualan_detail';
    private $produk              = 'produk';
    private $store               = 'store';
    private $nota_konsinyasi     = 'nota_konsinyasi';
    private $nota_konsinyasi_detail = 'nota_konsinyasi_detail';
    private $wholesale_order     = 'wholesale_order';
    private $wholesale_order_detail = 'wholesale_order_detail';
    private $produksi            = 'produksi';
    private $produksi_detail     = 'produksi_detail';

    private function rekapjual($month, $year, $storeid)
    {
        $sql = "SELECT * 
                FROM {$this->penjualan} 
                WHERE MONTH(tanggal) = ? 
                  AND YEAR(tanggal) = ? 
                  AND storeid = ?";
        $penjualan = $this->db->query($sql, [$month, $year, $storeid])->getResultArray();

        $total = 0;
        foreach ($penjualan as $dt) {
            $dsql = "SELECT * 
                     FROM {$this->penjualan_detail} 
                     WHERE id = ?";
            $detail = $this->db->query($dsql, [$dt["id"]])->getResultArray();

            foreach ($detail as $det) {
                $sqlHarga = "SELECT harga, barcode, tanggal 
                             FROM {$this->harga} 
                             WHERE tanggal <= ? AND barcode = ? 
                             ORDER BY tanggal DESC 
                             LIMIT 1";
                $hargaRow = $this->db->query($sqlHarga, [$dt["tanggal"], $det["barcode"]])->getRow();

                $harga = $hargaRow ? $hargaRow->harga : 0;

                $total += ($det["jumlah"] * $harga) - $det["diskonn"] - $det["diskonp"];
            }
        }

        return $total;
    }

    public function getPenjualan($month, $year)
    {
        $sql = "SELECT * 
                FROM {$this->store} 
                WHERE status = '0' 
                  AND store <> 'Hanaka Office'";
        $store = $this->db->query($sql)->getResultArray();

        $data = [];
        foreach ($store as $dt) {
            $temp["store"] = $dt["store"];
            $temp["total"] = $this->rekapjual($month, $year, $dt["storeid"]);
            $data[] = $temp;
        }

        $total = array_sum(array_column($data, 'total'));

        $mdata = [];
        foreach ($data as $dt) {
            $mdata[] = [
                ($total > 0 ? ($dt["total"] * 100 / $total) : 0),
                $dt["store"]
            ];
        }

        return $mdata;
    }

    public function getBrand($month, $year)
    {
        $sql = "SELECT SUM(b.jumlah) as total, c.namabrand 
                FROM {$this->penjualan} a
                INNER JOIN {$this->penjualan_detail} b ON a.id = b.id 
                INNER JOIN {$this->produk} c ON b.barcode = c.barcode 
                WHERE MONTH(a.tanggal) = ? 
                  AND YEAR(a.tanggal) = ? 
                GROUP BY c.namabrand";
        $brand = $this->db->query($sql, [$month, $year])->getResultArray();

        $mdata = [];
        foreach ($brand as $b) {
            $mdata[] = [
                (float) $b["total"],
                $b["namabrand"]
            ];
        }

        return $mdata;
    }

    public function getBrandstore($month, $year)
    {
        $store = $this->db->query("SELECT * FROM {$this->store} WHERE store <> 'Hanaka Office'")->getResultArray();
        $brand = $this->db->query("SELECT * FROM {$this->brand}")->getResultArray();

        $sql = "SELECT a.storeid, SUM(b.jumlah) as total, c.namabrand 
                FROM {$this->penjualan} a 
                INNER JOIN {$this->penjualan_detail} b ON a.id = b.id 
                INNER JOIN {$this->produk} c ON b.barcode = c.barcode 
                WHERE MONTH(a.tanggal) = ? 
                  AND YEAR(a.tanggal) = ? 
                GROUP BY c.namabrand, a.storeid";
        $jbrand = $this->db->query($sql, [$month, $year])->getResultArray();

        $data = [];
        foreach ($store as $dstore) {
            $temp = [
                "type" => "bar",
                "showInLegend" => true,
                "name" => $dstore["store"],
                "dataPoints" => []
            ];

            foreach ($brand as $b) {
                $temp2 = [
                    "y" => 0,
                    "label" => $b["namabrand"]
                ];

                foreach ($jbrand as $jb) {
                    if ($dstore["storeid"] == $jb["storeid"] && $b["namabrand"] == $jb["namabrand"]) {
                        $temp2["y"] += (float) $jb["total"];
                    }
                }

                $temp["dataPoints"][] = $temp2;
            }

            $data[] = $temp;
        }

        return $data;
    }

    public function toptenpenjualan()
    {
        $sql = "SELECT x.barcode, pr.namaproduk, pr.namabrand,
                    SUM(x.qty) AS total_qty,
                    SUM(x.total_jual) / SUM(x.qty) AS avg_jual,
                    m.avg_modal,
                    (SUM(x.total_jual) / SUM(x.qty) - m.avg_modal) AS avg_profit
                FROM (
                    -- gabungan penjualan, konsinyasi, wholesale
                    SELECT d.barcode, SUM(d.jumlah) AS qty,
                        SUM((h.harga - d.diskonn - (d.diskonp/100.0*h.harga)) * d.jumlah) AS total_jual
                    FROM {$this->penjualan_detail} d
                    JOIN {$this->penjualan} p ON p.id = d.id
                    JOIN {$this->harga} h ON h.barcode = d.barcode
                        AND h.tanggal = (
                            SELECT MAX(h2.tanggal)
                            FROM {$this->harga} h2
                            WHERE h2.barcode = d.barcode
                              AND h2.tanggal <= p.tanggal
                        )
                    GROUP BY d.barcode

                    UNION ALL

                    SELECT d.barcode, SUM(d.jumlah) AS qty,
                        SUM(h.harga_konsinyasi * d.jumlah) AS total_jual
                    FROM {$this->nota_konsinyasi_detail} d
                    JOIN {$this->nota_konsinyasi} n ON n.notajual = d.notajual
                    JOIN {$this->harga} h ON h.barcode = d.barcode
                        AND h.tanggal = (
                            SELECT MAX(h2.tanggal)
                            FROM {$this->harga} h2
                            WHERE h2.barcode = d.barcode
                              AND h2.tanggal <= n.tanggal
                        )
                    WHERE n.status != 'void'
                    GROUP BY d.barcode

                    UNION ALL

                    SELECT d.barcode, SUM(d.jumlah) AS qty,
                        SUM((h.harga_wholesale - d.potongan) * d.jumlah) AS total_jual
                    FROM {$this->wholesale_order_detail} d
                    JOIN {$this->wholesale_order} w ON w.notaorder = d.notaorder
                    JOIN {$this->harga} h ON h.barcode = d.barcode
                        AND h.tanggal = (
                            SELECT MAX(h2.tanggal)
                            FROM {$this->harga} h2
                            WHERE h2.barcode = d.barcode
                              AND h2.tanggal <= w.tanggal
                        )
                    WHERE w.is_void = 0
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
                LIMIT 10";

        return $this->db->query($sql)->getResultArray();
    }
}
