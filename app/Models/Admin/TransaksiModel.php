<?php

namespace App\Models\Admin;

use CodeIgniter\Model;

class TransaksiModel extends Model
{
    protected $penjualan        = 'penjualan';
    protected $penjualan_detail = 'penjualan_detail';
    protected $harga            = 'harga';
    protected $produk           = 'produk';

    /**
     * List transaksi berdasarkan tanggal & storeid
     */
    public function listTransaksi(string $tanggal, string $storeid): array
    {
        $sql = "SELECT y.nonota, y.tanggal, y.member_id, y.method, 
                       SUM((x.harga * a.jumlah) - a.diskonn - a.diskonp) AS total 
                FROM {$this->penjualan_detail} a
                INNER JOIN (
                    SELECT barcode, harga, MAX(tanggal) 
                    FROM {$this->harga} 
                    WHERE tanggal <= (
                        SELECT tanggal 
                        FROM {$this->penjualan} 
                        WHERE DATE(tanggal) = ?
                    )
                    GROUP BY barcode
                ) x ON a.barcode = x.barcode 
                INNER JOIN {$this->penjualan} y ON a.id = y.id 
                WHERE y.storeid = ? 
                  AND DATE(y.tanggal) = ?
                GROUP BY a.id";

        return $this->db->query($sql, [$tanggal, $storeid, $tanggal])
                        ->getResultArray();
    }

    /**
     * Detail transaksi berdasarkan nomor nota & tanggal
     */
    public function detailTransaksi(string $nonota, string $tanggal): array
    {
        $sql = "SELECT b.*, c.namaproduk, x.harga, 
                       ((x.harga * b.jumlah) - b.diskonn - b.diskonp) AS total 
                FROM {$this->penjualan} a
                INNER JOIN {$this->penjualan_detail} b ON a.id = b.id
                INNER JOIN {$this->produk} c ON b.barcode = c.barcode
                INNER JOIN (
                    SELECT barcode, harga, MAX(tanggal)
                    FROM {$this->harga} 
                    WHERE tanggal <= ?
                    GROUP BY barcode
                ) x ON b.barcode = x.barcode
                WHERE a.nonota = ?";

        return $this->db->query($sql, [$tanggal, $nonota])
                        ->getResultArray();
    }

    /**
     * Ganti metode pembayaran
     */
    public function changePayment(string $nonota, string $bayar): bool
    {
        $sql = "UPDATE {$this->penjualan} SET method = ? WHERE nonota = ?";
        return $this->db->query($sql, [$bayar, $nonota]);
    }
}
