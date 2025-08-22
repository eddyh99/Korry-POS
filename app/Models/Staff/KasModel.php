<?php

namespace App\Models\Staff;

use CodeIgniter\Model;

class KasModel extends Model
{
    protected $table = 'kas';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'storeid', 'tanggal', 'dateonly', 'nominal', 'jenis', 'keterangan', 'userid', 'lastupdate'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'tanggal';
    protected $updatedField  = 'lastupdate';

    // public function insertData($data)
    // {
    //     $today = date("Y-m-d");

    //     if ($data['jenis'] === 'Kas Awal') {
    //         $sql = "SELECT * FROM kas WHERE dateonly=? AND storeid=? AND jenis='Kas Awal'";
    //         $query = $this->db->query($sql, [$today, $data['storeid']]);

    //         if ($query->getNumRows() > 0) {
    //             return ['code' => 5051, 'message' => 'Kas awal sudah pernah dimasukkan hari ini'];
    //         } else {
    //             $this->insert($data);
    //         }
    //     } else {
    //         $this->insert($data);
    //     }
    // }
    public function insertData($data)
    {
        $today = date("Y-m-d");

        if ($data['jenis'] === 'Kas Awal') {
            $sql = "SELECT * FROM kas WHERE dateonly=? AND storeid=? AND jenis='Kas Awal'";
            $query = $this->db->query($sql, [$today, $data['storeid']]);

            if ($query->getNumRows() > 0) {
                return [
                    'code'    => 5051,
                    'message' => 'Kas awal sudah pernah dimasukkan hari ini'
                ];
            }
        }

        // Proses insert data
        if ($this->insert($data)) {
            return [
                'code'    => 0,
                'message' => 'Data berhasil disimpan'
            ];
        } else {
            return [
                'code'    => 500,
                'message' => 'Gagal menyimpan data'
            ];
        }
    }

    public function openkas()
    {
        $today = date("Y-m-d");
        $before = date('Y-m-d', strtotime($today . ' -1 day'));
        $storeid = session()->get('logged_status')['storeid'];

        $sql = "SELECT * FROM kas WHERE storeid=? AND jenis='Kas Awal'";
        $open = $this->db->query($sql, [$storeid])->getNumRows();

        if ($open > 1) {
            $sql = "SELECT * FROM kas WHERE dateonly=? AND storeid=? AND jenis='Kas Sisa' ORDER BY dateonly DESC LIMIT 1";
            $query = $this->db->query($sql, [$before, $storeid]);
            if ($query->getNumRows() === 0) {
                return "5052";
            } else {
                $sql = "SELECT * FROM kas WHERE dateonly=? AND storeid=? AND jenis='Kas Awal'";
                $query = $this->db->query($sql, [$today, $storeid]);
                if ($query->getNumRows() === 0) {
                    return "5051";
                }
            }
        } else {
            $sql = "SELECT * FROM kas WHERE dateonly=? AND storeid=? AND jenis='Kas Awal'";
            $query = $this->db->query($sql, [$today, $storeid]);
            if ($query->getNumRows() === 0) {
                return "5051";
            }
        }
    }

    public function listkas()
    {
        $now = date("Y-m-d");
        $storeid = session()->get('logged_status')['storeid'];

        $sql = "SELECT * FROM kas WHERE DATE(tanggal)=? AND storeid=?";
        return $this->db->query($sql, [$now, $storeid])->getResultArray();
    }

    public function lastsaldo($today, $storeid = '')
    {
        if (empty($storeid)) {
            $storeid = session()->get('logged_status')['storeid'];
        }

        $mdata['saldo'] = 0;
        $sql = "SELECT nominal FROM kas WHERE storeid=? AND jenis='Kas Sisa' AND tanggal<? ORDER BY tanggal DESC LIMIT 1";
        $query = $this->db->query($sql, [$storeid, $today]);
        if ($query->getNumRows() > 0) {
            $mdata['saldo'] = $query->getRow()->nominal;
        }

        // ambil seluruh transaksi hari ini
        $sjual = "SELECT a.tanggal, a.method, b.barcode, b.jumlah, b.diskonn, b.diskonp 
                  FROM penjualan a INNER JOIN penjualan_detail b ON a.id=b.id 
                  WHERE date(a.tanggal)=? AND a.storeid=?";
        $penjualan = $this->db->query($sjual, [$today, $storeid])->getResultArray();

        $mdata['jual'] = 0;
        $mdata['tunai'] = 0;
        foreach ($penjualan as $dt) {
            $sql = "SELECT harga FROM harga WHERE tanggal<=? AND barcode=? ORDER BY tanggal DESC LIMIT 1";
            $harga = $this->db->query($sql, [$dt['tanggal'], $dt['barcode']])->getRow()->harga;

            $total = ($dt['jumlah'] * $harga) - $dt['diskonn'] - $dt['diskonp'];
            $mdata['jual'] += $total;
            if ($dt['method'] === 'cash') {
                $mdata['tunai'] += $total;
            }
        }

        // ambil seluruh retur hari ini
        $sretur = "SELECT c.barcode, c.jumlah, a.jual_id FROM retur a 
                   INNER JOIN retur_detail c ON a.id=c.id 
                   WHERE date(a.tanggal)=? AND a.storeid=?";
        $retur = $this->db->query($sretur, [$today, $storeid])->getResultArray();

        $mdata['retur']['tunai'] = 0;
        $mdata['retur']['non'] = 0;
        foreach ($retur as $dt) {
            $sjual = "SELECT a.tanggal, a.method, (b.diskonn+b.diskonp) as diskon 
                      FROM penjualan a
                      INNER JOIN penjualan_detail b ON a.id=b.id
                      WHERE a.id=? AND b.barcode=?";
            $qjual = $this->db->query($sjual, [$dt['jual_id'], $dt['barcode']])->getResultArray();

            $sql = "SELECT harga FROM harga WHERE tanggal<=? AND barcode=? ORDER BY tanggal DESC LIMIT 1";
            $harga = $this->db->query($sql, [$qjual[0]['tanggal'], $dt['barcode']])->getRow()->harga;

            $total = $dt['jumlah'] * ($harga - $qjual[0]['diskon']);
            if ($qjual[0]['method'] === 'cash') {
                $mdata['retur']['tunai'] += $total;
            } else {
                $mdata['retur']['non'] += $total;
            }
        }

        // tarik keluar masuk kas
        $sql = "SELECT * FROM kas WHERE storeid=? AND date(tanggal)=?";
        $mdata['kas'] = $this->db->query($sql, [$storeid, $today])->getResultArray();

        return $mdata;
    }

    // public function setSisa($data)
    // {
    //     $today = date("Y-m-d");
    //     $sql = "SELECT * FROM kas WHERE dateonly=? AND storeid=? AND jenis='Kas Sisa'";
    //     $query = $this->db->query($sql, [$today, $data['storeid']]);

    //     if ($query->getNumRows() > 0) {
    //         return ['code' => 5051];
    //     } else {
    //         $this->insert($data);
    //         return ['code' => 0];
    //     }
    // }
    public function setSisa($data) 
    {
        $tanggal = $data['tanggal']; // ambil tanggal dari form, bukan hari ini

        $sql = "SELECT * FROM kas WHERE dateonly=? AND storeid=? AND jenis='Kas Sisa'";
        $query = $this->db->query($sql, [$tanggal, $data['storeid']]);

        if ($query->getNumRows() > 0) {
            return ['code' => 5051]; // apabila sudah pernah ditutup utk tanggal tsb
        } else {
            $this->insert($data);
            return ['code' => 0];
        }
    }


    public function notclosedstore()
    {
        $now = date("Y-m-d");
        $sql = "SELECT storeid FROM store WHERE storeid NOT IN (SELECT storeid FROM kas WHERE jenis='Kas Sisa' AND dateonly=?) AND storeid!=6";
        return $this->db->query($sql, [$now])->getResultArray();
    }
}
