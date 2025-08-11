<?php  
namespace App\Models\Admin;

use CodeIgniter\Model;

class DashboardModel extends Model
{
    protected $db;

    public function __construct()
    {
        parent::__construct();
        $this->db = \Config\Database::connect();
    }

    private function rekapjual($month, $year, $storeid)
    {
        $sql = "SELECT * FROM penjualan WHERE MONTH(tanggal) = ? AND YEAR(tanggal) = ? AND storeid = ?";
        $penjualan = $this->db->query($sql, [$month, $year, $storeid])->getResultArray();

        $total = 0;
        foreach ($penjualan as $dt) {
            $dsql = "SELECT * FROM penjualan_detail WHERE id = ?";
            $detail = $this->db->query($dsql, [$dt["id"]])->getResultArray();

            foreach ($detail as $det) {
                $sqlHarga = "SELECT harga, barcode, tanggal FROM harga 
                             WHERE tanggal <= ? AND barcode = ? 
                             ORDER BY tanggal DESC LIMIT 1";
                $hargaRow = $this->db->query($sqlHarga, [$dt["tanggal"], $det["barcode"]])->getRow();

                $harga = $hargaRow ? $hargaRow->harga : 0;

                $total += ($det["jumlah"] * $harga) - $det["diskonn"] - $det["diskonp"];
            }
        }

        return $total;
    }

    public function getPenjualan($month, $year)
    {
        $sql = "SELECT * FROM store WHERE status = '0' AND store <> 'Hanaka Office'";
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
                FROM penjualan a
                INNER JOIN penjualan_detail b ON a.id = b.id 
                INNER JOIN produk c ON b.barcode = c.barcode 
                WHERE MONTH(a.tanggal) = ? AND YEAR(a.tanggal) = ? 
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
        $store = $this->db->query("SELECT * FROM store WHERE store <> 'Hanaka Office'")->getResultArray();
        $brand = $this->db->query("SELECT * FROM brand")->getResultArray();

        $sql = "SELECT a.storeid, SUM(b.jumlah) as total, c.namabrand 
                FROM penjualan a 
                INNER JOIN penjualan_detail b ON a.id = b.id 
                INNER JOIN produk c ON b.barcode = c.barcode 
                WHERE MONTH(a.tanggal) = ? AND YEAR(a.tanggal) = ? 
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
}
