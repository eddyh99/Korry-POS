<?php

namespace App\Models\Admin;

use CodeIgniter\Model;

class WholesaleModel extends Model
{
    protected $wholesale_order          = 'wholesale_order';
    protected $wholesale_order_detail   = 'wholesale_order_detail';
    protected $wholesale_cicilan        = 'wholesale_cicilan';

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
                "message" => "Data berhasil disimpan"
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
}
