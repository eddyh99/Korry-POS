<?php

namespace App\Models\Admin;

use CodeIgniter\Model;

class WholesaleModel extends Model
{
    protected $wholesaler               = 'wholesaler';

    protected $wholesale_order          = 'wholesale_order';
    protected $wholesale_order_detail   = 'wholesale_order_detail';
    protected $wholesale_cicilan        = 'wholesale_cicilan';

    protected $harga        = 'harga';
    protected $pengguna     = 'pengguna';
    protected $produk       = 'produk';
    protected $produksize   = 'produksize';

    // === Wholesale Order : Index ===

    public function listOrderWholesale()
    {
        $sql = "SELECT * FROM {$this->wholesale_order} WHERE is_void ='0'";
        $query = $this->db->query($sql);

        if ($query) {
            return $query->getResultArray();
        } else {
            return $this->db->error();
        }
    }

    // === Wholesale Order : Tambah === 

    public function insertWholesaleOrder($data)
    {
        $this->db->transStart();

        // Auto-generate No. Nota Order Wholesale
        $sql = "SELECT LPAD(
                    COALESCE(CAST(MAX(notaorder) AS UNSIGNED), 0) + 1,
                    6,
                    '0'
                ) AS next_notaorder
                FROM wholesale_order";

        $notaorder = $this->db->query($sql)->getRow()->next_notaorder;

        // Insert master (wholesale_order)
        $wholesale_order = [
            'notaorder'     => $notaorder,
            'id_wholesaler' => $data["id_wholesaler"],
            'tanggal'       => date("Y-m-d H:i:s"),
            'lama'          => !empty($data["lama"])   ? (int)$data["lama"]   : 0,    // default 0 hari
            'diskon'        => !empty($data["diskon"]) ? (int)$data["diskon"] : 0,    // default 0
            'ppn'           => !empty($data["ppn"])    ? (float)$data["ppn"]  : 0.00, // default 0.00
            'dp'            => $data["dp"],
            'userid'        => $data["userid"],
            'is_void'       => 0
        ];

        $this->db->table($this->wholesale_order)->insert($wholesale_order);

        // Insert detail (wholesale_order_detail)
        foreach ($data["detail"] as $row) {
            $detail = [
                'notaorder' => $notaorder,
                'barcode'   => $row["barcode"],
                'jumlah'    => $row["jumlah"],
                'potongan'  => $row["potongan"]
            ];
            $this->db->table($this->wholesale_order_detail)->insert($detail);
        }

        $this->db->transComplete();

        if ($this->db->transStatus() === false) {
            $this->db->transRollback();
            return [
                "status"  => false,
                "message" => "DB Error: " . $this->db->error()["message"]
            ];
        } else {
            $this->db->transCommit();
            return [
                "status"  => true,
                "message" => "Data berhasil disimpan",
                "notaorder" => $notaorder   // <== ini buat dipakai ke JS cetak
            ];
        }
    }

    // === Wholesale Cicilan : Index ===

    public function listCicilanWholesale()
    {
        $sql = "SELECT * FROM {$this->wholesale_cicilan} WHERE status ='paid'";
        $query = $this->db->query($sql);

        if ($query) {
            return $query->getResultArray();
        } else {
            return $this->db->error();
        }
    }

    // === Wholesale Cicilan : Tambah ===

    public function insertWholesaleCicilan($data)
    {
        $insertData = [
            'nonota'    => $data['nonota'],
            'tanggal'   => date("Y-m-d H:i:s"),
            'notaorder' => $data['notaorder'],
            'bayar'     => $data['bayar'],
            'userid'    => $data['userid'],
            'status'    => 'paid' // default saat insert cicilan baru
        ];

        $query = $this->db->table($this->wholesale_cicilan)->insert($insertData);

        if ($query) {
            return ["code" => 0, "message" => ""];
        } else {
            return $this->db->error(); // otomatis ada ['code'] & ['message']
        }
    }

    // === Wholesale Order : Hapus ===

    public function hapusOrderWholesale($data, $notaorder)
    {
        $builder = $this->db->table($this->wholesale_order)->where('notaorder', $notaorder);
        $query = $builder->update($data);

        if ($query) {
            return ["code" => 0, "message" => ""];
        } else {
            return $this->db->error();
        }
    }

    // === Wholesale Cicilan : Hapus ===

    public function hapusCicilanWholesale($data, $nonota)
    {
        $builder = $this->db->table($this->wholesale_cicilan)->where('nonota', $nonota);
        $query = $builder->update($data);

        if ($query) {
            return ["code" => 0, "message" => ""];
        } else {
            return $this->db->error();
        }
    }

    // Order & Cicilan : Print

    public function getAllNotaOrder($notaorder)
    {
        $mdata = [
            "header" => null,
            "detail" => []
        ];

        // === Ambil header Wholesale Order
        $sql = "SELECT a.notaorder, a.tanggal, a.lama, a.userid, a.diskon, a.ppn, a.dp,
                    b.nama AS nama_user, 
                    c.nama AS nama_wholesaler, c.alamat AS alamat_wholesaler, c.kontak AS kontak_wholesaler
                FROM {$this->wholesale_order} a
                INNER JOIN {$this->pengguna} b ON a.userid = b.username
                INNER JOIN {$this->wholesaler} c ON a.id_wholesaler = c.id
                WHERE a.notaorder = ? AND a.is_void = 0 AND c.status = 0
                LIMIT 1";

        $header = $this->db->query($sql, [$notaorder])->getRow();
        if ($header) {
            $mdata["header"] = $header;
        } else {
            // Kalau DO tidak ditemukan, return tetap ada structure kosong supaya view aman
            $mdata["header"] = (object) [
                "notaorder"     => $notaorder,
                "tanggal"       => null,
                "userid"        => null,
                "diskon"        => 0,
                "ppn"           => 0,
                "dp"            => 0,
                "nama_user"     => "-",
                "nama_wholesaler"  => "-",
                "alamat_wholesaler"=> "-",
                "kontak_wholesaler"=> "-"
            ];
        }

        // === Ambil detail Wholesale Order (join produk, size, harga)
        $sql = "SELECT a.barcode, a.jumlah, a.potongan,
                    b.namaproduk, b.namabrand, b.namakategori, b.namafabric, 
                    b.namawarna, b.sku,
                    s.size,
                    h.harga_wholesale
                FROM {$this->wholesale_order_detail} a
                INNER JOIN {$this->produk} b ON a.barcode = b.barcode
                LEFT JOIN {$this->produksize} s ON a.barcode = s.barcode AND s.status = 0
                LEFT JOIN {$this->harga} h 
                    ON h.barcode = a.barcode 
                    AND h.tanggal = (
                        SELECT MAX(h2.tanggal) 
                        FROM {$this->harga} h2 
                        WHERE h2.barcode = a.barcode
                    )
                WHERE a.notaorder = ?";

        $detail = $this->db->query($sql, [$notaorder])->getResultArray();

        foreach ($detail as $i => $det) {
            $mdata["detail"][$i] = [
                "barcode"    => $det["barcode"],
                "namaproduk" => $det["namaproduk"],
                "sku"        => $det["sku"],
                "jumlah"     => $det["jumlah"],
                "potongan"   => $det["potongan"],
                "brand"      => $det["namabrand"],
                "kategori"   => $det["namakategori"],
                "fabric"     => $det["namafabric"],
                "warna"      => $det["namawarna"],
                "size"       => $det["size"],
                "harga"      => $det["harga_wholesale"]
            ];
        }

        return $mdata;
    }
}
