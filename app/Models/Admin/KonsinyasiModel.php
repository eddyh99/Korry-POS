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

    protected $penyesuaian        = 'penyesuaian';
    protected $store              = 'store';
    protected $penjualan          = 'penjualan';
    protected $penjualan_detail   = 'penjualan_detail';
    protected $pindah             = 'pindah';
    protected $pindah_detail      = 'pindah_detail';
    protected $produk             = 'produk';
    protected $produksize         = 'produksize';

    protected $pengguna = 'pengguna';

    protected $metode_bayar       = 'metode_bayar';

    public function getStokReturKonsinyasi($barcode, $storeid, $size)
    {
        $where = [
            $barcode, $size, $storeid,
            $barcode, $size, $storeid,
            $barcode, $size, $storeid,
            $barcode, $size, $storeid,
            $barcode, $size, $storeid,
            $barcode, $size, $storeid,
        ];

        $sql = "SELECT IFNULL(SUM(x.total),0) AS stok
                FROM (
                    -- Penjualan (keluar)
                    SELECT barcode, SUM(jumlah)*-1 AS total, size, storeid
                    FROM penjualan c INNER JOIN penjualan_detail d ON c.id=d.id
                    WHERE barcode=? AND size=? AND storeid=?

                    UNION ALL

                    -- Penyesuaian (approved)
                    SELECT barcode, SUM(jumlah) AS total, size, storeid
                    FROM penyesuaian
                    WHERE approved='1' AND barcode=? AND size=? AND storeid=?

                    UNION ALL

                    -- Retur (masuk)
                    SELECT barcode, SUM(jumlah) AS total, size, storeid
                    FROM retur a INNER JOIN retur_detail b ON a.id=b.id
                    WHERE barcode=? AND size=? AND storeid=?

                    UNION ALL

                    -- Pindah keluar
                    SELECT barcode, SUM(jumlah)*-1 AS total, size, dari AS storeid
                    FROM pindah e INNER JOIN pindah_detail f ON e.mutasi_id=f.mutasi_id
                    WHERE e.approved='1' AND barcode=? AND size=? AND dari=?

                    UNION ALL

                    -- Pindah masuk
                    SELECT barcode, SUM(jumlah) AS total, size, tujuan AS storeid
                    FROM pindah e INNER JOIN pindah_detail f ON e.mutasi_id=f.mutasi_id
                    WHERE e.approved='1' AND barcode=? AND size=? AND tujuan=?

                    UNION ALL

                    -- Pinjam (keluar yg belum kembali)
                    SELECT barcode, SUM(jumlah)*-1 AS total, size, storeid
                    FROM pinjam a INNER JOIN pinjam_detail b ON a.id=b.id
                    WHERE (ISNULL(kembali) OR status='tidak') 
                    AND barcode=? AND size=? AND storeid=?
                ) x";

        return $this->db->query($sql, $where)->getRow()->stok;
    }

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

    public function listDoinvoice()
    {
        $sql = "SELECT 
                    a.nonota,
                    a.tanggal,
                    p.nama AS partner,
                    COALESCE(SUM((d.jumlah 
                                - IFNULL(rkd.total_retur,0) 
                                - IFNULL(nkd.total_invoiced,0)) * h.harga_konsinyasi), 0) AS total_sisa,
                    CONCAT(
                        '[',
                        GROUP_CONCAT(
                            CONCAT(
                                '{',
                                    '\"barcode\":', '\"', d.barcode, '\"', ',',
                                    '\"namaproduk\":', '\"', IFNULL(pr.namaproduk,''), '\"', ',',
                                    '\"size\":', '\"', d.size, '\"', ',',
                                    '\"jumlah_do\":', d.jumlah, ',', 
                                    '\"jumlah_retur\":', IFNULL(rkd.total_retur,0), ',', 
                                    '\"jumlah_invoice\":', IFNULL(nkd.total_invoiced,0), ',', 
                                    '\"sisa\":', (d.jumlah - IFNULL(rkd.total_retur,0) - IFNULL(nkd.total_invoiced,0)),
                                '}'
                            )
                            SEPARATOR ','
                        ),
                        ']'
                    ) AS list_barang_sisa
                FROM do_konsinyasi a
                INNER JOIN partner_konsinyasi p 
                    ON a.id_partnerkonsinyasi = p.id
                INNER JOIN do_konsinyasi_detail d 
                    ON a.nonota = d.nonota
                LEFT JOIN (
                    SELECT rk.noretur, barcode, size, SUM(jumlah) AS total_retur
                    FROM retur_konsinyasi_detail rkd
                    INNER JOIN retur_konsinyasi rk 
                        ON rkd.noretur = rk.noretur
                    WHERE rk.is_void = 0
                    GROUP BY noretur, barcode, size
                ) rkd ON rkd.noretur IN (
                            SELECT noretur 
                            FROM retur_konsinyasi 
                            WHERE nokonsinyasi = a.nonota
                        )
                    AND d.barcode = rkd.barcode 
                    AND d.size = rkd.size
                LEFT JOIN (
                    SELECT notakonsinyasi, barcode, size, SUM(jumlah) AS total_invoiced
                    FROM nota_konsinyasi_detail
                    GROUP BY notakonsinyasi, barcode, size
                ) nkd ON d.nonota = nkd.notakonsinyasi 
                    AND d.barcode = nkd.barcode 
                    AND d.size = nkd.size
                INNER JOIN (
                    SELECT hh.barcode, hh.harga_konsinyasi, hh.tanggal
                    FROM harga hh
                    INNER JOIN (
                        SELECT barcode, MAX(tanggal) AS maxtgl
                        FROM harga
                        GROUP BY barcode
                    ) xx ON hh.barcode = xx.barcode AND hh.tanggal = xx.maxtgl
                ) h ON d.barcode = h.barcode
                LEFT JOIN produk pr 
                    ON d.barcode = pr.barcode
                WHERE a.is_void = 0
                AND (d.jumlah - IFNULL(rkd.total_retur,0) - IFNULL(nkd.total_invoiced,0)) > 0
                GROUP BY a.nonota, a.tanggal, p.nama
                ORDER BY a.tanggal DESC;
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
                'size'    => $row["size"],
                'jumlah'  => $row["jumlah"]
            ];
            $this->db->table($this->do_konsinyasi_detail)->insert($detail);
        }

        $this->db->transComplete();

        if ($this->db->transStatus() === false) {
            $this->db->transRollback();
            return [
                "status"  => false,
                "message" => "DB Error: " . $this->db->error()->message
            ];
        } else {
            $this->db->transCommit();
            return [
                "status"  => true,
                "message" => "Data berhasil disimpan",
                "nonota"  => $nonota   // <== ini buat dipakai ke JS cetak
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

    // Tidak Bisa Tanpa DO

    public function listNotaKonsinyasi1()
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

    // Bisa tanpa DO

    public function listNotaKonsinyasi()
    {
        $sql = "
            SELECT 
                n.notajual,
                n.tanggal,
                COALESCE(p.nama, '-') AS partner,
                COALESCE(SUM(d.jumlah * h.harga_konsinyasi), 0) AS total
            FROM {$this->nota_konsinyasi} n
            INNER JOIN {$this->nota_konsinyasi_detail} d 
                ON n.notajual = d.notajual
            LEFT JOIN {$this->do_konsinyasi} doo 
                ON d.notakonsinyasi = doo.nonota 
                AND doo.is_void = 0
            LEFT JOIN {$this->partner_konsinyasi} p 
                ON doo.id_partnerkonsinyasi = p.id
                AND p.status = 0
            LEFT JOIN (
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

        return $this->db->query($sql)->getResultArray();
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
                "status"   => true,
                "message"  => "Nota Konsinyasi berhasil disimpan",
                "notajual" => $notajual   // <== ini buat dipakai ke JS cetak
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


    public function insertReturKonsinyasi($data)
    {
        $this->db->transStart();

        // Auto-generate No. Retur Konsinyasi
        $sql = "SELECT LPAD(
                    COALESCE(CAST(MAX(noretur) AS UNSIGNED), 0) + 1,
                    6,
                    '0'
                ) AS next_noretur
                FROM retur_konsinyasi";

        $noretur = $this->db->query($sql)->getRow()->next_noretur;

        // Insert master retur
        $notaData = [
            "noretur"      => $noretur,
            "tanggal"      => date("Y-m-d H:i:s"),
            "nokonsinyasi" => $data["nokonsinyasi"],
            "is_void"      => 0,
            "userid"       => $data["userid"],
        ];
        $this->db->table($this->retur_konsinyasi)->insert($notaData);

        // Insert detail retur + catat ke penyesuaian
        foreach ($data["detail"] as $row) {
            $detailData = [
                "noretur" => $noretur,
                "barcode" => $row["barcode"],
                "jumlah"  => $row["jumlah"],
                "size"    => $row["size"],
                "alasan"  => $row["alasan"]
            ];
        }
        $this->db->table($this->retur_konsinyasi_detail)->insertBatch($detailData);

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
    // public function getProdukByDo($do_id)
    // {
    //     $sql = "
    //         SELECT 
    //             d.barcode,
    //             p.namaproduk AS nama,
    //             h.harga_konsinyasi AS harga,
    //             d.jumlah - IFNULL(SUM(n.jumlah), 0) AS sisa
    //         FROM do_konsinyasi_detail d
    //         JOIN do_konsinyasi dox ON dox.nonota = d.nonota
    //         JOIN produk p ON p.barcode = d.barcode
    //         JOIN harga h ON h.barcode = d.barcode
    //         LEFT JOIN nota_konsinyasi_detail n 
    //             ON n.notakonsinyasi = d.nonota 
    //             AND n.barcode = d.barcode
    //         WHERE d.nonota = ?
    //         AND dox.is_void = 0
    //         GROUP BY d.nonota, d.barcode, d.jumlah, p.namaproduk, h.harga_konsinyasi
    //         HAVING sisa > 0
    //     ";

    //     return $this->db->query($sql, [$do_id])->getResultArray();
    // }
    // public function getProdukByDo($do_id)
    // {
    //     $sql = "
    //         SELECT 
    //             d.barcode,
    //             p.namaproduk AS nama,
    //             h.harga_konsinyasi AS harga,
    //             d.jumlah 
    //                 - IFNULL(SUM(n.jumlah), 0) 
    //                 - IFNULL(SUM(r.jumlah), 0) AS sisa
    //         FROM do_konsinyasi_detail d
    //         JOIN do_konsinyasi dox 
    //             ON dox.nonota = d.nonota
    //         JOIN produk p 
    //             ON p.barcode = d.barcode
    //         JOIN harga h 
    //             ON h.barcode = d.barcode
    //         LEFT JOIN nota_konsinyasi_detail n 
    //             ON n.notakonsinyasi = d.nonota 
    //             AND n.barcode = d.barcode
    //         LEFT JOIN retur_konsinyasi_detail r 
    //             ON r.barcode = d.barcode
    //         LEFT JOIN retur_konsinyasi rh 
    //             ON rh.noretur = r.noretur
    //             AND rh.nokonsinyasi = d.nonota
    //             AND rh.is_void = 0
    //         WHERE d.nonota = ?
    //         AND dox.is_void = 0
    //         GROUP BY d.nonota, d.barcode, d.jumlah, p.namaproduk, h.harga_konsinyasi
    //         HAVING sisa > 0
    //     ";

    //     return $this->db->query($sql, [$do_id])->getResultArray();
    // }

    // Join produksize untuk retur_konsinyasi
    public function getProdukByDo($do_id)
    {
        $sql = "
            SELECT 
                d.barcode,
                p.namaproduk AS nama,
                ps.size,
                h.harga_konsinyasi AS harga,
                d.jumlah 
                    - IFNULL(SUM(n.jumlah), 0) 
                    - IFNULL(SUM(r.jumlah), 0) AS sisa
            FROM do_konsinyasi_detail d
            JOIN do_konsinyasi dox 
                ON dox.nonota = d.nonota
            JOIN produk p 
                ON p.barcode = d.barcode
            JOIN harga h 
                ON h.barcode = d.barcode
            JOIN produksize ps 
                ON ps.barcode = d.barcode
            LEFT JOIN nota_konsinyasi_detail n 
                ON n.notakonsinyasi = d.nonota 
                AND n.barcode = d.barcode
            LEFT JOIN retur_konsinyasi_detail r 
                ON r.barcode = d.barcode
            LEFT JOIN retur_konsinyasi rh 
                ON rh.noretur = r.noretur
                AND rh.nokonsinyasi = d.nonota
                AND rh.is_void = 0
            WHERE d.nonota = ?
            AND dox.is_void = 0
            AND ps.status = 0
            GROUP BY d.nonota, d.barcode, d.jumlah, p.namaproduk, ps.size, h.harga_konsinyasi
            HAVING sisa > 0
        ";

        return $this->db->query($sql, [$do_id])->getResultArray();
    }

    public function getAllProdukTanpaDo()
    {
        $sql = "
            SELECT 
                p.barcode,
                p.namaproduk AS nama,
                ps.size,
                h.harga_konsinyasi AS harga,
                NULL AS sisa
            FROM produk p
            JOIN harga h 
                ON h.barcode = p.barcode
            JOIN produksize ps 
                ON ps.barcode = p.barcode
            WHERE ps.status = 0
        ";

        return $this->db->query($sql)->getResultArray();
    }

    // DO, Nota, Retur : Print

    public function getAllNotaDo($nonota_do)
    {
        $mdata = [
            "header" => null,
            "detail" => [],
            "footer" => null,
        ];

        // === Ambil header DO Konsinyasi
        $sql1 = "SELECT a.nonota, a.tanggal, a.userid, b.nama AS nama_user, 
                    c.nama AS nama_partner, c.alamat AS alamat_partner, c.kontak AS kontak_partner
                FROM {$this->do_konsinyasi} a
                INNER JOIN {$this->pengguna} b ON a.userid = b.username
                INNER JOIN {$this->partner_konsinyasi} c ON a.id_partnerkonsinyasi = c.id
                WHERE a.nonota = ? AND a.is_void = 0 AND c.status = 0
                LIMIT 1";

        $header = $this->db->query($sql1, [$nonota_do])->getRow();

        if ($header) {
            $mdata["header"] = $header;
        } else {
            // Kalau DO tidak ditemukan, return tetap ada structure kosong supaya view aman
            $mdata["header"] = (object) [
                "nonota"        => $nonota_do,
                "tanggal"       => null,
                "userid"        => null,
                "nama_user"     => "-",
                "nama_partner"  => "-",
                "alamat_partner"=> "-",
                "kontak_partner"=> "-"
            ];
        }

        // === Ambil detail DO Konsinyasi (join produk, size, harga)
        $sql2 = "SELECT a.barcode, a.jumlah, 
                    b.namaproduk, b.namabrand, b.namakategori, b.namafabric, 
                    b.namawarna, b.sku,
                    s.size,
                    h.harga_konsinyasi
                FROM {$this->do_konsinyasi_detail} a
                INNER JOIN {$this->produk} b ON a.barcode = b.barcode
                LEFT JOIN {$this->produksize} s ON a.barcode = s.barcode AND s.status = 0
                LEFT JOIN {$this->harga} h 
                    ON h.barcode = a.barcode 
                    AND h.tanggal = (
                        SELECT MAX(h2.tanggal) 
                        FROM {$this->harga} h2 
                        WHERE h2.barcode = a.barcode
                    )
                WHERE a.nonota = ?";

        $detail = $this->db->query($sql2, [$nonota_do])->getResultArray();

        foreach ($detail as $i => $det) {
            $mdata["detail"][$i] = [
                "barcode"    => $det["barcode"],
                "namaproduk" => $det["namaproduk"],
                "sku"        => $det["sku"],
                "jumlah"     => $det["jumlah"],
                "brand"      => $det["namabrand"],
                "kategori"   => $det["namakategori"],
                "fabric"     => $det["namafabric"],
                "warna"      => $det["namawarna"],
                "size"       => $det["size"],
                "harga"      => $det["harga_konsinyasi"]
            ];
        }

        // === Ambil footer payment method
        $sql3 = "SELECT * FROM {$this->metode_bayar}";

        $footer = $this->db->query($sql3)->getRow();

        if ($footer) {
            $mdata["footer"] = $footer;
        } else {
            // Kalau tidak ditemukan, return tetap ada structure kosong supaya view aman
            $mdata["footer"] = (object) [
                "namaakun"   => "XXXXXXXX",
                "noakun"     => "XXXXXXXX",
                "namabank"   => "XXXXXXXX",
                "cabangbank" => "XXXXXXXX",
                "kodeswift"  => "XXXXXXXX",
                "matauang"   => "XXXXXXXX",
                "negara"     => "XXXXXXXX"
            ];
        }

        return $mdata;
    }

    // Tidak Bisa Tanpa DO

    // public function getAllNotajualNota1($notajual_nota)
    // {
    //     $mdata = [
    //         "header" => null,
    //         "detail" => [],
    //         "footer" => null
    //     ];

    //     // === Ambil header Nota Konsinyasi + info partner via DO
    //     $sql1 = "SELECT n.notajual, n.tanggal, n.userid, n.diskon, n.ppn, n.status,
    //                 u.nama AS nama_user,
    //                 p.nama AS nama_partner, p.alamat AS alamat_partner, p.kontak AS kontak_partner
    //             FROM {$this->nota_konsinyasi} n
    //             INNER JOIN {$this->pengguna} u ON n.userid = u.username
    //             INNER JOIN {$this->nota_konsinyasi_detail} nd ON n.notajual = nd.notajual
    //             INNER JOIN {$this->do_konsinyasi} d ON nd.notakonsinyasi = d.nonota AND d.is_void = 0
    //             INNER JOIN {$this->partner_konsinyasi} p ON d.id_partnerkonsinyasi = p.id AND p.status = 0
    //             WHERE n.notajual = ?
    //             LIMIT 1";

    //     $header = $this->db->query($sql1, [$notajual_nota])->getRow();

    //     if ($header) {
    //         $mdata["header"] = $header;
    //     } else {
    //         // Kalau Nota tidak ditemukan
    //         $mdata["header"] = (object) [
    //             "notajual"       => $notajual_nota,
    //             "tanggal"        => null,
    //             "userid"         => null,
    //             "diskon"         => 0,
    //             "ppn"            => 0,
    //             "status"         => "pending",
    //             "nama_user"      => "-",
    //             "nama_partner"   => "-",
    //             "alamat_partner" => "-",
    //             "kontak_partner" => "-"
    //         ];
    //     }

    //     // === Ambil detail Nota Konsinyasi
    //     $sql2 = "SELECT nd.barcode, nd.jumlah,
    //                 pr.namaproduk, pr.namabrand, pr.namakategori, pr.namafabric, 
    //                 pr.namawarna, pr.sku,
    //                 sz.size,
    //                 hg.harga_konsinyasi
    //             FROM {$this->nota_konsinyasi_detail} nd
    //             INNER JOIN {$this->produk} pr ON nd.barcode = pr.barcode
    //             LEFT JOIN {$this->produksize} sz ON nd.barcode = sz.barcode AND sz.status = 0
    //             LEFT JOIN {$this->harga} hg 
    //                 ON hg.barcode = nd.barcode 
    //                 AND hg.tanggal = (
    //                     SELECT MAX(h2.tanggal) 
    //                     FROM {$this->harga} h2 
    //                     WHERE h2.barcode = nd.barcode
    //                 )
    //             INNER JOIN {$this->do_konsinyasi} d ON nd.notakonsinyasi = d.nonota AND d.is_void = 0
    //             WHERE nd.notajual = ?";

    //     $detail = $this->db->query($sql2, [$notajual_nota])->getResultArray();

    //     foreach ($detail as $i => $det) {
    //         $mdata["detail"][$i] = [
    //             "barcode"    => $det["barcode"],
    //             "namaproduk" => $det["namaproduk"],
    //             "sku"        => $det["sku"],
    //             "jumlah"     => $det["jumlah"],
    //             "brand"      => $det["namabrand"],
    //             "kategori"   => $det["namakategori"],
    //             "fabric"     => $det["namafabric"],
    //             "warna"      => $det["namawarna"],
    //             "size"       => $det["size"],
    //             "harga"      => $det["harga_konsinyasi"]
    //         ];
    //     }

    //     // === Ambil footer payment method
    //     $sql3 = "SELECT * FROM {$this->metode_bayar}";

    //     $footer = $this->db->query($sql3)->getRow();

    //     if ($footer) {
    //         $mdata["footer"] = $footer;
    //     } else {
    //         // Kalau tidak ditemukan, return tetap ada structure kosong supaya view aman
    //         $mdata["footer"] = (object) [
    //             "namaakun"   => "XXXXXXXX",
    //             "noakun"     => "XXXXXXXX",
    //             "namabank"   => "XXXXXXXX",
    //             "cabangbank" => "XXXXXXXX",
    //             "kodeswift"  => "XXXXXXXX",
    //             "matauang"   => "XXXXXXXX",
    //             "negara"     => "XXXXXXXX"
    //         ];
    //     }
        
    //     return $mdata;
    // }

    // Bisa Tanpa DO

    public function getAllNotajualNota($notajual_nota)
    {
        $mdata = [
            "header" => null,
            "detail" => [],
            "footer" => null,
        ];

        // === Ambil header Nota Konsinyasi + info partner via DO
        $sql1 = "SELECT n.notajual, n.tanggal, n.userid, n.diskon, n.ppn, n.status,
                    u.nama AS nama_user,
                    COALESCE(p.nama, '-')   AS nama_partner,
                    COALESCE(p.alamat, '-') AS alamat_partner,
                    COALESCE(p.kontak, '-') AS kontak_partner
                FROM {$this->nota_konsinyasi} n
                INNER JOIN {$this->pengguna} u ON n.userid = u.username
                INNER JOIN {$this->nota_konsinyasi_detail} nd ON n.notajual = nd.notajual
                LEFT JOIN {$this->do_konsinyasi} d 
                    ON nd.notakonsinyasi = d.nonota AND d.is_void = 0
                LEFT JOIN {$this->partner_konsinyasi} p 
                    ON d.id_partnerkonsinyasi = p.id AND p.status = 0
                WHERE n.notajual = ?
                LIMIT 1";

        $header = $this->db->query($sql1, [$notajual_nota])->getRow();
        
        if ($header) {
            $mdata["header"] = $header;
        } else {
            // Kalau Nota tidak ditemukan
            $mdata["header"] = (object) [
                "notajual"       => $notajual_nota,
                "tanggal"        => null,
                "userid"         => null,
                "diskon"         => 0,
                "ppn"            => 0,
                "status"         => "pending",
                "nama_user"      => "-",
                "nama_partner"   => "-",
                "alamat_partner" => "-",
                "kontak_partner" => "-"
            ];
        }

        // === Ambil detail Nota Konsinyasi
        $sql2 = "SELECT nd.barcode, nd.jumlah,
                    pr.namaproduk, pr.namabrand, pr.namakategori, pr.namafabric, 
                    pr.namawarna, pr.sku,
                    sz.size,
                    hg.harga_konsinyasi
                FROM {$this->nota_konsinyasi_detail} nd
                INNER JOIN {$this->produk} pr ON nd.barcode = pr.barcode
                LEFT JOIN {$this->produksize} sz ON nd.barcode = sz.barcode AND sz.status = 0
                LEFT JOIN {$this->harga} hg 
                    ON hg.barcode = nd.barcode 
                    AND hg.tanggal = (
                            SELECT MAX(h2.tanggal) 
                            FROM {$this->harga} h2 
                            WHERE h2.barcode = nd.barcode
                    )
                LEFT JOIN {$this->do_konsinyasi} d 
                    ON nd.notakonsinyasi = d.nonota AND d.is_void = 0
                WHERE nd.notajual = ?";

        $detail = $this->db->query($sql2, [$notajual_nota])->getResultArray();

        foreach ($detail as $i => $det) {
            $mdata["detail"][$i] = [
                "barcode"    => $det["barcode"],
                "namaproduk" => $det["namaproduk"],
                "sku"        => $det["sku"],
                "jumlah"     => $det["jumlah"],
                "brand"      => $det["namabrand"],
                "kategori"   => $det["namakategori"],
                "fabric"     => $det["namafabric"],
                "warna"      => $det["namawarna"],
                "size"       => $det["size"],
                "harga"      => $det["harga_konsinyasi"]
            ];
        }

        // === Ambil footer payment method
        $sql3 = "SELECT * FROM {$this->metode_bayar}";

        $footer = $this->db->query($sql3)->getRow();

        if ($footer) {
            $mdata["footer"] = $footer;
        } else {
            // Kalau tidak ditemukan, return tetap ada structure kosong supaya view aman
            $mdata["footer"] = (object) [
                "namaakun"   => "XXXXXXXX",
                "noakun"     => "XXXXXXXX",
                "namabank"   => "XXXXXXXX",
                "cabangbank" => "XXXXXXXX",
                "kodeswift"  => "XXXXXXXX",
                "matauang"   => "XXXXXXXX",
                "negara"     => "XXXXXXXX"
            ];
        }

        return $mdata;
    }

    public function getReturKonsinyasiDetail($noretur)
    {
        $mdata = [
            "header" => null,
            "detail" => []
        ];

        // === Ambil header retur konsinyasi
        $sql1 = "SELECT r.noretur, r.tanggal, r.nokonsinyasi, r.userid,
                        u.nama AS nama_user
                FROM {$this->retur_konsinyasi} r
                INNER JOIN {$this->pengguna} u ON r.userid = u.username
                WHERE r.noretur = ? AND r.is_void = 0
                LIMIT 1";
        $header = $this->db->query($sql1, [$noretur])->getRow();

        if ($header) {
            $mdata["header"] = $header;
        } else {
            $mdata["header"] = (object) [
                "noretur"      => $noretur,
                "tanggal"      => null,
                "nokonsinyasi" => null,
                "userid"       => null,
                "nama_user"    => "-"
            ];
        }

        // === Ambil detail retur (barang yang dikembalikan)
        $sql2 = "SELECT d.barcode, d.jumlah, d.size, d.alasan,
                        p.namaproduk, p.sku, p.namawarna
                FROM {$this->retur_konsinyasi_detail} d
                INNER JOIN {$this->produk} p ON d.barcode = p.barcode
                WHERE d.noretur = ?";
        $detail = $this->db->query($sql2, [$noretur])->getResultArray();

        foreach ($detail as $i => $det) {
            $mdata["detail"][$i] = [
                "barcode"    => $det["barcode"],
                "namaproduk" => $det["namaproduk"],
                "sku"        => $det["sku"],
                "jumlah"     => $det["jumlah"],
                "size"       => $det["size"],
                "warna"      => $det["namawarna"],
                "alasan"     => $det["alasan"]
            ];
        }

        return $mdata;
    }

}
