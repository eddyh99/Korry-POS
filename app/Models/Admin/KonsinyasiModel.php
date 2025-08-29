<?php

namespace App\Models\Admin;

use CodeIgniter\Model;

class KonsinyasiModel extends Model
{
    protected $do_konsinyasi            = 'do_konsinyasi';
    protected $do_konsinyasi_detail     = 'do_konsinyasi_detail';
    protected $nota_konsinyasi          = 'nota_konsinyasi';
    protected $nota_konsinyasi_detail   = 'nota_konsinyasi_detail';
    protected $retur_konsinyasi         = 'retur_konsinyasi';
    protected $retur_konsinyasi_detail  = 'retur_konsinyasi_detail';
    
    protected $partner_konsinyasi = 'partner_konsinyasi';
    protected $harga = 'harga';

    // === DO Konsinyasi: Index ===

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

    // === DO Konsinyasi: Tambah ===

    public function insertDoKonsinyasi($data)
    {
        $this->db->transStart();

        // Auto-generate No. Do Konsinyasi
        $sql = "SELECT LPAD(
                    COALESCE(CAST(MAX(nonota) AS UNSIGNED), 0) + 1,
                    6,
                    '0'
                ) AS next_nonota
                FROM do_konsinyasi";

        $nonota = $this->db->query($sql)->getRow()->next_nonota;

        // Insert master
        $do_konsinyasi = [
            'nonota'               => $nonota,
            'tanggal'              => date("Y-m-d H:i:s"),
            'id_partnerkonsinyasi' => $data["partner"],
            'userid'               => $data["userid"]
        ];

        $this->db->table($this->do_konsinyasi)->insert($do_konsinyasi);

        // Insert detail
        foreach ($data["detail"] as $row) {
            $detail = [
                'nonota'  => $nonota,
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
    // public function insertDoKonsinyasi($data)
    // {
    //     $this->db->transStart();

    //     // Insert master
    //     $do_konsinyasi = [
    //         'nonota'               => $data["nonota"],
    //         'tanggal'              => date("Y-m-d H:i:s"),
    //         'id_partnerkonsinyasi' => $data["partner"],
    //         'userid'               => $data["userid"]
    //     ];

    //     $this->db->table($this->do_konsinyasi)->insert($do_konsinyasi);

    //     // Insert detail
    //     foreach ($data["detail"] as $row) {
    //         $detail = [
    //             'nonota'  => $data["nonota"],
    //             'barcode' => $row["barcode"],
    //             'jumlah'  => $row["jumlah"]
    //         ];
    //         $this->db->table($this->do_konsinyasi_detail)->insert($detail);
    //     }

    //     $this->db->transComplete();

    //     if ($this->db->transStatus() === false) {
    //         $this->db->transRollback();
    //         return [
    //             "status"  => false,
    //             "message" => "DB Error: " . $this->db->error()["message"]
    //         ];
    //     } else {
    //         $this->db->transCommit();
    //         return [
    //             "status"  => true,
    //             "message" => "Data berhasil disimpan"
    //         ];
    //     }
    // }

    // === DO Konsinyasi: Hapus ===

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

    // === Nota Konsinyasi: Index ===

    public function listNotaKonsinyasi()
    {
        $sql = "
            SELECT 
                n.notajual,
                n.tanggal,
                p.nama AS partner,
                COALESCE(SUM(d.jumlah * h.harga_konsinyasi), 0) AS total
            FROM {$this->nota_konsinyasi} n
            INNER JOIN {$this->nota_konsinyasi_detail} d 
                ON n.notajual = d.notajual
            INNER JOIN {$this->do_konsinyasi} doo 
                ON d.notakonsinyasi = doo.nonota 
            AND doo.is_void = 0
            INNER JOIN {$this->partner_konsinyasi} p 
                ON doo.id_partnerkonsinyasi = p.id
            INNER JOIN (
                SELECT hh.barcode, hh.harga_konsinyasi, hh.tanggal
                FROM {$this->harga} hh
                INNER JOIN (
                    SELECT barcode, MAX(tanggal) AS maxtgl
                    FROM {$this->harga}
                    GROUP BY barcode
                ) xx 
                ON hh.barcode = xx.barcode 
            AND hh.tanggal = xx.maxtgl
            ) h ON d.barcode = h.barcode
            WHERE n.status = 'pending'
            GROUP BY n.notajual, n.tanggal, p.nama
            ORDER BY n.tanggal DESC
        ";

        $query = $this->db->query($sql);
        return $query->getResultArray();
    }
    // public function listNotaKonsinyasi()
    // {
    //     $sql = "
    //         SELECT 
    //             n.notajual,
    //             n.tanggal,
    //             p.nama AS partner,
    //             COALESCE(SUM(d.jumlah * h.harga_konsinyasi), 0) AS total
    //         FROM {$this->nota_konsinyasi} n
    //         INNER JOIN {$this->nota_konsinyasi_detail} d ON n.notajual = d.notajual
    //         INNER JOIN {$this->do_konsinyasi} doo 
    //             ON d.notakonsinyasi = doo.nonota 
    //         AND doo.is_void = 0
    //         INNER JOIN {$this->partner_konsinyasi} p ON doo.id_partnerkonsinyasi = p.id
    //         INNER JOIN (
    //             SELECT hh.barcode, hh.harga_konsinyasi, hh.tanggal
    //             FROM {$this->harga} hh
    //             INNER JOIN (
    //                 SELECT barcode, MAX(tanggal) AS maxtgl
    //                 FROM {$this->harga}
    //                 GROUP BY barcode
    //             ) xx ON hh.barcode = xx.barcode AND hh.tanggal = xx.maxtgl
    //         ) h ON d.barcode = h.barcode
    //         GROUP BY n.notajual, n.tanggal, p.nama
    //         ORDER BY n.tanggal DESC
    //     ";

    //     $query = $this->db->query($sql);
    //     return $query->getResultArray();
    // }

    // === Nota Konsinyasi: Tambah ===

    public function insertNotaKonsinyasi($data)
    {
        $this->db->transStart();

        // Auto-generate No. Nota Konsinyasi
        $sql = "SELECT LPAD(
                    COALESCE(CAST(MAX(notajual) AS UNSIGNED), 0) + 1,
                    6,
                    '0'
                ) AS next_notajual
                FROM nota_konsinyasi";

        $notajual = $this->db->query($sql)->getRow()->next_notajual;

        // Insert ke master nota_konsinyasi
        $notaData = [
            "notajual" => $notajual,
            "tanggal"  => date("Y-m-d H:i:s"),
            "diskon"   => $data["diskon"] ?? 0,
            "ppn"      => $data["ppn"] ?? 0,
            "userid"   => $data["userid"],
            // kolom status default 'pending' (di DB)
        ];

        $this->db->table($this->nota_konsinyasi)->insert($notaData);

        // Insert detail ke nota_konsinyasi_detail
        foreach ($data["detail"] as $row) {
            $detailData = [
                "notajual"       => $notajual,
                "notakonsinyasi" => $row["notakonsinyasi"],
                "barcode"        => $row["barcode"],
                "jumlah"         => $row["jumlah"]
            ];
            $this->db->table($this->nota_konsinyasi_detail)->insert($detailData);
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
                "message" => "Nota Konsinyasi berhasil disimpan"
            ];
        }
    }
    // public function insertNotaKonsinyasi($data)
    // {
    //     $this->db->transStart();

    //     // Insert ke master nota_konsinyasi
    //     $notaData = [
    //         "notajual" => $data["notajual"],
    //         "tanggal"  => date("Y-m-d H:i:s"),
    //         "diskon"   => $data["diskon"],
    //         "ppn"      => $data["ppn"],
    //         "userid"   => $data["userid"],
    //         // kolom status default 'pending' (di DB)
    //     ];

    //     $this->db->table($this->nota_konsinyasi)->insert($notaData);

    //     // Insert detail ke nota_konsinyasi_detail
    //     foreach ($data["detail"] as $row) {
    //         $detailData = [
    //             "notajual"       => $data["notajual"],
    //             "notakonsinyasi" => $row["notakonsinyasi"],
    //             "barcode"        => $row["barcode"],
    //             "jumlah"         => $row["jumlah"]
    //         ];
    //         $this->db->table($this->nota_konsinyasi_detail)->insert($detailData);
    //     }

    //     $this->db->transComplete();

    //     if ($this->db->transStatus() === false) {
    //         $this->db->transRollback();
    //         return [
    //             "status"  => false,
    //             "message" => "DB Error: " . $this->db->error()["message"]
    //         ];
    //     } else {
    //         $this->db->transCommit();
    //         return [
    //             "status"  => true,
    //             "message" => "Nota Konsinyasi berhasil disimpan"
    //         ];
    //     }
    // }


    // === Nota Konsinyasi: Hapus ===

    public function hapusNotaKonsinyasi($data, $notajual)
    {
        $builder = $this->db->table($this->nota_konsinyasi)->where('notajual', $notajual);
        $query = $builder->update($data);

        if ($query) {
            return ["code" => 0, "message" => ""];
        } else {
            return $this->db->error();
        }
    }

    // === Retur Konsinyasi: Index ===

    public function listReturKonsinyasi()
    {
        $sql = "SELECT * FROM {$this->retur_konsinyasi} WHERE is_void='0'";
        $query = $this->db->query($sql);

        if ($query) {
            return $query->getResultArray();
        } else {
            return $this->db->error();
        }
    }

    // === Retur Konsinyasi: Tambah ===

    public function insertReturKonsinyasi($data)
    {
        $this->db->transStart();

        // Insert ke master nota_konsinyasi (retur)
        $notaData = [
            "noretur"      => $data["noretur"],
            "tanggal"      => date("Y-m-d H:i:s"),
            "nokonsinyasi" => $data["nokonsinyasi"],
            "is_void"      => 0,
            "userid"       => $data["userid"],
        ];

        $this->db->table($this->retur_konsinyasi)->insert($notaData);

        // Insert detail retur
        foreach ($data["detail"] as $row) {
            $detailData = [
                "noretur" => $data["noretur"],
                "barcode" => $row["barcode"],
                "jumlah"  => $row["jumlah"],
                "alasan"  => $row["alasan"]
            ];
            $this->db->table($this->retur_konsinyasi_detail)->insert($detailData);
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
                "message" => "Retur Konsinyasi berhasil disimpan"
            ];
        }
    }

    // === Retur Konsinyasi: Hapus ===

    public function hapusReturKonsinyasi($data, $noretur)
    {
        $builder = $this->db->table($this->retur_konsinyasi)->where('noretur', $noretur);
        $query = $builder->update($data);

        if ($query) {
            return ["code" => 0, "message" => ""];
        } else {
            return $this->db->error();
        }
    }

    // === Nota dan Retur Konsinyasi: Fungsi Pendukung ===

    public function getAvailableDO()
    {
        $sql = "
            SELECT 
                dox.nonota AS do_id
            FROM do_konsinyasi dox
            JOIN do_konsinyasi_detail d ON dox.nonota = d.nonota
            LEFT JOIN nota_konsinyasi_detail n 
                ON n.notakonsinyasi = d.nonota 
                AND n.barcode = d.barcode
            WHERE dox.is_void = 0
            GROUP BY dox.nonota
            HAVING SUM(d.jumlah) - IFNULL(SUM(n.jumlah), 0) > 0
            ORDER BY dox.nonota DESC
        ";

        return $this->db->query($sql)->getResultArray();
    }

    // public function getProdukByDo($do_id)
    // {
    //     $sql = "
    //         SELECT 
    //             d.barcode,
    //             p.namaproduk AS nama,
    //             d.jumlah - IFNULL(SUM(n.jumlah), 0) AS sisa
    //         FROM do_konsinyasi_detail d
    //         JOIN do_konsinyasi dox ON dox.nonota = d.nonota
    //         JOIN produk p ON p.barcode = d.barcode
    //         LEFT JOIN nota_konsinyasi_detail n 
    //             ON n.notakonsinyasi = d.nonota 
    //             AND n.barcode = d.barcode
    //         WHERE d.nonota = ?
    //         AND dox.is_void = 0
    //         GROUP BY d.nonota, d.barcode, d.jumlah, p.namaproduk
    //         HAVING sisa > 0
    //     ";

    //     return $this->db->query($sql, [$do_id])->getResultArray();
    // }
    public function getProdukByDo($do_id)
    {
        $sql = "
            SELECT 
                d.barcode,
                p.namaproduk AS nama,
                h.harga_konsinyasi AS harga,
                d.jumlah - IFNULL(SUM(n.jumlah), 0) AS sisa
            FROM do_konsinyasi_detail d
            JOIN do_konsinyasi dox ON dox.nonota = d.nonota
            JOIN produk p ON p.barcode = d.barcode
            JOIN harga h ON h.barcode = d.barcode
            LEFT JOIN nota_konsinyasi_detail n 
                ON n.notakonsinyasi = d.nonota 
                AND n.barcode = d.barcode
            WHERE d.nonota = ?
            AND dox.is_void = 0
            GROUP BY d.nonota, d.barcode, d.jumlah, p.namaproduk, h.harga_konsinyasi
            HAVING sisa > 0
        ";

        return $this->db->query($sql, [$do_id])->getResultArray();
    }
}
