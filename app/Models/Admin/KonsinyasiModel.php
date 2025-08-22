<?php

namespace App\Models\Admin;

use CodeIgniter\Model;

class KonsinyasiModel extends Model
{
    protected $do_konsinyasi = 'do_konsinyasi';
    protected $do_konsinyasi_detail = 'do_konsinyasi_detail';

    protected $partner_konsinyasi = 'partner_konsinyasi';
    protected $harga = 'harga';
    // protected $nota_konsinyasi = 'nota_konsinyasi';
    // protected $nota_konsinyasi_detail = 'nota_konsinyasi_detail';
    // protected $retur_konsinyasi = 'retur_konsinyasi';
    // protected $retur_konsinyasi_detail = 'retur_konsinyasi_detail';    

    // public function listDoKonsinyasi()
    // {
    //     $sql = "SELECT * FROM {$this->do_konsinyasi}";
    //     $query = $this->db->query($sql);

    //     if ($query) {
    //         return $query->getResultArray();
    //     } else {
    //         return $this->db->error();
    //     }
    // }
    public function listDoKonsinyasi()
    {
        $sql = "
            SELECT 
                a.nonota,
                a.tanggal,
                p.nama AS partner,
                COALESCE(SUM(d.jumlah * h.harga_konsinyasi), 0) AS total
            FROM {$this->do_konsinyasi} a
            INNER JOIN {$this->partner_konsinyasi} p ON a.id_partnerkonsinyasi = p.id
            INNER JOIN {$this->do_konsinyasi_detail} d ON a.nonota = d.nonota
            INNER JOIN (
                SELECT hh.barcode, hh.harga_konsinyasi, hh.tanggal
                FROM {$this->harga} hh
                INNER JOIN (
                    SELECT barcode, MAX(tanggal) AS maxtgl
                    FROM {$this->harga}
                    GROUP BY barcode
                ) xx ON hh.barcode = xx.barcode AND hh.tanggal = xx.maxtgl
            ) h ON d.barcode = h.barcode
            WHERE a.is_void = 0
            GROUP BY a.nonota, a.tanggal, p.nama
            ORDER BY a.tanggal DESC
        ";

        $query = $this->db->query($sql);

        return $query->getResultArray();
    }

    // public function insertDoKonsinyasi($data)
    // {
    //     $do_konsinyasi = [
    //         'nonota'      => $data["..."],
    //         'tanggal' => date("Y-m-d H:i:s"),
    //         'id_partnerkonsinyasi'   => $data["..."],
    //         'userid'       => $data["userid"]
    //     ];

    //     $do_konsinyasi_detail = [
    //         'nonota'      => $data["..."],
    //         'barcode' => $data["barcode"],
    //         'jumlah'   => $data["..."]
    //     ];

    //     $this->db->transStart();

    //     $this->db->table($this->do_konsinyasi)->insert($do_konsinyasi);

    //     $this->db->table($this->do_konsinyasi_detail)->insert($do_konsinyasi_detail);

    //     $this->db->transComplete();

    //     if ($this->db->transStatus() === false) {
    //         $this->db->transRollback();
    //         return ["code" => 511, "message" => "Data gagal disimpan"];
    //     } else {
    //         $this->db->transCommit();
    //         return ["code" => 0, "message" => "Data berhasil disimpan"];
    //     }
    // }
    public function insertDoKonsinyasi($data)
    {
        $this->db->transStart();

        // Insert master
        $do_konsinyasi = [
            'nonota'               => $data["nonota"],
            'tanggal'              => date("Y-m-d H:i:s"),
            'id_partnerkonsinyasi' => $data["partner"],
            'userid'               => $data["userid"]
        ];

        $this->db->table($this->do_konsinyasi)->insert($do_konsinyasi);

        // Insert detail
        foreach ($data["detail"] as $row) {
            $detail = [
                'nonota'  => $data["nonota"],
                'barcode' => $row["barcode"],
                'jumlah'  => $row["jumlah"]
            ];
            $this->db->table($this->do_konsinyasi_detail)->insert($detail);
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

    public function hapusDoKonsinyasi($data, $nonota_do)
    {
        $builder = $this->db->table($this->do_konsinyasi)->where('nonota', $nonota_do);
        $query = $builder->update($data);

        if ($query) {
            return ["code" => 0, "message" => ""];
        } else {
            return $this->db->error();
        }
    }
}
