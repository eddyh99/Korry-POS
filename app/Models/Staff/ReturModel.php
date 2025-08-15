<?php

namespace App\Models\Staff;

use CodeIgniter\Model;

class ReturModel extends Model
{
    private $penjualan   = "penjualan";
    private $detjual     = "penjualan_detail";
    private $tblretur    = "retur";
    private $detretur    = "retur_detail";
    private $produk      = "produk";
    private $member      = "member";

    public function listnota()
    {
        $now      = date("Y-m-d");
        $awal     = date('Y-m-d', strtotime('-30 days'));
        $storeid  = $_SESSION["logged_status"]["storeid"];

        $sql = "SELECT a.*, SUM(b.diskonn + b.diskonp)*-1 AS total, c.nama, 
                       IF(ISNULL(d.jual_id), 1, 0) AS jual_id 
                FROM {$this->penjualan} a 
                INNER JOIN {$this->detjual} b ON a.id = b.id 
                LEFT JOIN {$this->member} c ON a.member_id = c.member_id 
                LEFT JOIN {$this->tblretur} d ON a.id = d.jual_id
                WHERE DATE(a.tanggal) BETWEEN ? AND ? 
                  AND a.storeid = ?
                GROUP BY id";

        $query = $this->db->query($sql, [$awal, $now, $storeid]);
        $penjualan = $query->getResultArray();

        $mdata = [];
        $i = 0;
        foreach ($penjualan as $dt) {
            $mdata[$i] = $dt;

            $det = "SELECT * FROM {$this->detjual} WHERE id = ?";
            $detail = $this->db->query($det, [$dt["id"]])->getResultArray();

            foreach ($detail as $detjual) {
                $sqlHarga = "SELECT harga, barcode, tanggal 
                             FROM harga 
                             WHERE tanggal <= ? AND barcode = ? 
                             ORDER BY tanggal DESC 
                             LIMIT 0, 1";
                $harga = $this->db->query($sqlHarga, [$dt["tanggal"], $detjual["barcode"]])
                                  ->getRow()
                                  ->harga;
                $mdata[$i]["total"] = $mdata[$i]["total"] + ($detjual["jumlah"] * $harga);
            }
            $i++;
        }

        return $mdata;
    }

    public function detailnota($key = "")
    {
        $storeid = $_SESSION["logged_status"]["storeid"];
        $sql = "SELECT * FROM {$this->penjualan} WHERE id = ? AND storeid = ?";
        return $this->db->query($sql, [$key, $storeid])->getRow();
    }

    public function ganti_bayar($id, $mdata)
    {
        return $this->db->table($this->penjualan)
                        ->where("id", $id)
                        ->update($mdata);
    }

    public function getDetail($key)
    {
        $sql = "SELECT c.namaproduk, c.namabrand, a.tanggal, b.* 
                FROM {$this->penjualan} a 
                INNER JOIN {$this->detjual} b ON a.id = b.id 
                INNER JOIN {$this->produk} c ON b.barcode = c.barcode 
                WHERE a.id = ?";
        $barang = $this->db->query($sql, [$key])->getResultArray();

        $mdata = [];
        foreach ($barang as $dt) {
            $temp["barcode"]    = $dt["barcode"];
            $temp["namaproduk"] = $dt["namaproduk"];
            $temp["namabrand"]  = $dt["namabrand"];
            $temp["size"]       = $dt["size"];
            $temp["jumlah"]     = $dt["jumlah"];

            $sqlHarga = "SELECT harga, barcode, tanggal 
                         FROM harga 
                         WHERE tanggal <= ? AND barcode = ? 
                         ORDER BY tanggal DESC 
                         LIMIT 0, 1";
            $harga = $this->db->query($sqlHarga, [$dt["tanggal"], $dt["barcode"]])
                              ->getRow()
                              ->harga;
            $temp["harga"] = $harga - $dt["diskonn"] - $dt["diskonp"];
            $temp["total"] = ($dt["jumlah"] * $temp["harga"]);

            $mdata[] = $temp;
        }

        return $mdata;
    }

    public function lastsaldo()
    {
        $today   = date("Y-m-d");
        $storeid = 1; // $_SESSION["logged_status"]["storeid"];

        $mdata["saldo"] = 0;

        // cek saldo akhir hari sebelumnya
        $sql = "SELECT nominal 
                FROM kas 
                WHERE storeid = ? 
                  AND keterangan = 'Sisa Kas' 
                ORDER BY tanggal DESC";
        $query = $this->db->query($sql, [$storeid]);
        if ($query->getNumRows() > 0) {
            $mdata["saldo"] = $query->getRow()->nominal;
        }

        // ambil seluruh transaksi hari ini
        $sjual = "SELECT a.tanggal, a.method, b.barcode, b.jumlah, b.diskonn, b.diskonp 
                  FROM penjualan a 
                  INNER JOIN penjualan_detail b ON a.id = b.id 
                  WHERE DATE(tanggal) = ? AND a.storeid = ?";
        $qjual = $this->db->query($sjual, [$today, $storeid]);
        $penjualan = $qjual->getResultArray();

        $mdata["jual"]  = 0;
        $mdata["tunai"] = 0;

        foreach ($penjualan as $dt) {
            $sqlHarga = "SELECT harga, barcode, tanggal 
                         FROM harga 
                         WHERE tanggal <= ? AND barcode = ? 
                         ORDER BY tanggal DESC 
                         LIMIT 0, 1";
            $harga = $this->db->query($sqlHarga, [$dt["tanggal"], $dt["barcode"]])
                              ->getRow()
                              ->harga;

            $subtotal = ($dt["jumlah"] * $harga) - $dt["diskonn"] - $dt["diskonp"];
            $mdata["jual"] += $subtotal;

            if ($dt["method"] == 'cash') {
                $mdata["tunai"] += $subtotal;
            }
        }

        // tarik keluar masuk
        $sql = "SELECT * FROM kas WHERE storeid = ? AND DATE(tanggal) = ?";
        $qkas = $this->db->query($sql, [$storeid, $today]);
        $mdata["kas"] = $qkas->getResultArray();

        return $mdata;
    }


    public function setSisa($data)
    {
        $today = date("Y-m-d");
        $sql = "SELECT * FROM {$this->kas} WHERE dateonly=? AND storeid=? AND jenis='Kas Sisa'";
        $query = $this->db->query($sql, [$today, $data["storeid"]]);

        if ($query->getNumRows() > 0) {
            return ["code" => 5051];
        } else {
            $this->db->table($this->kas)->insert($data);
            return ["code" => 0];
        }
    }

    public function insertData($jual, $barang, $retur, $barangretur)
    {
        $this->db->transStart();

        // Insert retur
        $this->db->table($this->tblretur)->insert($retur);
        $returid = $this->db->insertID();

        // Detail retur
        $detailretur = [];
        foreach ($barangretur as $dt) {
            $detailretur[] = [
                "id"      => $returid,
                "barcode" => $dt[0],
                "size"    => $dt[1],
                "jumlah"  => $dt[2]
            ];
        }
        $this->db->table($this->detretur)->insertBatch($detailretur);

        // Insert penjualan
        $this->db->table($this->penjualan)->insert($jual);
        $id = $this->db->insertID();

        // Detail penjualan
        $detail = [];
        foreach ($barang as $dt) {
            $detail[] = [
                "id"      => $id,
                "barcode" => $dt[0],
                "size"    => $dt[2],
                "jumlah"  => $dt[3],
                "diskonn" => $dt[5],
                "diskonp" => $dt[6],
                "alasan"  => $dt[8]
            ];
        }
        $this->db->table($this->detjual)->insertBatch($detail);

        $this->db->transComplete();

        if ($this->db->transStatus()) {
            return ["code" => 0, "message" => ""];
        } else {
            return ["code" => 1, "message" => "Transaksi gagal"];
        }
    }

    public function bataldata($id, $retur)
    {
        $this->db->transStart();

        // Insert retur
        $this->db->table($this->tblretur)->insert($retur);
        $returid = $this->db->insertID();

        // Ambil data penjualan detail
        $sql = "SELECT * FROM {$this->detjual} WHERE id=?";
        $barangretur = $this->db->query($sql, [$id])->getResultArray();

        // Detail retur
        $detailretur = [];
        foreach ($barangretur as $dt) {
            $detailretur[] = [
                "id"      => $returid,
                "barcode" => $dt["barcode"],
                "size"    => $dt["size"],
                "jumlah"  => $dt["jumlah"]
            ];
        }
        $this->db->table($this->detretur)->insertBatch($detailretur);

        $this->db->transComplete();

        if ($this->db->transStatus()) {
            return ["code" => 0, "message" => ""];
        } else {
            return ["code" => 1, "message" => "Transaksi gagal"];
        }
    }
}
